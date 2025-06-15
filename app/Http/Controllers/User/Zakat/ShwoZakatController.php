<?php

namespace App\Http\Controllers\User\Zakat;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Zakat;
use Illuminate\Http\Request;

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
}
