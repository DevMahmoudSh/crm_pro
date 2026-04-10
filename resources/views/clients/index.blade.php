@extends('layouts.app')

@section('content')
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.75);
        --glass-border: rgba(255, 255, 255, 0.5);
        --soft-primary: #818cf8;
        --soft-bg: #f8fafc;
        --text-dark: #1e293b;
        --text-muted: #64748b;
    }

    .section-header { margin-bottom: 2rem; }
    
    @media (max-width: 576px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1.5rem;
        }
        .btn-add-customer { width: 100%; justify-content: center; }
    }

    .title-underline {
        width: 35px; height: 4px;
        background: var(--soft-primary);
        border-radius: 10px; margin-top: 8px; opacity: 0.6;
    }

    .card-glass {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        transition: transform 0.3s ease;
    }

    .table-responsive { border-radius: 20px; overflow: hidden; }
    .table thead th {
        background: rgba(241, 245, 249, 0.5); border: none;
        color: var(--text-muted); font-size: 0.85rem; padding: 1.25rem;
    }
    .table tbody td { padding: 1rem; border-bottom: 1px solid rgba(0,0,0,0.03); vertical-align: middle; }
    
    .modal-content { direction: rtl; text-align: right; }
    .modal-header { flex-direction: row-reverse; }
    .modal-header .btn-close { margin: 0; padding: 0.5rem; }
    .modal-footer { justify-content: flex-start !important; gap: 10px; flex-direction: row-reverse; }
    .form-label { width: 100%; display: block; text-align: right; }
    .ltr-input { direction: ltr !important; text-align: left !important; }
    
    .btn-add-customer {
        background: white; color: var(--soft-primary);
        border: 2px solid #eef2ff; padding: 12px 24px;
        border-radius: 16px; font-weight: 600;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex; align-items: center; gap: 10px;
    }
    .btn-add-customer:hover {
        background: var(--soft-primary); color: white;
        transform: translateY(-2px); box-shadow: 0 10px 20px rgba(129, 140, 248, 0.2);
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark">إدارة الزبائن</h3>
        <button class="btn btn-add-customer" data-bs-toggle="modal" data-bs-target="#addClientModal">
            <i class="fas fa-plus-circle"></i>
            <span>إضافة زبون جديد</span>
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="clientsTable" class="table table-hover align-middle text-center w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>رقم الهاتف</th>
                            <th>تاريخ الإضافة</th>
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

<div class="modal fade" id="addClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('clients.store') }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title m-0">إضافة زبون جديد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">الاسم</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">رقم الهاتف</label>
                        <input type="text" name="phone" class="form-control ltr-input" minlength="10" maxlength="10" placeholder="05xxxxxxxx" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary px-4">حفظ</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title m-0">تعديل بيانات الزبون</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">الاسم</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">رقم الهاتف</label>
                        <input type="text" name="phone" id="edit_phone" class="form-control ltr-input" minlength="10" maxlength="10" placeholder="05xxxxxxxx" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-info text-white px-4">تحديث</button>
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
            // 1. إعداد DataTables ليعمل بنظام AJAX
            $('#clientsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('clients.data') }}", // مسار جلب البيانات (سننشئه تالياً)
                language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" },
                order: [[ 3, "desc" ]], // ترتيب افتراضي حسب تاريخ الإضافة
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // الترقيم التلقائي
                    { data: 'name', name: 'name', className: 'fw-bold' },
                    { data: 'phone', name: 'phone' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // 2. إعداد SweetAlert
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

            // 3. فتح نافذة التعديل (Event Delegation)
            $(document).on('click', '.edit-btn', function() {
                $('#edit_name').val($(this).data('name'));
                $('#edit_phone').val($(this).data('phone'));
                
                let updateUrl = "{{ route('clients.update', ':id') }}".replace(':id', $(this).data('id'));
                $('#editForm').attr('action', updateUrl);
                
                var editModal = new bootstrap.Modal(document.getElementById('editClientModal'));
                editModal.show();
            });

            // 4. تأكيد الحذف
            $(document).on('click', '.confirm-delete-btn', function(e) {
                e.preventDefault();
                const form = $(this).closest('.delete-form');
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "لن تتمكن من استعادة بيانات الزبون!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => { 
                    if (result.isConfirmed) form.submit(); 
                });
            });
        });
    </script>
@endpush