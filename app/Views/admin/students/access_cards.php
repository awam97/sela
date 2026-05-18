<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>بيانات الدخول للمنصة - <?= $school_name ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap');
        
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 30px;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
            justify-items: center;
        }

        .access-card {
            width: 280px; 
            height: 200px;
            border: 2px dashed #192A56;
            border-radius: 12px;
            background: #fff;
            padding: 15px;
            position: relative;
            break-inside: avoid;
            text-align: right;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .header {
            border-bottom: 2px solid #C5A021;
            padding-bottom: 8px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }

        .header i {
            color: #192A56;
            font-size: 1.4rem;
            margin-left: 10px;
        }

        .header h6 {
            margin: 0;
            color: #192A56;
            font-weight: 800;
            font-size: 0.9rem;
        }

        .student-info {
            font-size: 0.85rem;
            margin-bottom: 10px;
            border-right: 4px solid #C5A021;
            padding-right: 10px;
        }

        .credentials {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
            font-family: monospace;
            font-size: 0.9rem;
            border: 1px solid #e9ecef;
        }

        .cred-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .label {
            color: #64748b;
            font-family: 'Tajawal', sans-serif;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .value {
            color: #1e293b;
            font-weight: 700;
        }

        .footer {
            position: absolute;
            bottom: 10px;
            right: 15px;
            left: 15px;
            display: flex;
            justify-content: space-between;
            font-size: 0.65rem;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 6px;
        }

        @media print {
            .no-print { display: none !important; }
            body { 
                padding: 0; 
                background: none; 
                margin: 0;
            }
            .cards-grid {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
                width: 100%;
            }
            .access-card { 
                border: 1.5px dashed #192A56; 
                box-shadow: none; 
                margin: 0;
                width: 100%;
                height: 190px;
            }
        }
    </style>
</head>
<body>

<div class="container no-print mb-5">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-4 bg-white d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-nile mb-1"><i class='bx bxs-key-block me-2 text-gold'></i> بطاقات الدخول لـ <?= $system_name ?></h5>
                <p class="text-muted small mb-0">تحتوي هذه البطاقات على بيانات الدخول الخاصة بكل طالب لمتابعة خدمات <?= $school_name ?>.</p>
            </div>
            <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 shadow">
                <i class='bx bx-print me-1'></i> طباعة كافة البطاقات
            </button>
        </div>
    </div>
</div>

<div class="cards-grid">
    <?php foreach ($students as $student): ?>
        <div class="access-card">
            <div class="header">
                <i class='bx bxs-lock-alt'></i>
                <div>
                    <h6>نظام <?= $system_name ?></h6>
                    <small style="font-size: 0.6rem; color: #C5A021;">بطاقة الوصول الآمن للمستخدم</small>
                </div>
            </div>

            <div class="student-info">
                <div class="fw-bold text-nile fs-6"><?= $student['name'] ?></div>
                <small class="text-muted"><?= $student['class_name'] ?> - <?= $student['section_name'] ?></small>
            </div>

            <div class="credentials">
                <div class="cred-row">
                    <span class="label">اسم المستخدم:</span>
                    <span class="value"><?= $student['student_id'] ?></span>
                </div>
            </div>

            <div class="footer">
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
