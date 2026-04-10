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
            --primary-color: #4f46e5;
            --text-muted: #64748b;
            --text-dark: #1e293b;
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--main-bg);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* --- Fixed Sidebar Fixes --- */
        .sidebar-wrapper {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            right: 0;
            background: var(--sidebar-bg);
            border-left: 1px solid #e2e8f0;
            z-index: 1040; /* Lower than Select2 but higher than content */
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .main-content {
            margin-right: var(--sidebar-width);
            padding: 25px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* --- Sidebar Toggle States --- */
        .sidebar-wrapper.collapsed {
            transform: translateX(100%);
        }

        .main-content.expanded {
            margin-right: 0 !important;
        }

        /* --- Select2 RTL & Z-Index Fix --- */
        .select2-container {
            z-index: 9999 !important; /* Force dropdown above fixed elements */
        }
        .select2-dropdown {
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1) !important;
        }

        /* --- Internal Styling --- */
        .sidebar-header { padding: 30px 20px; text-align: center; }
        .logo-icon {
            /* background: var(--primary-color); */
            color: white;
            width: 190px; height: 120px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 12px; margin: 0 auto 10px;
            font-size: 1.5rem;
            box-shadow: 0 5px 15px -3px rgba(79, 70, 229, 0.4);
            border-radius:30px;
        }

        .nav-link {
            color: var(--text-muted);
            padding: 12px 20px;
            margin: 4px 15px;
            border-radius: 10px;
            font-weight: 500;
            display: flex; align-items: center;
            transition: 0.3s;
            text-decoration: none;
        }

        .nav-link i { font-size: 1.2rem; margin-left: 12px; width: 25px; text-align: center; }
        .nav-link:hover { color: var(--primary-color); background-color: #f1f5f9; }
        .nav-link.active { background-color: #eef2ff; color: var(--primary-color); position: relative; }

        /* --- Top Navbar Blur Fix --- */
        .main-navbar {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(8px);
            border-radius: 16px;
            border: 1px solid #ffffff;
            z-index: 1020;
        }

        .footer-love { padding: 30px; color: var(--text-muted); font-size: 13px; text-align: center; margin-top: auto; }
        .heart { color: #f43f5e; }

        /* --- Mobile Responsiveness Fix --- */
        @media (max-width: 768px) {
            .sidebar-wrapper {
                transform: translateX(100%);
                display: block !important; /* Ensure visibility for JS toggle */
            }
            .main-content { margin-right: 0; }
            .sidebar-wrapper.mobile-show { transform: translateX(0); }
        }
        /* for admin icon list logout*/
        /* 1. The Container */
        .premium-hover-dropdown {
            position: relative;
            display: inline-block;
            padding-bottom: 15px; /* Creates a small buffer area */
            margin-bottom: -15px; /* Offsets padding so it doesn't push layout */
        }

        /* 2. The Menu */
        .premium-hover-dropdown .dropdown-menu {
            display: block !important;
            visibility: hidden;
            opacity: 0;
            position: absolute;
            top: 100%; /* Sits exactly at the bottom of the container */
            right: auto !important;
            left: 0 !important;
            
            min-width: 220px;
            background: #ffffff;
            border-radius: 16px !important;
            padding: 8px; /* Padding inside the menu */
            box-shadow: 0 20px 40px rgba(0,0,0,0.12) !important;
            
            /* Animation - slowed down slightly for "smoothness" */
            transform-origin: top right;
            transform: translateY(10px) scale(0.98);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            pointer-events: none;
            z-index: 9999;
        }

        /* 3. THE MAGIC BRIDGE: The Invisible Connector */
        .premium-hover-dropdown .dropdown-menu::before {
            content: "";
            position: absolute;
            top: -20px; /* Reaches up to touch the avatar */
            left: 0;
            right: 0;
            height: 25px; /* The height of the "gap" */
            background: transparent; /* Invisible to the user */
            display: block;
        }

        /* 4. Hover State */
        .premium-hover-dropdown:hover .dropdown-menu {
            visibility: visible;
            opacity: 1;
            transform: translateY(0px) scale(1);
            pointer-events: auto;
            /* Added a tiny delay on exit so it doesn't flicker */
            transition-delay: 0s; 
        }

        /* 5. Exit Transition (The "Smooth Fade Out") */
        .premium-hover-dropdown .dropdown-menu {
            /* This adds a 100ms grace period before the menu starts disappearing */
            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s;
            transition-delay: 0.1s; 
        }

        /* Refined Item Styles */
        .premium-hover-dropdown .dropdown-item {
            padding: 10px 15px;
            border-radius: 10px;
            margin-bottom: 2px;
            transition: all 0.2s ease;
            text-align: right;
        }

        .premium-hover-dropdown .dropdown-item:hover {
            background-color: #f8f9fa;
            transform: translateX(-5px); /* Smooth slide to the left for RTL */
        }

        

    /* Status Dot Animation */
    /* The Status Dot Container */
    .status-container {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    /* The Dot itself */
    .status-dot {
        position: relative;
        width: 8px;
        height: 8px;
        background-color: #10b981; /* Emerald Green */
        border-radius: 50%;
        display: inline-block;
    }

    /* The Animated Pulse Ring */
    .status-dot::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #10b981;
        border-radius: 50%;
        z-index: -1;
        animation: status-pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes status-pulse {
        0% {
            transform: scale(1);
            opacity: 0.8;
        }
        70% {
            transform: scale(3);
            opacity: 0;
        }
        100% {
            transform: scale(1);
            opacity: 0;
        }
    }
    /* --- Add Button --- */
    .logo-icon {
        background: white;
        color: var(--soft-primary);
        border: 2px solid #eef2ff;
        padding: 12px 24px;
        border-radius: 16px;
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .logo-icon:hover {
        background: var(--soft-primary);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(129, 140, 248, 0.2);
    }

    </style>
</head>
<body>

    <div class="sidebar-wrapper d-md-block" id="sidebar">
        <div class="d-flex flex-column h-100">
            <div class="sidebar-header">
                <!-- <div class="logo-icon"><i class="fas fa-chart-pie"></i></div> -->
                <div class="col-4 logo-icon btn-add-customer">
                    <img width="100%" src="{{ asset('images/logo.png') }}" alt="Company Logo">
                </div>
                <h5 class="fw-bold text-dark m-0">لوحة الإدارة</h5>
            </div>
            
            <hr class="mx-3 opacity-10">
            
            <ul class="nav nav-pills flex-column mb-auto w-100 px-0">
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
            
            <div class="p-3 mt-auto">
                <div class="rounded-3 p-1 bg-light text-center border">
                    <small class="text-muted d-block mb-1 text-truncate">
                        Developed with <span class="heart">&hearts;</span> by <strong>DEV7MOD</strong>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content" id="content">
        <nav class="navbar navbar-expand navbar-light main-navbar shadow-sm d-none d-md-flex px-4 py-2 bg-white">
            <button class="btn btn-light border-0 shadow-none rounded-circle me-3" id="sidebarToggle">
                <i class="fas fa-indent text-muted"></i>
            </button>
            
            <div class="ms-auto d-flex align-items-center">
                <div class="text-end me-3">
                    <p class="m-0 small fw-bold text-dark" style="letter-spacing: -0.3px;">
                        {{ Auth::user()->name ?? 'مدير النظام' }}
                    </p>
                    <p class="m-0 text-success fw-medium d-flex align-items-center justify-content-end" style="font-size: 11px;">
                        متصل الآن <span class="status-dot ms-2"></span>
                    </p>
                </div>

                <div class="dropdown premium-hover-dropdown">
                    <div class="avatar-trigger" style="cursor: pointer;">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'Admin') }}&background=4f46e5&color=fff" 
                            class="rounded-circle shadow-sm border border-2 border-white" width="42" height="42">
                    </div>
                    
                    <ul class="dropdown-menu shadow-lg border-0">
                        <div class="menu-header px-3 py-3 text-center bg-light mb-2">
                            <p class="m-0 text-muted" style="font-size: 10px; text-transform: uppercase; letter-spacing: 1px;">مرحباً بك</p>
                            <p class="m-0 fw-bold text-dark">{{ Auth::user()->name ?? 'المدير' }}</p>
                            <!-- <a href="{{ route('dashboard.backup') }}" class="btn btn-warning rounded-pill px-4 shadow-sm fw-bold">
                                <i class="fas fa-database me-2"></i> تحميل نسخة SQL
                            </a> -->
                        </div>
                        
                        <li>
                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="#">
                                <i class="fas fa-user-edit text-muted"></i>
                                <span>تعديل الملف</span>
                            </a>
                        </li>
                        
                        <li><hr class="dropdown-divider opacity-50 my-1 mx-2"></li>
                        
                        <li>
                            <a class="dropdown-item text-danger d-flex align-items-center justify-content-between" 
                            href="{{ route('logout') }}" 
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-power-off"></i>
                                <span>تسجيل الخروج</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </nav>

        <nav class="navbar navbar-light bg-white shadow-sm mb-4 d-md-none rounded-4 px-3">
            <span class="navbar-brand fw-bold text-primary">إدارة النظام</span>
            <button class="btn btn-primary btn-sm" id="mobileToggle">
                <i class="fas fa-bars"></i>
            </button>
        </nav>

        <div class="container-fluid px-0">
            @yield('content')
        </div>
        
        <!-- <footer class="footer-love">
            صنع بكل <span class="heart">&hearts;</span> بواسطة <strong>DEV7MOD</strong> &copy; 2026
        </footer> -->
    </div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Desktop Toggle Logic
        $('#sidebarToggle').on('click', function() {
            $('#sidebar').toggleClass('collapsed');
            $('#content').toggleClass('expanded');
            
            // Sync icons
            if($('#sidebar').hasClass('collapsed')) {
                $(this).find('i').removeClass('fa-indent').addClass('fa-outdent');
            } else {
                $(this).find('i').removeClass('fa-outdent').addClass('fa-indent');
            }
        });

        // Mobile Toggle Logic
        $('#mobileToggle').on('click', function() {
            $('#sidebar').toggleClass('mobile-show');
        });

        // Close mobile menu when clicking outside
        $(document).click(function(event) {
            if (!$(event.target).closest('#sidebar, #mobileToggle').length) {
                $('#sidebar').removeClass('mobile-show');
            }
        });
    });
</script>
<script>
    // Detect if the page is being loaded from the "Back/Forward" Cache
    window.addEventListener("pageshow", function (event) {
        if (event.persisted || (typeof window.performance != "undefined" && window.performance.navigation.type === 2)) {
            // Force a reload from the server
            window.location.reload();
        }
    });
</script>
@stack('scripts')
</body>
</html>