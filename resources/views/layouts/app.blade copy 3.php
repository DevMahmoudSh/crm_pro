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
        :root {
            --sidebar-bg: #ffffff;
            --main-bg: #f8fafc;
            --primary-color: #4f46e5; /* Indigo */
            --text-muted: #64748b;
            --text-dark: #1e293b;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--main-bg);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Premium Light Sidebar */
        .sidebar {
            min-height: 100vh;
            background: var(--sidebar-bg);
            border-left: 1px solid #e2e8f0; /* Subtle border instead of heavy shadow */
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
        }

        .sidebar-header {
            padding: 30px 20px;
            text-align: center;
        }

        .sidebar-header .logo-icon {
            background: var(--primary-color);
            color: white;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            margin: 0 auto 10px;
            font-size: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        }

        .sidebar hr {
            border-color: #f1f5f9;
            margin: 0 20px 20px;
        }

        .nav-link {
            color: var(--text-muted);
            padding: 12px 20px;
            margin: 4px 15px;
            border-radius: 10px;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .nav-link i {
            font-size: 1.2rem;
            margin-left: 12px;
            width: 25px;
            text-align: center;
        }

        /* Hover Effect */
        .nav-link:hover {
            color: var(--primary-color);
            background-color: #f1f5f9;
        }

        /* Elegant Active State */
        .nav-link.active {
            background-color: #eef2ff;
            color: var(--primary-color);
            position: relative;
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            right: -15px;
            top: 20%;
            height: 60%;
            width: 4px;
            background: var(--primary-color);
            border-radius: 4px 0 0 4px;
        }

        /* Collapsed Sidebar */
        .sidebar.collapsed {
            margin-right: -250px;
            position: absolute;
        }

        /* Top Navbar Customization */
        .main-navbar {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 16px;
        }

        .main-content { padding: 25px; transition: 0.3s; }

        /* Mobile Menu */
        .mobile-nav-link {
            color: var(--text-dark);
            padding: 15px;
            text-decoration: none;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #f1f5f9;
        }

        .footer-love {
            padding: 30px;
            color: var(--text-muted);
            font-size: 13px;
            margin-top: auto;
            text-align: center;
        }
        .heart { color: #f43f5e; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row flex-nowrap">
        
        <div class="col-auto col-md-2 px-0 sidebar d-none d-md-block">
            <div class="d-flex flex-column min-vh-100">
                <div class="sidebar-header">
                    <div class="logo-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h5 class="fw-bold text-dark m-0">لوحة الإدارة</h5>
                </div>
                
                <hr>
                
                <ul class="nav nav-pills flex-column mb-auto w-100">
                    <li class="w-100">
                        <a href="{{ route('dashboard.index') }}" class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
                            <i class="fas fa-columns"></i> <span>الرئيسية</span>
                        </a>
                    </li>
                    <li class="w-100">
                        <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                            <i class="fas fa-address-book"></i> <span>الزبائن</span>
                        </a>
                    </li>
                    <li class="w-100">
                        <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-bag"></i> <span>الطلبات</span>
                        </a>
                    </li>
                    <li class="w-100">
                        <a href="{{ route('payments.history') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                            <i class="fas fa-credit-card"></i> <span>المدفوعات</span>
                        </a>
                    </li>
                </ul>
                
                <div class="p-3">
                    <div class="rounded-3 p-3 bg-light text-center">
                        <small class="text-muted d-block mb-2">تحتاج مساعدة؟</small>
                        <a href="#" class="btn btn-sm btn-outline-primary w-100">الدعم الفني</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col py-3 main-content">
            <nav class="navbar navbar-expand navbar-light main-navbar shadow-sm mb-4 d-none d-md-flex px-4">
                <button class="btn btn-white border shadow-sm rounded-circle me-3" id="sidebarToggle">
                    <i class="fas fa-indent"></i>
                </button>
                <div class="ms-auto d-flex align-items-center">
                    <div class="text-end me-3">
                        <p class="m-0 small fw-bold text-dark">مدير النظام</p>
                        <p class="m-0 x-small text-muted" style="font-size: 11px;">متصل الآن</p>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=Admin&background=4f46e5&color=fff" class="rounded-circle shadow-sm" width="40" height="40">
                </div>
            </nav>

            <nav class="navbar navbar-light bg-white shadow-sm mb-4 d-md-none rounded-4 px-3">
                <span class="navbar-brand fw-bold text-primary">إدارة النظام</span>
                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu">
                    <i class="fas fa-th-list"></i>
                </button>
            </nav>

            <div class="collapse d-md-none mb-4" id="mobileMenu">
                <div class="card card-body border-0 shadow-lg rounded-4 p-0">
                    <a href="{{ url('/') }}" class="mobile-nav-link"><i class="fas fa-home me-2"></i> الرئيسية</a>
                    <a href="{{ route('clients.index') }}" class="mobile-nav-link"><i class="fas fa-users me-2"></i> الزبائن</a>
                    <a href="{{ route('orders.index') }}" class="mobile-nav-link"><i class="fas fa-shopping-cart me-2"></i> الطلبات</a>
                    <a href="{{ route('payments.history') }}" class="mobile-nav-link"><i class="fas fa-wallet me-2"></i> المدفوعات</a>
                </div>
            </div>

            @yield('content')
            
            <footer class="footer-love">
                صنع بكل <span class="heart">&hearts;</span> بواسطة <strong>DEV7MOD</strong> &copy; 2026
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
            $('.sidebar').toggleClass('collapsed');
            // Toggle icon direction
            $(this).find('i').toggleClass('fa-indent fa-outdent');
        });
    });
</script>
@stack('scripts')
</body>
</html>