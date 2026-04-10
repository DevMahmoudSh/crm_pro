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
    .btn-action { width: 32px; height: 32px; padding: 0; line-height: 32px; border-radius: 8px; }
    .bg-raspberry {
        background-color: #c5006e;
        color: #ffffff;
    }
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

    <!-- @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif -->
    @if(session('success') || session('error'))
        <div style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            ...
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
                        <table id="historyTable" class="table table-hover align-middle w-100 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">#</th>
                                    <th class="text-center">العميل</th>
                                    <th class="text-center">المبلغ</th>
                                    <th class="text-center">الطريقة</th>
                                    <th class="text-center">ملاحظات</th>
                                    <th class="text-center">التاريخ</th>
                                    <th class="text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payment->id }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $payment->client->name ?? 'عميل غير معروف' }}</div>
                                        <!-- <small class="text-muted">{{ $payment->client->phone ?? '' }}</small> -->
                                        <small class="text-muted ms-1">({{ $payment->client->phone }})</small>
                                    </td>
                                    <td class="text-success fw-bold">{{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        @php
                                            $methodClass = ['wallet' => 'bg-primary', 'cash' => 'bg-info text-dark', 'app' => 'bg-raspberry text-white'][$payment->method] ?? 'bg-secondary';
                                            $methodName = ['wallet' => 'محفظة', 'cash' => 'كاش', 'app' => 'تطبيق'][$payment->method] ?? $payment->method;
                                        @endphp
                                        <span class="badge {{ $methodClass }}" style="font-size: 14px">{{ $methodName }}</span>
                                    </td>
                                    <td class="small text-muted">{{ $payment->notes ?? '-' }}</td>
                                    <td dir="ltr" class="small">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-outline-primary btn-action edit-payment-btn" 
                                                data-id="{{ $payment->id }}"
                                                data-amount="{{ $payment->amount }}"
                                                data-method="{{ $payment->method }}"
                                                data-notes="{{ $payment->notes }}"
                                                data-client-name="{{ $payment->client->name ?? 'غير معروف' }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <!-- <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-action" onclick="return confirm('هل أنت متأكد من حذف هذه الدفعة؟')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form> -->
                                            <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf 
                                                @method('DELETE')
                                                <button type="button" class="btn btn-outline-danger btn-action confirm-delete-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
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
                        <table id="paymentsTable" class="table table-hover align-middle w-100 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>العميل</th>
                                    <th>إجمالي الطلبات</th>
                                    <th>إجمالي المدفوعات</th>
                                    <th>الرصيد الحالي</th>
                                    <th>آخر معاملة</th>
                                    <th class=""col-td-1>كشف حساب</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clients as $client)
                                @php
                                    $total_orders = $client->orders_sum_total_amount ?? 0;
                                    $total_payments = $client->payments_sum_amount ?? 0;
                                    $balance = $total_payments - $total_orders;
                                    $last_order_date = $client->latestOrder ? $client->latestOrder->created_at : null;
                                    $last_payment_date = $client->latestPayment ? $client->latestPayment->created_at : null;
                                    $last_transaction = ($last_order_date > $last_payment_date) ? $last_order_date : ($last_payment_date ?? $last_order_date);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 me-2 text-dark"><i class="fas fa-user-tie"></i></div>
                                            <span class="fw-bold">{{ $client->name }}</span>
                                            <small class="text-muted ms-1">({{ $client->phone }})</small>
                                        </div>
                                    </td>
                                    <td class="text-danger fw-bold">{{ number_format($total_orders, 2) }}</td>
                                    <td class="text-success fw-bold">{{ number_format($total_payments, 2) }}</td>
                                    <td>
                                        @if($balance < 0)
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger p-2">عليه: {{ number_format(abs($balance), 2) }}</span>
                                        @elseif($balance > 0)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success p-2">له: {{ number_format($balance, 2) }}</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary p-2">0.00</span>
                                        @endif
                                    </td>
                                    <!-- <td dir="ltr" class="text-secondary small">{{ $last_transaction ? $last_transaction->format('Y-m-d|h:i') : '--' }}</td> -->
                                    <td dir="ltr" class="text-secondary small">{{ $last_transaction ? $last_transaction->format('Y:m:d:h:i') : '--' }}</td>
                                    <!-- <td>
                                        <a href="{{ route('clients.statement', $client->id) }}" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="fas fa-file-invoice"></i></a>
                                    </td> -->
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
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold">تسجيل دفعة جديدة</h5>
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
                        <textarea name="notes" class="form-control" rows="2" placeholder="رقم التحويل أو أي ملاحظة..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">حفظ الدفعة</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editPaymentModal" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editPaymentForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">تعديل الدفعة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">العميل</label>
                        <input type="text" id="edit_client_name" class="form-control bg-light" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">المبلغ</label>
                            <input type="number" step="0.01" name="amount" id="edit_amount" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">طريقة الدفع</label>
                            <select name="method" id="edit_method" class="form-select" required>
                                <option value="cash">كاش</option>
                                <option value="wallet">محفظة</option>
                                <option value="app">تطبيق</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">ملاحظات</label>
                        <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">تحديث البيانات</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        // 1. DataTables Setup
        $('#historyTable').DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" },
            order: [[ 0, "desc" ]]
        });

        if (!$.fn.DataTable.isDataTable('#paymentsTable')) {
            $('#paymentsTable').DataTable({
                language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" },
                order: [[ 4, "desc" ]]
            });
        }

        // 2. Edit Modal Logic
        $('.edit-payment-btn').on('click', function() {
            const btn = $(this);
            $('#edit_amount').val(btn.data('amount'));
            $('#edit_method').val(btn.data('method'));
            $('#edit_notes').val(btn.data('notes'));
            $('#edit_client_name').val(btn.data('client-name'));

            let url = "{{ route('payments.update', ':id') }}".replace(':id', btn.data('id'));
            $('#editPaymentForm').attr('action', url);
            $('#editPaymentModal').modal('show');
        });

        // 3. Select2 Setup
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

        // 4. BEAUTY DELETE ALERT
        $(document).on('click', '.confirm-delete-btn', function(e) {
            e.preventDefault();
            const form = $(this).closest('.delete-form');

            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف هذه الدفعة نهائياً!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذف الآن',
                cancelButtonText: 'تراجع',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-4 shadow-lg border-0'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // 5. BEAUTY TOAST (SUCCESS/ERROR) - Fixed for RTL
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-start', // Top-left for Arabic layouts
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session("success") }}'
            });
        @endif

        @if(session('error') || session('failed'))
            Toast.fire({
                icon: 'error',
                title: '{{ session("error") ?? session("failed") }}'
            });
        @endif
    });
</script>
@endsection