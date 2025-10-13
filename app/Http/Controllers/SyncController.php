<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

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

    if ($table == "reports") {
            $data = DB::table($table)
                ->where('updated_at', '>', $since)
                ->where("report_id", auth()->id())
                ->get();
    }else{
        $data = DB::table($table)
            ->where('updated_at', '>', $since)
            ->where("user_id", auth()->id())
            ->get()
            ->map(function ($row) use ($table) {
                $row = (array) $row;

                // ---- معالجة الجدول "invoies"
                if ($table === 'invoies') {
                    if (!empty($row['Transaction_id'])) {
                        $transaction = DB::table('transactions')
                            ->where('id', $row['Transaction_id'])
                            ->where('user_id', auth()->id())
                            ->first();
                        if ($transaction) {
                            $row['Transaction_uuid'] = $transaction->uuid;
                        }
                    }
                    unset($row['Transaction_id']);
                }

                // ---- معالجة جدول "products"
                if ($table === 'products') {
                    // category
                    if (!empty($row['categoris_id'])) {
                        $category = DB::table('categoris')
                            ->where('id', $row['categoris_id'])
                            ->where('user_id', auth()->id())
                            ->first();
                        if ($category) {
                            $row['categoris_uuid'] = $category->uuid;
                        }
                    }
                    unset($row['categoris_id']);

                    // invoice
                    if (!empty($row['invoies_id'])) {
                        $invoice = DB::table('invoies')
                            ->where('id', $row['invoies_id'])
                            ->where('user_id', auth()->id())
                            ->first();
                        if ($invoice) {
                            $row['invoies_uuid'] = $invoice->uuid;
                        }
                    }
                    unset($row['invoies_id']);
                }

                return $row;
        });
                }

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

    // إذا جا سجل واحد فقط نخليه داخل array
    if (isset($payload['uuid'])) {
        $payload = [$payload];
    }

    $results = [];

    foreach ($payload as $data) {
        if (!isset($data['uuid'])) {
            $results[] = ['status' => 'error', 'error' => 'uuid required'];
            continue;
        }

        // ----------------------
        // معالجة العلاقات
        // ----------------------
        if ($table === 'products') {
            // جلب category_id من category_uuid
            if (isset($data['categoris_uuid'])) {
                $category = DB::table('categoris')
                    ->where('uuid', $data['categoris_uuid'])
                    ->where('user_id', auth()->id())
                    ->first();
                if ($category) {
                    $data['categoris_id'] = $category->id;
                }
                unset($data['categoris_uuid']);
            }

            unset($data['categoris_uuid']);

            // جلب invoice_id من invoice_uuid
            if (isset($data['invoies_uuid'])) {
                $invoice = DB::table('invoies')
                    ->where('uuid', $data['invoies_uuid'])
                    ->where('user_id', auth()->id())
                    ->first();
                if ($invoice) {
                    $data['invoies_id'] = $invoice->id;
                }
                unset($data['invoies_uuid']);
            }
             unset($data['invoies_uuid']);


                    // معالجة صورة المنتج (Base64 فقط)
            if ($request->hasFile("Product_image")) {
                $file = $request->file("Product_image");
                $path = $file->store('products', 'public');
                $data['Product_image'] = $path;
            } elseif (!empty($data['Product_image']) && str_starts_with($data['Product_image'], "data:image")) {
                try {
                    $imageName = 'product_' . uniqid() . '.png';
                    $imagePath = 'products/' . $imageName;
                    $base64 = explode(',', $data['Product_image'])[1];
                    Storage::disk('public')->put($imagePath, base64_decode($base64));
                    $data['Product_image'] = $imagePath;
                } catch (\Exception $e) {
                    unset($data['Product_image']);
                }
            }
     }

        if ($table === 'invoies') {
            // جلب transaction_id من Transaction_uuid
            if (isset($data['Transaction_uuid'])) {
                $transaction = DB::table('transactions')
                    ->where('uuid', $data['Transaction_uuid'])
                    ->where('user_id', auth()->id())
                    ->first();
                if ($transaction) {
                    $data['Transaction_id'] = $transaction->id;
                }
                unset($data['Transaction_uuid']);
            }
        }

        if ($table === 'categoris') {
            // 🖼️ معالجة صورة التصنيف (ملف مرفوع أو Base64)
            if ($request->hasFile("categoris_image")) {
                $file = $request->file("categoris_image");
                $path = $file->store('categoris', 'public');
                $data['categoris_image'] = $path;
            } elseif (!empty($data['categoris_image']) && str_starts_with($data['categoris_image'], "data:image")) {
                try {
                    $imageName = 'category_' . uniqid() . '.png';
                    $imagePath = 'categoris/' . $imageName;
                    $base64 = explode(',', $data['categoris_image'])[1];
                    Storage::disk('public')->put($imagePath, base64_decode($base64));
                    $data['categoris_image'] = $imagePath;
                } catch (\Exception $e) {
                    unset($data['categoris_image']);
                }
            }
        }


        // ----------------------
        // معالجة المزامنة
        // ----------------------
        $uuid = $data['uuid'];
        $localUpdatedAt = isset($data['updated_at'])
            ? Carbon::parse($data['updated_at'])
            : Carbon::createFromTimestamp(0);

    if ($table == "reports"){
        $existing = DB::table($table)
            ->where('uuid', $uuid)
            ->where('report_id', auth()->id())
            ->first();
        }else{
                    $existing = DB::table($table)
            ->where('uuid', $uuid)
            ->where('user_id', auth()->id())
            ->first();
        }

        if (!$existing) {
            $now = now();
            $data['created_at'] = isset($data['created_at'])
                ? Carbon::parse($data['created_at'])->format('Y-m-d H:i:s')
                : $now->format('Y-m-d H:i:s');

            $data['updated_at'] = isset($data['updated_at'])
                ? Carbon::parse($data['updated_at'])->format('Y-m-d H:i:s')
                : $now->format('Y-m-d H:i:s');
                if ($table == "reports") {
                    $data['report_id'] = auth()->id();
                }else{
                    $data['user_id'] = auth()->id();

                }

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

                if ($table == "reports") {
                    DB::table($table)
                        ->where('uuid', $uuid)
                        ->where('report_id', auth()->id())
                        ->update($data);
                }else{
                    DB::table($table)
                        ->where('uuid', $uuid)
                        ->where('user_id', auth()->id())
                        ->update($data);
                }
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

    $hasError = collect($results)->contains(function ($r) {
    return isset($r['status']) && $r['status'] === 'error';
    });

    $statusCode = $hasError ? 500 : 200;

    return response()->json($results, $statusCode);

    return response()->json($results);
}




public function syncDeleteData(Request $request, $table)
{
    $allowedTables = [
        'categoris',
        'invoies',
        'notifications',
        'products',
        'reports',
        'transactions',
        'zakats',];
    if (!in_array($table, $allowedTables)) {
        return response()->json(['status' => 0, 'message' => 'جدول غير مسموح'], 400);
    }

    $uuids = $request->input('uuid');

    // 2️⃣ تأكد أن uuid موجود
    if (!$uuids) {
        return response()->json(['status' => 0, 'message' => 'uuid مطلوب'], 422);
    }

    // 3️⃣ إذا جاء واحد فقط حوّله لمصفوفة لتوحيد المعالجة
    if (!is_array($uuids)) {
        $uuids = [$uuids];
    }

    $results = [];

    foreach ($uuids as $uuid) {
        try {
            if($table == "reports"){
            $record = DB::table($table)
                ->where('uuid', $uuid)
                ->where('report_id', auth()->id())
                ->first();
            }else{
             $record = DB::table($table)
                ->where('uuid', $uuid)
                ->where('user_id', auth()->id())
                ->first();
            }
            if (!$record) {
                $results[] = ['uuid' => $uuid, 'status' => 'skipped', 'message' => 'غير موجود'];
                continue;
            }

            if($table == "reports"){
             DB::table($table)
                ->where('uuid', $uuid)
                ->where('report_id', auth()->id())
                ->delete();
            }else{
            DB::table($table)
                ->where('uuid', $uuid)
                ->where('user_id', auth()->id())
                ->delete();
            }


            $results[] = ['uuid' => $uuid, 'status' => 'deleted'];
        } catch (\Exception $e) {
            $results[] = ['uuid' => $uuid, 'status' => 'error', 'message' => $e->getMessage()];
        }
    }

    return response()->json($results);
}

}
