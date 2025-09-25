<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SyncController extends Controller
{
    private $allowedTables = [
        'categoris',
        'invoies',
        'notifications',
        'products',
        'reports',
        'transactions',
        'zakats',
    ];

    /**
     * ✅ Pull: جلب البيانات المحدثة منذ آخر تزامن
     */
    public function getData(Request $request, $table)
    {
        if (!in_array($table, $this->allowedTables)) {
            return response()->json(['error' => 'Invalid table'], 400);
        }

        $since = $request->query('since', "1970-01-01T00:00:00Z");

        // فلترة حسب updated_at
        $data = DB::table($table)
            ->where('updated_at', '>', $since)
            ->where("user_id",auth()->id())
            ->get();

        return response()->json($data);
    }

    /**
     * ✅ Push: إدخال أو تحديث البيانات مع حل التعارض
     */
    public function syncData(Request $request, $table)
    {
        if (!in_array($table, $this->allowedTables)) {
            return response()->json(['error' => 'Invalid table'], 400);
        }

        $data = $request->all();

        if (!isset($data['uuid'])) {
            return response()->json(['error' => 'uuid required'], 400);
        }

        $uuid = $data['uuid'];
        $localUpdatedAt = isset($data['updated_at'])
            ? Carbon::parse($data['updated_at'])
            : Carbon::createFromTimestamp(0);

        $existing = DB::table($table)->where('uuid', $uuid)->where("user_id",auth()->id())
          ->first();

        if (!$existing) {
            // إدخال سجل جديد
            $data['created_at'] = $data['created_at'] ?? now()->toISOString();
            $data['updated_at'] = $data['updated_at'] ?? now()->toISOString();
            $data["user_id"]=auth()->id();
            DB::table($table)->insert($data);
        } else {
            $serverUpdatedAt = Carbon::parse($existing->updated_at);

            // تعارض: Last-Write-Wins (آخر تحديث يربح)
            if ($localUpdatedAt->gt($serverUpdatedAt)) {
                $data['updated_at'] = now()->toISOString();
                DB::table($table)->where('uuid', $uuid)->where("user_id",auth()->id())->update($data);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
