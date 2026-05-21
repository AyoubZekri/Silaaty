<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فشل الدفع</title>

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
            background: #fee2e2;
            color: #dc2626;
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
            background: #ef4444;
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
            ✖
        </div>

        <h1>فشل الدفع</h1>

        <p>
            لم تكتمل عملية الدفع أو تم إلغاؤها.
            يرجى المحاولة مرة أخرى أو استخدام بطاقة أخرى.
        </p>

        <a href="#" class="btn">
            المحاولة مجددًا
        </a>

    </div>

</body>

</html>
