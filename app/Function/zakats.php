<?php

namespace App\Function;

use App\Models\categories;
use App\Models\invoies;
use App\Models\Product;
use App\Models\Zakat;


class Zakats
{

    // زكاة العروض = [(قيمة البضاعة + السيولة النقدية + الديون المستحقة لك) - (الديون التي عليك)] × 2.5%


    public static function Zakats()
    {
        $userId = auth()->id();

        // 1. البضائع العادية
        $total_goods = Product::where('user_id', $userId)
            ->where('categorie_id', 1)
            ->sum('product_price_total');

        // 2. ديون الزبائن (زبائن ميؤوس منهم)
// العملاء المدينين (transactions = 2)
        $total_debtors_products = invoies::where('user_id', $userId)
            ->whereHas('transaction', function ($q) {
                $q->where('transactions', 2)
                ->where('Status', '!=', 1);
            })
            ->get()
            ->reduce(function ($carry, $invoice) {
                $products_total = $invoice->products->sum('product_price_total');
                $paid = $invoice->Payment_price ?? 0;
                $net_due = $products_total - $paid;

                if ($net_due > 0) {
                    // العميل مدين لي
                    return $carry + $net_due;
                } elseif ($net_due < 0) {
                    // العميل دفع أكثر = دين علي
                    return $carry - abs($net_due); // نحوله لخصم
                }

                return $carry;
            }, 0);

        // الموردين (transactions = 1)
        $total_supplier_products = invoies::where('user_id', $userId)
            ->whereHas('transaction', function ($q) {
                $q->where('transactions', 1);
            })
            ->get()
            ->reduce(function ($carry, $invoice) {
                $products_total = $invoice->products->sum('product_price_total_purchase');
                $paid = $invoice->Payment_price ?? 0;
                $net_due = $products_total - $paid;

                if ($net_due > 0) {
                    // أنا مديون للمورد
                    return $carry + $net_due;
                } elseif ($net_due < 0) {
                    // دفعت للمورد أكثر = دين لي
                    return $carry - abs($net_due); // نحوله لأصل
                }

                return $carry;
            }, 0);

        // 4. السيولة النقدية
        $zakat = Zakat::where('user_id', $userId)->first();

        if (!$zakat) {
            return [
                'status' => 0,
                'message' => 'حساب الزكاة غير موجود'
            ];
        }

        $cash = $zakat->zakat_Cash_liquidity ?? 0;

        // 5. إجمالي الأصول = بضائع + ديون لصالحي + السيولة
        $total_assets = $total_goods + $total_debtors_products + $cash;

        // 6. خصم ديون الموردين (ديون عليّ)
        $zakat_mal = $total_assets - $total_supplier_products;

        // 7. حساب الزكاة
        $zakat_due = ($zakat_mal * $zakat->zakat_due_amount) / 100;

        // 8. التحديث في الجدول
        $zakat->update([
            "zakat_total_asset_value" => $total_goods,
            "zakat_total_debts_value" => $total_supplier_products,
            "zakat_total_deborts_value" => $total_debtors_products,
            "zakat_Cash_liquidity" => $cash,
            "zakat_due" => $zakat_due,
        ]);

        return [
            'status' => 1,
            'message' => 'تم حساب الزكاة بنجاح',
            'zakat_mal' => $zakat_mal,
            'zakat_due' => $zakat_due
        ];
    }

}
