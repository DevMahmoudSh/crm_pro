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
            background-color: #f4f7fe; /* Lighter, more modern background */
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Modern Sidebar Styling */
        .sidebar {
            min-height: 100vh;
            background: #111827; /* Solid Deep Navy */
            box-shadow: 4px 0 10px rgba(0,0,0,0.1);
            transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
            z-index: 1000;
            will-change: transform;
        }

        .sidebar-brand {
            padding: 25px 20px;
            color: #fff;
            font-size: 1.3rem;
            letter-spacing: 0.5px;
        }

        .sidebar hr {
            border-color: rgba(255,255,255,0.1);
            margin: 0 15px 20px 15px;
        }

        .sidebar a.nav-link {
            color: #9ca3af; /* Muted gray text */
            padding: 14px 20px;
            font-size: 0.95rem;
            border-radius: 12px;
            margin: 4px 15px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease;
        }

        /* Icon styling */
        .sidebar a.nav-link i {
            font-size: 1.1rem;
            margin-left: 12px; /* RTL spacing */
            width: 20px;
            transition: transform 0.2s ease;
        }

        /* Hover State */
        .sidebar a.nav-link:hover {
            background-color: rgba(255,255,255,0.05);
            color: #fff;
            transform: translateX(-5px);
        }

        /* Active State */
        .sidebar a.nav-link.active {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .sidebar a.nav-link.active i {
            color: #fff;
            transform: scale(1.1);
        }

        /* Collapsed State Logic */
        .sidebar.collapsed {
            margin-right: -250px;
            position: absolute;
        }

        /* Main Content Adjustments */
        .main-content { 
            padding: 25px; 
            width: 100%; 
            transition: all 0.3s ease; 
        }

        .navbar {
            border-radius: 15px;
            border: none;
        }

        /* Mobile Adjustments */
        .mobile-nav-link {
            color: #fff;
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .mobile-nav-link i { margin-left: 10px; }
        
        /* Select2 and Footer fix */
        .select2-container--open { z-index: 9999999 !important; }
        .footer-love {
            padding: 20px 0;
            text-align: center;
            background-color: transparent;
            color: #6b7280;
            font-size: 13px;
            margin-top: auto;
        }
        .heart { color: #ef4444; display: inline-block; padding: 0 3px; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row flex-nowrap">
        
        <div class="col-auto col-md-2 col-xl-2 px-0 sidebar d-none d-md-block">
            <div class="d-flex flex-column align-items-center align-items-sm-start text-white min-vh-100">
                <div class="sidebar-brand d-flex align-items-center w-100">
                    <i class="fas fa-layer-group text-primary fs-3 me-2"></i>
                    <span class="fw-bold">نظام الطلبات</span>
                </div>
                
                <hr class="w-100">
                
                <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start w-100">
                    <li class="nav-item w-100">
                        <a href="{{ route('dashboard.index') }}" class="nav-link {{ request()->routeIs('dashboard.*') ? 'active' : '' }}">
                            <i class="fas fa-th-large"></i> <span>الرئيسية</span>
                        </a>
                    </li>
                    <li class="w-100">
                        <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                            <i class="fas fa-user-friends"></i> <span>الزبائن</span>
                        </a>
                    </li>
                    <li class="w-100">
                        <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                            <i class="fas fa-receipt"></i> <span>الطلبات</span>
                        </a>
                    </li>
                    <li class="w-100">
                        <a href="{{ route('payments.history') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                            <i class="fas fa-wallet"></i> <span>المدفوعات</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col py-3 main-content">
            <nav class="navbar navbar-expand navbar-light bg-white mb-4 shadow-sm d-none d-md-flex px-3">
                <button class="btn btn-light rounded-circle me-3" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="navbar-brand mb-0 h1 fw-bold text-dark">نظام الإدارة</span>
                <div class="ms-auto">
                    <span class="text-muted small">{{ date('Y-m-d') }}</span>
                </div>
            </nav>
            
            <nav class="navbar navbar-light bg-white mb-4 d-md-none rounded shadow-sm px-3">
                <span class="navbar-brand mb-0 h1 fw-bold">لوحة التحكم</span>
                <button class="btn btn-dark" type="button" data-bs-toggle="collapse" data-bs-target="#mobileMenu">
                    <i class="fas fa-bars"></i>
                </button>
            </nav>

            <div class="collapse d-md-none mb-4" id="mobileMenu">
                <div class="card card-body bg-dark text-white p-0 border-0 rounded-4 shadow">
                    <div class="list-group list-group-flush bg-dark">
                        <a href="{{ url('/') }}" class="mobile-nav-link"><i class="fas fa-home"></i> الرئيسية</a>
                        <a href="{{ route('clients.index') }}" class="mobile-nav-link"><i class="fas fa-users"></i> الزبائن</a>
                        <a href="{{ route('orders.index') }}" class="mobile-nav-link"><i class="fas fa-shopping-cart"></i> الطلبات</a>
                        <a href="{{ route('payments.history') }}" class="mobile-nav-link"><i class="fas fa-hand-holding-usd"></i> المدفوعات</a>
                    </div>
                </div>
            </div>

            @yield('content')
            
            <footer class="footer-love mt-5">
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
            $('.sidebar').toggleClass('collapsed');
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