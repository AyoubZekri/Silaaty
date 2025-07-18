<?php

namespace App\Http\Controllers\Dashbaord\Notification;

use App\Function\Notification;
use App\Http\Controllers\Controller;
use App\Models\Notifications;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class NotificationController extends Controller
{
    public function index()
    {
        return view("content.admin.Notification.Notification");
    }

    public function list(Request $request)
    {
        try {
            $data = Notifications::with('users')
                ->whereHas('users', function ($query) {
                    $query->where('user_role', 1); // فرضاً "1" هو دور الإدمن
                })
                ->latest();

            if ($request->ajax()) {


                return DataTables::of($data)
                    ->addIndexColumn()
                    ->editColumn('created_at', function ($row) {
                        return \Carbon\Carbon::parse($row->created_at)->format('Y-m-d');
                    })
                    ->addColumn('action', function ($row) {
                        $btn = '<button class="btn btn-sm btn-primary edit" data-id="' . $row->id . '"><i class="bx bx-edit"></i></button>';
                        $btn .= ' <button class="btn btn-sm btn-warning resend" data-id="' . $row->id . '"><i class="bx bx-send"></i></button>';
                        $btn .= ' <button class="btn btn-sm btn-danger delete" data-id="' . $row->id . '"><i class="bx bx-trash"></i></button>';
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

    public function createNotification(Request $request)
    {
        $request->validate([
            'Title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        try {

            Notifications::create([
                'title' => $request->Title,
                'content' => $request->body,
                'is_read' => false,
                'user_id' => auth()->user()->id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'تم إنشاء الإشعار بنجاح.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'حدث خطأ أثناء الإنشاء.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function edit($id)
    {
        $notification = Notifications::find($id);

        if (!$notification) {
            return response()->json(['status' => false, 'message' => 'الإشعار غير موجود'], 404);
        }

        return response()->json(['status' => true, 'data' => $notification]);
    }



    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:notifications,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $notification = Notifications::find($request->id);
        $notification->title = $request->title;
        $notification->content = $request->content;
        $notification->save();

        return response()->json(['status' => true, 'message' => 'تم تعديل الإشعار بنجاح']);
    }


    public function deleteNotification($id)
    {
        try {
            $notification = Notifications::findOrFail($id);
            $notification->delete();

            return response()->json([
                'status' => true,
                'message' => 'تم حذف الإشعار بنجاح.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'فشل في حذف الإشعار.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function resend(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:notifications,id'
        ]);

        $notificationData = Notifications::find($request->id);

        if (!$notificationData) {
            return response()->json(['status' => false, 'message' => 'الإشعار غير موجود.'], 404);
        }

        try {
            $notification = new Notification();
            $result = $notification->sendNotificationToTopic('users', $notificationData->title, $notificationData->content);

            if ($result['status']) {
                return response()->json(['status' => true, 'message' => 'تم إعادة إرسال الإشعار.']);
            } else {
                return response()->json(['status' => false, 'message' => 'فشل في الإرسال.', 'error' => $result['error'] ?? null], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'حدث خطأ أثناء الإرسال.', 'error' => $e->getMessage()], 500);
        }
    }




}
