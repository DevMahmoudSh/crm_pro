@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
    .select2-container { z-index: 999999 !important; }
    .select2-search__field { direction: rtl; text-align: right; }
    .select2-results__option { direction: rtl; text-align: right; }
    
    .nav-tabs .nav-link { color: #6c757d; font-weight: 500; border: none; }
    .nav-tabs .nav-link.active { color: #198754; border-bottom: 3px solid #198754; background: none; }
    .table-responsive { min-height: 400px; }
</style>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">سجل المدفوعات والأرصدة</h3>
            <span class="text-muted small">عرض تفصيلي لجميع الدفعات وحالات الحسابات</span>
        </div>
        <button class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
            <i class="fas fa-hand-holding-usd me-2"></i> تسجيل دفعة جديدة
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <ul class="nav nav-tabs mb-4 border-0" id="paymentTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-content" type="button">
                <i class="fas fa-history me-2"></i> سجل المدفوعات التفصيلي
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary-content" type="button">
                <i class="fas fa-list-ul me-2"></i> ملخص أرصدة العملاء
            </button>
        </li>
    </ul>

    <div class="tab-content" id="paymentTabsContent">
        <div class="tab-pane fade show active" id="history-content">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="historyTable" class="table table-hover align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>العميل</th>
                                    <th>المبلغ</th>
                                    <th>الطريقة</th>
                                    <th>ملاحظات</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payment->id }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $payment->client->name ?? 'عميل غير معروف' }}</div>
                                        <small class="text-muted">{{ $payment->client->phone ?? '' }}</small>
                                    </td>
                                    <td class="text-success fw-bold">{{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        @if($payment->method == 'wallet')
                                            <span class="badge bg-primary">محفظة</span>
                                        @elseif($payment->method == 'cash')
                                            <span class="badge bg-info text-dark">كاش</span>
                                        @else
                                            <span class="badge bg-dark">تطبيق</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ $payment->notes ?? '-' }}</td>
                                    <td dir="ltr" class="small">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="summary-content">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="paymentsTable" class="table table-hover align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>العميل</th>
                                    <th>إجمالي الطلبات</th>
                                    <th>إجمالي المدفوعات</th>
                                    <th>الرصيد الحالي</th>
                                    <th>آخر معاملة</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clients as $client)
                                @php
                                    // الحسابات
                                    $total_orders = $client->orders_sum_total_amount ?? 0;
                                    $total_payments = $client->payments_sum_amount ?? 0;
                                    $balance = $total_payments - $total_orders; // (الموجب يعني له رصيد، السالب يعني عليه دين)

                                    // تحديد تاريخ آخر معاملة (الأحدث بين آخر طلب وآخر دفعة)
                                    $last_order_date = $client->latestOrder ? $client->latestOrder->created_at : null;
                                    $last_payment_date = $client->latestPayment ? $client->latestPayment->created_at : null;
                                    
                                    $last_transaction = null;
                                    if ($last_order_date && $last_payment_date) {
                                        $last_transaction = $last_order_date > $last_payment_date ? $last_order_date : $last_payment_date;
                                    } elseif ($last_order_date) {
                                        $last_transaction = $last_order_date;
                                    } elseif ($last_payment_date) {
                                        $last_transaction = $last_payment_date;
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 me-2 text-dark">
                                                <i class="fas fa-user-tie"></i>
                                            </div>
                                            <span class="fw-bold">{{ $client->name }}</span>
                                            <small class="text-muted ms-1">({{ $client->phone }})</small>
                                        </div>
                                    </td>
                                    <td class="text-danger fw-bold">{{ number_format($total_orders, 2) }}</td>
                                    <td class="text-success fw-bold">{{ number_format($total_payments, 2) }}</td>
                                    
                                    <td>
                                        @if($balance < 0)
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger p-2">
                                                عليه: {{ number_format(abs($balance), 2) }}
                                            </span>
                                        @elseif($balance > 0)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success p-2">
                                                له: {{ number_format($balance, 2) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary p-2">0.00</span>
                                        @endif
                                    </td>

                                    <td dir="ltr" class="text-secondary small">
                                        {{ $last_transaction ? $last_transaction->format('Y-m-d|h:i') : '--' }}
                                    </td>
                                    
                                    <td>
                                        <a href="{{ route('clients.statement', $client->id) }}" 
                                        class="btn btn-sm btn-outline-secondary" 
                                        title="كشف حساب" 
                                        target="_blank"> <i class="fas fa-file-invoice"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addPaymentModal" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('payments.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">تسجيل دفعة جديدة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">العميل</label>
                        <select name="client_id" id="payment_client_select" class="form-select" required></select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">المبلغ</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required placeholder="0.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">طريقة الدفع</label>
                            <select name="method" class="form-select" required>
                                <option value="cash">كاش</option>
                                <option value="wallet">محفظة</option>
                                <option value="app">تطبيق</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">حفظ الدفعة</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {
        $('#historyTable').DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" },
            order: [[ 0, "desc" ]]
        });

        $('#balancesTable').DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" }
        });

        $('#payment_client_select').select2({
            dropdownParent: $('#addPaymentModal .modal-content'),
            theme: "bootstrap-5",
            dir: "rtl",
            width: '100%',
            placeholder: "ابحث عن العميل...",
            ajax: {
                url: "{{ route('clients.select2') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) { return { search: params.term, page: params.page || 1 }; },
                processResults: function (data, params) {
                    return { results: data.results, pagination: { more: data.pagination.more } };
                }
            }
        });
    });
</script>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        
        // 1. تفعيل الجدول
        if (!$.fn.DataTable.isDataTable('#paymentsTable')) {
            $('#paymentsTable').DataTable({
                language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" },
                order: [[ 4, "desc" ]] // ترتيب حسب آخر معاملة
            });
        }

        // 2. إعداد Select2 مع AJAX (نفس كود الطلبات تماماً)
        $('#payment_client_select').select2({
            dropdownParent: $('#addPaymentModal .modal-content'),
            theme: "bootstrap-5",
            dir: "rtl",
            width: '100%',
            placeholder: "ابحث عن العميل...",
            allowClear: true,
            ajax: {
                url: "{{ route('clients.select2') }}", // نستخدم نفس الرابط السابق
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: { more: data.pagination.more }
                    };
                },
                cache: true
            }
        });

        // إصلاح التركيز
        $.fn.modal.Constructor.prototype._enforceFocus = function() {};
    });
</script>

@endsection