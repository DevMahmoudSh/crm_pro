@extends('layouts.app')

@section('content')
@php
    // --- Data Processing Layer ---
    $totalUnPaidbalance = 0;
    $clientsPhones = array();
    
    foreach($clients as $client) {
        $total_orders = $client->orders_sum_total_amount ?? 0;
        $total_payments = $client->payments_sum_amount ?? 0;
        $balance = $total_payments - $total_orders;
        
        if($balance < 0) {
            $totalUnPaidbalance += $balance;
            $clientsPhones[] = $client->phone;
        }
    }
    $totalVolume = $totalPaidbalance + abs($totalUnPaidbalance);
    $collectionRate = $totalVolume > 0 ? ($totalPaidbalance / $totalVolume) * 100 : 0;
@endphp
@php
    // 1. الرصيد المقبوض (المدفوع)
    $paid = $totalPaidbalance; 

    // 2. الرصيد غير المقبوض (الديون) - نستخدم abs لتحويل الرقم لسالب إن وجد
    $unpaid = abs($totalUnPaidbalance);
    // 3. إجمالي القيمة (المقبوض + الديون)
    $totalVolume = $paid + $unpaid;

    // 4. حساب النسبة المئوية
    // نتحقق أولاً أن الإجمالي ليس صفراً لتجنب خطأ القسمة على صفر
    $collectionRate = ($totalVolume > 0) ? ($paid / $totalVolume) * 100 : 0;
@endphp

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

<style>
    :root {
        --smooth-bg: #fdfeff;
        --card-bg: #ffffff;
        --accent-blue: #4f46e5;
        --text-main: #1e293b;
        --text-muted: #64748b;
        --soft-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }

    body { font-family: 'Cairo', sans-serif; background-color: var(--smooth-bg); color: var(--text-main); }

    .soft-card {
        background: var(--card-bg);
        border: 1px solid rgba(0,0,0,0.04);
        border-radius: 20px;
        box-shadow: var(--soft-shadow);
        transition: all 0.3s ease;
    }

    .stat-value { font-size: 1.75rem; font-weight: 900; }
    .blur-mode { filter: blur(6px); opacity: 0.4; transition: 0.3s; }

    /* --- FIXED TOAST CSS --- */
    #toast-container {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%) translateY(100px); /* Start hidden below */
        visibility: hidden;
        opacity: 0;
        z-index: 9999;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        min-width: 250px;
    }

    #toast-container.show {
        visibility: visible;
        opacity: 1;
        transform: translateX(-50%) translateY(0); /* Slide up into view */
    }

    .btn-smooth-dark {
        background: #1e293b; color: white; border-radius: 12px;
        padding: 10px 24px; font-weight: 600; border: none;
    }
</style>
<style>
    /* تحسينات إضافية للكروت */
    .soft-card {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 160px;
    }

    /* حاوية الأيقونة */
    .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 15px;
    }

    /* ألوان مخصصة لكل نوع */
    .bg-light-success { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
    .bg-light-danger { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .bg-light-warning { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .bg-light-info { background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .bg-light-purple { background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
    .bg-light-dark { background-color: rgba(30, 41, 59, 0.1); color: #1e293b; }

    .stat-label {
        font-size: 0.85rem;
        letter-spacing: 0.3px;
    }

    .stat-value {
        font-family: 'JetBrains Mono', sans-serif; /* اختياري للأرقام */
        letter-spacing: -1px;
    }
</style>
<style>
    /* تحسين العنوان */
    .dashboard-title {
        font-family: 'Cairo', sans-serif;
        font-weight: 900;
        background: linear-gradient(45deg, #1e293b, #4f46e5);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        letter-spacing: -1px;
    }

    /* زر "إخفاء البيانات" - تصميم زجاجي */
    .btn-glass {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 0, 0, 0.05);
        color: #64748b;
        transition: all 0.3s ease;
        font-weight: 600;
    }

    .btn-glass:hover {
        background: #fff;
        color: #1e293b;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }

    /* زر "طلب جديد" - تصميم نيون ناعم */
    .btn-action-primary {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        color: white;
        border: none;
        padding: 12px 28px;
        border-radius: 50px;
        font-weight: 700;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
    }

    .btn-action-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
        filter: brightness(1.1);
    }

    /* زخرفة خفيفة خلف العنوان */
    .header-decorator {
        width: 4px;
        height: 40px;
        background: #4f46e5;
        border-radius: 10px;
        margin-left: 15px;
    }
    .privacy-target {
        transition: filter 0.3s ease, opacity 0.3s ease;
    }

    .blur-mode {
        filter: blur(10px);
        opacity: 0.3;
        pointer-events: none; /* User can't select/copy text while blurred */
    }
</style>
    
    {{-- Header --}}
    <div class="row align-items-center mb-5 mt-4">
        <div class="col-md-7 d-flex align-items-center">
            <div class="header-decorator d-none d-md-block"></div>
            <div>
                <h1 class="dashboard-title mb-0">نظرة عامة</h1>
                <p class="text-muted mb-0 small fw-bold">
                    <i class="far fa-calendar-alt me-1"></i> ملخص مبيعاتك ومديونياتك ليوم {{ date('Y/m/d') }}
                </p>
            </div>
        </div>

        <div class="col-md-5 d-flex justify-content-md-end gap-3 mt-3 mt-md-0">
            <button class="btn btn-glass rounded-pill px-4 shadow-sm d-flex align-items-center" onclick="togglePrivacy()">
                <div class="me-2" style="width: 30px; height: 30px; background: rgba(0,0,0,0.03); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-shield-alt text-primary" style="font-size: 14px;"></i>
                </div>
                <span id="privacy-text" style="font-size: 0.9rem;">إخفاء الأرقام</span>
            </button>

            <button class="btn btn-action-primary d-flex align-items-center">
                <i class="fas fa-plus-circle me-2"></i>
                <span>إنشاء طلب جديد</span>
            </button>
            <!-- <a href="{{ route('dashboard.backup') }}" class="btn btn-warning rounded-pill px-4 shadow-sm fw-bold">
                <i class="fas fa-database me-2"></i> تحميل نسخة SQL
            </a> -->
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row g-4 mb-5">
        <div class="row g-4">
    <div class="col-md-4">
        <div class="soft-card p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted stat-label fw-bold d-block mb-1">المقبوضات</span>
                    <div class="stat-value privacy-target text-dark mt-2" data-val="{{ $totalPaidbalance }}">{{ number_format($totalPaidbalance, 2) }}</div>
                </div>
                <div class="icon-box bg-light-success">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
            <div class="mt-auto">
                <div class="d-flex justify-content-between mb-1 small">
                    <span class="text-muted">نسبة التحصيل</span>
                    <span class="fw-bold text-success">{{ round($collectionRate) }}%</span>
                </div>
                <div class="progress" style="height: 6px; background-color: rgba(16, 185, 129, 0.1);">
                    <div class="progress-bar bg-success" style="width: {{ $collectionRate }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="soft-card p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted stat-label fw-bold d-block mb-1">الديون النشطة</span>
                    <div class="stat-value privacy-target text-danger mt-2" data-val="{{ abs($totalUnPaidbalance) }}">{{ number_format(abs($totalUnPaidbalance), 2) }}</div>
                </div>
                <div class="icon-box bg-light-danger">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
            </div>
            <p class="text-muted small mt-3 mb-0"><i class="fas fa-info-circle me-1"></i> مبالغ لم يتم تسويتها بعد</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="soft-card p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted stat-label fw-bold d-block mb-1">طلبات في الانتظار</span>
                    <div class="stat-value text-warning mt-2" data-val="{{ $pendingOrdersCount }}">{{ $pendingOrdersCount }}</div>
                </div>
                <div class="icon-box bg-light-warning">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="mt-3 small">
                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3">تحتاج معالجة</span>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="soft-card p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted stat-label fw-bold d-block mb-1">طلبات جاهزة</span>
                    <div class="stat-value text-info mt-2" data-val="{{ $readyOrdersCount }}">{{ $readyOrdersCount }}</div>
                </div>
                <div class="icon-box bg-light-info">
                    <i class="fas fa-box-open"></i>
                </div>
            </div>
            <div class="mt-3 small text-info fw-bold">
                <i class="fas fa-truck me-1"></i> جاهزة للاستلام
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="soft-card p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted stat-label fw-bold d-block mb-1">إجمالي الطلبات</span>
                    <div class="stat-value text-purple mt-2" data-val="{{ $allOrdersCount }}" style="color: #8b5cf6;">{{ $allOrdersCount }}</div>
                </div>
                <div class="icon-box bg-light-purple">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
            <p class="text-muted small mt-3 mb-0">حجم المبيعات الكلي</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="soft-card p-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <span class="text-muted stat-label fw-bold d-block mb-1">قاعدة الزبائن</span>
                    <div class="stat-value text-dark mt-2" data-val="{{ $clientsCount }}">{{ $clientsCount }}</div>
                </div>
                <div class="icon-box bg-light-dark">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="mt-3 small">
                <a href="{{ route('clients.index') }}" class="text-decoration-none fw-bold">إدارة الزبائن <i class="fas fa-arrow-left ms-1" style="font-size: 10px;"></i></a>
            </div>
        </div>
    </div>
    </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="soft-card p-4">
                    <h5 class="fw-bold mb-4">العمليات الأخيرة</h5>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="bg-light">
                                <tr><th>العميل</th><th>المبلغ</th><th>الحالة</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>عينة عميل</td>
                                    <td class="fw-bold">500.00</td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">ناجح</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="soft-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-bold mb-0">المتأخرين في السداد</h6>
                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="copyPhones()">
                            <i class="fas fa-copy me-1"></i> نسخ الكل
                        </button>
                    </div>
                    <div class="phone-list" style="max-height: 300px; overflow-y: auto;">
                        <code id="phoneText" style="display:none;">{{ implode(", ", $clientsPhones) }}</code>
                        @foreach($clientsPhones as $phone)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <span class="small fw-bold">{{ $phone }}</span>
                            <a href="https://wa.me/{{ $phone }}" class="text-success"><i class="fab fa-whatsapp"></i></a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

{{-- Modern Toast Notification --}}
<div id="toast-container" class="bg-white border text-dark px-4 py-3 rounded-4 shadow-lg d-flex align-items-center gap-3">
    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width:24px; height:24px;">
        <i class="fas fa-check text-white" style="font-size: 12px;"></i>
    </div>
    <span class="fw-bold">تم نسخ جهات الاتصال بنجاح!</span>
</div>

<script>
    function togglePrivacy() {
        // Target ONLY the money values
        const sensitiveData = document.querySelectorAll('.privacy-target');
        const btnText = document.getElementById('privacy-text');
        const icon = document.querySelector('#privacy-btn i');
        
        const isHidden = sensitiveData[0].classList.contains('blur-mode');
        
        sensitiveData.forEach(el => {
            isHidden ? el.classList.remove('blur-mode') : el.classList.add('blur-mode');
        });

        // Update Button UI
        if (isHidden) {
            btnText.innerText = "إخفاء المبالغ";
            icon.className = "fas fa-eye me-2";
        } else {
            btnText.innerText = "إظهار المبالغ";
            icon.className = "fas fa-eye-slash me-2";
        }
    }

    // Auto-hide on load
    document.addEventListener('DOMContentLoaded', () => {
        togglePrivacy(); 
    });

    function copyPhones() {
        const text = document.getElementById('phoneText').innerText;
        
        // Use standard clipboard API
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => {
                showToast();
            });
        } else {
            // Fallback for non-https/older browsers
            let textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showToast();
        }
    }

    function showToast() {
        const toast = document.getElementById('toast-container');
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
</script>
@endsection