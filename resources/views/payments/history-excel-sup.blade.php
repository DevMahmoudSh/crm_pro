@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.bootstrap5.min.css">

<style>
    /* تحسينات عامة */
    .select2-container { z-index: 999999 !important; }
    .select2-search__field { direction: rtl; text-align: right; }
    .select2-results__option { direction: rtl; text-align: right; }
    
    .nav-tabs .nav-link { color: #6c757d; font-weight: 600; border: none; transition: 0.3s; }
    .nav-tabs .nav-link.active { color: #198754; border-bottom: 3px solid #198754; background: none; }
    .table-responsive { min-height: 400px; }
    
    /* تنسيق الأزرار الجديد */
    div.dt-buttons .btn {
        border-radius: 50px;
        padding: 6px 20px;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-left: 5px;
    }
    
    /* تحسين شكل الجدول */
    table.dataTable thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }
    
    .bg-raspberry { background-color: #c5006e; color: #ffffff; }
    .btn-action { width: 32px; height: 32px; padding: 0; line-height: 32px; border-radius: 8px; }
</style>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">سجل المدفوعات والأرصدة</h3>
            <span class="text-muted small">إدارة شاملة لحسابات العملاء</span>
        </div>
        <button class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
            <i class="fas fa-plus me-2"></i> دفعة جديدة
        </button>
    </div>

    @if(session('success') || session('error'))
        <div style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
           </div>
    @endif

    <ul class="nav nav-tabs mb-4 border-0" id="paymentTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-content" type="button">
                <i class="fas fa-history me-2"></i> السجل التفصيلي
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary-content" type="button">
                <i class="fas fa-chart-pie me-2"></i> ملخص الأرصدة (تصدير)
            </button>
        </li>
    </ul>


    <div class="tab-content" id="paymentTabsContent">
        
        <div class="tab-pane fade show active" id="history-content">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table id="historyTable" class="table table-hover align-middle w-100 text-center">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>العميل</th>
                                    <th>المبلغ</th>
                                    <th>الطريقة</th>
                                    <th>ملاحظات</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payment->id }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $payment->client->name ?? 'غير معروف' }}</div>
                                        <small class="text-muted">({{ $payment->client->phone }})</small>
                                    </td>
                                    <td class="text-success fw-bold">{{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        @php
                                            $methodClass = ['wallet' => 'bg-primary', 'cash' => 'bg-info text-dark', 'app' => 'bg-raspberry text-white'][$payment->method] ?? 'bg-secondary';
                                            $methodName = ['wallet' => 'محفظة', 'cash' => 'كاش', 'app' => 'تطبيق'][$payment->method] ?? $payment->method;
                                        @endphp
                                        <span class="badge {{ $methodClass }} rounded-pill">{{ $methodName }}</span>
                                    </td>
                                    <td class="text-muted small">{{ $payment->notes ?? '-' }}</td>
                                    <td dir="ltr" class="small">{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-outline-primary btn-action edit-payment-btn" 
                                                data-id="{{ $payment->id }}"
                                                data-amount="{{ $payment->amount }}"
                                                data-method="{{ $payment->method }}"
                                                data-notes="{{ $payment->notes }}"
                                                data-client-name="{{ $payment->client->name ?? 'غير معروف' }}">
                                                <i class="fas fa-pen" style="font-size: 12px;"></i>
                                            </button>
                                            <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="d-inline delete-form">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn btn-outline-danger btn-action confirm-delete-btn">
                                                    <i class="fas fa-trash" style="font-size: 12px;"></i>
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
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('clients.balance.export') }}" 
                        class="btn btn-success rounded-pill px-4 shadow-sm">
                            <i class="fas fa-file-excel me-2"></i>
                            تصدير Excel
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table id="paymentsTable" class="table table-hover align-middle w-100 text-center nowrap">
                            <thead class="table-light">
                                <tr>
                                    <th>العميل</th>
                                    <th>إجمالي الطلبات</th>
                                    <th>إجمالي المدفوعات</th>
                                    <th>الرصيد النهائي</th>
                                    <th>آخر حركة</th>
                                    <th>كشف حساب</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clients as $client)
                                @php
                                    $total_orders = $client->orders_sum_total_amount ?? 0;
                                    $total_payments = $client->payments_sum_amount ?? 0;
                                    $balance = $total_payments - $total_orders;
                                    $last_order = $client->latestOrder ? $client->latestOrder->created_at : null;
                                    $last_pay = $client->latestPayment ? $client->latestPayment->created_at : null;
                                    $last_trans = ($last_order > $last_pay) ? $last_order : ($last_pay ?? $last_order);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 me-2"><i class="fas fa-user text-secondary"></i></div>
                                            <div class="text-start">
                                                <div class="fw-bold">{{ $client->name }}</div>
                                                <div class="small text-muted">{{ $client->phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-danger fw-bold">{{ number_format($total_orders, 2) }}</td>
                                    <td class="text-success fw-bold">{{ number_format($total_payments, 2) }}</td>
                                    <td>
                                        @if($balance < 0)
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger p-2 rounded-3">عليه: {{ number_format(abs($balance), 2) }}</span>
                                        @elseif($balance > 0)
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success p-2 rounded-3">له: {{ number_format($balance, 2) }}</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary p-2 rounded-3">0.00</span>
                                        @endif
                                    </td>
                                    <td dir="ltr" class="text-muted small">{{ $last_trans ? $last_trans->format('Y-m-d') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('clients.statement', $client->id) }}" class="btn btn-sm btn-light border text-dark" target="_blank">
                                            <i class="fas fa-file-invoice me-1"></i> عرض
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
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('payments.store') }}" method="POST" class="w-100">
            @csrf
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-success text-white border-0 rounded-top-4">
                    <h5 class="modal-title fw-bold">تسجيل دفعة جديدة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold small">العميل</label>
                        <select name="client_id" id="payment_client_select" class="form-select" required></select>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold small">المبلغ</label>
                            <input type="number" step="0.01" name="amount" class="form-control form-control-lg" required placeholder="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold small">طريقة الدفع</label>
                            <select name="method" class="form-select form-select-lg" required>
                                <option value="cash">كاش</option>
                                <option value="wallet">محفظة</option>
                                <option value="app">تطبيق</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label text-muted fw-bold small">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-text text-muted fw-bold" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold">حفظ</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editPaymentModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editPaymentForm" method="POST" class="w-100">
            @csrf @method('PUT')
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white border-0 rounded-top-4">
                    <h5 class="modal-title fw-bold">تعديل الدفعة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold small">العميل</label>
                        <input type="text" id="edit_client_name" class="form-control bg-light" readonly>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold small">المبلغ</label>
                            <input type="number" step="0.01" name="amount" id="edit_amount" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted fw-bold small">طريقة الدفع</label>
                            <select name="method" id="edit_method" class="form-select" required>
                                <option value="cash">كاش</option>
                                <option value="wallet">محفظة</option>
                                <option value="app">تطبيق</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label text-muted fw-bold small">ملاحظات</label>
                        <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-text text-muted fw-bold" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">تحديث</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ======================================================= --}}
{{-- Scripts Section: الترتيب الصحيح للمكتبات الحديثة --}}
{{-- ======================================================= --}}

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.print.min.js"></script>

<script>
    $(document).ready(function () {
        
        // 1. إعداد جدول السجل (Basic)
        $('#historyTable').DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/2.1.8/i18n/ar.json" },
            order: [[ 0, "desc" ]]
        });

        // 2. إعداد جدول الملخص (Advanced with Buttons)
        // استخدام خاصية 'layout' الجديدة بدلاً من 'dom' القديمة
        if (!$.fn.DataTable.isDataTable('#paymentsTable')) {
            $('#paymentsTable').DataTable({
                language: { url: "//cdn.datatables.net/plug-ins/2.1.8/i18n/ar.json" },
                order: [[ 4, "desc" ]],
                layout: {
                    topStart: {
                        buttons: [
                            {
                                extend: 'excelHtml5',
                                text: '<i class="fas fa-file-excel me-2"></i> تصدير إكسيل',
                                className: 'btn btn-success',
                                title: 'ملخص أرصدة العملاء',
                                charset: 'UTF-8',
                                bom: true,
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4]
                                }
                            },
                            {
                                extend: 'print',
                                text: '<i class="fas fa-print me-2"></i> طباعة',
                                className: 'btn btn-secondary',
                                title: 'ملخص أرصدة العملاء',
                                exportOptions: {
                                    columns: [0, 1, 2, 3, 4]
                                }
                            }
                        ]
                    },
                    topEnd: 'search'
                }
            });
        }

        // 3. Modals & Logic
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

        // 4. Alerts
        const Toast = Swal.mixin({
            toast: true, position: 'top-start', showConfirmButton: false, 
            timer: 4000, timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        @if(session('success'))
            Toast.fire({ icon: 'success', title: '{{ session("success") }}' });
        @endif
        @if(session('error') || session('failed'))
            Toast.fire({ icon: 'error', title: '{{ session("error") ?? session("failed") }}' });
        @endif

        // Delete Confirm
        $(document).on('click', '.confirm-delete-btn', function(e) {
            e.preventDefault();
            const form = $(this).closest('.delete-form');
            Swal.fire({
                title: 'حذف الدفعة؟', text: "لا يمكن التراجع عن هذا الإجراء!", icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                confirmButtonText: 'حذف نهائي', cancelButtonText: 'إلغاء',
                customClass: { popup: 'rounded-4 shadow-lg border-0' }
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });
    });
</script>

@endsection