<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عن المنصة - <?= $system_name ?></title>
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

        /* Ambient Background Elements */
        .ambient-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .blob {
            position: absolute;
            filter: blur(120px);
            opacity: 0.15;
            border-radius: 50%;
            animation: move 20s infinite alternate;
        }

        .blob-1 {
            width: 600px;
            height: 600px;
            background: var(--primary);
            top: -200px;
            right: -200px;
        }

        .blob-2 {
            width: 500px;
            height: 500px;
            background: var(--secondary);
            bottom: -100px;
            left: -100px;
            animation-delay: -5s;
        }

        @keyframes move {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(100px, 100px) scale(1.1); }
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

        .mobile-menu.active {
            left: 0;
        }

        .mobile-menu ul {
            list-style: none;
        }

        .mobile-menu ul li {
            margin-bottom: 2rem;
        }

        .mobile-menu ul li a {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            text-decoration: none;
            display: block;
        }

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

        .overlay.active {
            display: block;
        }

        /* About Hero - Light Theme */
        .about-hero {
            padding: 14rem 10% 8rem;
            text-align: center;
            position: relative;
        }

        .about-hero h1 {
            font-size: clamp(3rem, 8vw, 5rem);
            font-weight: 900;
            margin-bottom: 1.5rem;
            color: var(--primary);
        }

        .about-hero p {
            font-size: 1.6rem;
            max-width: 800px;
            margin: 0 auto;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Content Sections */
        .section {
            padding: 6rem 10%;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .content-box h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: var(--primary);
        }

        .content-box p {
            font-size: 1.2rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
        }

        .image-box img {
            width: 100%;
            border-radius: 30px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.08);
            border: 1px solid var(--glass-border);
        }

        /* Values Grid */
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2.5rem;
            margin-top: 4rem;
        }

        .value-card {
            background: white;
            padding: 4rem 2.5rem;
            border-radius: 24px;
            text-align: center;
            border: 1px solid var(--glass-border);
            box-shadow: 0 10px 40px rgba(0,0,0,0.02);
        }

        .value-card i {
            font-size: 3.5rem;
            color: var(--secondary);
            margin-bottom: 2rem;
        }

        .value-card h3 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: var(--primary);
            font-weight: 800;
        }

        /* Footer - Primary Theme */
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
        .footer-col ul li a:hover { color: var(--secondary); }

        .copyright {
            text-align: center;
            padding-top: 2.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.4);
        }

        .footer-logo { filter: brightness(0) invert(1); }

        @media (max-width: 768px) {
            .nav-links { display: none; }
            .menu-toggle { display: block; }
            .grid-2 { grid-template-columns: 1fr; text-align: center; }
            .about-hero h1 { font-size: 3rem; }
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="spinner"></div>
    </div>

    <div class="overlay"></div>
    
    <div class="mobile-menu">
        <ul>
            <li><a href="<?= base_url('/') ?>">الرئيسية</a></li>
            <li><a href="<?= base_url('about') ?>">عن المنصة</a></li>
            <li><a href="<?= base_url('register') ?>">إنشاء حساب</a></li>
            <li><a href="<?= base_url('auth/login') ?>" class="btn-login" style="text-align: center; display: inline-block; width: 100%;">دخول النظام</a></li>
        </ul>
    </div>

    <div class="ambient-bg">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <nav>
        <a href="<?= base_url('/') ?>" class="logo-box" style="text-decoration: none;">
            <img src="<?= base_url('public/uploads/logo.png') ?>" alt="<?= $system_name ?>">
        </a>
        <div class="nav-links">
            <a href="<?= base_url('/') ?>">الرئيسية</a>
            <a href="<?= base_url('about') ?>" style="color: var(--secondary);">عن المنصة</a>
            <a href="<?= base_url('contact') ?>">تواصل معنا</a>
            <a href="<?= base_url('school-registration') ?>" style="font-weight: 800;">تسجيل مدرسة</a>
            <a href="<?= base_url('auth/login') ?>" class="btn-login">دخول النظام</a>
        </div>
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </nav>

    <header class="about-hero">
        <h1><?= $about_hero_h1 ?></h1>
        <p><?= $about_hero_p ?></p>
    </header>

    <section class="section">
        <div class="grid-2">
            <div class="content-box">
                <h2><?= $about_who_title ?></h2>
                <p><?= str_replace('{system_name}', $system_name, $about_who_p1) ?></p>
                <p><?= str_replace('{system_name}', $system_name, $about_who_p2) ?></p>
            </div>
            <div class="image-box">
                <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1471&q=80" alt="Team working">
            </div>
        </div>
    </section>

    <section class="section" style="background: white;">
        <div style="text-align: center; margin-bottom: 4rem;">
            <h2 style="font-size: 2.8rem; color: var(--primary); font-weight: 900;">قيمنا الجوهرية</h2>
            <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto; font-size: 1.2rem;">المبادئ التي تقودنا في تطوير أفضل الحلول التعليمية لعملائنا.</p>
        </div>
        <div class="values-grid">
            <div class="value-card">
                <i class="fas fa-rocket"></i>
                <h3>الابتكار المستمر</h3>
                <p>نسعى دائماً لتطوير أدوات جديدة تسبق تطلعات المؤسسات التعليمية وتواكب العصر.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-shield-alt"></i>
                <h3>الأمان والموثوقية</h3>
                <p>حماية بياناتكم هي التزامنا الأول والأهم في كل ما نقوم به من عمليات تطوير.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-users"></i>
                <h3>التركيز على المستخدم</h3>
                <p>نصمم واجهاتنا لتكون بسيطة وفعالة، مما يوفر وقتاً ثميناً للطاقم الإداري والتعليمي.</p>
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
                    <li><a href="<?= base_url('/') ?>">الرئيسية</a></li>
                    <li><a href="<?= base_url('about') ?>">عن المنصة</a></li>
                    <li><a href="<?= base_url('contact') ?>">تواصل معنا</a></li>
                    <li><a href="<?= base_url('school-registration') ?>">تسجيل مدرسة جديدة</a></li>
                    <li><a href="<?= base_url('register') ?>">بوابة الطلاب</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>الدعم والمساعدة</h4>
                <ul>
                    <li><a href="#">مركز الدعم الفني</a></li>
                    <li><a href="#">تواصل معنا</a></li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            &copy; <?= date('Y') ?> <?= $system_name ?>. جميع الحقوق محفوظة لشركة السيلا للحلول البرمجية.
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(window).on('load', function() {
            $('.preloader').addClass('fade-out');
        });

        $(document).ready(function() {
            $('.menu-toggle, .overlay, .mobile-menu ul li a').on('click', function() {
                $('.mobile-menu').toggleClass('active');
                $('.overlay').toggleClass('active');
            });
        });
    </script>
</body>
</html>
