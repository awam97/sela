<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | <?= $system_name ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap"
        rel="stylesheet">
    <!-- Bootstrap 5 RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root {
            --primary:
                <?= $primary_color ?>
            ;
            --gold:
                <?= $secondary_color ?>
            ;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            display: flex;
            width: 900px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-info {
            flex: 1;
            background: var(--primary);
            color: #fff;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .login-info i {
            font-size: 5rem;
            color: var(--gold);
            margin-bottom: 20px;
        }

        .login-form-side {
            flex: 1.2;
            padding: 60px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #eee;
            background: #fbfbfb;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: var(--gold);
            background: #fff;
        }

        .btn-login {
            background: var(--primary);
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 700;
            color: #fff;
            width: 100%;
            margin-top: 30px;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #0d1e42;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 42, 86, 0.3);
        }

        .login-header h2 {
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 30px;
        }

        .text-gold {
            color: var(--gold);
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                width: 90%;
            }

            .login-info {
                padding: 30px;
            }
        }

        /* Spinner */
        .preloader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #fff;
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        .spinner {
            width: 60px;
            height: 60px;
            border: 6px solid #f3f3f3;
            border-top: 6px solid var(--primary);
            border-right: 6px solid var(--gold);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .preloader.fade-out {
            opacity: 0;
            visibility: hidden;
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

        .btn-home i {
            font-size: 1.2rem;
        }
    </style>
</head>

<body>
    <div class="preloader">
        <div class="spinner"></div>
    </div>

    <a href="<?= base_url('/') ?>" class="btn-home">
        <i class='bx bx-home-alt'></i>
        <span>الرئيسية</span>
    </a>

    <div class="login-container">
        <div class="login-info">
            <center>
                <a href="<?= base_url('/') ?>">
                    <img src="<?= base_url('public/uploads/logo.png') ?>" alt="Logo" class="img-fluid mb-4" style="width: 55%; height: auto; filter: brightness(0) invert(1);">
                </a>
            </center>
            <h1 class="fw-bold"><?= $system_name ?></h1>
            <p class="mt-3 opacity-75"><?= $system_desc ?></p>
        </div>

        <div class="login-form-side">
            <div class="login-header">
                <h2>تسجيل الدخول</h2>
                <p class="text-muted small">مرحباً بك مجدداً، يرجى إدخال بياناتك للمتابعة</p>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger border-0 shadow-sm rounded-3">
                    <i class='bx bx-error-circle me-1'></i> <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= $form_action ?? base_url('auth/login') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <label class="form-label fw-bold text-muted small">اسم المستخدم</label>
                    <input type="text" name="username" class="form-control" placeholder="أدخل اسم المستخدم" required>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold text-muted small">كلمة المرور</label>
                    <input type="password" name="password" class="form-control" placeholder="أدخل كلمة المرور" required>
                </div>
                <?php if (!isset($is_super_login) || !$is_super_login): ?>
                    <div class="text-start mb-4">
                        <a href="#" class="text-gold small text-decoration-none fw-bold">نسيت كلمة المرور؟</a>
                    </div>
                <?php else: ?>
                    <div class="mb-4"></div>
                <?php endif; ?>
                
                <button type="submit" class="btn btn-login shadow">دخول للمنصة</button>
                                
                <?php if (!isset($is_super_login) || !$is_super_login): ?>
                    <div class="mt-4 text-center">
                        <p class="text-muted small">هل أنت طالب جديد؟ 
                            <a href="<?= base_url('register') ?>" class="text-gold fw-bold text-decoration-none ms-1">انضم إلينا الآن</a>
                        </p>
                    </div>
                <?php endif; ?>
            </form>

            <div class="mt-5 pt-3 border-top text-center text-muted small">
                &copy; <?= date('Y') ?> جميع الحقوق محفوظة لـ <?= $system_name ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(window).on('load', function() {
            $('.preloader').addClass('fade-out');
        });
    </script>
</body>

</html>