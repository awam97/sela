<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التحقق من الهوية | <?= $system_name ?></title>
    
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

        .otp-container {
            width: 100%;
            max-width: 450px;
            background: #fff;
            padding: 50px;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .otp-icon {
            font-size: 5rem;
            color: var(--secondary);
            margin-bottom: 20px;
        }

        .otp-input {
            letter-spacing: 15px;
            text-align: center;
            font-size: 2rem;
            font-weight: 900;
            color: var(--primary);
            border: 2px solid #eee;
            border-radius: 15px;
            padding: 15px;
            margin: 20px 0;
            background: #fbfbfb;
        }

        .otp-input:focus {
            border-color: var(--secondary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(197, 160, 33, 0.1);
            outline: none;
        }

        .btn-verify {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 15px;
            border-radius: 15px;
            font-weight: 800;
            width: 100%;
            font-size: 1.1rem;
            transition: 0.3s;
        }

        .btn-verify:hover {
            background: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(197, 160, 33, 0.2);
        }

        .resend-link {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
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
            background: var(--secondary);
            color: #fff;
            transform: translateX(5px);
            border-color: var(--secondary);
        }
    </style>
</head>
<body>

    <a href="<?= base_url('/') ?>" class="btn-home">
        <i class='bx bx-home-alt'></i>
        <span>الرئيسية</span>
    </a>

    <div class="otp-container">
        <div class="otp-icon">
            <i class='bx bx-shield-quarter'></i>
        </div>
        <h2 class="fw-bold mb-3" style="color: var(--primary);">تحقق من هويتك</h2>
        <p class="text-muted">تم إرسال رمز التحقق (OTP) إلى بريدك الإلكتروني المسجل لدينا. يرجى إدخال الرمز المكون من 6 أرقام للمتابعة.</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger border-0 small py-2 mt-3">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('auth/verify-otp') ?>" method="POST">
            <?= csrf_field() ?>
            <input type="text" name="otp" class="otp-input w-100" maxlength="6" placeholder="000000" required autofocus>
            
            <button type="submit" class="btn-verify shadow">تأكيد الدخول</button>
        </form>

        <div class="mt-4">
            <p class="small text-muted mb-0">لم يصلك الرمز؟ 
                <a href="<?= base_url('auth/login') ?>" class="resend-link">إعادة المحاولة</a>
            </p>
        </div>
    </div>

</body>
</html>
