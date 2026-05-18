<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $system_name ?> - الصفحة الرئيسية</title>
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
            --glass: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(0, 0, 0, 0.05);
            --card-bg: #ffffff;
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
            line-height: 1.6;
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

        .logo-box img {
            height: 45px;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 700;
            font-size: 1rem;
        }

        .nav-links a:hover {
            color: var(--secondary);
        }

        .btn-login {
            background: var(--primary);
            color: white !important;
            padding: 0.7rem 1.8rem;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(25, 42, 86, 0.15);
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(25, 42, 86, 0.25);
            background: var(--secondary);
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

        /* Hero Section */
        .hero {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 10%;
            position: relative;
        }

        .hero h1 {
            font-size: clamp(2.5rem, 7vw, 5rem);
            font-weight: 900;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: var(--primary);
            animation: fadeInUp 1s ease-out;
        }

        .hero p {
            font-size: 1.4rem;
            max-width: 800px;
            color: var(--text-muted);
            margin-bottom: 3rem;
            animation: fadeInUp 1s ease-out 0.2s backwards;
        }

        .hero-btns {
            display: flex;
            gap: 1.5rem;
            animation: fadeInUp 1s ease-out 0.4s backwards;
        }

        .btn-main {
            padding: 1.1rem 2.8rem;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 10px 25px rgba(25, 42, 86, 0.2);
        }

        .btn-outline {
            background: white;
            color: var(--primary);
            border: 1px solid var(--glass-border);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .btn-main:hover {
            transform: translateY(-5px);
            filter: brightness(1.05);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Features Section */
        .features {
            padding: 8rem 10%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
        }

        .feature-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            padding: 3.5rem 2.5rem;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
            position: relative;
            overflow: hidden;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
            border-color: var(--secondary);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            background: #f8fafc;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin-bottom: 2rem;
            color: var(--primary);
            border: 1px solid #f1f5f9;
        }

        .feature-card h3 {
            font-size: 1.6rem;
            margin-bottom: 1rem;
            color: var(--primary);
            font-weight: 800;
        }

        .feature-card p {
            color: var(--text-muted);
            font-size: 1.1rem;
            line-height: 1.7;
        }

        /* Roles Section */
        .roles {
            padding: 8rem 10%;
            background: white;
        }

        .roles-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            margin-top: 4rem;
        }

        .role-card {
            padding: 3rem;
            border-radius: 30px;
            background: var(--bg-light);
            border: 1px solid var(--glass-border);
            position: relative;
        }

        .role-card.admin { border-right: 8px solid var(--primary); }
        .role-card.teacher { border-right: 8px solid var(--secondary); }

        .role-card h3 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: var(--primary);
            font-weight: 900;
        }

        .role-card ul {
            list-style: none;
        }

        .role-card ul li {
            margin-bottom: 1rem;
            font-size: 1.1rem;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .role-card ul li i {
            color: var(--secondary);
        }

        /* Stats Section - Primary Theme */
        .stats {
            background: var(--primary);
            padding: 6rem 10%;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 4rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            color: white;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 4rem;
            font-weight: 900;
            display: block;
            color: var(--secondary);
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.7);
            font-weight: 700;
            letter-spacing: 1px;
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

        .footer-col ul {
            list-style: none;
        }

        .footer-col ul li {
            margin-bottom: 1rem;
        }

        .footer-col ul li a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-weight: 500;
        }

        .footer-col ul li a:hover {
            color: var(--secondary);
        }

        .copyright {
            text-align: center;
            padding-top: 2.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.4);
            font-size: 0.95rem;
        }

        .footer-logo {
            filter: brightness(0) invert(1);
        }

        .social-links a {
            transition: transform 0.3s ease;
            display: inline-block;
        }

        .social-links a:hover {
            transform: scale(1.2);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .nav-links { display: none; }
            .menu-toggle { display: block; }
            .hero h1 { font-size: 3rem; }
            .hero-btns { flex-direction: column; width: 100%; }
            .btn-main { width: 100%; justify-content: center; }
            .footer-grid { grid-template-columns: 1fr; gap: 3rem; }
            .stats { gap: 2.5rem; }
            .roles-grid { grid-template-columns: 1fr; }
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
            <li><a href="#features">المميزات</a></li>
            <li><a href="<?= base_url('about') ?>">عن المنصة</a></li>
            <li><a href="<?= base_url('school-registration') ?>">تسجيل مدرسة</a></li>
            <li><a href="<?= base_url('contact') ?>">تواصل معنا</a></li>
            <li><a href="<?= base_url('auth/login') ?>" class="btn-login" style="text-align: center; display: inline-block; width: 100%;">دخول النظام</a></li>
        </ul>
    </div>

    <div class="ambient-bg">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <nav>
        <div class="logo-box">
            <a href="<?= base_url('/') ?>">
                <img src="<?= base_url('public/uploads/logo.png') ?>" alt="<?= $system_name ?>">
            </a>
        </div>
        <div class="nav-links">
            <a href="#features">المميزات</a>
            <a href="<?= base_url('about') ?>">عن المنصة</a>
            <a href="<?= base_url('contact') ?>">تواصل معنا</a>
            <a href="<?= base_url('school-registration') ?>" class="btn-outline" style="padding: 0.5rem 1.5rem; border-radius: 12px; font-weight: 700;">تسجيل مدرسة</a>
            <a href="<?= base_url('auth/login') ?>" class="btn-login">دخول النظام</a>
        </div>
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
    </nav>

    <header class="hero">
        <h1><?= $landing_hero_h1 ?></h1>
        <p><?= $landing_hero_p ?></p>
        <div class="hero-btns">
            <a href="<?= base_url('school-registration') ?>" class="btn-main btn-primary">سجل مدرستك الآن</a>
            <a href="<?= base_url('register') ?>" class="btn-main btn-outline">بوابة الطلاب</a>
        </div>
    </header>

    <section class="stats">
        <div class="stat-item">
            <span class="stat-value"><?= $landing_stats_1_value ?></span>
            <span class="stat-label"><?= $landing_stats_1_label ?></span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?= $landing_stats_2_value ?></span>
            <span class="stat-label"><?= $landing_stats_2_label ?></span>
        </div>
        <div class="stat-item">
            <span class="stat-value"><?= $landing_stats_3_value ?></span>
            <span class="stat-label"><?= $landing_stats_3_label ?></span>
        </div>
    </section>

    <section id="features" class="features">
        <div class="feature-card">
            <div class="feature-icon">💎</div>
            <h3><?= $landing_feature_1_title ?></h3>
            <p><?= $landing_feature_1_desc ?></p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📈</div>
            <h3><?= $landing_feature_2_title ?></h3>
            <p><?= $landing_feature_2_desc ?></p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🛡️</div>
            <h3><?= $landing_feature_3_title ?></h3>
            <p><?= $landing_feature_3_desc ?></p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">⚡</div>
            <h3><?= $landing_feature_4_title ?></h3>
            <p><?= $landing_feature_4_desc ?></p>
        </div>
    </section>

    <section class="roles">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h2 style="font-size: 3rem; color: var(--primary); font-weight: 900;"><?= $landing_roles_title ?></h2>
            <p style="color: var(--text-muted); font-size: 1.2rem; max-width: 700px; margin: 0 auto;"><?= $landing_roles_subtitle ?></p>
        </div>
        <div class="roles-grid">
            <div class="role-card admin">
                <h3><?= $landing_role_1_title ?></h3>
                <ul>
                    <?php 
                    $role1_items = explode("\n", $landing_role_1_list);
                    foreach ($role1_items as $item): 
                        if (trim($item) == '') continue;
                    ?>
                    <li><i class="fas fa-check-circle"></i> <?= trim($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="role-card teacher">
                <h3><?= $landing_role_2_title ?></h3>
                <ul>
                    <?php 
                    $role2_items = explode("\n", $landing_role_2_list);
                    foreach ($role2_items as $item): 
                        if (trim($item) == '') continue;
                    ?>
                    <li><i class="fas fa-check-circle"></i> <?= trim($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-grid">
            <div class="footer-col">
                <div class="logo-box" style="margin-bottom: 1.5rem;">
                    <a href="<?= base_url('/') ?>">
                        <img src="<?= base_url('public/uploads/logo.png') ?>" alt="<?= $system_name ?>" class="footer-logo" style="height: 40px;">
                    </a>
                </div>
                <p style="color: rgba(255,255,255,0.7); max-width: 320px; margin-bottom: 1.5rem;">الشريك التقني الأمثل للمؤسسات التعليمية الطموحة التي تسعى للتميز والريادة في العصر الرقمي.</p>
                <div class="social-links">
                    <a href="https://www.facebook.com/graya.ly/" target="_blank" style="font-size: 1.5rem; color: #fff;"><i class="fab fa-facebook"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h4>روابط سريعة</h4>
                <ul>
                    <li><a href="#">الصفحة الرئيسية</a></li>
                    <li><a href="#features">المميزات والخدمات</a></li>
                    <li><a href="<?= base_url('about') ?>">عن المنصة</a></li>
                    <li><a href="<?= base_url('school-registration') ?>">تسجيل مدرسة جديدة</a></li>
                    <li><a href="<?= base_url('register') ?>">بوابة الطلاب</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>الدعم والمساعدة</h4>
                <ul>
                    <li><a href="<?= base_url('contact') ?>">مركز الدعم الفني</a></li>
                    <li><a href="#">الأسئلة الشائعة</a></li>
                    <li><a href="<?= base_url('contact') ?>">تواصل معنا</a></li>
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
