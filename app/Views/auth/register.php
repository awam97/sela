<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل طالب جديد | <?= $system_name ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Intl-Tel-Input -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/css/intlTelInput.css">
    <meta name="X-CSRF-TOKEN" content="<?= csrf_hash() ?>">
    <style>
        :root {
            --primary-color: <?= $primary_color ?>;
            --secondary-color: <?= $secondary_color ?>;
        }
        .iti, .iti__country-list { text-align: left; direction: ltr !important; }
        .iti--allow-dropdown, .iti--show-flags, .iti--inline-dropdown { direction: ltr !important; }
        .iti input { direction: ltr !important; text-align: left !important; padding-left: 90px !important; padding-right: 15px !important; }
        .iti__flag-container, .iti__country-container { left: 0 !important; right: auto !important; }
        .iti--separate-dial-code .iti__selected-dial-code { margin-left: 6px !important; }
        
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .reg-card {
            width: 100%;
            max-width: 550px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 15px 45px rgba(25, 42, 86, 0.1);
            overflow: hidden;
            border: 1px solid #f0f0f0;
        }
        .reg-header {
            background: var(--primary-color);
            padding: 40px 30px;
            text-align: center;
            color: #fff;
        }
        .reg-body {
            padding: 40px;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        .step-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #e2e8f0;
            transition: all 0.3s;
        }
        .step-dot.active {
            background: var(--secondary-color);
            width: 30px;
            border-radius: 10px;
        }
        .form-step {
            display: none;
        }
        .form-step.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .btn-sela {
            background: var(--primary-color);
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s;
        }
        .btn-sela:hover {
            background: #0d1e42;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(25, 42, 86, 0.2);
            color: #fff;
        }
        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px 18px;
            border: 1px solid #eef0f5;
            margin-bottom: 20px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 4px rgba(197, 160, 33, 0.1);
        }
        label {
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--primary-color);
            font-size: 0.9rem;
        }
        .otp-input {
            letter-spacing: 15px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--secondary-color);
        }

        /* Spinner */
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
            border-top: 6px solid var(--primary-color);
            border-right: 6px solid var(--secondary-color);
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

        .btn-home {
            position: fixed;
            top: 30px;
            left: 30px;
            background: #fff;
            color: var(--primary-color);
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.3s;
            z-index: 1000;
            border: 1px solid #eee;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .btn-home:hover {
            background: var(--secondary-color);
            color: #fff;
            transform: translateX(5px);
            border-color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <div class="preloader">
        <div class="spinner"></div>
    </div>

    <a href="<?= base_url('/') ?>" class="btn-home">
        <i class='bx bx-home-alt'></i>
        <span>الرئيسية</span>
    </a>

<div class="reg-card">
    <div class="reg-header">
        <h4 class="fw-bold mb-1">بوابة التسجيل الذاتي</h4>
        <p class="text-white-50 small mb-0">سجل الآن للانضمام إلى <?= $system_name ?></p>
    </div>
    
    <div class="reg-body">
        <div class="step-indicator">
            <div class="step-dot active" id="dot1"></div>
            <div class="step-dot" id="dot2"></div>
            <div class="step-dot" id="dot3"></div>
        </div>

        <form id="registrationForm">
            <!-- Step 1: Personal Info -->
            <div class="form-step active" id="step1">
                <div class="mb-3">
                    <label>الاسم الكامل (ثلاثي على الأقل)</label>
                    <input type="text" name="name" class="form-control" placeholder="أدخل اسمك بالكامل" required>
                </div>
                <div class="mb-3">
                    <label>الجنس</label>
                    <select name="sex" class="form-select" required>
                        <option value="">اختر الجنس</option>
                        <option value="male">ذكر</option>
                        <option value="female">أنثى</option>
                    </select>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-sela px-5" onclick="nextStep(2)">التالي <i class='bx bx-left-arrow-alt ms-1'></i></button>
                </div>
            </div>

            <!-- Step 2: Educational Selection -->
            <div class="form-step" id="step2">
                <div class="mb-3">
                    <label>المدرسة</label>
                    <select name="school_id" id="school_select" class="form-select" required>
                        <option value="">اختر المدرسة</option>
                        <?php foreach($schools as $s): ?>
                            <option value="<?= $s['ID'] ?>"><?= $s['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>الصف الدراسي</label>
                    <select name="class_id" id="class_select" class="form-select" required disabled>
                        <option value="">اختر المدرسة أولاً</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>رقم الهاتف (الواتساب)</label>
                    <div class="input-group-sela">
                        <input type="tel" id="wa_phone" name="phone_input" class="form-control mb-0" placeholder="09XXXXXXXX" required>
                        <input type="hidden" name="phone" id="phone_full">
                    </div>
                    <small class="text-muted mt-1 d-block"><i class='bx bxl-whatsapp text-success me-1'></i> سيتم إرسال رمز التحقق عبر الواتساب</small>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" onclick="prevStep(1)">السابق</button>
                    <button type="button" class="btn btn-sela px-5" onclick="sendOtpStep()">إرسال الرمز <i class='bx bx-send ms-1'></i></button>
                </div>
            </div>

            <!-- Step 3: OTP Verification -->
            <div class="form-step" id="step3">
                <div class="text-center mb-4">
                    <div class="display-6 text-gold mb-2"><i class='bx bx-mobile-alt animate__animated animate__shakeX animate__infinite'></i></div>
                    <h6 class="fw-bold">تحقق من حسابك</h6>
                    <p class="text-muted small">أدخل الرمز المكون من 6 أرقام المرسل إلى هاتفك</p>
                </div>
                <div class="mb-3">
                    <input type="text" id="otp_code" class="form-control otp-input" maxlength="6" placeholder="000000">
                </div>
                <div class="d-grid mt-4">
                    <button type="button" id="verifyBtn" class="btn btn-sela py-3 mb-2" onclick="verifyOtp()">تأكيد وإرسال الطلب</button>
                    <button type="button" class="btn btn-link text-muted small text-decoration-none" onclick="sendOtpStep()">لم يصلك الرمز؟ إعادة الإرسال</button>
                </div>
            </div>
        </form>
        
        <div class="text-center mt-4">
            <a href="<?= base_url('auth/login') ?>" class="text-decoration-none text-muted small"><i class='bx bx-chevron-right'></i> العودة لتسجيل الدخول</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/intlTelInput.min.js"></script>

<script>
    // Global AJAX Setup for CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="X-CSRF-TOKEN"]').attr('content')
        }
    });

    const phoneInput = document.querySelector("#wa_phone");
    const phoneFull = document.querySelector("#phone_full");
    
    const iti = window.intlTelInput(phoneInput, {
        initialCountry: "ly",
        separateDialCode: true,
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/utils.js",
    });
    function nextStep(step) {
        $('.form-step').removeClass('active');
        $(`#step${step}`).addClass('active');
        $('.step-dot').removeClass('active');
        $(`#dot${step}`).addClass('active');
    }

    function prevStep(step) {
        nextStep(step);
    }

    // Dynamic Classes
    $('#school_select').on('change', function() {
        const schoolId = $(this).val();
        if (schoolId) {
            $('#class_select').prop('disabled', true).html('<option>جاري التحميل...</option>');
            $.get('<?= base_url('get-classes') ?>/' + schoolId, function(res) {
                let options = '<option value="">اختر الصف</option>';
                res.forEach(c => {
                    options += `<option value="${c.class_id}">${c.name}</option>`;
                });
                $('#class_select').prop('disabled', false).html(options);
            });
        }
    });

    function sendOtpStep() {
        const phone = iti.getNumber();
        if (!phone) {
            Swal.fire('خطأ', 'يرجى إدخال رقم الهاتف', 'error');
            return;
        }

        // Update full phone number
        phoneFull.value = phone;

        // Show loading
        Swal.fire({
            title: 'جاري الإرسال...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.post('<?= base_url('register/send-otp') ?>', { phone: phone }, function(res) {
            Swal.close();
            if (res.status === 'success') {
                nextStep(3);
            } else {
                Swal.fire('خطأ', res.message, 'error');
            }
        });
    }

    function verifyOtp() {
        const otp = $('#otp_code').val();
        if (otp.length < 6) {
            Swal.fire('تنبيه', 'الرمز يجب أن يتكون من 6 أرقام', 'warning');
            return;
        }

        Swal.fire({
            title: 'جاري التحقق...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.post('<?= base_url('register/verify-otp') ?>', { otp: otp }, function(res) {
            if (res.status === 'success') {
                submitFinal();
            } else {
                Swal.fire('فشل', 'رمز التحقق غير صحيح', 'error');
            }
        });
    }

    function submitFinal() {
        const formData = {
            name: $('input[name="name"]').val(),
            sex: $('select[name="sex"]').val(),
            school_id: $('#school_select').val(),
            class_id: $('#class_select').val(),
            phone: $('#phone_input').val(),
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        };

        $.post('<?= base_url('register/submit') ?>', formData, function(res) {
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'تم بنجاح',
                    text: res.message,
                    confirmButtonText: 'حسناً'
                }).then(() => {
                    window.location.href = '<?= base_url('auth/login') ?>';
                });
            } else {
                Swal.fire('خطأ', res.message, 'error');
            }
        });
    }
</script>

    <script>
        $(window).on('load', function() {
            $('.preloader').addClass('fade-out');
        });
    </script>
</body>
</html>
