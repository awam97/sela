<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل مدرسة جديدة - <?= $system_name ?></title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('favicon.ico') ?>">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- Intl-Tel-Input -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/css/intlTelInput.css">
    <style>
        .iti, .iti__country-list { text-align: left; direction: ltr !important; }
        .iti--allow-dropdown, .iti--show-flags, .iti--inline-dropdown { direction: ltr !important; }
        .iti input { direction: ltr !important; text-align: left !important; padding-left: 90px !important; padding-right: 15px !important; }
        .iti__flag-container, .iti__country-container { left: 0 !important; right: auto !important; }
        .iti--separate-dial-code .iti__selected-dial-code { margin-left: 6px !important; }
    </style>
    
    <style>
        :root {
            --primary: <?= $primary_color ?>;
            --secondary: <?= $secondary_color ?>;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .reg-card {
            width: 100%;
            max-width: 700px;
            background: #fff;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(25, 42, 86, 0.1);
            overflow: hidden;
            border: 1px solid #f0f0f0;
        }

        .reg-header {
            background: var(--primary);
            padding: 50px 40px;
            text-align: center;
            color: #fff;
            position: relative;
        }

        .reg-header h2 {
            font-weight: 900;
            margin-bottom: 1rem;
        }

        .reg-header p {
            opacity: 0.8;
            font-size: 1.1rem;
        }

        .reg-body {
            padding: 50px;
        }

        .form-label {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.8rem;
        }

        .form-control, .form-select {
            padding: 0.9rem 1.2rem;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .form-control:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 4px rgba(197, 160, 33, 0.1);
            background: #fff;
        }

        .btn-submit {
            background: var(--primary);
            color: white;
            border: none;
            padding: 1.2rem;
            border-radius: 12px;
            font-weight: 800;
            font-size: 1.1rem;
            width: 100%;
            margin-top: 2rem;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(197, 160, 33, 0.2);
        }

        .info-alert {
            background: rgba(197, 160, 33, 0.05);
            border: 1px solid rgba(197, 160, 33, 0.2);
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2.5rem;
            display: flex;
            gap: 1rem;
            align-items: center;
            color: var(--primary);
        }

        .info-alert i {
            font-size: 2rem;
            color: var(--secondary);
        }
    </style>
</head>
<body>

    <div class="reg-card">
        <div class="reg-header">
            <h2>انضم إلينا اليوم</h2>
            <p>سجل مدرستك في نظام <?= $system_name ?> وابدأ رحلة التحول الرقمي</p>
        </div>
        
        <div class="reg-body">
            <div class="info-alert">
                <i class='bx bx-info-circle'></i>
                <div>
                    <strong>ملاحظة هامة:</strong>
                    يتم إنشاء الحساب في حالة "انتظار التفعيل". سيتواصل معكم فريق الدعم الفني لإتمام عملية الاشتراك وتفعيل البوابة.
                </div>
            </div>

            <form id="schoolRegForm">
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <label class="form-label">اسم المدرسة</label>
                        <input type="text" name="school_name" class="form-control" placeholder="أدخل الاسم الرسمي للمدرسة" required>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <label class="form-label">المدينة</label>
                        <select name="city" class="form-select" required>
                            <option value="">اختر المدينة</option>
                            <?php foreach($cities as $city): ?>
                                <option value="<?= $city['ID'] ?>"><?= $city['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <label class="form-label">رقم الهاتف</label>
                        <input type="tel" id="wa_phone" name="phone_input" class="form-control" placeholder="09XXXXXXXX" required>
                        <input type="hidden" name="phone" id="phone_full">
                    </div>

                    <div class="col-md-12 mb-4">
                        <label class="form-label">العنوان بالتفصيل</label>
                        <input type="text" name="address" class="form-control" placeholder="المدينة، الحي، الشارع" required>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control" placeholder="school@email.com" required>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">اسم مدير المدرسة</label>
                        <input type="text" name="manager_name" class="form-control" placeholder="الاسم الكامل للمدير" required>
                    </div>
                </div>

                <button type="submit" class="btn-submit">إرسال طلب التسجيل</button>
                
                <div class="text-center mt-4">
                    <a href="<?= base_url('/') ?>" class="text-muted text-decoration-none small">
                        <i class='bx bx-right-arrow-alt'></i> العودة للرئيسية
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/intlTelInput.min.js"></script>
    
    <script>
        const phoneInput = document.querySelector("#wa_phone");
        const phoneFull = document.querySelector("#phone_full");
        
        const iti = window.intlTelInput(phoneInput, {
            initialCountry: "ly",
            separateDialCode: true,
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/utils.js",
        });

        $('#schoolRegForm').on('submit', function(e) {
            e.preventDefault();
            
            // Update full phone number
            phoneFull.value = iti.getNumber();
            
            Swal.fire({
                title: 'جاري إرسال الطلب...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.post('<?= base_url('school-registration') ?>', $(this).serialize(), function(res) {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الإرسال بنجاح',
                        text: res.message,
                        confirmButtonText: 'حسناً'
                    }).then(() => {
                        window.location.href = '<?= base_url('/') ?>';
                    });
                } else {
                    Swal.fire('خطأ', 'حدث خطأ أثناء إرسال الطلب، يرجى المحاولة لاحقاً', 'error');
                }
            });
        });
    </script>

</body>
</html>
