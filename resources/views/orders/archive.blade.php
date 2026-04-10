@extends('layouts.app')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<style>
    /* إصلاح مشكلة ظهور القائمة خلف المودال */
    .select2-container {
        z-index: 999999 !important; /* طبقة أعلى من المودال */
    }
    
    /* تنسيق خاص للغة العربية */
    .select2-search__field {
        direction: rtl;
        text-align: right;
    }
    .select2-results__option {
        direction: rtl;
        text-align: right;
    }
    
    /* ضمان عرض القائمة بشكل صحيح */
    .select2-container .select2-selection--single {
        height: 38px !important; /* نفس ارتفاع حقول بوتستراب */
    }
</style>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">إدارة الطلبات - الأرشيف</h3>
            <span class="text-muted small">متابعة حالات الطلبات (Pending, Ready, Received)</span>
        </div>
        <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addOrderModal">
            <i class="fas fa-plus me-2"></i> إضافة طلب جديد
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table id="ordersTable" class="table table-hover align-middle w-100">
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
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-2 me-2 text-primary">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <span class="fw-bold">{{ $order->client->name ?? 'غير معروف' }}</span>
                                </div>
                            </td>
                            <td>
                                @if($order->details)
                                    <span class="text-muted small" title="{{ $order->details }}">
                                        {{ Str::limit($order->details, 20) }}
                                    </span>
                                @else
                                    <span class="text-muted small fst-italic">--</span>
                                @endif
                            </td>
                            <td class="fw-bold text-dark">${{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                @php $stage = $order->stage ?? 'pending'; @endphp
                                @if($stage == 'pending')
                                    <span class="badge bg-warning text-dark bg-opacity-75 px-3 py-2 rounded-pill">قيد الانتظار</span>
                                @elseif($stage == 'ready')
                                    <span class="badge bg-primary bg-opacity-75 px-3 py-2 rounded-pill">جاهز</span>
                                @elseif($stage == 'received')
                                    <span class="badge bg-success bg-opacity-75 px-3 py-2 rounded-pill">تم الاستلام</span>
                                @else
                                    <span class="badge bg-secondary">{{ $stage }}</span>
                                @endif
                            </td>
                            <td dir="ltr" class="text-secondary small">{{ $order->created_at->format('Y-m-d|h:i') }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-info edit-btn rounded-circle"
                                    data-id="{{ $order->id }}"
                                    data-client-id="{{ $order->client_id }}" 
                                    data-client-name="{{ $order->client->name }} - ({{ $order->client->phone ?? '' }})" 
                                    data-amount="{{ $order->total_amount }}"
                                    data-details="{{ $order->details }}"
                                    data-stage="{{ $order->stage }}"
                                    data-bs-toggle="modal" data-bs-target="#editOrderModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف الطلب؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger rounded-circle"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addOrderModal" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('orders.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">إضافة طلب جديد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">العميل</label>
                    <label class="form-label fw-bold">العميل</label>
                    <select name="client_id" id="add_client_select" class="form-select" style="width: 100%" required>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">المبلغ ($)</label>
                        <input type="number" name="total_amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">تفاصيل الطلب</label>
                        <textarea name="details" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">حالة الطلب</label>
                        <select name="stage" class="form-select">
                            <option value="pending" selected>Pending</option>
                            <option value="ready">Ready</option>
                            <option value="received">Received</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ الطلب</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="editOrderModal" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editOrderForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">تعديل الطلب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">العميل</label>
                        <select name="client_id" id="edit_client_id" class="form-select" style="width: 100%">
                            </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">المبلغ ($)</label>
                        <input type="number" name="total_amount" id="edit_amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">تفاصيل الطلب</label>
                        <textarea name="details" id="edit_details" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">حالة الطلب</label>
                        <select name="stage" id="edit_stage" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="ready">Ready</option>
                            <option value="received">Received</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning">تحديث</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        
        // إعدادات الجدول (كما هي)
        if (!$.fn.DataTable.isDataTable('#ordersTable')) {
            $('#ordersTable').DataTable({
                language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json" },
                order: [[ 0, "desc" ]] 
            });
        }

        // دالة إعدادات الـ AJAX المشتركة (لتجنب تكرار الكود)
        function getSelect2AjaxConfig(dropdownParent) {
            return {
                dropdownParent: dropdownParent,
                dir: "rtl",
                width: '100%',
                placeholder: "ابحث عن العميل...",
                allowClear: true,
                ajax: {
                    url: "{{ route('clients.select2') }}", // الرابط الذي أنشأناه
                    dataType: 'json',
                    delay: 250, // انتظار قليل قبل الإرسال لتخفيف الضغط
                    data: function (params) {
                        return {
                            search: params.term, // كلمة البحث
                            page: params.page || 1 // رقم الصفحة
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.results,
                            pagination: {
                                more: data.pagination.more // هل يوجد المزيد؟
                            }
                        };
                    },
                    cache: true
                }
            };
        }

        // 1. تفعيل Select2 لمودال الإضافة
        $('#add_client_select').select2(getSelect2AjaxConfig($('#addOrderModal')));

        // 2. تفعيل Select2 لمودال التعديل
        $('#edit_client_id').select2(getSelect2AjaxConfig($('#editOrderModal')));

        // 3. التعامل مع زر التعديل (الخدعة هنا)
        $('body').on('click', '.edit-btn', function() {
            let id = $(this).data('id');
            let clientId = $(this).data('client-id');
            let clientName = $(this).data('client-name'); // الاسم الذي أضفناه للزر
            let amount = $(this).data('amount');
            let details = $(this).data('details');
            let stage = $(this).data('stage');

            // تعبئة الحقول العادية
            $('#edit_amount').val(amount);
            $('#edit_details').val(details);
            $('#edit_stage').val(stage);
            $('#editOrderForm').attr('action', "{{ url('orders') }}/" + id);

            // --- التعامل مع Select2 في التعديل ---
            // بما أن القائمة فارغة وتعتمد على AJAX، يجب علينا
            // إنشاء "خيار مؤقت" وعرضه للمستخدم يدوياً
            
            var studentSelect = $('#edit_client_id');
            
            // نحذف أي خيار سابق
            studentSelect.empty();

            if (clientId) {
                // ننشئ Option جديد بالبيانات الحالية ونضيفه
                var option = new Option(clientName, clientId, true, true);
                studentSelect.append(option).trigger('change');
                
                // (اختياري) نخبر Select2 أن هذا العنصر تم اختياره يدوياً
                studentSelect.trigger({
                    type: 'select2:select',
                    params: {
                        data: { id: clientId, text: clientName }
                    }
                });
            }
        });
        
        // إصلاح مشكلة التركيز في بوتستراب (Force Focus)
        $.fn.modal.Constructor.prototype._enforceFocus = function() {};
    });
</script>

@endsection