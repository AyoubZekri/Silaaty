<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم الدفع بنجاح</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f9fafb;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .card {
            background: white;
            width: 90%;
            max-width: 420px;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .icon {
            width: 90px;
            height: 90px;
            background: #dcfce7;
            color: #16a34a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            margin: auto;
        }

        h1 {
            margin-top: 24px;
            color: #111827;
            font-size: 28px;
        }

        p {
            color: #6b7280;
            margin-top: 12px;
            line-height: 1.8;
            font-size: 15px;
        }

        .btn {
            display: inline-block;
            margin-top: 28px;
            background: #4f46e5;
            color: white;
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 15px;
            transition: 0.2s;
        }

        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>

<body>

    <div class="card">

        <div class="icon">
            ✔
        </div>

        <h1>تم الدفع بنجاح</h1>

        <p>
            تم تفعيل اشتراكك بنجاح.
            يمكنك الآن العودة إلى التطبيق ومتابعة استخدام جميع المميزات.
        </p>

        {{-- <a href="#" class="btn">
            العودة للتطبيق
        </a> --}}

    </div>

</body>

</html>
