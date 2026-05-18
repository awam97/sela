<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center mb-5">
    <div>
        <h1 class="mb-1"><i class='bx bxs-calendar-event text-gold me-2'></i> جدول الحصص الأسبوعي</h1>
        <p class="text-muted small ps-5 mb-0">إدارة التوقيت الزمني للمواد الدراسية: <?= $class_name ?> - <?= $section_name ?></p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn-sela btn-sela-gold shadow-sm" data-bs-toggle="modal" data-bs-target="#addPeriodModal">
            <i class='bx bx-plus-circle me-1'></i> إضافة حصة جديدة
        </button>
    </div>
</div>

<div class="timetable-grid animate__animated animate__fadeIn">
    <?php 
    $days = [
        'saturday'  => 'السبت',
        'sunday'    => 'الأحد',
        'monday'    => 'الاثنين',
        'tuesday'   => 'الثلاثاء',
        'wednesday' => 'الأربعاء',
        'thursday'  => 'الخميس',
        'friday'    => 'الجمعة'
    ];
    foreach ($days as $day_key => $day_name): 
        // Skip hidden weekend days
        if (in_array($day_key, $off_days)) continue;
    ?>
    <div class="day-column shadow-sm border-0 bg-white">
        <div class="day-header text-center pb-3 border-bottom">
            <span class="day-name fw-800 text-nile fs-5"><?= $day_name ?></span>
            <div class="small fw-bold text-gold mt-1"><i class='bx bxs-time-five me-1'></i> <?= count($weekly_routine[$day_key]) ?> حصة</div>
        </div>
        
        <div class="day-body pt-3">
            <?php if (empty($weekly_routine[$day_key])): ?>
                <div class="text-center py-5 opacity-25">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-calendar-6060197-5036729.png" width="80" class="mb-2">
                    <p class="small mb-0 fw-bold">يوم راحة</p>
                </div>
            <?php else: ?>
                <?php foreach ($weekly_routine[$day_key] as $period): ?>
                    <div class="period-card-premium shadow-sm border-0 position-relative animate__animated animate__zoomIn">
                        <!-- Private Action: Delete -->
                        <a href="<?= base_url("admin/routines/delete/{$period['class_routine_id']}/{$class_id}/{$section_id}") ?>" 
                           class="btn-delete-period-premium shadow-sm" 
                           onclick="return confirm('هل أنت متأكد من حذف هذه الحصة؟')">
                            <i class='bx bx-x'></i>
                        </a>

                        <div class="period-time-premium">
                            <i class='bx bx-time me-1'></i>
                            <?= sprintf('%02d:%02d', $period['time_start'], $period['time_start_min']) ?> - 
                            <?= sprintf('%02d:%02d', $period['time_end'], $period['time_end_min']) ?>
                            <span class="period-ampm ms-1 text-uppercase"><?= $period['am_pm'] ?></span>
                        </div>
                        <div class="period-subject-premium text-nile fw-800 mb-1"><?= $period['subject_name'] ?></div>
                        <div class="period-teacher-premium small">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($period['teacher_name'] ?? 'T') ?>&background=fdfaf0&color=c5a021&size=20" class="rounded-circle me-1 border border-gold-light">
                            <span class="text-muted fw-bold"><?= $period['teacher_name'] ?? 'معلم غير محدد' ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<style>
    .timetable-grid { display: flex; overflow-x: auto; padding-bottom: 25px; gap: 20px; padding-top: 10px; }
    .day-column { min-width: 280px; flex: 1; border-radius: 24px; padding: 25px 15px; transition: all 0.3s; }
    .day-column:hover { background-color: #fdfdfd; transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.05) !important; }
    .day-name { letter-spacing: -0.5px; }
    
    .period-card-premium { background: #fff; border-radius: 18px; padding: 20px 18px; margin-bottom: 15px; border-right: 5px solid var(--secondary-color) !important; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
    .period-card-premium:hover { transform: scale(1.03); box-shadow: 0 12px 30px rgba(25, 42, 86, 0.08) !important; z-index: 10; }
    .period-time-premium { font-size: 0.7rem; font-weight: 800; color: var(--secondary-color); margin-bottom: 8px; font-family: 'Inter', sans-serif; }
    .period-ampm { opacity: 0.7; }
    .period-subject-premium { font-size: 0.95rem; line-height: 1.2; }
    .period-teacher-premium { display: flex; align-items: center; opacity: 0.9; }
    .border-gold-light { border-color: rgba(197, 160, 33, 0.2) !important; }

    .btn-delete-period-premium { position: absolute; top: -8px; left: -8px; width: 24px; height: 24px; background: #ef4444; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; border: 2px solid #fff; opacity: 0; transition: all 0.2s; text-decoration: none; }
    .period-card-premium:hover .btn-delete-period-premium { opacity: 1; transform: scale(1.1); }
    .btn-delete-period-premium:hover { background: #dc2626; color: #fff; }
</style>

<!-- Add Period Modal -->
<div class="modal fade modal-sela" id="addPeriodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="modal-title fw-bold text-nile"><i class='bx bx-plus-circle text-gold me-2'></i> إضافة حصة جديدة</h5>
                    <p class="text-muted small mb-0">تحديد وقت ومادة الحصة في الجدول</p>
                </div>
                <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('admin/routines/save') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="class_id" value="<?= $class_id ?>">
                    <input type="hidden" name="section_id" value="<?= $section_id ?>">
                    
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="service-label fw-bold"><i class='bx bx-book-open me-1 text-gold'></i> المادة الدراسية</label>
                            <select name="subject_id" class="form-select minimal-select shadow-sm" required>
                                <option value="" disabled selected>اختر المادة...</option>
                                <?php foreach ($subjects as $s): ?>
                                    <option value="<?= $s['subject_id'] ?>"><?= $s['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="service-label fw-bold"><i class='bx bx-calendar-alt me-1 text-gold'></i> اليوم</label>
                            <select name="day" class="form-select minimal-select shadow-sm" required>
                                <option value="" disabled selected>اختر اليوم...</option>
                                <?php foreach ($days as $k => $v): 
                                    if (in_array($k, $off_days)) continue;
                                ?>
                                    <option value="<?= $k ?>"><?= $v ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="service-label fw-bold"><i class='bx bx-sun me-1 text-gold'></i> التوقيت</label>
                            <select name="am_pm" class="form-select minimal-select shadow-sm">
                                <option value="am">صباحاً (AM)</option>
                                <option value="pm">مساءً (PM)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="service-label fw-bold"><i class='bx bx-play-circle me-1 text-gold'></i> وقت البدء</label>
                            <div class="input-group-mark">
                                <select name="time_start" class="form-select border-0 bg-transparent text-center fw-bold" style="max-width: 70px;">
                                    <?php for($i=1; $i<=12; $i++): ?> <option value="<?= $i ?>"><?= $i ?></option> <?php endfor; ?>
                                </select>
                                <span class="input-group-text-sela">:</span>
                                <select name="time_start_min" class="form-select border-0 bg-transparent text-center fw-bold">
                                    <option value="00">00</option><option value="05">05</option><option value="10">10</option>
                                    <option value="15">15</option><option value="20">20</option><option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="service-label fw-bold"><i class='bx bx-stop-circle me-1 text-gold'></i> وقت الانتهاء</label>
                            <div class="input-group-mark">
                                <select name="time_end" class="form-select border-0 bg-transparent text-center fw-bold" style="max-width: 70px;">
                                    <?php for($i=1; $i<=12; $i++): ?> <option value="<?= $i ?>"><?= $i ?></option> <?php endfor; ?>
                                </select>
                                <span class="input-group-text-sela">:</span>
                                <select name="time_end_min" class="form-select border-0 bg-transparent text-center fw-bold">
                                    <option value="00">00</option><option value="10">10</option><option value="15">15</option>
                                    <option value="20">20</option><option value="30">30</option><option value="45">45</option>
                                    <option value="50">50</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 text-center">
                        <button type="submit" class="btn-sela btn-sela-primary w-100 py-3 shadow-lg hover-up">
                            <i class='bx bxs-save me-2'></i> إضافة الحصة للجدول
                        </button>
                        <p class="text-muted x-small mt-3 mb-0">يرجى التأكد من عدم تضارب الأوقات مع حصص أخرى في نفس اليوم</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
