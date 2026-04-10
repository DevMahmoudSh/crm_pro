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
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" id="openAddOrderModal">
            <i class="fas fa-plus me-2"></i> إضافة طلب جديد
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
                        @foreach($orders as $order)
                        <tr>
                            <td class="fw-bold">#{{ $order->id }}</td>
                            <td>
                                <span class="fw-bold text-primary">{{ $order->client->name ?? 'غير معروف' }}</span>
                            </td>
                            <td>
                                <span class="text-muted small">{{ Str::limit($order->details, 25) ?? '--' }}</span>
                            </td>
                            <td class="fw-bold text-dark">${{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                @php $stage = $order->stage ?? 'pending'; @endphp
                                @if($stage == 'pending')
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">قيد الانتظار</span>
                                @elseif($stage == 'ready')
                                    <span class="badge bg-primary px-3 py-2 rounded-pill">جاهز</span>
                                @elseif($stage == 'received')
                                    <span class="badge bg-success px-3 py-2 rounded-pill">تم الاستلام</span>
                                @endif
                            </td>
                            <td dir="ltr" class="text-secondary small">{{ $order->created_at->format('Y-m-d | h:i') }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-info edit-order-btn rounded-circle"
                                        data-id="{{ $order->id }}"
                                        data-client-id="{{ $order->client_id }}" 
                                        data-client-name="{{ $order->client->name ?? '' }} - ({{ $order->client->phone ?? '' }})" 
                                        data-amount="{{ $order->total_amount }}"
                                        data-details="{{ $order->details }}"
                                        data-stage="{{ $order->stage }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="d-inline delete-order-form">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle confirm-delete-order">
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
                        <select name="client_id" id="edit_client_id" class="form-select" style="width: 100%" required ></select>
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

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {

        // 1. DataTables
        const table = $('#ordersTable').DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" },
            order: [[ 0, "desc" ]] 
        });

        // Toast configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-start',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        // Show Success Alert
        @if(session('success'))
            Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
        @endif

        // Show Error Alert
        @if(session('error'))
            Toast.fire({ icon: 'error', title: "{{ session('error') }}" });
        @endif

        // 2. Select2 Configuration Function
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

        // Initialize Select2
        $('#add_client_select').select2(getSelect2Config($('#addOrderModal')));
        $('#edit_client_id').select2(getSelect2Config($('#editOrderModal')));

        // 3. Add Button Trigger
        $('#openAddOrderModal').on('click', function() {
            new bootstrap.Modal(document.getElementById('addOrderModal')).show();
        });

        // 4. Edit Button Trigger (Delegation)
        $(document).on('click', '.edit-order-btn', function() {
            const data = $(this).data();
            
            $('#edit_amount').val(data.amount);
            $('#edit_details').val(data.details);
            $('#edit_stage').val(data.stage);
            $('#editOrderForm').attr('action', "{{ url('orders') }}/" + data.id);

            // Set Select2 value manually
            const select = $('#edit_client_id');
            select.empty().append(new Option(data.clientName, data.clientId, true, true)).trigger('change');

            new bootstrap.Modal(document.getElementById('editOrderModal')).show();
        });

        // 5. Delete Confirmation
        $(document).on('click', '.confirm-delete-order', function() {
            const form = $(this).closest('form');
            Swal.fire({
                title: 'حذف الطلب؟',
                text: "لن تتمكن من استعادة البيانات!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء'
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });

    });
</script>

@endsection