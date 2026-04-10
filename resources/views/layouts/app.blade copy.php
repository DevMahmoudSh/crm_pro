<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>نظام إدارة الطلبات</title>
  
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        /* تنسيق القائمة الجانبية (Desktop) */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: #fff;
        }
        .sidebar a.nav-link {
            color: rgba(255,255,255,0.75);
            padding: 12px 20px;
            font-size: 1rem;
            border-radius: 8px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }
        .sidebar a.nav-link:hover, .sidebar a.nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: #fff;
            transform: translateX(-5px);
            border-right: 4px solid #0d6efd;
        }
        .sidebar i { width: 25px; text-align: center; }
        
        .main-content { padding: 20px; width: 100%; }

        /* إصلاحات Select2 */
        .select2-container--open { z-index: 9999999 !important; }
        .select2-search__field { color: #000 !important; background-color: #fff !important; direction: rtl; text-align: right; }
        .select2-results__option { direction: rtl; text-align: right; }
        
        /* تنسيق قائمة الموبايل */
        .mobile-nav-link {
            color: #fff;
            padding: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: block;
            text-decoration: none;
        }
        .mobile-nav-link:hover { background: rgba(255,255,255,0.1); color: #fff; }
        /* تحسينات السايدبار للحركة */
        .sidebar {
            /* 1. تحديد الخصائص بدلاً من all لتحسين الأداء */
            transition: transform 0.4s cubic-bezier(0.25, 1, 0.5, 1), 
                        width 0.4s cubic-bezier(0.25, 1, 0.5, 1),
                        opacity 0.4s ease;

            /* 2. استخدام التدرج في السرعة بدلاً من ease العادي */
            /* cubic-bezier يجعل الحركة تبدأ سريعة وتنتهي بنعومة فائقة */
            
            z-index: 1000;
            will-change: transform; /* تنبيه المتصفح لتجهيز المعالج الرسومي */
        }

        /* الكلاس الذي سيتم إضافته عند الإخفاء */
        .sidebar.collapsed {
            margin-right: -250px; /* يخفي القائمة خارج الشاشة */
            position: absolute;
        }

        /* تعديل عرض المحتوى الرئيسي عند إخفاء القائمة */
        .main-content {
            transition: all 0.3s ease;
        }

        /* زر التبديل */
        #sidebarToggle {
            cursor: pointer;
            font-size: 1.2rem;
            margin-left: 15px;
        }

        /* style of footer */
        .footer-love {
            width: 100%;
            padding: 20px 0;
            text-align: center;
            background-color: #f9f9f9; /* Light background to separate from content */
            color: #555;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            border-top: 1px solid #eee;
        }

        .heart {
            color: #e25555; /* A nice soft red */
            display: inline-block;
            padding: 0 3px;
            transition: transform 0.2s ease;
        }

        .footer-love:hover .heart {
            transform: scale(1.2); /* Makes the heart pop slightly on hover */
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        footer {
            margin-top: auto;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row flex-nowrap">
        
        <div class="col-auto col-md-2 col-xl-2 px-0 sidebar d-none d-md-block">
            <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-4 text-white min-vh-100">
                <a href="/" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                    <span class="fs-4 fw-bold"><i class="fas fa-cube me-2"></i> الإدارة</span>
                </a>
                <hr class="w-100 border-secondary">
                
                <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start w-100">
                    <li class="nav-item w-100">
                        <a href="{{ route('dashboard.index') }}" class="nav-link align-middle {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
                            <i class="fs-5 fas fa-home"></i> <span class="ms-1 d-none d-sm-inline">الرئيسية</span>
                        </a>
                    </li>
                    <li class="w-100">
                        <a href="{{ route('clients.index') }}" class="nav-link align-middle {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                            <i class="fs-5 fas fa-users"></i> <span class="ms-1 d-none d-sm-inline">الزبائن</span>
                        </a>
                    </li>
                    <li class="w-100">
                        <a href="{{ route('orders.index') }}" class="nav-link align-middle {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                            <i class="fs-5 fas fa-shopping-cart"></i> <span class="ms-1 d-none d-sm-inline">الطلبات</span>
                        </a>
                    </li>
                    <!-- <li class="w-100">
                        <a href="{{ route('payments.index') }}" class="nav-link align-middle {{ request()->routeIs('payments.index') ? 'active' : '' }}">
                            <i class="fs-5 fas fa-hand-holding-usd"></i> <span class="ms-1 d-none d-sm-inline">المدفوعات</span>
                        </a>
                    </li> -->
                    <li class="w-100">
                        <a href="{{ route('payments.history') }}" class="nav-link align-middle {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                            <i class="fs-5 fas fa-hand-holding-usd"></i> <span class="ms-1 d-none d-sm-inline">المدفوعات</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col py-3 main-content">
            <div class="col py-3 main-content">
                <nav class="navbar navbar-light bg-white mb-4 shadow-sm d-none d-md-flex rounded">
                    <div class="container-fluid">
                        <button class="btn btn-light" id="sidebarToggle">
                            <i class="fas fa-bars"></i>
                        </button>
                        <span class="navbar-brand mb-0 h1">نظام إدارة الطلبات</span>
                    </div>
                </nav>
            
            <nav class="navbar navbar-light bg-light mb-4 d-md-none rounded shadow-sm">
                <div class="container-fluid">
                    <span class="navbar-brand mb-0 h1 fw-bold">لوحة التحكم</span>
                    
                    <button class="btn btn-outline-dark" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu" aria-controls="mobileMenu" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </nav>

            <div class="collapse d-md-none mb-4" id="mobileMenu">
                <div class="card card-body bg-dark text-white p-0">
                    <div class="list-group list-group-flush bg-dark">
                        <a href="{{ url('/') }}" class="mobile-nav-link">
                            <i class="fas fa-home me-2"></i> الرئيسية
                        </a>
                        <a href="{{ route('clients.index') }}" class="mobile-nav-link">
                            <i class="fas fa-users me-2"></i> الزبائن
                        </a>
                        <a href="{{ route('orders.index') }}" class="mobile-nav-link">
                            <i class="fas fa-shopping-cart me-2"></i> الطلبات
                        </a>
                        <a href="{{ route('payments.index') }}" class="mobile-nav-link">
                            <i class="fas fa-hand-holding-usd me-2"></i> المدفوعات
                        </a>
                    </div>
                </div>
            </div>

            {{-- هنا سيتم حقن محتوى الصفحات الأخرى --}}
            @yield('content')
            <footer class="footer-love">
                Developed with <span class="heart">&hearts;</span> by <strong>DEV7MOD</strong>
            </footer>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#sidebarToggle').on('click', function() {
            // تبديل الكلاس للسايدبار
            $('.sidebar').toggleClass('collapsed');
            
            // إذا أردت إخفاء السايدبار تماماً (d-none) بدلاً من تحريكه
            if ($('.sidebar').hasClass('collapsed')) {
                $('.sidebar').removeClass('d-md-block');
            } else {
                $('.sidebar').addClass('d-md-block');
            }
        });
    });
</script>
@stack('scripts')



</body>
</html>