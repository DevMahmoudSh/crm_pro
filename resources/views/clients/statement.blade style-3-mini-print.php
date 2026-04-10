<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>كشف حساب - {{ $client->name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #1e293b;
            --secondary-color: #64748b;
            --accent-color: #3b82f6;
            --bg-table-head: #f1f5f9;
            --border-color: #e2e8f0;
        }

        body {
            background: #f8f9fa;
            font-family: 'Tajawal', sans-serif;
            font-size: 13px; /* حجم خط مثالي للطباعة */
            color: #0f172a;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        h1, h2, h3, h4, h5, h6 { font-family: 'Cairo', sans-serif; }

        .invoice-box {
            max-width: 900px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
        }

        /* Compact Header */
        .header-section {
            border-bottom: 2px solid var(--accent-color);
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .client-box {
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 10px;
        }

        /* Compact Table */
        .table-responsive { margin-top: 10px; }
        
        .custom-table {
            width: 100%;
            margin-bottom: 0;
            border: 1px solid var(--border-color);
        }

        .custom-table thead th {
            background-color: var(--primary-color) !important;
            color: #fff !important;
            padding: 8px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .custom-table td {
            padding: 6px 8px; /* تقليل الحشو لتوفير الطول */
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .custom-table tr:nth-child(even) { background-color: #f8fafc; }
        
        /* Balance Styling */
        .balance-cell {
            background-color: #fffbeb; /* لون خفيف جداً للتمييز */
            font-weight: bold;
            font-family: 'Cairo', sans-serif;
        }

        /* Badge Styling - Smaller */
        .badge-mini {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .bg-credit-text { color: #166534; font-weight: bold; }
        .bg-debit-text { color: #991b1b; font-weight: bold; }

        /* Footer */
        .footer-section {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-size: 11px;
            color: #64748b;
        }

        /* Print Settings - The Most Important Part */
        @media print {
            @page {
                size: A4;
                margin: 10mm; /* هوامش صغيرة للورقة */
            }
            body { background: #fff; }
            .no-print { display: none !important; }
            .invoice-box {
                border: none;
                padding: 0;
                margin: 0;
                width: 100%;
                max-width: 100%;
            }
            .header-section {
                border-bottom: 1px solid #000;
            }
            .custom-table thead th {
                background-color: #ddd !important;
                color: #000 !important;
                border-bottom: 2px solid #000;
            }
            /* تجنب فصل الجدول في منتصف السطر */
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

<div class="container-fluid"> <div class="d-flex justify-content-center mt-3 mb-3 no-print gap-2">
        <button onclick="window.print()" class="btn btn-sm btn-primary shadow-sm">
            <i class="fa fa-print me-1"></i> طباعة (A4)
        </button>
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary shadow-sm">عودة</a>
    </div>

    <div class="invoice-box">
        <div class="header-section">
            <div class="row align-items-center">
                <div class="col-6">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('images/logo.png') }}" style="max-height: 45px; margin-left: 15px;" alt="Logo">
                        <div>
                            <h4 class="fw-bold mb-0 text-primary">كشف حساب</h4>
                            <small class="text-muted d-block" dir="ltr">{{ date('Y-m-d') }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="client-box">
                        <div class="row g-1">
                            <div class="col-12">
                                <span class="text-muted small">العميل:</span>
                                <span class="fw-bold text-dark">{{ $client->name }}</span>
                            </div>
                            @if($client->phone)
                            <div class="col-12">
                                <span class="text-muted small">هاتف:</span>
                                <span class="fw-bold text-dark" dir="ltr">{{ $client->phone }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table custom-table text-center table-sm"> <thead>
                    <tr>
                        <th width="12%">التاريخ</th>
                        <th width="40%">البيان</th>
                        <th width="14%">مدين (عليه)</th>
                        <th width="14%">دائن (له)</th>
                        <th width="20%">الرصيد</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="2" class="text-end fw-bold text-secondary ps-3">
                            الرصيد الافتتاحي
                        </td>
                        <td colspan="2"></td>
                        <td class="fw-bold">0.00</td>
                    </tr>

                    @forelse($transactions as $trans)
                        <tr>
                            <td class="text-nowrap" dir="ltr">
                                {{ $trans->created_at->format('Y-m-d') }}
                                <span class="text-muted ms-1" style="font-size: 10px;">{{ $trans->created_at->format('H:i') }}</span>
                            </td>

                            <td class="text-end text-truncate" style="max-width: 300px;">
                                {{ $trans->description }}
                            </td>

                            <td>
                                @if($trans->debit > 0)
                                    <span class="text-danger">{{ number_format($trans->debit, 2) }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            <td>
                                @if($trans->credit > 0)
                                    <span class="text-success">{{ number_format($trans->credit, 2) }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>

                            <td class="balance-cell">
                                {{ number_format(abs($trans->balance), 2) }}
                                <span class="badge-mini border ms-1 {{ $trans->balance >= 0 ? 'bg-debit-text border-danger' : 'bg-credit-text border-success' }}">
                                    {{ $trans->balance >= 0 ? 'عليه' : 'له' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-muted">لا توجد حركات مسجلة.</td>
                        </tr>
                    @endforelse
                </tbody>
                
                @if($transactions->count() > 0)
                <tfoot class="border-top-2">
                    <tr class="table-light">
                        <td colspan="3" class="text-end fw-bold pt-2">الرصيد النهائي:</td>
                        <td colspan="2" class="fw-bold pt-2 fs-6">
                             <span class="{{ $transactions->last()->balance >= 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format(abs($transactions->last()->balance), 2) }}
                             </span>
                             <small class="text-muted" style="font-size: 11px">
                                ({{ $transactions->last()->balance >= 0 ? 'مطلوب منه' : 'له رصيد' }})
                             </small>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <div class="footer-section row">
            <div class="col-8 text-end">
                يعتمد الرصيد أعلاه ما لم يرد اعتراض خلال 15 يوماً.
            </div>
            <div class="col-4 text-start">
                صفحة 1 من 1
            </div>
        </div>
    </div>
</div>

</body>
</html>