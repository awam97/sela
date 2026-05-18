<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center mb-4 no-print">
    <div>
        <h1 class="mb-1"><i class='bx bxs-printer text-gold me-2'></i> طباعة كشف الدرجات</h1>
        <p class="text-muted small ps-5 mb-0">عرض مجمع لنتائج الطلاب في جميع المواد والامتحانات</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn-sela btn-sela-primary shadow-sm">
            <i class='bx bx-printer me-1'></i> طباعة الكشف
        </button>
        <a href="<?= base_url('admin/reports') ?>" class="btn-sela btn-sela-gold shadow-sm">
            <i class='bx bx-arrow-back me-1'></i> العودة للمركز
        </a>
    </div>
</div>

<div class="card-sela primary shadow-sm printable-area p-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold text-nile mb-1">كشف درجات الطلاب مجمع</h2>
        <h5 class="text-muted">للعام الدراسي: <?= $year ?></h5>
        <div class="mt-3 fs-5">
            <span class="me-4 border-bottom pb-1">الصف: <strong><?= $class['name'] ?></strong></span>
            <?php if ($section): ?>
                <span class="border-bottom pb-1">الفصل: <strong><?= $section['name'] ?></strong></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered border-dark align-middle text-center marksheet-table">
            <thead class="bg-light">
                <tr>
                    <th class="align-middle">اسم الطالب</th>
                    <?php foreach ($subjects as $su): ?>
                        <th class="align-middle text-nile bg-soft-gold" style="min-width: 100px;">
                            <div class="small mb-1"><?= $su['name'] ?></div>
                            <span class="x-small text-muted fw-normal">من <?= $su['total_mark'] ?? 100 ?></span>
                        </th>
                    <?php endforeach; ?>
                    <th class="align-middle bg-gold text-white">المجموع العام</th>
                    <th class="align-middle bg-nile text-white">النسبة %</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $s): 
                    $studentTotalObtained = 0;
                    $studentTotalPossible = 0;
                ?>
                    <tr>
                        <td class="text-start ps-3 fw-bold student-name-cell"><?= $s['name'] ?></td>
                        <?php foreach ($subjects as $su): ?>
                            <?php 
                                $mark = $marks_data[$s['student_id']][$su['subject_id']] ?? null;
                                $obtained = $mark['total_obtained'] ?? 0;
                                $total = $mark['total_possible'] ?? ($su['total_mark'] ?? 100);
                                
                                $studentTotalObtained += (float)$obtained;
                                $studentTotalPossible += (float)$total;
                                
                                $isFailing = ($mark && $total > 0 && ($obtained < ($su['pass_mark'] ?? ($total/2))));
                            ?>
                            <td class="<?= $isFailing ? 'text-danger fw-bold bg-light-red' : '' ?>">
                                <div class="fw-bold"><?= ($mark ? $obtained : '-') ?></div>
                                <?php if ($mark && !empty($mark['marks_json'])): ?>
                                    <div class="x-small text-muted no-print" style="font-size: 0.6rem;">
                                        <?php 
                                            $breakdown = json_decode($mark['marks_json'], true);
                                            $parts = [];
                                            foreach($breakdown as $k => $v) if($v > 0) $parts[] = $k.':'.$v;
                                            echo implode(' | ', $parts);
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        <td class="fw-bold bg-light border-start border-2 border-dark">
                            <?= $studentTotalObtained ?>
                        </td>
                        <td class="fw-bold bg-light">
                            <?php 
                                $percentage = ($studentTotalPossible > 0) ? round(($studentTotalObtained / $studentTotalPossible) * 100, 1) : 0;
                                echo $percentage . '%';
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-5 d-flex justify-content-between px-5 pt-4">
        <div class="text-center">
            <p class="mb-5 fw-bold">ختم المدرسة</p>
            <div style="width: 120px; height: 120px; border: 2px dashed #ccc; margin: 0 auto;"></div>
        </div>
        <div class="text-center">
            <p class="mb-5 fw-bold">توقيع مدير المدرسة</p>
            <p class="mt-5">...........................</p>
        </div>
    </div>
</div>

<style>
    :root {
        --sela-nile: #004d40;
        --sela-gold: #c5a059;
        --sela-soft-gold: #fdf8ef;
    }
    
    .bg-soft-gold { background-color: var(--sela-soft-gold) !important; }
    .text-nile { color: var(--sela-nile) !important; }
    .bg-nile { background-color: var(--sela-nile) !important; }
    .bg-gold { background-color: var(--sela-gold) !important; }
    .bg-light-red { background-color: #fff5f5 !important; }

    @media print {
        .no-print { display: none !important; }
        .printable-area { border: none !important; box-shadow: none !important; padding: 0 !important; width: 100% !important; }
        .card-sela { border: none !important; }
        .marksheet-table th, .marksheet-table td { border-color: #000 !important; -webkit-print-color-adjust: exact; }
        body { background: white !important; }
        .student-name-cell { min-width: 150px; }
    }

    .marksheet-table { border-collapse: collapse; }
    .marksheet-table th { 
        font-size: 0.85rem; 
        font-weight: 700;
        vertical-align: middle;
        border: 1px solid #333 !important;
    }
    .marksheet-table td { 
        font-size: 0.85rem; 
        padding: 6px 4px;
        border: 1px solid #333 !important;
    }
    .exam-header { font-size: 0.7rem !important; background: #eee !important; color: #444; }
    .student-name-cell { background-color: #fcfcfc; border-right: 2px solid #333 !important; }
    
    .card-sela.primary {
        border-top: 5px solid var(--sela-nile);
    }
</style>
<?= $this->endSection() ?>
