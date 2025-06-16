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
        $total_price_Dealer = Product::where('user_id', auth()->id())
            ->where('categorie_id', 3)
            ->sum('product_price_total_purchase');
        $total_price_Convicts = Product::where('user_id', auth()->id())
            ->where('categorie_id', 4)
            ->sum('product_price_total');

            $zakat = Zakat::where('user_id', auth()->id())->first();

            if (!$zakat) {
                return [
                    'status' => 0,
                    'message' => 'حساب الزكاة غير موجود'
                ];
            }
        $zakat_mal = ($total_price + $total_price_Convicts) - $total_price_Dealer;
        $zakat_due = ($zakat_mal * $zakat->zakat_due_amount) / 100;

        $zakat->update([
            "zakat_total_asset_value" => $total_price,
            "zakat_due" => $zakat_due,
        ]);
     }

}
