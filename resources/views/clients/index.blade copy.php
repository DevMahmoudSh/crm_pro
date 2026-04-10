@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.4);
        --soft-primary: #818cf8;
        --soft-secondary: #94a3b8;
        --bg-gradient: linear-gradient(135deg, #f5f7fa 0%, #e4e9f2 100%);
    }

    body {
        background: var(--bg-gradient);
        font-family: 'Cairo', sans-serif;
        color: #475569;
        min-height: 100vh;
        direction: rtl;
    }

    /* --- Smooth Section Header --- */
    .section-header h3 {
        color: #1e293b;
        font-weight: 700;
        position: relative;
    }

    .title-underline {
        width: 35px;
        height: 4px;
        background: var(--soft-primary);
        border-radius: 10px;
        margin-top: 8px;
        opacity: 0.6;
    }

    /* --- Add Button (Soft & Bubbly) --- */
    .btn-add-customer {
        background: white;
        color: var(--soft-primary);
        border: 2px solid #eef2ff;
        padding: 10px 24px;
        border-radius: 20px;
        font-weight: 600;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }

    .btn-add-customer:hover {
        background: var(--soft-primary);
        color: white;
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(129, 140, 248, 0.2);
    }

    /* --- Glass Table Card --- */
    .card-glass {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
    }

    .table thead th {
        border-bottom: 2px solid #f1f5f9;
        color: var(--soft-secondary);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1.5rem 1rem;
    }

    .table tbody td {
        padding: 1.2rem 1rem;
        border-bottom: 1px solid rgba(0,0,0,0.02);
    }

    .table-hover tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }

    /* --- Modal: Soft & Floating --- */
    .modal-content {
        border-radius: 35px;
        border: none;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(15px);
        padding: 10px;
        direction: rtl;
        text-align: right;
    }

    .modal-header {
        border-bottom: none;
        padding: 1.5rem 2rem;
        flex-direction: row-reverse;
    }

    .modal-title { font-weight: 700; color: #334155; }

    .modal-footer {
        border-top: none;
        padding: 1.5rem 2rem;
        justify-content: flex-start !important;
        flex-direction: row-reverse;
        gap: 12px;
    }

    /* --- Inputs: Clean & Minimal --- */
    .form-control {
        background: #f8fafc;
        border: 1.5px solid #f1f5f9;
        border-radius: 16px;
        padding: 12px 18px;
        transition: 0.3s;
    }

    .form-control:focus {
        background: white;
        border-color: var(--soft-primary);
        box-shadow: 0 0 0 4px rgba(129, 140, 248, 0.1);
    }

    .ltr-input { direction: ltr !important; text-align: left !important; }

    /* --- Action Buttons (Minimalist) --- */
    .btn-soft {
        width: 38px;
        height: 38px;
        border-radius: 14px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: 0.3s;
    }

    .btn-soft-edit { background: #eef2ff; color: #6366f1; }
    .btn-soft-edit:hover { background: #6366f1; color: white; }

    .btn-soft-delete { background: #fff1f2; color: #f43f5e; }
    .btn-soft-delete:hover { background: #f43f5e; color: white; }

    /* Pagination Styling for DataTables */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 12px !important;
        border: none !important;
    }
</style>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5 section-header">
        <div>
            <h3 class="mb-0">إدارة الزبائن</h3>
            <div class="title-underline"></div>
        </div>

        <button class="btn btn-add-customer d-flex align-items-center gap-2" id="openAddModal">
            <i class="fas fa-plus-circle"></i>
            <span>إضافة زبون جديد</span>
        </button>
    </div>

    <div class="card card-glass border-0">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table id="clientsTable" class="table table-hover align-middle text-center w-100 mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th class="text-end">الاسم</th>
                            <th>رقم الهاتف</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $client)
                        <tr>
                            <td class="text-muted small">{{ $loop->iteration }}</td>
                            <td class="text-end fw-bold">{{ $client->name }}</td>
                            <td><span class="badge bg-white text-primary rounded-pill px-3 py-2 border shadow-sm">{{ $client->phone ?? '-' }}</span></td>
                            <td dir="ltr" class="small text-muted">{{ $client->created_at->format('Y/m/d') }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn-soft btn-soft-edit edit-btn" 
                                            data-id="{{ $client->id }}" 
                                            data-name="{{ $client->name }}" 
                                            data-phone="{{ $client->phone }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn-soft btn-soft-delete confirm-delete-btn">
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

<div class="modal fade" id="addClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('clients.store') }}" method="POST" class="w-100">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">زبون جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label small fw-bold">الاسم الكامل</label>
                        <input type="text" name="name" class="form-control" placeholder="مثال: أحمد محمد" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">رقم الهاتف</label>
                        <input type="text" name="phone" class="form-control ltr-input" placeholder="05xxxxxxxx" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-add-customer">حفظ الزبون</button>
                    <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">تراجع</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editForm" method="POST" class="w-100">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تعديل البيانات</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label small fw-bold">الاسم</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">رقم الهاتف</label>
                        <input type="text" name="phone" id="edit_phone" class="form-control ltr-input" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-add-customer">تحديث البيانات</button>
                    <button type="button" class="btn btn-link text-muted text-decoration-none" data-bs-dismiss="modal">إلغاء</button>
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
        $('#clientsTable').DataTable({ 
            language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" },
            pageLength: 8,
            dom: '<"d-flex justify-content-between mb-3"lf>rtip'
        });

        // Soft Toast Alerts
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-start',
            showConfirmButton: false,
            timer: 2500,
            background: '#fff',
            color: '#475569'
        });

        @if(session('success')) Toast.fire({ icon: 'success', title: "{{ session('success') }}" }); @endif

        // Modal triggers
        $('#openAddModal').on('click', function() {
            new bootstrap.Modal(document.getElementById('addClientModal')).show();
        });

        $(document).on('click', '.edit-btn', function() {
            $('#edit_name').val($(this).data('name'));
            $('#edit_phone').val($(this).data('phone'));
            $('#editForm').attr('action', '/clients/' + $(this).data('id'));
            new bootstrap.Modal(document.getElementById('editClientModal')).show();
        });

        // Smooth Delete Confirmation
        $(document).on('click', '.confirm-delete-btn', function() {
            const form = $(this).closest('.delete-form');
            Swal.fire({
                title: 'حذف الزبون؟',
                text: "لا يمكن التراجع عن هذا الإجراء",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f43f5e',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                customClass: { popup: 'rounded-5' }
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });
    });
</script>
@endsection