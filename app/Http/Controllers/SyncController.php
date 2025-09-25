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

    $payload = $request->all();

    // إذا جا سجل واحد فقط نخليه داخل array لتوحيد المعالجة
    if (isset($payload['uuid'])) {
        $payload = [$payload];
    }

    $results = [];

    foreach ($payload as $data) {
        if (!isset($data['uuid'])) {
            $results[] = ['status' => 'error', 'error' => 'uuid required'];
            continue;
        }

        $uuid = $data['uuid'];
        $localUpdatedAt = isset($data['updated_at'])
            ? Carbon::parse($data['updated_at'])
            : Carbon::createFromTimestamp(0);

        $existing = DB::table($table)
            ->where('uuid', $uuid)
            ->where('user_id', auth()->id())
            ->first();

        if (!$existing) {
            $now = now();
            $data['created_at'] = isset($data['created_at'])
                ? Carbon::parse($data['created_at'])->format('Y-m-d H:i:s')
                : $now->format('Y-m-d H:i:s');

            $data['updated_at'] = isset($data['updated_at'])
                ? Carbon::parse($data['updated_at'])->format('Y-m-d H:i:s')
                : $now->format('Y-m-d H:i:s');

            $data['user_id'] = auth()->id();

            try {
                DB::table($table)->insert($data);
                $results[] = ['status' => 'inserted', 'uuid' => $uuid];
            } catch (\Exception $e) {
                $results[] = [
                    'status' => 'error',
                    'uuid' => $uuid,
                    'error' => $e->getMessage()
                ];
            }
        } else {
            $serverUpdatedAt = Carbon::parse($existing->updated_at);

            if ($localUpdatedAt->gt($serverUpdatedAt)) {
                $data['updated_at'] = now()->format('Y-m-d H:i:s');
                try {
                    DB::table($table)
                        ->where('uuid', $uuid)
                        ->where('user_id', auth()->id())
                        ->update($data);
                    $results[] = ['status' => 'updated', 'uuid' => $uuid];
                } catch (\Exception $e) {
                    $results[] = [
                        'status' => 'error',
                        'uuid' => $uuid,
                        'error' => $e->getMessage()
                    ];
                }
            } else {
                $results[] = ['status' => 'skipped', 'uuid' => $uuid];
            }
        }
    }

    return response()->json($results);
}

}
