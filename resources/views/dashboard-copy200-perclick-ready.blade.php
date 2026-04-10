@extends('layouts.app')

@section('content')
@php
    // --- Data Processing Layer ---
    $totalUnPaidbalance = 0;
    $totalPaidbalance = 0; 
    $clientsPhones = array();
    
    foreach($clients as $client) {
        $total_orders = $client->orders_sum_total_amount ?? 0;
        $total_payments = $client->payments_sum_amount ?? 0;
        
        if($client->phone != "0590000000" && $client->phone != "0595590721") {
            // تنظيف الرقم من أي مسافات زائدة
            $clientsPhones[] = trim($client->phone);
        }
    }

    // تقسيم الأرقام إلى مجموعات (200 لكل دفعة)
    $phoneChunks = array_chunk($clientsPhones, 200);

    // حسابات الإحصائيات
    $paid = $totalPaidbalance; 
    $unpaid = abs($totalUnPaidbalance);
    $totalVolume = $paid + $unpaid;
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
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 160px;
    }

    .icon-box {
        width: 48px; height: 48px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; margin-bottom: 15px;
    }

    .bg-light-success { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
    .bg-light-danger { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .bg-light-warning { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .bg-light-info { background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .bg-light-purple { background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
    .bg-light-dark { background-color: rgba(30, 41, 59, 0.1); color: #1e293b; }

    .stat-value { font-size: 1.75rem; font-weight: 900; letter-spacing: -1px; }
    .stat-label { font-size: 0.85rem; font-weight: 700; }
    
    .blur-mode { filter: blur(8px); opacity: 0.3; transition: 0.3s; pointer-events: none; }

    #toast-container {
        position: fixed; bottom: 30px; left: 50%;
        transform: translateX(-50%) translateY(100px);
        visibility: hidden; opacity: 0; z-index: 9999;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        min-width: 280px;
    }
    #toast-container.show {
        visibility: visible; opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    .btn-glass {
        background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 0, 0, 0.05); color: #64748b;
        transition: all 0.3s ease; font-weight: 600;
    }

    .btn-action-primary {
        background: linear-gradient(135deg, #4f46e5, #6366f1); color: white; border: none;
        padding: 12px 28px; border-radius: 50px; font-weight: 700; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
    }

    .dashboard-title {
        font-weight: 900; background: linear-gradient(45deg, #1e293b, #4f46e5);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }
</style>
    
    {{-- Header --}}
    <div class="row align-items-center mb-5 mt-4">
        <div class="col-md-7 d-flex align-items-center">
            <div style="width: 4px; height: 40px; background: #4f46e5; border-radius: 10px; margin-left: 15px;"></div>
            <div>
                <h1 class="dashboard-title mb-0">نظرة عامة</h1>
                <p class="text-muted mb-0 small fw-bold">إحصائيات المبيعات والديون</p>
            </div>
        </div>

        <div class="col-md-5 d-flex justify-content-md-end gap-3 mt-3 mt-md-0">
            <button class="btn btn-glass rounded-pill px-4 shadow-sm d-flex align-items-center" onclick="togglePrivacy()">
                <i id="privacy-icon" class="fas fa-eye me-2"></i>
                <span id="privacy-text" style="font-size: 0.9rem;">إخفاء المبالغ</span>
            </button>
            <button class="btn btn-action-primary d-flex align-items-center">
                <i class="fas fa-plus-circle me-2"></i><span>طلب جديد</span>
            </button>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="soft-card p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="text-muted stat-label d-block mb-1">المقبوضات</span>
                        <div class="stat-value privacy-target text-dark mt-2">{{ number_format($totalPaidbalance, 2) }}</div>
                    </div>
                    <div class="icon-box bg-light-success"><i class="fas fa-hand-holding-usd"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="soft-card p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="text-muted stat-label d-block mb-1">الديون النشطة</span>
                        <div class="stat-value privacy-target text-danger mt-2">{{ number_format(abs($totalUnPaidbalance), 2) }}</div>
                    </div>
                    <div class="icon-box bg-light-danger"><i class="fas fa-exclamation-circle"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="soft-card p-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="text-muted stat-label d-block mb-1">قاعدة الزبائن</span>
                        <div class="stat-value text-dark mt-2">{{ $clientsCount ?? 0 }}</div>
                    </div>
                    <div class="icon-box bg-light-dark"><i class="fas fa-users"></i></div>
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
                        <thead class="bg-light"><tr><th>العميل</th><th>المبلغ</th><th>الحالة</th></tr></thead>
                        <tbody><tr><td>عينة عميل</td><td class="fw-bold">500.00</td><td><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">ناجح</span></td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- قسم نسخ الأرقام - التنسيق المطلوب --}}
        <div class="col-lg-4">
            <div class="soft-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0">نسخ الأرقام (دفعات 200)</h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">{{ count($clientsPhones) }}</span>
                </div>
                
                <div class="d-flex flex-wrap gap-2 mb-3">
                    @if(count($phoneChunks) > 0)
                        @foreach($phoneChunks as $index => $chunk)
                            {{-- لاحظ التغيير هنا: implode(', ', $chunk) --}}
                            <textarea id="chunk-text-{{ $index }}" style="display:none;">{{ implode(', ', $chunk) }}</textarea>
                            
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 py-2 fw-bold" onclick="copySpecificChunk({{ $index }})">
                                <i class="fas fa-copy me-1"></i> دفعة {{ $index + 1 }}
                            </button>
                        @endforeach
                    @else
                        <p class="text-muted small text-center w-100">لا توجد أرقام</p>
                    @endif
                </div>

                <div class="phone-list mt-2" style="max-height: 200px; overflow-y: auto; border-top: 1px solid #eee; padding-top: 10px;">
                    @foreach($clientsPhones as $phone)
                    <div class="d-flex justify-content-between py-1">
                        <span class="small text-muted">{{ $phone }}</span>
                        <a href="https://wa.me/{{ $phone }}" target="_blank" class="text-success small"><i class="fab fa-whatsapp"></i></a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div id="toast-container" class="bg-white border text-dark px-4 py-3 rounded-4 shadow-lg d-flex align-items-center gap-3">
        <div class="bg-success rounded-circle d-flex align-items-center justify-content-center" style="width:24px; height:24px;">
            <i class="fas fa-check text-white" style="font-size: 12px;"></i>
        </div>
        <span class="fw-bold" id="toast-message">تم النسخ!</span>
    </div>

<script>
    function togglePrivacy() {
        const targets = document.querySelectorAll('.privacy-target');
        const isHidden = targets[0].classList.contains('blur-mode');
        targets.forEach(el => isHidden ? el.classList.remove('blur-mode') : el.classList.add('blur-mode'));
        document.getElementById('privacy-text').innerText = isHidden ? "إخفاء المبالغ" : "إظهار المبالغ";
        document.getElementById('privacy-icon').className = isHidden ? "fas fa-eye me-2" : "fas fa-eye-slash me-2";
    }

    function copySpecificChunk(index) {
        const text = document.getElementById('chunk-text-' + index).value;
        const msg = "تم نسخ الدفعة " + (index + 1) + " (فاصلة)";

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(() => showToast(msg));
        } else {
            let textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showToast(msg);
        }
    }

    function showToast(message) {
        const toast = document.getElementById('toast-container');
        document.getElementById('toast-message').innerText = message;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }
</script>
@endsection