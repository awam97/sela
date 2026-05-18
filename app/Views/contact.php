<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تواصل معنا - <?= $system_name ?></title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('favicon.ico') ?>">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary: <?= $primary_color ?>;
            --secondary: <?= $secondary_color ?>;
            --bg-light: #F0F2F5;
            --text-dark: #192A56;
            --text-muted: #64748b;
            --card-bg: #ffffff;
            --glass-border: rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', 'Outfit', sans-serif;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            overflow-x: hidden;
            line-height: 1.8;
        }

        /* Preloader */
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
            border-right: 6px solid var(--secondary);
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

        /* Navigation */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.2rem 10%;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid var(--glass-border);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
        }

        .logo-box {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-box img { height: 45px; }

        .nav-links { display: flex; gap: 2rem; align-items: center; }
        .nav-links a { color: var(--text-dark); text-decoration: none; font-weight: 700; }
        .nav-links a:hover { color: var(--secondary); }

        .btn-login {
            background: var(--primary);
            color: white !important;
            padding: 0.7rem 1.8rem;
            border-radius: 12px;
            text-decoration: none;
            box-shadow: 0 10px 20px rgba(25, 42, 86, 0.1);
        }

        /* Hamburger Menu */
        .menu-toggle {
            display: none;
            font-size: 1.5rem;
            color: var(--primary);
            cursor: pointer;
            z-index: 1001;
        }

        .mobile-menu {
            position: fixed;
            top: 0;
            left: -100%;
            width: 80%;
            height: 100vh;
            background: white;
            z-index: 2000;
            padding: 5rem 2rem;
            box-shadow: 10px 0 50px rgba(0,0,0,0.1);
            transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .mobile-menu.active { left: 0; }
        .mobile-menu ul { list-style: none; }
        .mobile-menu ul li { margin-bottom: 2rem; }
        .mobile-menu ul li a { font-size: 1.5rem; font-weight: 800; color: var(--primary); text-decoration: none; }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0,0,0,0.5);
            z-index: 1999;
            display: none;
            backdrop-filter: blur(5px);
        }

        .overlay.active { display: block; }

        /* Contact Hero */
        .contact-hero {
            padding: 12rem 10% 6rem;
            background: white;
            text-align: center;
        }

        .contact-hero h1 {
            font-size: 3.5rem;
            font-weight: 900;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }

        .contact-hero p {
            font-size: 1.3rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Contact Section */
        .contact-section {
            padding: 4rem 10% 8rem;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 4rem;
        }

        .contact-info {
            background: var(--primary);
            padding: 4rem;
            border-radius: 30px;
            color: white;
        }

        .info-item {
            margin-bottom: 2.5rem;
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
        }

        .info-item i {
            font-size: 1.8rem;
            color: var(--secondary);
        }

        .info-item h4 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            font-weight: 800;
        }

        .info-item p {
            opacity: 0.8;
            font-size: 1.1rem;
        }

        .contact-form-box {
            background: white;
            padding: 4rem;
            border-radius: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.03);
            border: 1px solid var(--glass-border);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.8rem;
            font-weight: 700;
            color: var(--primary);
        }

        .form-control {
            width: 100%;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary);
            background: white;
            box-shadow: 0 0 0 4px rgba(197, 160, 33, 0.1);
        }

        .btn-submit {
            background: var(--primary);
            color: white;
            border: none;
            padding: 1.2rem 2.5rem;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            background: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(197, 160, 33, 0.2);
        }

        /* Footer */
        footer {
            padding: 6rem 10% 2.5rem;
            background: var(--primary);
            color: white;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 5rem;
            margin-bottom: 4rem;
        }

        .footer-col h4 {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            color: var(--secondary);
            font-weight: 800;
        }

        .footer-col ul { list-style: none; }
        .footer-col ul li { margin-bottom: 1rem; }
        .footer-col ul li a { color: rgba(255,255,255,0.7); text-decoration: none; }

        .copyright {
            text-align: center;
            padding-top: 2.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.4);
        }

        .footer-logo { filter: brightness(0) invert(1); }

        @media (max-width: 992px) {
            .contact-grid { grid-template-columns: 1fr; }
            .contact-info { padding: 3rem; }
            .contact-form-box { padding: 3rem; }
        }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .menu-toggle { display: block; }
            .contact-hero h1 { font-size: 2.5rem; }
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="preloader">
        <div class="spinner"></div>
    </div>

    <div class="overlay"></div>
    
    <div class="mobile-menu">
        <ul>
            <li><a href="<?= base_url('/') ?>">الرئيسية</a></li>
            <li><a href="<?= base_url('about') ?>">عن المنصة</a></li>
            <li><a href="<?= base_url('contact') ?>">تواصل معنا</a></li>
            <li><a href="<?= base_url('register') ?>">إنشاء حساب</a></li>
            <li><a href="<?= base_url('auth/login') ?>" class="btn-login" style="text-align: center; display: inline-block; width: 100%;">دخول النظام</a></li>
        </ul>
    </div>

    <nav>
        <a href="<?= base_url('/') ?>" class="logo-box" style="text-decoration: none;">
            <img src="<?= base_url('public/uploads/logo.png') ?>" alt="<?= $system_name ?>">
        </a>
        <div class="nav-links">
            <a href="<?= base_url('/') ?>">الرئيسية</a>
            <a href="<?= base_url('about') ?>">عن المنصة</a>
            <a href="<?= base_url('contact') ?>" style="color: var(--secondary);">تواصل معنا</a>
            <a href="<?= base_url('auth/login') ?>" class="btn-login">دخول النظام</a>
        </div>
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </nav>

    <header class="contact-hero">
        <h1>تواصل معنا</h1>
        <p>نحن هنا للإجابة على استفساراتكم وتقديم الدعم اللازم لمؤسستكم التعليمية.</p>
    </header>

    <section class="contact-section">
        <div class="contact-grid">
            <div class="contact-info">
                <h3>معلومات الاتصال</h3>
                <p style="margin-bottom: 3rem; opacity: 0.7;">يسعدنا دائماً سماع مقترحاتكم أو الرد على تساؤلاتكم عبر قنواتنا الرسمية.</p>
                
                <div class="info-item">
                    <i class="fas fa-phone-alt"></i>
                    <div>
                        <h4>رقم الهاتف</h4>
                        <p dir="ltr">+218 91 000 0000</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h4>البريد الإلكتروني</h4>
                        <p>support@sela.ly</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h4>الموقع الرئيسي</h4>
                        <p>طرابلس، ليبيا - شارع النصر</p>
                    </div>
                </div>
                
                <div class="social-links" style="margin-top: 4rem;">
                    <a href="https://www.facebook.com/graya.ly/" target="_blank" style="font-size: 2rem; color: #fff; margin-left: 1.5rem;"><i class="fab fa-facebook"></i></a>
                    <a href="#" style="font-size: 2rem; color: #fff; margin-left: 1.5rem;"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>

            <div class="contact-form-box">
                <form>
                    <div class="form-group">
                        <label>الاسم الكامل</label>
                        <input type="text" class="form-control" placeholder="أدخل اسمك هنا">
                    </div>
                    <div class="form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" class="form-control" placeholder="example@email.com">
                    </div>
                    <div class="form-group">
                        <label>الموضوع</label>
                        <input type="text" class="form-control" placeholder="كيف يمكننا مساعدتك؟">
                    </div>
                    <div class="form-group">
                        <label>الرسالة</label>
                        <textarea class="form-control" rows="5" placeholder="اكتب رسالتك هنا..."></textarea>
                    </div>
                    <button type="submit" class="btn-submit">إرسال الرسالة</button>
                </form>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-grid">
            <div class="footer-col">
                <div class="logo-box" style="margin-bottom: 1.5rem;">
                    <img src="<?= base_url('public/uploads/logo.png') ?>" alt="<?= $system_name ?>" class="footer-logo" style="height: 40px;">
                </div>
                <p style="color: rgba(255,255,255,0.7); max-width: 320px; margin-bottom: 1.5rem;">الشريك التقني الأمثل للمؤسسات التعليمية الطموحة.</p>
                <div class="social-links">
                    <a href="https://www.facebook.com/graya.ly/" target="_blank" style="font-size: 1.5rem; color: #fff;"><i class="fab fa-facebook"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h4>روابط سريعة</h4>
                <ul>
                    <li><a href="<?= base_url('/') ?>">الصفحة الرئيسية</a></li>
                    <li><a href="<?= base_url('about') ?>">عن المنصة</a></li>
                    <li><a href="<?= base_url('contact') ?>">تواصل معنا</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>الدعم والمساعدة</h4>
                <ul>
                    <li><a href="#">مركز الدعم الفني</a></li>
                    <li><a href="#">الأسئلة الشائعة</a></li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            &copy; <?= date('Y') ?> <?= $system_name ?>. جميع الحقوق محفوظة لشركة السيلا للحلول البرمجية.
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(window).on('load', function() { $('.preloader').addClass('fade-out'); });
        $(document).ready(function() {
            $('.menu-toggle, .overlay, .mobile-menu ul li a').on('click', function() {
                $('.mobile-menu').toggleClass('active');
                $('.overlay').toggleClass('active');
            });
        });
    </script>
</body>
</html>
