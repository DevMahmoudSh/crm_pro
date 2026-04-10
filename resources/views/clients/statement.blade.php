<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>كشف حساب - {{ $client->name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Corporate Palette */
            --corp-dark: #1a1a1a;       /* أسود فحمي */
            --corp-gray: #595959;       /* رمادي متوسط */
            --corp-light: #f4f4f4;      /* خلفية خفيفة */
            --corp-blue: #003366;       /* أزرق رسمي */
            --border-line: #e0e0e0;     /* فواصل ناعمة */
        }

        body {
            background: #e9ecef;
            font-family: 'Tajawal', sans-serif;
            font-size: 13px;
            color: var(--corp-dark);
            -webkit-print-color-adjust: exact;
        }

        .invoice-box {
            max-width: 850px;
            margin: 40px auto;
            padding: 40px 50px;
            background: #fff;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            border-radius: 0;
        }

        /* HEADER */
        .brand-header {
            border-bottom: 2px solid var(--corp-dark);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .document-title {
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
            font-size: 24px;
            color: var(--corp-blue);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .meta-label {
            font-size: 10px;
            text-transform: uppercase;
            color: #888;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .meta-value {
            font-weight: 600;
            font-size: 14px;
        }

        /* INFO SECTIONS */
        .info-section { margin-bottom: 40px; }
        .info-box { padding: 0; }

        .info-title {
            font-family: 'Cairo', sans-serif;
            font-size: 12px;
            font-weight: 700;
            color: var(--corp-gray);
            border-bottom: 1px solid var(--border-line);
            padding-bottom: 5px;
            margin-bottom: 10px;
            display: block;
        }

        /* TABLE */
        .corp-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .corp-table thead th {
            text-align: center; /* توسيط العناوين */
            font-size: 11px;
            font-weight: 700;
            color: var(--corp-dark);
            text-transform: uppercase;
            padding: 12px 8px;
            border-bottom: 2px solid var(--corp-dark);
            background-color: #fff;
        }

        .corp-table tbody td {
            text-align: center; /* توسيط جميع محتويات الجدول */
            padding: 12px 8px;
            border-bottom: 1px solid var(--border-line);
            font-size: 13px;
            color: #333;
            vertical-align: middle;
        }

        .corp-table tbody tr:last-child td {
            border-bottom: 2px solid var(--corp-dark);
        }

        /* FONTS & BALANCE */
        .num-font {
            font-family: 'Courier Prime', 'Courier New', monospace;
            font-weight: 600;
            letter-spacing: -0.5px;
        }

        .balance-running {
            font-weight: 700;
            color: var(--corp-dark);
            background-color: #fcfcfc;
        }

        /* TOTALS */
        .summary-box {
            background-color: var(--corp-light);
            padding: 20px;
            border-right: 4px solid var(--corp-blue);
            float: left;
            width: 250px;
        }

        /* FOOTER */
        .corp-footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid var(--border-line);
            font-size: 10px;
            color: #999;
            text-align: center;
        }

        /* PRINT STYLES - Hides buttons */
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; } /* إخفاء الأزرار بقوة */
            .invoice-box { box-shadow: none; margin: 0; padding: 20px 0; width: 100%; max-width: 100%; }
            .summary-box { background-color: #f4f4f4 !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="d-flex justify-content-center mt-4 mb-4 no-print">
        <button onclick="window.print()" class="btn btn-dark btn-sm px-4 rounded-0 shadow-sm">
            <i class="fa fa-print me-2"></i> طباعة (Print)
        </button>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm px-4 rounded-0 ms-2 shadow-sm">عودة</a>
    </div>

    <div class="invoice-box">
        <div class="info-section">
            <div class="row">
                <div class="col-4">
                    <img src="{{ asset('images/logo.png') }}" style="max-height: 85px; margin-bottom: 10px;" alt="Logo">
                </div>
                <div class="col-4">
                    <div class="info-box">
                        <span class="info-title">بيانات العميل (Bill To)</span>
                        <h5 class="fw-bold mb-1">{{ $client->name }}</h5>
                        @if($client->phone)
                            <p class="mb-0 text-muted small">هاتف: <span dir="ltr">{{ $client->phone }}</span></p>
                        @endif
                        <!-- <p class="mb-0 text-muted small">العنوان: فلسطين، رام الله</p>  -->
                    </div>
                </div>
                <div class="col-4 text-start">
                    <div class="info-box ps-5">
                        <span class="info-title">كشف حساب</span>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small text-muted">عدد الحركات:</span>
                            <span class="fw-bold small">{{ $transactions->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="small text-muted">تاريخ الاصدار</span>
                            <span class="fw-bold small"><div class="meta-value" dir="ltr">{{ date('Y-m-d') }}</div></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <table class="corp-table">
            <thead>
                <tr>
                    <th width="15%">التاريخ</th>
                    <th width="40%">الوصف / البيان</th>
                    <th width="15%">مدين (Debit)</th>
                    <th width="15%">دائن (Credit)</th>
                    <th width="15%">الرصيد (Balance)</th>
                </tr>
            </thead>
            <tbody>
                <!-- <tr>
                    <td class="text-muted" colspan="2">الرصيد الافتتاحي (Opening Balance)</td>
                    <td>-</td>
                    <td>-</td>
                    <td class="balance-running num-font">0.00</td>
                </tr> -->

                @forelse($transactions as $trans)
                    <tr>
                        <td>
                            <div dir="ltr" class="fw-bold">{{ $trans->created_at->format('Y-m-d') }}</div>
                        </td>
                        
                        <td>
                            {{ $trans->description }}
                        </td>

                        <td class="num-font">
                            @if($trans->debit > 0)
                                <span class="text-dark">{{ number_format($trans->debit, 2) }}</span>
                            @else
                                <span class="text-muted" style="color: #ccc !important;">—</span>
                            @endif
                        </td>

                        <td class="num-font">
                            @if($trans->credit > 0)
                                <span class="text-dark">{{ number_format($trans->credit, 2) }}</span>
                            @else
                                <span class="text-muted" style="color: #ccc !important;">—</span>
                            @endif
                        </td>

                        <td class="balance-running num-font">
                            {{ number_format(abs($trans->balance), 2) }}
                            <!-- <small class="ms-1" style="font-size: 9px; color: #888;">
                                {{ $trans->balance >= 0 ? 'شيكل' : 'CR' }}
                            </small> -->
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-5 text-muted">
                            لا توجد قيود مالية مسجلة خلال هذه الفترة.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="row mt-4">
            <div class="col-7">
                <p class="small text-muted mb-1">الشروط والأحكام:</p>
                <ul class="list-unstyled small text-muted" style="font-size: 11px; line-height: 1.6;">
                    <li>1. يعتبر هذا الكشف صحيحاً ومعتمداً ما لم يردنا اعتراض خلال 15 يوماً من تاريخه.</li>
                    <li>2. جميع المبالغ المذكورة بعملة الشيكل (NIS).</li>
                </ul>
                <small class="text-muted text-center d-block mb-1 text-truncate">
                        Developed with <span class="heart">&hearts;</span> by <strong>DEV7MOD</strong>
                </small>
            </div>
            <div class="col-5">
                @if($transactions->count() > 0)
                <div class="summary-box">
                    <div class="mb-2 text-muted small">الرصيد النهائي (Closing Balance)</div>
                    <h3 class="fw-bold mb-0 num-font text-dark" dir="ltr">
                        {{ number_format(abs($transactions->last()->balance), 2) }} <span style="font-size: 16px; font-family: sans-serif;">₪</span>
                    </h3>
                    <div class="mt-2 pt-2 border-top border-secondary small">
                        الحالة: 
                        <span class="fw-bold {{ $transactions->last()->balance >= 0 ? 'text-danger' : 'text-success' }}">
                            @if($transactions->last()->balance > 0)
                                {{ 'مستحق للدفع' }}
                            @elseif($transactions->last()->balance == 0)
                                {{ 'لا يوجد مبالغ مستحقة' }}
                            @else
                                {{ 'رصيد دائن' }}
                            @endif
                            <!-- {{ $transactions->last()->balance >= 0 ? 'مستحق للدفع' : 'رصيد دائن' }} -->
                        </span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- <div class="corp-footer">
            <p class="mb-1">مطبعة أبوكويك - البريج - دوار دعابس - الشارع المقابل لصيدلية الصطفاوي - واتس: ,<span>970597686133+</span>  </p>
            <p>This document is system generated and requires no signature.</p>
            
        </div> -->
    </div>
</div>

</body>
</html>