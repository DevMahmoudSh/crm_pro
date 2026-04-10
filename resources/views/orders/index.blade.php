    @extends('layouts.app')

    @section('content')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        /* Force Modal RTL */
        .modal-content { direction: rtl; text-align: right; }
        .modal-header { flex-direction: row-reverse; }
        .modal-header .btn-close { margin: 0; padding: 0.5rem; }
        .modal-footer { justify-content: flex-start !important; gap: 10px; flex-direction: row-reverse; }
        
        /* Select2 Fixes */
        .select2-container { z-index: 9999 !important; }
        .select2-dropdown { direction: rtl !important; text-align: right !important; }
        .select2-container .select2-selection--single { height: 38px !important; display: flex; align-items: center; }

        /* زر "طلب جديد" - تصميم نيون ناعم */
        .btn-action-primary {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: white; border: none; padding: 12px 28px;
            border-radius: 50px; font-weight: 700;
            transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }
        .btn-action-primary:hover {
            transform: translateY(-2px); box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
            filter: brightness(1.1);
        }
    </style>

    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">إدارة الطلبات</h3>
                <div class="mt-2">
                    @if (request()->routeIs('orders.index'))
                        <a href="{{ route('orders.archive') }}" class="btn btn-sm btn-secondary rounded-pill px-3">عرض الأرشيف</a>
                    @else
                        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-primary rounded-pill px-3">العودة للطلبات الحالية</a>
                    @endif
                </div>
            </div>    
            <button class="btn btn-action-primary d-flex align-items-center" id="openAddOrderModal">
                <i class="fas fa-plus-circle me-2"></i>
                <span>إنشاء طلب جديد</span>
            </button>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="ordersTable" class="table table-hover align-middle w-100 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                <th>العميل</th>
                                <th>التفاصيل</th>
                                <th>المبلغ</th>
                                <th>حالة الطلب</th>
                                <th>تاريخ الطلب</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('orders.store') }}" method="POST">
                @csrf
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">إضافة طلب جديد</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">العميل</label>
                            <select name="client_id" id="add_client_select" class="form-select" style="width: 100%" required></select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">المبلغ ($)</label>
                            <input type="number" step="0.01" name="total_amount" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">تفاصيل الطلب</label>
                            <textarea name="details" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">حالة الطلب</label>
                            <select name="stage" class="form-select">
                                <option value="pending" selected>قيد الانتظار (Pending)</option>
                                <option value="ready">جاهز (Ready)</option>
                                <option value="received">تم الاستلام (Received)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary px-4">حفظ الطلب</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editOrderModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editOrderForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">تعديل الطلب</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">العميل</label>
                            <select name="client_id" id="edit_client_id" class="form-select" style="width: 100%"></select>
                            <input type="hidden" name="client_id" id="hidden_client_id">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">المبلغ ($)</label>
                            <input type="number" step="0.01" name="total_amount" id="edit_amount" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">تفاصيل الطلب</label>
                            <textarea name="details" id="edit_details" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">حالة الطلب</label>
                            <select name="stage" id="edit_stage" class="form-select">
                                <option value="pending">قيد الانتظار (Pending)</option>
                                <option value="ready">جاهز (Ready)</option>
                                <option value="received">تم الاستلام (Received)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info text-white px-4">تحديث</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @endsection

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {

            // 1. DataTables لتعمل بالأجاكس
            const table = $('#ordersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('orders.data') }}",
                    data: function (d) {
                        // إرسال متغير لمعرفة إذا كنا في صفحة الأرشيف أو الرئيسية
                        d.is_archive = "{{ request()->routeIs('orders.archive') ? 1 : 0 }}";
                    }
                },
                language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" },
                order: [[ 0, "desc" ]],
                columns: [
                    { data: 'id_formatted', name: 'id' },
                    { data: 'client_info', name: 'client.name' },
                    { data: 'details_formatted', name: 'details' },
                    { data: 'amount', name: 'total_amount' },
                    { data: 'stage_badge', name: 'stage' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // 2. إعدادات SweetAlert والتنبيهات
            const Toast = Swal.mixin({
                toast: true, position: 'top-start',
                showConfirmButton: false, timer: 3000, timerProgressBar: true
            });

            @if(session('success'))
                Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
            @endif

            @if(session('error'))
                Toast.fire({ icon: 'error', title: "{{ session('error') }}" });
            @endif

            @if ($errors->any())
                Swal.fire({
                    icon: 'error', title: 'خطأ في البيانات!',
                    html: '<ul style="text-align: right; direction: rtl; list-style-type: none; padding: 0;">' +
                            @foreach ($errors->all() as $error)
                                '<li><i class="fas fa-times-circle text-danger me-1"></i> {{ $error }}</li>' +
                            @endforeach
                        '</ul>',
                    confirmButtonColor: '#198754', confirmButtonText: 'حسناً',
                    customClass: { popup: 'rounded-4 shadow-lg border-0' }
                });
            @endif

            // 3. إعداد Select2
            function getSelect2Config(parentModal) {
                return {
                    dropdownParent: parentModal,
                    dir: "rtl",
                    ajax: {
                        url: "{{ route('clients.select2') }}",
                        dataType: 'json',
                        delay: 250,
                        data: (params) => ({ search: params.term, page: params.page || 1 }),
                        processResults: (data) => ({
                            results: data.results,
                            pagination: { more: data.pagination.more }
                        }),
                        cache: true
                    },
                    placeholder: "ابحث عن عميل...",
                    allowClear: true
                };
            }

            $('#add_client_select').select2(getSelect2Config($('#addOrderModal')));
            $('#edit_client_id').select2(getSelect2Config($('#editOrderModal')));

            // 4. فتح النوافذ المنبثقة
            $('#openAddOrderModal').on('click', function() {
                new bootstrap.Modal(document.getElementById('addOrderModal')).show();
            });

            $(document).on('click', '.edit-order-btn', function() {
                const data = $(this).data();
                
                $('#edit_amount').val(data.amount);
                $('#edit_details').val(data.details);
                $('#edit_stage').val(data.stage);
                $('#editOrderForm').attr('action', "{{ url('orders') }}/" + data.id);

                const select = $('#edit_client_id');
                select.empty().append(new Option(data.clientName, data.clientId, true, true)).trigger('change');
                select.prop('disabled', true); // منع تغيير العميل
                $('#hidden_client_id').val(data.clientId); // إرسال الـ ID في الخلفية

                new bootstrap.Modal(document.getElementById('editOrderModal')).show();
            });

            // 5. تأكيد الحذف
            $(document).on('click', '.confirm-delete-order', function() {
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'حذف الطلب؟',
                    text: "لن تتمكن من استعادة البيانات!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => { if (result.isConfirmed) form.submit(); });
            });

        });
    </script>
    @endpush