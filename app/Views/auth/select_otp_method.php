<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختر وسيلة التحقق | <?= $system_name ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <style>
        :root {
            --primary: <?= $primary_color ?>;
            --secondary: <?= $secondary_color ?>;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f0f2f5;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .select-container {
            width: 100%;
            max-width: 500px;
            background: #fff;
            padding: 50px;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .method-card {
            border: 2px solid #f1f5f9;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 20px;
            text-align: right;
            position: relative;
        }

        .method-card:hover {
            border-color: var(--secondary);
            background: rgba(197, 160, 33, 0.02);
            transform: translateY(-3px);
        }

        .method-card.active {
            border-color: var(--primary);
            background: rgba(25, 42, 86, 0.02);
        }

        .method-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--primary);
        }

        .method-info h4 {
            font-weight: 800;
            margin-bottom: 5px;
            font-size: 1.1rem;
            color: var(--primary);
        }

        .method-info p {
            margin-bottom: 0;
            font-size: 0.9rem;
            color: #64748b;
        }

        .btn-send {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 15px;
            border-radius: 15px;
            font-weight: 800;
            width: 100%;
            font-size: 1.1rem;
            margin-top: 20px;
        }

        .btn-send:hover {
            background: var(--secondary);
            box-shadow: 0 10px 20px rgba(197, 160, 33, 0.2);
        }

        input[type="radio"] {
            display: none;
        }

        .btn-home {
            position: fixed;
            top: 30px;
            left: 30px;
            background: #fff;
            color: var(--primary);
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
            z-index: 1000;
            border: 1px solid #eee;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .btn-home:hover {
            background: var(--gold);
            color: #fff;
            transform: translateX(5px);
            border-color: var(--gold);
        }
    </style>
</head>
<body>

    <a href="<?= base_url('/') ?>" class="btn-home">
        <i class='bx bx-home-alt'></i>
        <span>الرئيسية</span>
    </a>

    <div class="select-container">
        <h2 class="fw-bold mb-3" style="color: var(--primary);">تأكيد الهوية</h2>
        <p class="text-muted mb-5">يرجى اختيار الوسيلة المناسبة لك لتلقي رمز التحقق الخاص بالدخول.</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger border-0 small py-2 mb-4">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('auth/send-selected-otp') ?>" method="POST" id="methodForm">
            <?= csrf_field() ?>
            
            <?php if (!empty($user_email)): ?>
            <label class="method-card" for="method_email">
                <input type="radio" name="method" value="email" id="method_email" required>
                <div class="method-icon"><i class='bx bx-envelope'></i></div>
                <div class="method-info">
                    <h4>البريد الإلكتروني</h4>
                    <p><?= substr($user_email, 0, 3) ?>***@<?= explode('@', $user_email)[1] ?></p>
                </div>
            </label>
            <?php endif; ?>

            <?php if ($wa_enabled && !empty($user_phone)): ?>
            <label class="method-card" for="method_whatsapp">
                <input type="radio" name="method" value="whatsapp" id="method_whatsapp" required>
                <div class="method-icon" style="color: #25D366;"><i class='bx bxl-whatsapp'></i></div>
                <div class="method-info">
                    <h4>تطبيق واتساب</h4>
                    <p><?= substr($user_phone, 0, 4) ?>****<?= substr($user_phone, -2) ?></p>
                </div>
            </label>
            <?php endif; ?>

            <button type="submit" class="btn-send shadow">إرسال رمز التحقق</button>
        </form>

        <div class="mt-4">
            <a href="<?= base_url('auth/login') ?>" class="text-muted text-decoration-none small">إلغاء والعودة</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('.method-card').on('click', function() {
            $('.method-card').removeClass('active');
            $(this).addClass('active');
        });
    </script>
</body>
</html>
