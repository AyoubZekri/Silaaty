<?php

namespace App\Function;

use App\Models\categories;
use App\Models\Product;
use App\Models\Zakat;


class Zakats
{

     public static function Zakats(){



        $total_price = Product::where('user_id', auth()->id())
            ->where('categorie_id', 1)
            ->sum('product_price_total');

            $zakat = Zakat::where('user_id', auth()->id())->first();

            if (!$zakat) {
                return [
                    'status' => 0,
                    'message' => 'حساب الزكاة غير موجود'
                ];
            }
        $zakat_due = ($total_price * $zakat->zakat_due_amount) / 100;

        $zakat->update([
            "zakat_total_asset_value" => $total_price,
            "zakat_due" => $zakat_due,
        ]);
     }

}
