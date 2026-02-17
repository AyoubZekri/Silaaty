<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Exception;

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
        'sales',
    ];

    // ===============================================
    //               ⚙️ دوال مساعدة (Helper Methods)
    // ===============================================

    /**
     * يحول ID محلي (Foreign Key) إلى UUID المقابل في جدول آخر (لعملية Pull).
     *
     * @param array $row مصفوفة البيانات التي يتم جلبها
     * @param string $sourceTable الجدول الذي يحتوي على ID (مثلاً products)
     * @param string $fkIdName اسم عمود الـ ID في $row (مثلاً categoris_id)
     * @param string $fkUuidName اسم العمود الجديد للـ UUID (مثلاً categoris_uuid)
     * @param string $targetTable اسم الجدول المراد البحث فيه (مثلاً categoris)
     * @return array مصفوفة البيانات بعد إضافة الـ UUID وإزالة الـ ID
     */
    private function mapIdToUuid(array $row, string $sourceTable, string $fkIdName, string $fkUuidName, string $targetTable): array
    {
        // 1. التحقق من وجود وقيمة الـ ID
        if (!empty($row[$fkIdName])) {
            $record = DB::table($targetTable)
                ->where('id', $row[$fkIdName])
                // التحقق من الملكية بناءً على 'user_id' باستثناء جدول 'reports'
                ->where('user_id', auth()->id())
                ->first();

            if ($record) {
                // 2. إضافة الـ UUID
                $row[$fkUuidName] = $record->uuid;
            }
        }
        // 3. إزالة الـ ID المحلي
        unset($row[$fkIdName]);

        return $row;
    }

    /**
     * يحول UUID إلى ID محلي (Foreign Key) في جدول آخر (لعملية Push).
     *
     * @param array $data مصفوفة البيانات التي يتم إرسالها
     * @param string $uuidName اسم عمود الـ UUID في $data (مثلاً categoris_uuid)
     * @param string $idName اسم العمود الجديد للـ ID (مثلاً categoris_id)
     * @param string $targetTable اسم الجدول المراد البحث فيه (مثلاً categoris)
     * @return array مصفوفة البيانات بعد إضافة الـ ID وإزالة الـ UUID
     */
    private function mapUuidToId(array $data, string $uuidName, string $idName, string $targetTable): array
    {
        // 1. التحقق من وجود وقيمة الـ UUID
        if (isset($data[$uuidName]) && !empty($data[$uuidName])) {
            $record = DB::table($targetTable)
                ->where('uuid', $data[$uuidName])
                // التحقق من الملكية
                ->where('user_id', auth()->id())
                ->first();

            if ($record) {
                // 2. إضافة الـ ID المحلي
                $data[$idName] = $record->id;
            }
        }
        // 3. إزالة الـ UUID
        unset($data[$uuidName]);

        return $data;
    }

    /**
     * معالجة تخزين صورة Base64 أو ملف مرفوع وإرجاع المسار.
     *
     * @param Request $request طلب HTTP
     * @param array $data مصفوفة البيانات
     * @param string $fieldName اسم حقل الصورة (مثلاً Product_image)
     * @param string $storageFolder اسم مجلد التخزين (مثلاً products)
     * @return array مصفوفة البيانات بعد تحديث حقل الصورة بمسار التخزين
     */
private function processAndStoreImage(Request $request, array $data, string $fieldName, string $storageFolder): array
{
    // 1️⃣ معالجة ملف مرفوع من الفورم
    if ($request->hasFile($fieldName)) {
        try {
            $file = $request->file($fieldName);
            $path = $file->store($storageFolder, 'public');
            $data[$fieldName] = $path;
        } catch (Exception $e) {
            unset($data[$fieldName]);
        }
    }
    // 2️⃣ معالجة بيانات Base64
    elseif (!empty($data[$fieldName]) && str_starts_with($data[$fieldName], "data:image")) {
        try {
            $imageName = $storageFolder . '_' . uniqid() . '.png';
            $imagePath = $storageFolder . '/' . $imageName;
            $base64 = explode(',', $data[$fieldName])[1];
            Storage::disk('public')->put($imagePath, base64_decode($base64));
            $data[$fieldName] = $imagePath;
        } catch (Exception $e) {
            unset($data[$fieldName]);
        }
    }
    // 3️⃣ معالجة مسار محلي من Flutter (/data/user/...)
    elseif (!empty($data[$fieldName]) && file_exists($data[$fieldName])) {
        try {
            $clientFilePath = $data[$fieldName];
            $imageName = $storageFolder . '_' . uniqid() . '.' . pathinfo($clientFilePath, PATHINFO_EXTENSION);
            $imagePath = $storageFolder . '/' . $imageName;

            $contents = file_get_contents($clientFilePath);
            Storage::disk('public')->put($imagePath, $contents);

            $data[$fieldName] = $imagePath;
        } catch (Exception $e) {
            unset($data[$fieldName]);
        }
    }

    return $data;
}



    // ===============================================
    //                  ✅ Pull (getData)
    // ===============================================

    /**
     * جلب البيانات المحدثة منذ آخر تزامن
     */
public function getData(Request $request, $table)
{
    if (!in_array($table, $this->allowedTables)) {
        return response()->json(['error' => 'Invalid table'], 400);
    }

    $since  = $request->query('since', '1970-01-01T00:00:00Z');
    $limit  = (int) $request->query('limit', 50);
    $offset = (int) $request->query('offset', 0);

    if ($table === 'reports') {
        $query = DB::table($table)
            ->where('updated_at', '>', $since)
            ->where('report_id', auth()->id());
    } else {
        $query = DB::table($table)
            ->where('updated_at', '>', $since)
            ->where('user_id', auth()->id());
    }

    $data = $query
        ->orderBy('updated_at')
        ->offset($offset)
        ->limit($limit)
        ->get()
        ->map(function ($row) use ($table) {
            $row = (array) $row;

            if ($table === 'invoies') {
                $row = $this->mapIdToUuid($row, $table, 'Transaction_id', 'Transaction_uuid', 'transactions');
            }

            if ($table === 'products') {
                $row = $this->mapIdToUuid($row, $table, 'categoris_id', 'categoris_uuid', 'categoris');
                $row = $this->mapIdToUuid($row, $table, 'invoies_id', 'invoies_uuid', 'invoies');
            }

            if ($table === 'sales') {
                $row = $this->mapIdToUuid($row, $table, 'product_id', 'product_uuid', 'products');
                $row = $this->mapIdToUuid($row, $table, 'invoie_id', 'invoie_uuid', 'invoies');
            }

            return $row;
        });

    return response()->json($data);
}


    // ===============================================
    //                  ✅ Push (syncData)
    // ===============================================

    /**
     * إدخال أو تحديث البيانات مع حل التعارض
     */
public function syncData(Request $request, $table)
{
    if (!in_array($table, $this->allowedTables)) {
        return response()->json(['error' => 'Invalid table'], 400);
    }

    $payload = $request->all();

    if (isset($payload['uuid'])) {
        $payload = [$payload];
    }
    $batchSize = 50;
    $results = [];
    foreach (array_chunk($payload, $batchSize) as $batch){
      foreach ($batch as $data) {
        if (!isset($data['uuid'])) {
            $results[] = ['status' => 'error', 'error' => 'uuid required'];
            continue;
        }

        // ----------------------
        //  معالجة العلاقات والملفات (Push)
        // ----------------------
        if ($table === 'products') {
            // تحويل UUIDs إلى IDs محلية
            $data = $this->mapUuidToId($data, 'categoris_uuid', 'categoris_id', 'categoris');
            $data = $this->mapUuidToId($data, 'invoies_uuid', 'invoies_id', 'invoies');

            // معالجة صورة المنتج
            $data = $this->processAndStoreImage($request, $data, 'Product_image', 'products');
        }

        if ($table === 'sales') {
            // تحويل UUIDs إلى IDs محلية
            $data = $this->mapUuidToId($data, 'product_uuid', 'product_id', 'products');
            $data = $this->mapUuidToId($data, 'invoie_uuid', 'invoie_id', 'invoies');
        }

        if ($table === 'invoies') {
             // تحويل Transaction_uuid إلى Transaction_id
             // لا حاجة لـ !empty() هنا لأنها مدمجة في mapUuidToId
             $data = $this->mapUuidToId($data, 'Transaction_uuid', 'Transaction_id', 'transactions');
        }

        if ($table === 'categoris') {
            // معالجة صورة التصنيف
            $data = $this->processAndStoreImage($request, $data, 'categoris_image', 'categoris');
        }


        // ----------------------
        // معالجة المزامنة Core Sync Logic
        // ----------------------
        $uuid = $data['uuid'];
        $localUpdatedAt = isset($data['updated_at'])
            ? Carbon::parse($data['updated_at'])
            : Carbon::createFromTimestamp(0);
        $localUpdatedAt = $localUpdatedAt->addMinutes(70);

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
            $now = now()->addMinutes(70);
            $data['created_at'] = isset($data['created_at'])
                ? Carbon::parse($data['created_at'])->format('Y-m-d H:i:s')
                : $now->format('Y-m-d H:i:s');

            $data['updated_at'] = isset($data['updated_at'])
                ? Carbon::parse($data['updated_at'])->addMinutes(70)->format('Y-m-d H:i:s')
                : $now->addMinutes(70)->format('Y-m-d H:i:s');

                if ($table == "reports") {
                    $data['report_id'] = auth()->id();
                }else{
                    $data['user_id'] = auth()->id();

                }

            try {
                DB::table($table)->insert($data);
                $results[] = ['status' => 'inserted', 'uuid' => $uuid];
            } catch (Exception $e) {
                $results[] = [
                    'status' => 'error',
                    'uuid' => $uuid,
                    'error' => $e->getMessage()
                ];
            }
        } else {
            $serverUpdatedAt = Carbon::parse($existing->updated_at);

            if ($localUpdatedAt->gt($serverUpdatedAt)) {
                $data['updated_at'] = now()->addMinutes(70)->format('Y-m-d H:i:s');
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
                } catch (Exception $e) {
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
    }


    $hasError = collect($results)->contains(function ($r) {
    return isset($r['status']) && $r['status'] === 'error';
    });

    $statusCode = $hasError ? 500 : 200;

    return response()->json($results, $statusCode);
}

    // ===============================================
    //                  ✅ Delete (syncDeleteData)
    // ===============================================

public function syncDeleteData(Request $request, $table)
{
    $allowedTables = [
        'categoris',
        'invoies',
        'notifications',
        'products',
        'reports',
        'transactions',
        'zakats',
        'sales',
    ];
    if (!in_array($table, $allowedTables)) {
        return response()->json(['status' => 0, 'message' => 'جدول غير مسموح'], 400);
    }

    $uuids = $request->input('uuid');

    // 2️⃣ تأكد أن uuid موجود
    if (!$uuids) {
        return response()->json(['status' => 0, 'message' => 'uuid مطلوب'], 422);
    }

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

            // حذف الملفات المرتبطة عند الحذف
            if ($table === 'products' && !empty($record->Product_image)) {
                Storage::disk('public')->delete($record->Product_image);
            }

            if ($table === 'categoris' && !empty($record->categoris_image)) {
                Storage::disk('public')->delete($record->categoris_image);
            }



            $results[] = ['uuid' => $uuid, 'status' => 'deleted'];
        } catch (Exception $e) {
            $results[] = ['uuid' => $uuid, 'status' => 'error', 'message' => $e->getMessage()];
        }
    }

    return response()->json($results);
}

}
