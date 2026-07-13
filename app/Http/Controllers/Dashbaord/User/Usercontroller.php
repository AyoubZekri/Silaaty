<?php

namespace App\Http\Controllers\Dashbaord\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class Usercontroller extends Controller
{
    public function index()
    {
        return view("content.admin.user.User");
    }

    public function list(Request $request)
    {
        try {
            $data = User::where('user_role', 2)->latest();
            if ($search = $request->input('search.value')) {
                $data->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%");
                });
            }


            if ($request->ajax()) {


                return DataTables::of($data)
                    ->addIndexColumn()
                    ->editColumn('created_at', function ($row) {
                        return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d');
                    })
                    ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex flex-wrap gap-2">';
                        $btn .= '<div class="btn-group">';
                        $btn .= '<button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">تفعيل / اشتراك</button>';
                        $btn .= '<ul class="dropdown-menu">';
                        $btn .= '<li><a class="dropdown-item experiment" href="javascript:void(0);" data-id="' . $row->id . '">فترة تجريبية</a></li>';
                        $btn .= '<li><a class="dropdown-item make-experiment" href="javascript:void(0);" data-id="' . $row->id . '">فترة اشتراك (مشرف)</a></li>';
                        $btn .= '<li><a class="dropdown-item update" href="javascript:void(0);" data-id="' . $row->id . '">فترة تفعيل (مشرف وبائع)</a></li>';
                        $btn .= '<li><hr class="dropdown-divider"></li>';
                        $btn .= '<li><a class="dropdown-item permanent-supervisor" href="javascript:void(0);" data-id="' . $row->id . '">تفعيل دائم (مشرف)</a></li>';
                        $btn .= '<li><a class="dropdown-item permanent-seller-supervisor" href="javascript:void(0);" data-id="' . $row->id . '">تفعيل دائم (مشرف وبائع)</a></li>';
                        $btn .= '<li><hr class="dropdown-divider"></li>';
                        $btn .= '<li><a class="dropdown-item edit-sell-settings" href="javascript:void(0);" data-id="' . $row->id . '" data-sell_type="' . $row->sell_type . '" data-max_sellers="' . $row->max_sellers . '">إعدادات البيع</a></li>';
                        $btn .= '</ul>';
                        $btn .= '</div>';
                        $btn .= '<button class="btn btn-sm btn-danger delete" data-id="' . $row->id . '" title="حذف"><i class="bx bx-trash me-1"></i> حذف</button>';
                        $btn .= '</div>';
                        return $btn;
                    })

                    ->rawColumns(['action'])
                    ->make(true);
            }

            return response()->json([
                "data" => $data,
                'status' => false,
                'message' => 'Invalid request type.'
            ], 400);
        } catch (\Exception $e) {
            \Log::error('User list error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Error fetching users.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function delete(Request $request)
    {
        try {
            $user = User::findOrFail($request->id);
            $user->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم الحذف بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء الحذف',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
