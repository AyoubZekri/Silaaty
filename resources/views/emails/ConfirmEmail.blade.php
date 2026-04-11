<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأكيد البريد الإلكتروني</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f5f9;
            font-family: 'Cairo', Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        table {
            border-spacing: 0;
            border-collapse: collapse;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            margin-top: 40px;
            margin-bottom: 40px;
        }
        .header {
            /* استخدمت نفس التدرج اللوني لتوحيد الهوية البصرية */
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff !important;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
            color: #334155;
        }
        .content h2 {
            color: #0f172a;
            font-size: 22px;
            margin-top: 0;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 20px;
        }
        /* كود التأكيد */
        .verification-code {
            display: inline-block;
            background-color: #f8fafc;
            border: 2px dashed #4F46E5;
            color: #4F46E5;
            font-size: 40px;
            font-weight: 700;
            letter-spacing: 6px;
            padding: 15px 40px;
            border-radius: 10px;
            margin: 20px 0 30px 0;
        }
        .footer {
            background-color: #f8fafc;
            padding: 30px 20px;
            text-align: center;
            color: #64748b;
            font-size: 14px;
            border-top: 1px solid #e2e8f0;
        }
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                border-radius: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body style="background-color: #f3f5f9; padding: 20px;">

    <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#f3f5f9">
        <tr>
            <td align="center">
                <table class="container" border="0" cellpadding="0" cellspacing="0" width="600" bgcolor="#ffffff">
                    <!-- Header -->
                    <tr>
                        <td class="header" style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);">
                            <h1 style="color: #ffffff; margin: 0;">منصة Silaaty</h1>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td class="content" style="padding: 40px 30px; text-align: center;">
                            <h2 style="color: #0f172a; margin-top: 0;">مرحباً بك {{ $codeverfy['name'] ?? '' }}</h2>
                            <p style="color: #475569; font-size: 16px; line-height: 1.6;">
                                لقد تلقينا طلباً لتأكيد بريدك الإلكتروني.<br>
                                يرجى استخدام رمز التحقق أدناه لإتمام العملية بنجاح:
                            </p>
                            
                            <!-- رمز التحقق -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" style="padding: 10px 0;">
                                        <div class="verification-code" style="background-color: #f8fafc; border: 2px dashed #4F46E5; color: #4F46E5; font-size: 38px; font-weight: bold; letter-spacing: 6px; padding: 15px 40px; border-radius: 10px; display: inline-block;">
                                            {{ $codeverfy["email_verified"] ?? '0000' }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="margin-top: 20px; font-size: 14px; color: #94a3b8;">
                                تنويه: رمز التحقق هذا متاح للاستخدام مرة واحدة وصالح لفترة قصيرة.<br> إذا لم تقم بهذا الطلب، يرجى تجاهل هذا البريد.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td class="footer" style="background-color: #f8fafc; padding: 30px 20px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 10px 0; color: #64748b;">أطيب التحيات،</p>
                            <p style="margin: 0 0 15px 0; color: #0f172a; font-weight: bold;">فريق الدعم</p>
                            
                            <p style="margin: 15px 0 0 0; font-size: 12px; color: #94a3b8;">
                                © {{ date('Y') }} جميع الحقوق محفوظة لتطبيق Silaaty.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>
