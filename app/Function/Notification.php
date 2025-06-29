<?php


namespace App\Function;

use App\Models\Notifications;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Firebase\Exception\MessagingException;

class Notification
{
    protected $messaging;

    public function __construct()
    {
        $this->messaging = app('firebase.messaging');
    }

    /**
     * إرسال إشعار FCM + حفظه في قاعدة البيانات
     *
     * @param string $fcmToken
     * @param array $tokens
     * @param string $title
     * @param string $body
     * @param int $userId
     * @param array $userIds
     * @return array
     */
    public function sendNotification(string $fcmToken, string $title, string $body, int $userId): array
    {
        try {
            $notification = FirebaseNotification::create($title, $body);

            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification($notification);

            $this->messaging->send($message);

            // حفظ في جدول notifications
            Notifications::create([
                'title'   => $title,
                'content' => $body,
                'is_read' => false,
                'user_id' => $userId,
            ]);

            return ['status' => true, 'message' => 'تم الإرسال والحفظ بنجاح'];
        } catch (MessagingException $e) {
            return ['status' => false, 'message' => 'فشل الإرسال', 'error' => $e->getMessage()];
        } catch (\Throwable $e) {
            return ['status' => false, 'message' => 'خطأ غير متوقع', 'error' => $e->getMessage()];
        }
    }


    public function sendBulkNotification(array $tokens, string $title, string $body, array $userIds): array
    {
        if (count($tokens) !== count($userIds)) {
            return ['status' => false, 'message' => 'عدد التوكنات لا يطابق عدد معرفات المستخدمين'];
        }

        try {
            $notification = FirebaseNotification::create($title, $body);

            $message = CloudMessage::new()->withNotification($notification);

            $report = $this->messaging->sendMulticast($message, $tokens);

            foreach ($userIds as $index => $userId) {
                Notifications::create([
                    'title'   => $title,
                    'content' => $body,
                    'is_read' => false,
                    'user_id' => $userId,
                ]);
            }

            return [
                'status' => 1,
                'message' => 'تم إرسال الإشعارات إلى ' . $report->successes()->count() . ' مستخدم/ين',
                'failures' => $report->failures()->count(),
            ];
        } catch (MessagingException $e) {
            return ['status' => 0, 'message' => 'فشل الإرسال', 'error' => $e->getMessage()];
        } catch (\Throwable $e) {
            return ['status' => 0, 'message' => 'خطأ غير متوقع', 'error' => $e->getMessage()];
        }
    }
}
