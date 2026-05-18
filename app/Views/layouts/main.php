<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $system_name ?> | <?= $page_title ?? 'Dashboard' ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Custom Style -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css?v=2.5') ?>">
    <meta name="X-CSRF-TOKEN" content="<?= csrf_hash() ?>">
    <?= $this->renderSection('styles') ?>
</head>
<?php 
$is_modal_iframe = (request()->getGet('modal') == 1);
$body_classes = [];
if (isset($body_class)) $body_classes[] = $body_class;
if ($is_modal_iframe) $body_classes[] = 'modal-iframe-mode';
?>
<body class="<?= implode(' ', $body_classes) ?>">
    <!-- Preloader -->
    <?php if (!$is_modal_iframe): ?>
    <div class="preloader">
        <div class="spinner"></div>
    </div>
    <?php endif; ?>

<div id="wrapper">
    <!-- Sidebar -->
    <?php if (!$is_modal_iframe) echo $this->include('partials/sidebar'); ?>

    <!-- Content -->
    <div id="content">
        <!-- Topbar -->
        <?php if (!$is_modal_iframe) echo $this->include('partials/navbar'); ?>

        <!-- Main Content Area -->
        <main class="container-fluid py-4 px-lg-5">
            <?= $this->renderSection('content') ?>
        </main>
        
        <!-- Main Footer -->
        <?php if (!$is_modal_iframe) echo $this->include('partials/footer'); ?>
    </div>

    <!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Global Modals (Require JS Libraries) -->
<?= view('admin/students/partials/student_create_modal') ?>
<script>
    // Global AJAX Setup for CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="X-CSRF-TOKEN"]').attr('content')
        }
    });

    // Global Toast Notification
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    function showAlert(type, message) {
        if (type === 'success') {
            Toast.fire({ icon: 'success', title: message });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: message,
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#192A56'
            });
        }
    }
</script>
<script>
    $(document).ready(function () {
        // Hide Preloader
        $('.preloader').addClass('fade-out');

        $('#sidebarCollapse, #desktopSidebarCollapse').on('click', function () {
            $('#sidebar, #content').toggleClass('active');
        });

        // Transform native select inputs into gorgeous custom dropdown menus
        $('select:not(.no-custom-style)').each(function() {
            var $this = $(this);
            if ($this.parent().hasClass('custom-select-wrapper')) return;
            
            // Hide the native select
            $this.addClass('d-none');
            
            // Build the wrapper
            $this.wrap('<div class="custom-select-wrapper"></div>');
            
            // Insert the trigger button
            var selectedText = $this.find('option:selected').text() || $this.find('option').first().text() || 'اختر...';
            $this.after('<div class="custom-select-trigger">' + selectedText + '</div>');
            
            // Build the options list
            var $optionsList = $('<div class="custom-select-options"></div>');
            $this.parent().append($optionsList);
            
            // Add search input if options count is more than 4
            var numberOfOptions = $this.children('option').length;
            if (numberOfOptions > 4) {
                var $searchWrapper = $('<div class="custom-select-search-wrapper">' +
                    '<i class="bx bx-search custom-select-search-icon"></i>' +
                    '<input type="text" class="custom-select-search-input" placeholder="بحث...">' +
                    '</div>');
                $optionsList.append($searchWrapper);
                
                // Prevent dropdown closing when clicking search input
                $searchWrapper.find('.custom-select-search-input').on('click', function(e) {
                    e.stopPropagation();
                });
                
                // Live options filter
                $searchWrapper.find('.custom-select-search-input').on('input', function() {
                    var query = $(this).val().toLowerCase().trim();
                    $optionsList.find('.custom-option').each(function() {
                        var text = $(this).text().toLowerCase();
                        if (text.indexOf(query) > -1) {
                            $(this).removeClass('d-none');
                        } else {
                            $(this).addClass('d-none');
                        }
                    });
                });
            }
            
            // Append options
            $this.children('option').each(function() {
                var isSelected = $(this).is(':selected') ? 'selected' : '';
                $optionsList.append('<div class="custom-option ' + isSelected + '" data-value="' + $(this).val() + '">' + $(this).text() + '</div>');
            });
            
            // Bind click to trigger
            var $wrapper = $this.parent();
            $wrapper.find('.custom-select-trigger').on('click', function(e) {
                e.stopPropagation();
                // Close all other open custom selects
                $('.custom-select-wrapper').not($wrapper).removeClass('open');
                
                // Clear any active search query and show all options when opening
                if (!$wrapper.hasClass('open')) {
                    $wrapper.find('.custom-select-search-input').val('');
                    $wrapper.find('.custom-option').removeClass('d-none');
                }
                
                $wrapper.toggleClass('open');
            });
            
            // Bind click to custom options
            $wrapper.find('.custom-option').on('click', function() {
                var val = $(this).data('value');
                $wrapper.find('.custom-option').removeClass('selected');
                $(this).addClass('selected');
                $wrapper.find('.custom-select-trigger').text($(this).text());
                $wrapper.removeClass('open');
                
                // Update native select and trigger native change event
                $this.val(val).trigger('change');
            });
        });
        
        // Close custom selects on clicking anywhere outside
        $(document).on('click', function() {
            $('.custom-select-wrapper').removeClass('open');
        });

        // Sync custom selects on form reset
        $('form').on('reset', function() {
            setTimeout(function() {
                $('.custom-select-wrapper').each(function() {
                    var $wrapper = $(this);
                    var $this = $wrapper.find('select');
                    var selectedText = $this.find('option:selected').text() || 'اختر...';
                    $wrapper.find('.custom-select-trigger').text(selectedText);
                    $wrapper.find('.custom-option').removeClass('selected');
                    $wrapper.find('.custom-option[data-value="' + $this.val() + '"]').addClass('selected');
                });
            }, 50);
        });

        // --- Global Dynamic Modal Iframe Interceptor ---
        $(document).on('click', 'a[href*="/create"], a[href*="/edit"]', function(e) {
            var url = $(this).attr('href');
            
            // Skip if it's explicitly marked to bypass, is a modal trigger, or is external/js
            if ($(this).attr('data-bs-toggle') === 'modal' || 
                $(this).hasClass('no-modal-iframe') || 
                !url ||
                url.startsWith('#') || 
                url.startsWith('javascript:')) {
                return;
            }
            
            e.preventDefault();
            e.stopPropagation();
            
            // Append modal=1 query parameter
            var modalUrl = url;
            if (modalUrl.indexOf('?') > -1) {
                modalUrl += '&modal=1';
            } else {
                modalUrl += '?modal=1';
            }
            
            // Derive a descriptive title
            var title = $(this).text().trim() || $(this).attr('title') || 'إجراء سريع';
            
            // Build and append modal markup if not already present
            if ($('#globalIframeModal').length === 0) {
                $('body').append(
                    '<div class="modal fade modal-glassmorphic" id="globalIframeModal" tabindex="-1" aria-hidden="true">' +
                    '  <div class="modal-dialog modal-dialog-centered" style="transition: max-width 0.3s ease;">' +
                    '    <div class="modal-content">' +
                    '      <div class="modal-header d-flex justify-content-between align-items-center">' +
                    '        <h5 class="modal-title fw-bold text-nile" id="globalIframeModalTitle"></h5>' +
                    '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
                    '      </div>' +
                    '      <div class="modal-body" style="min-height: 400px; position: relative; transition: height 0.3s ease;">' +
                    '        <div class="text-center py-5 iframe-loader" style="position: absolute; width: 100%; top: 35%; z-index: 10;">' +
                    '          <div class="spinner-border text-gold" style="width: 3rem; height: 3rem;" role="status"><span class="visually-hidden">Loading...</span></div>' +
                    '          <h6 class="text-muted mt-3 fw-bold">جاري تحميل النموذج الفني...</h6>' +
                    '        </div>' +
                    '        <iframe id="globalIframe" class="modal-iframe-container d-none" src="" style="width: 100%; height: 400px; border: none; border-bottom-left-radius: 28px; border-bottom-right-radius: 28px; transition: height 0.3s ease;"></iframe>' +
                    '      </div>' +
                    '    </div>' +
                    '  </div>' +
                    '</div>'
                );
            }
            
            // Setup loading state
            $('#globalIframeModalTitle').html('<i class="bx bx-window-open text-gold me-2"></i> ' + title);
            var $iframe = $('#globalIframe');
            var $loader = $('.iframe-loader');
            
            // Reset to default intermediate sizing to prevent visual layout jumps
            $('#globalIframeModal .modal-dialog').css('max-width', '720px');
            $iframe.css('height', '400px');
            
            $iframe.addClass('d-none').attr('src', modalUrl);
            $loader.removeClass('d-none');
            
            var modal = new bootstrap.Modal(document.getElementById('globalIframeModal'));
            modal.show();
            
            // Sync on load
            $iframe.off('load').on('load', function() {
                $loader.addClass('d-none');
                $iframe.removeClass('d-none');
                
                try {
                    var iframeWin = this.contentWindow;
                    var iframeDoc = this.contentDocument || iframeWin.document;
                    
                    // 1. Dynamic Width Fitting: Calculate the target form card width
                    var $card = $(iframeDoc).find('.card-sela').first();
                    var targetWidth = 720; // Standard form sweet spot width
                    if ($card.length > 0) {
                        targetWidth = $card.outerWidth();
                    } else {
                        var $form = $(iframeDoc).find('form').first();
                        if ($form.length > 0) {
                            targetWidth = $form.outerWidth();
                        }
                    }
                    
                    // Constrain boundaries for maximum visual pleasure
                    if (targetWidth > 350 && targetWidth < 1200) {
                        $('#globalIframeModal .modal-dialog').css('max-width', (targetWidth + 50) + 'px');
                    } else {
                        $('#globalIframeModal .modal-dialog').css('max-width', '750px');
                    }
                    
                    // 2. Dynamic Height Fitting: Adjust frame height to eliminate inside scrollbars
                    var docHeight = $(iframeDoc).find('body')[0].scrollHeight;
                    if (docHeight > 150) {
                        $iframe.css('height', (docHeight + 25) + 'px');
                    }
                    
                    var iframeLoc = iframeWin.location.href;
                    // If the page inside the iframe redirects back to a index/listing or a page that doesn't contain /create or /edit
                    if (iframeLoc && 
                        iframeLoc.indexOf('/create') === -1 && 
                        iframeLoc.indexOf('/edit') === -1 && 
                        iframeLoc.indexOf('modal=1') === -1) {
                        
                        modal.hide();
                        
                        // Show a beautiful SweetAlert toast
                        Swal.fire({
                            icon: 'success',
                            title: 'تمت العملية بنجاح!',
                            text: 'تم حفظ كافة التغييرات والبيانات المحدثة.',
                            timer: 2000,
                            showConfirmButton: false,
                            timerProgressBar: true,
                            position: 'top-end',
                            toast: true
                        });
                        
                        setTimeout(function() {
                            window.location.reload();
                        }, 1200);
                    }
                } catch (err) {
                    console.log("Same-origin policy check.");
                }
            });
        });
    });
</script>
<!-- TinyMCE -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: 'textarea:not(.no-editor)',
        directionality: 'rtl',
        height: 300,
        menubar: 'edit view insert format table tools help',
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic forecolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
        content_style: 'body { font-family: Tajawal, sans-serif; font-size: 16px; }',
        skin: 'oxide',
        promotion: false,
        branding: false
    });
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
