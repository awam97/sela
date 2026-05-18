<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="page-header-sela d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1"><i class='bx bx-edit-alt text-gold me-2'></i> تعديل الامتحان</h1>
        <p class="text-muted small ps-5 mb-0">تغيير المسمى أو الموعد المقترح للامتحان</p>
    </div>
    <a href="<?= base_url('admin/academic/exams') ?>" class="btn-sela btn-sela-gold shadow-sm">
        <i class='bx bx-arrow-back me-1'></i> العودة للجداول
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-sela gold shadow-lg animate__animated animate__fadeIn">
            <div class="card-body p-5">
                <form action="<?= base_url('admin/academic/exams/edit/' . $exam['ID']) ?>" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile px-1 mb-1">اسم الامتحان</label>
                            <input type="text" name="name" class="form-control px-4 py-3 rounded-3" value="<?= $exam['name'] ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile px-1 mb-1">تاريخ البدء المتوقع</label>
                            <input type="date" name="date" class="form-control px-4 py-3 rounded-3" value="<?= $exam['date'] ?>" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold text-nile px-1 mb-1">تصنيف الامتحان / الفترة</label>
                            <select name="category" class="form-select px-4 py-3 rounded-3" required>
                                <optgroup label="نظام السمستر">
                                    <option value="s1" <?php if(($exam['category'] ?? 's1') == 's1') echo 'selected'; ?>>الفصل الدراسي الأول</option>
                                    <option value="s2" <?php if(($exam['category'] ?? 's1') == 's2') echo 'selected'; ?>>الفصل الدراسي الثاني</option>
                                </optgroup>
                                <optgroup label="نظام الفترات">
                                    <option value="p1" <?php if(($exam['category'] ?? 's1') == 'p1') echo 'selected'; ?>>الفترة الأولى</option>
                                    <option value="p2" <?php if(($exam['category'] ?? 's1') == 'p2') echo 'selected'; ?>>الفترة الثانية</option>
                                    <option value="p3" <?php if(($exam['category'] ?? 's1') == 'p3') echo 'selected'; ?>>الفترة الثالثة - نهائي</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="col-md-12 mt-4 px-3">
                            <div class="bg-light p-4 rounded-3 border-start border-4 border-gold">
                                <p class="small text-muted mb-0">
                                    <i class='bx bx-info-circle me-1 text-gold'></i>
                                    يرجى ملاحظة أن تغيير مسمى الامتحان لن يؤثر على الدرجات المرصودة مسبقاً، بل يغير العرض فقط.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn-sela btn-sela-primary w-100 py-3 shadow-lg fs-5">
                            <i class='bx bxs-save me-1'></i> حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
