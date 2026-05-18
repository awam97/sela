<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحت الصيانة | <?= $system_name ?? 'Sela' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --nile: #192a56;
            --gold: #c5a021;
        }
        body {
            font-family: 'Cairo', sans-serif;
            background: #f8f9fa;
            color: var(--nile);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }
        .maintenance-card {
            background: white;
            padding: 3rem;
            border-radius: 2rem;
            box-shadow: 0 20px 50px rgba(25, 42, 86, 0.1);
            max-width: 600px;
            width: 90%;
            text-align: center;
            position: relative;
        }
        .icon-box {
            width: 100px;
            height: 100px;
            background: rgba(197, 160, 33, 0.1);
            color: var(--gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 2rem;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        h1 { font-weight: 700; margin-bottom: 1rem; color: var(--nile); }
        p { color: #6c757d; font-size: 1.1rem; line-height: 1.8; }
        .btn-admin {
            background: var(--nile);
            color: white;
            border-radius: 50px;
            padding: 0.8rem 2.5rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
            transition: all 0.3s ease;
        }
        .btn-admin:hover {
            background: var(--gold);
            color: white;
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <div class="maintenance-card">
        <div class="icon-box">
            <i class='bx bxs-wrench'></i>
        </div>
        <h1>الموقع تحت الصيانة</h1>
        <p>نحن نقوم حالياً ببعض التحديثات المهمة لتحسين تجربتكم. سنعود للعمل قريباً جداً. شكراً لتفهمكم وصبركم.</p>
        
        <div class="mt-4 pt-4 border-top">
            <small class="text-muted">إذا كنت أحد مديري النظام، يمكنك تسجيل الدخول للمتابعة</small><br>
            <a href="<?= base_url('superadmin/login') ?>" class="btn-admin">
                <i class='bx bxs-user-circle'></i> دخول الإدارة
            </a>
        </div>
    </div>
</body>
</html>
