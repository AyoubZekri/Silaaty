<?php

namespace App\Http\Controllers\User\Zakat;

use App\Function\Respons;
use App\Function\Zakats;
use App\Http\Controllers\Controller;
use App\Models\Zakat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShwoZakatController extends Controller
{
    public function index()
    {
        try {
            $zakat = Zakat::where('user_id', auth()->id())->get();

            return Respons::success(['data' => $zakat]);

        } catch (\Exception $e) {
            return Respons::error('حدث خطأ أثناء جلب البيانات', 500, $e->getMessage());
        }
    }


    public static function addCashliquidity(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'liquidity' => 'required',
        ]);


        if ($validator->fails()) {
            return Respons::error("خطا في البيانات ", 422, $validator->errors());
        }

        try {

            Zakat::where("user_id", auth()->id())->update([
                "zakat_Cash_liquidity" => $request->liquidity
            ]);

            Zakats::Zakats();

            return Respons::success();

        } catch (\Throwable $th) {
            return Respons::error("حدث خطا اثناء تحديث السيولة النقدية", 500, $th->getMessage());
        }
    }
}
