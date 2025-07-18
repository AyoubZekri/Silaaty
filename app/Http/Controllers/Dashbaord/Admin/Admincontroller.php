<?php

namespace App\Http\Controllers\Dashbaord\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class Admincontroller extends Controller
{
    public function index()
    {
        return view("content.admin.admin.admin");
    }

    public function list(Request $request)
    {
        try {
            $data = User::where('user_role', 1)->latest();

            if ($request->ajax()) {


                return DataTables::of($data)
                    ->addIndexColumn()
                    ->editColumn('created_at', function ($row) {
                        return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d');
                    })
                    ->addColumn('action', function ($row) {
                        $btn = '<button class="btn btn-sm btn-primary update" data-id="' . $row->id . '"><i class="bx bx-edit"></i></button>';
                        $btn .= ' <button class="btn btn-sm btn-danger delete" data-id="' . $row->id . '"><i class="bx bx-trash"></i></button>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }

            return response()->json([
                "data"=>$data,
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


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            User::create([
                'name' => $request->name,
                'family_name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                "user_role"=>1,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'تم إنشاء الأدمن بنجاح.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء الإنشاء.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function get(Request $request)
    {
        $admin = User::find($request->id);

        if (!$admin) {
            return response()->json([
                'status' => false,
                'message' => 'Admin not found.'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email
            ]
        ]);
    }



    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->id,
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $admin = User::findOrFail($request->id);
            $admin->name = $request->name;
            $admin->family_name = $request->name;
            $admin->email = $request->email;

            if ($request->filled('password')) {
                $admin->password = Hash::make($request->password);
            }

            $admin->save();

            return response()->json([
                'status' => true,
                'message' => 'تم تحديث بيانات الأدمن.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'فشل التحديث.',
                'error' => $e->getMessage()
            ], 500);
        }
    }



}
