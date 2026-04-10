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
    .bg-raspberry { background-color: #c5006e; color: #ffffff; }
</style>

<div class="container-fluid py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">سجل المدفوعات والأرصدة</h3>
            <span class="text-muted small">عرض تفصيلي لجميع الدفعات وحالات الحسابات</span>
        </div>
        <div class="d-flex gap-2">
            <!-- <a href="{{ route('clients.balance.pdf') }}" class="btn btn-outline-danger rounded-pill px-4 shadow-sm">
                <i class="fas fa-file-pdf me-2"></i> تحميل PDF
            </a>
            
            <a href="{{ route('clients.balance.export') }}" class="btn btn-outline-success rounded-pill px-4 shadow-sm">
                <i class="fas fa-file-excel me-2"></i> تحميل Excel
            </a> -->

            <button class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                <i class="fas fa-hand-holding-usd me-2"></i> تسجيل دفعة جديدة
            </button>
        </div>
    </div>

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
                                    <th>كشف حساب</th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                        </table>
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

</div>

@endsection 

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            
            // 1. إعداد DataTables للجدولين
            $('#historyTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('payments.history.data') }}",
                language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" },
                order: [[ 0, "desc" ]],
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'client_info', name: 'client.name' },
                    { data: 'amount', name: 'amount' },
                    { data: 'method_badge', name: 'method' },
                    { data: 'notes', name: 'notes' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            if (!$.fn.DataTable.isDataTable('#paymentsTable')) {
                $('#paymentsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('payments.summary.data') }}",
                    language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" },
                    
                    // التعديل هنا: 3 تعني العمود الرابع (الرصيد)، و desc تعني الأكبر فالأصغر (أعلى ديون في الأعلى)
                    order: [[ 3, "desc" ]], 
                    
                    columns: [
                        { data: 'client_info', name: 'name' },
                        { data: 'total_orders', name: 'total_orders', searchable: false },
                        { data: 'total_payments', name: 'total_payments', searchable: false },
                        // التعديل هنا: جعل orderable: true ليتمكن المستخدم من ترتيب العمود
                        { data: 'balance_badge', name: 'balance', searchable: false, orderable: true },
                        { data: 'last_transaction', name: 'last_transaction', searchable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });
            }

            // 2. منطق نافذة التعديل (Event Delegation للـ AJAX)
            $(document).on('click', '.edit-payment-btn', function() {
                const btn = $(this);
                $('#edit_amount').val(btn.data('amount'));
                $('#edit_method').val(btn.data('method'));
                $('#edit_notes').val(btn.data('notes'));
                $('#edit_client_name').val(btn.data('client-name'));

                let url = "{{ route('payments.update', ':id') }}".replace(':id', btn.data('id'));
                $('#editPaymentForm').attr('action', url);
                $('#editPaymentModal').modal('show');
            });

            // 3. إعداد Select2 داخل نافذة الإضافة
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
                    processResults: function (data) {
                        return { results: data.results, pagination: { more: data.pagination.more } };
                    }
                }
            });

            // 4. منطق الحذف مع SweetAlert
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
                    customClass: { popup: 'rounded-4 shadow-lg border-0' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // 5. التنبيهات (Toasts) وأخطاء التحقق (Validation Errors)
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-start',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });

            @if(session('success'))
                Toast.fire({ icon: 'success', title: '{{ session("success") }}' });
            @endif

            @if(session('error'))
                Toast.fire({ icon: 'error', title: '{{ session("error") }}' });
            @endif

            // إظهار أخطاء التحقق (Validation) عند فشل الحفظ
            @if ($errors->any())
                Swal.fire({
                    icon: 'error',
                    title: 'يوجد خطأ في البيانات!',
                    html: '<ul style="text-align: right; direction: rtl; list-style-type: none; padding: 0;">' +
                            @foreach ($errors->all() as $error)
                                '<li><i class="fas fa-times-circle text-danger me-1"></i> {{ $error }}</li>' +
                            @endforeach
                          '</ul>',
                    confirmButtonColor: '#198754',
                    confirmButtonText: 'حسناً',
                    customClass: { popup: 'rounded-4 shadow-lg border-0' }
                });
            @endif
        });
    </script>
@endpush