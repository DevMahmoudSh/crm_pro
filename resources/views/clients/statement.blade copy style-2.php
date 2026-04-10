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
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --accent-color: #3498db;
            --bg-color: #f4f6f9;
            --success-soft: #d4edda;
            --success-text: #155724;
            --danger-soft: #f8d7da;
            --danger-text: #721c24;
        }

        body {
            background: var(--bg-color);
            font-family: 'Tajawal', sans-serif;
            color: #333;
            -webkit-print-color-adjust: exact;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Cairo', sans-serif;
        }

        .invoice-box {
            max-width: 950px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border-top: 6px solid var(--accent-color);
            position: relative;
        }

        /* Header Styling */
        .header-section {
            padding: 40px;
            background: linear-gradient(to bottom, #fff, #fafafa);
            border-bottom: 1px solid #eee;
        }

        .client-info h4 {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 24px;
        }

        .statement-title {
            color: var(--secondary-color);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Table Styling */
        .table-container {
            padding: 0 30px 30px 30px;
        }

        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px; /* Space between rows */
        }

        .custom-table thead th {
            background-color: var(--primary-color) !important;
            color: #fff !important;
            padding: 15px;
            font-weight: 600;
            border: none;
            font-family: 'Cairo', sans-serif;
        }
        
        .custom-table thead th:first-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
        .custom-table thead th:last-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }

        .custom-table tbody tr {
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
            transition: all 0.2s ease;
        }

        .custom-table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .custom-table td {
            padding: 15px;
            vertical-align: middle;
            border-top: 1px solid #f0f0f0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .custom-table td:first-child { border-right: 1px solid #f0f0f0; border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
        .custom-table td:last-child { border-left: 1px solid #f0f0f0; border-top-left-radius: 8px; border-bottom-left-radius: 8px; }

        /* Balance Highlight */
        .balance-cell {
            background-color: #fcfcfc;
            font-weight: bold;
            font-family: 'Cairo', sans-serif;
        }

        .badge-soft {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .bg-credit { background-color: var(--success-soft); color: var(--success-text); }
        .bg-debit { background-color: var(--danger-soft); color: var(--danger-text); }

        /* Footer */
        .footer-section {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #eee;
            font-size: 0.9rem;
            color: #6c757d;
        }

        /* Print Styles */
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .invoice-box { 
                box-shadow: none; 
                border: none; 
                margin: 0; 
                width: 100%; 
                max-width: 100%;
                border-top: 2px solid #000; /* Simplification for print */
            }
            .custom-table thead th {
                background-color: #eee !important;
                color: #000 !important;
                border-bottom: 2px solid #000;
            }
            .custom-table tbody tr {
                box-shadow: none;
                border-bottom: 1px solid #ddd;
            }
            .custom-table td { border: none; border-bottom: 1px solid #ddd; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="d-flex justify-content-center mt-4 mb-4 no-print gap-2">
        <button onclick="window.print()" class="btn btn-primary px-4 shadow-sm rounded-pill">
            <i class="fa fa-print me-2"></i> طباعة الكشف
        </button>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-4 shadow-sm rounded-pill">
            <i class="fa fa-arrow-right me-2"></i> عودة
        </a>
    </div>

    <div class="invoice-box">
        <div class="header-section">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <h2 class="fw-bold text-primary mb-1">كشف حساب عميل</h2>
                    <p class="text-muted text-uppercase small ls-1 mb-0">Statement of Account</p>
                    <div class="mt-3">
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="far fa-calendar-alt me-1"></i> تاريخ الإصدار: <span dir="ltr">{{ date('Y-m-d') }}</span>
                        </span>
                    </div>
                </div>
                
                <div class="col-md-2 text-center d-none d-md-block">
                    <div style="height: 60px; width: 1px; background: #ddd; margin: 0 auto;"></div>
                </div>

                <div class="col-md-5 text-md-start text-center client-info">
                    <h4 class="mb-2">{{ $client->name }}</h4>
                    @if($client->phone)
                        <p class="mb-1 text-muted"><i class="fa fa-phone me-2 text-primary"></i> <span dir="ltr">{{ $client->phone }}</span></p>
                    @endif
                    <div class="mt-3">
                         <img src="{{ asset('images/logo.png') }}" style="max-height: 60px; max-width: 100%;" alt="Company Logo">
                    </div>
                </div>
            </div>
        </div>

        <div class="table-container mt-2">
            <div class="table-responsive">
                <table class="table custom-table text-center">
                    <thead>
                        <tr>
                            <th width="15%">التاريخ / الوقت</th>
                            <th width="35%">البيان / التفاصيل</th>
                            <th width="15%">مدين (عليه)</th>
                            <th width="15%">دائن (له)</th>
                            <th width="20%">الرصيد</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="background-color: #f9f9f9;">
                            <td colspan="2" class="text-end fw-bold text-secondary ps-4">
                                <i class="fa fa-wallet me-2"></i> الرصيد الافتتاحي
                            </td>
                            <td colspan="2"></td>
                            <td class="fw-bold font-monospace fs-5">0.00</td>
                        </tr>

                        @forelse($transactions as $trans)
                            <tr>
                                <td class="text-muted">
                                    <div class="fw-bold text-dark" dir="ltr">{{ $trans->created_at->format('Y-m-d') }}</div>
                                    <small style="font-size: 11px;">{{ $trans->created_at->format('H:i A') }}</small>
                                </td>

                                <td class="text-end text-dark">
                                    {{ $trans->description }}
                                </td>

                                <td class="font-monospace">
                                    @if($trans->debit > 0)
                                        <span class="text-danger fw-bold">{{ number_format($trans->debit, 2) }}</span>
                                    @else
                                        <span class="text-muted text-opacity-25">-</span>
                                    @endif
                                </td>

                                <td class="font-monospace">
                                    @if($trans->credit > 0)
                                        <span class="text-success fw-bold">{{ number_format($trans->credit, 2) }}</span>
                                    @else
                                        <span class="text-muted text-opacity-25">-</span>
                                    @endif
                                </td>

                                <td class="balance-cell">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <span class="fw-bolder font-monospace fs-6 text-dark">
                                            {{ number_format(abs($trans->balance), 2) }}
                                        </span>
                                        <span class="badge-soft mt-1 {{ $trans->balance >= 0 ? 'bg-debit' : 'bg-credit' }}">
                                            {{ $trans->balance >= 0 ? 'عليه' : 'له' }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-5 text-center text-muted">
                                    <i class="fa fa-folder-open fa-3x mb-3 text-light-gray"></i>
                                    <p class="mb-0">لا توجد حركات مالية مسجلة لهذا العميل حتى الآن.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    @if($transactions->count() > 0)
                    <tfoot>
                        <tr style="border: none; box-shadow: none;">
                            <td colspan="3" class="text-end pt-4 border-0">
                                <h5 class="fw-bold text-primary">الرصيد النهائي المستحق:</h5>
                            </td>
                            <td colspan="2" class="pt-3 border-0">
                                <div class="p-3 rounded-3 {{ $transactions->last()->balance >= 0 ? 'bg-danger text-white' : 'bg-success text-white' }} shadow-sm">
                                    <h3 class="mb-0 fw-bold" dir="ltr">{{ number_format(abs($transactions->last()->balance), 2) }}</h3>
                                    <small class="text-white-50">{{ $transactions->last()->balance >= 0 ? 'مبلغ مستحق على العميل' : 'رصيد لصالح العميل' }}</small>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>

        <div class="footer-section">
            <div class="row">
                <div class="col-md-6 text-md-end mb-2 mb-md-0">
                    <small>نقر بصحة الرصيد المذكور أعلاه ما لم يردنا اعتراض خطي خلال 15 يوماً.</small>
                </div>
                <div class="col-md-6 text-md-start">
                    <small class="text-muted">تم استخراج هذا الكشف آلياً ولا يحتاج إلى توقيع.</small>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>