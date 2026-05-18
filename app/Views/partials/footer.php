<footer class="mt-auto pt-4 pb-2 border-top-0 d-flex align-items-center justify-content-center">
    <?php if (session()->get('role') !== 'super_admin'): ?>
    <div class="year-switcher-container">
        <form action="<?= site_url('admin/switch_year') ?>" method="POST" id="yearSwitcherForm">
            <?= csrf_field() ?>
            <div class="input-group input-group-sm">
                <label class="input-group-text bg-gold border-0 text-nile fw-bold shadow-sm" for="academicYear">
                    <i class='bx bx-calendar-check me-1'></i> العام الدراسي النشط
                </label>
                <select name="year" class="form-select border-0 shadow-sm bg-white text-nile fw-bold" id="academicYear" onchange="document.getElementById('yearSwitcherForm').submit()">
                    <?php if (isset($available_years)): ?>
                    <?php foreach ($available_years as $year): ?>
                        <option value="<?= $year ?>" <?= isset($current_year) && $year == $current_year ? 'selected' : '' ?>>
                            <?= $year ?>
                        </option>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </form>
    </div>
    <?php endif; ?>
</footer>
