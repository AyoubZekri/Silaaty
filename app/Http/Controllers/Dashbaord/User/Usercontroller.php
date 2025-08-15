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
                        $btn = '<button class="btn btn-sm btn-primary update me-2" data-id="' . $row->id . '"><i class="bx  bx-check-circle"></i></button>';
                        $btn .= ' <button class="btn btn-sm btn-danger delete me-2" data-id="' . $row->id . '"><i class="bx bx-trash"></i></button>';
                        $btn .= '<button class="btn btn-sm btn-success make-experiment" data-id="' . $row->id . '"><i class="bx bx-bolt-circle"></i></button>';
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
