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
    /* body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f2 100%);
        font-family: 'Cairo', sans-serif;
        color: var(--text-dark);
        min-height: 100vh;
        direction: rtl;
        overflow-x: hidden;
    } */
        /* --- Responsive Header --- */
    .section-header {
        margin-bottom: 2rem;
    }
    
    @media (max-width: 576px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1.5rem;
        }
        .btn-add-customer { width: 100%; justify-content: center; }
    }

    .title-underline {
        width: 35px;
        height: 4px;
        background: var(--soft-primary);
        border-radius: 10px;
        margin-top: 8px;
        opacity: 0.6;
    }

    /* --- Soft Glass Card --- */
    .card-glass {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        transition: transform 0.3s ease;
    }

    /* --- Responsive Table Handling --- */
    .table-responsive {
        border-radius: 20px;
        overflow: hidden;
    }

    .table thead th {
        background: rgba(241, 245, 249, 0.5);
        border: none;
        color: var(--text-muted);
        font-size: 0.85rem;
        padding: 1.25rem;
    }

    .table tbody td {
        padding: 1rem;
        border-bottom: 1px solid rgba(0,0,0,0.03);
        vertical-align: middle;
    }
    /* Force the modal to be Right-to-Left and fix layout */
    .modal-content {
        direction: rtl;
        text-align: right;
    }
    
    /* Correcting header layout for RTL */
    .modal-header {
        flex-direction: row-reverse;
    }

    .modal-header .btn-close {
        margin: 0;
        padding: 0.5rem;
    }

    /* Flip footer buttons for natural RTL flow */
    .modal-footer {
        justify-content: flex-start !important;
        gap: 10px;
        flex-direction: row-reverse;
    }

    /* Ensure labels align to the right */
    .form-label {
        width: 100%;
        display: block;
        text-align: right;
    }

    /* Phone numbers remain Left-to-Right for readability */
    .ltr-input {
        direction: ltr !important;
        text-align: left !important;
    }
    /* --- Add Button --- */
    .btn-add-customer {
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

    .btn-add-customer:hover {
        background: var(--soft-primary);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(129, 140, 248, 0.2);
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark">إدارة الزبائن</h3>
        <button class="btn btn-add-customer" id="openAddModal">
            <i class="fas fa-plus-circle"></i>
            <span>إضافة زبون جديد</span>
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
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
                    @foreach($clients as $client)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-bold">{{ $client->name }}</td>
                        <td>{{ $client->phone ?? '-' }}</td>
                        <td dir="ltr" class="small text-muted">{{ $client->created_at->format('h:i | Y-m-d') }}</td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-info edit-btn"
                                    data-id="{{ $client->id }}"
                                    data-name="{{ $client->name }}"
                                    data-phone="{{ $client->phone }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-outline-danger confirm-delete-btn">
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
                    <button type="submit" class="btn btn-primary px-4">حفظ</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        // Initialize DataTable
        $('#clientsTable').DataTable({ 
            language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" } 
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

        // Open Add Modal
        $('#openAddModal').on('click', function() {
            var myModal = new bootstrap.Modal(document.getElementById('addClientModal'));
            myModal.show();
        });

        // Open Edit Modal (Using Delegation for DataTables compatibility)
        $(document).on('click', '.edit-btn', function() {
            $('#edit_name').val($(this).data('name'));
            $('#edit_phone').val($(this).data('phone'));
            $('#editForm').attr('action', '/clients/' + $(this).data('id'));
            var editModal = new bootstrap.Modal(document.getElementById('editClientModal'));
            editModal.show();
        });

        // Delete Confirmation
        $(document).on('click', '.confirm-delete-btn', function() {
            const form = $(this).closest('.delete-form');
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "لن تتمكن من استعادة بيانات الزبون!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });
    });
</script>
@endsection