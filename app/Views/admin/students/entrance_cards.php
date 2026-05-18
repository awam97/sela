<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>بطاقات التعريف - <?= $school_name ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap');
        
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
        }

        .cards-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }

        .card-id {
            width: 300px;
            height: 340px; /* Reduced to fit 3 rows */
            border: 2px dashed #192A56;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            background: #fff;
            box-shadow: 0 5px 15px rgba(25, 42, 86, 0.08);
            break-inside: avoid;
            margin-bottom: 10px;
        }

        .card-header-sela {
            background: #192A56;
            color: #fff;
            padding: 10px;
            text-align: center;
            border-bottom: 3px solid #C5A021;
        }

        .card-header-sela img.logo {
            width: 35px;
            height: 35px;
            margin-bottom: 5px;
            border: 1px solid #fff;
            border-radius: 50%;
            background: #fff;
        }

        .card-header-sela h6 {
            font-size: 0.8rem;
            margin: 0;
            font-weight: 700;
        }

        .card-body-sela {
            padding: 10px 15px;
            text-align: center;
        }

        .student-photo {
            width: 75px;
            height: 75px;
            border-radius: 10px;
            border: 2px solid #C5A021;
            margin-bottom: 10px;
            object-fit: cover;
        }

        .student-name {
            color: #192A56;
            font-weight: 800;
            font-size: 0.95rem;
            margin-bottom: 10px;
            line-height: 1.2;
            height: 2.3rem;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .info-grid {
            text-align: right;
            font-size: 0.75rem;
        }

        .info-row {
            display: flex;
            margin-bottom: 4px;
            padding: 4px 8px;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .info-label {
            color: #C5A021;
            font-weight: 700;
            width: 45%;
        }

        .info-value {
            color: #192A56;
            font-weight: 600;
            width: 55%;
        }

        .card-footer-sela {
            position: absolute;
            bottom: 0;
            width: 100%;
            background: #192A56;
            color: #fff;
            font-size: 0.6rem;
            padding: 6px;
            text-align: center;
        }

        @media print {
            .no-print { display: none !important; }
            body { padding: 0; background: none; margin: 0; }
            
            .cards-container {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                grid-template-rows: repeat(3, 1fr);
                gap: 10px;
                padding: 10px;
                width: 100%;
                height: 100vh; /* Force 9 cards per screen-height */
            }

            .card-id { 
                box-shadow: none; 
                border: 1.2px dashed #192A56;
                width: 100%;
                height: 310px; /* Precise height to fit 3x3 on A4 */
                margin: 0;
            }

            .page-break {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>

<div class="container-fluid no-print mb-4">
    <div class="alert alert-info d-flex justify-content-between align-items-center rounded-4 shadow-sm border-0 p-3">
        <div>
            <h6 class="m-0 fw-bold"><i class='bx bx-printer fs-4 me-2'></i> وضع الطباعة الاحترافي (9 بطاقات بالصفحة)</h6>
            <p class="text-muted small mb-0 mt-1">تم ضبط الأبعاد لتناسب طباعة 3 أعمدة في 3 صفوف بدقة عالية.</p>
        </div>
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm">
            <i class='bx bx-print me-2'></i> طباعة الآن
        </button>
    </div>
</div>

<div class="cards-container">
    <?php 
    $count = 0;
    foreach ($students as $student): 
        $count++;
    ?>
        <div class="card-id">
            <div class="card-header-sela">
                <img src="<?= base_url('uploads/logo.png') ?>" class="logo" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($system_name) ?>&background=fff&color=192A56'">
                <h6 class="m-0"><?= $school_name ?></h6>
                <div class="opacity-75" style="font-size: 0.5rem;"><?= $system_name ?></div>
            </div>
            
            <div class="card-body-sela">
                <img src="<?= base_url('upload/student_images/' . ($student['student_id'] ?? 'user') . '.jpg') ?>" class="student-photo" onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($student['name']) ?>&background=192A56&color=fff'">
                
                <div class="student-name"><?= $student['name'] ?></div>
                
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-label">اسم المستخدم:</div>
                        <div class="info-value"><?= $student['student_id'] ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">الصف:</div>
                        <div class="info-value"><?= $student['class_name'] ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">الفصل:</div>
                        <div class="info-value"><?= $student['section_name'] ?></div>
                    </div>
                </div>
            </div>

            <div class="card-footer-sela">
                نظام <?= $system_name ?> - <?= $student['year'] ?>
            </div>
        </div>
        
        <?php if ($count % 9 == 0): ?>
            <div class="page-break"></div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<script>
    // window.print(); // Optional: Auto-trigger print
</script>

</body>
</html>
