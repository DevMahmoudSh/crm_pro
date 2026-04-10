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
            --corp-gray: #595959;       /* رمادي متوسط للنصوص الثانوية */
            --corp-light: #f4f4f4;      /* خلفية خفيفة جداً */
            --corp-blue: #003366;       /* أزرق بنكي رسمي */
            --border-line: #e0e0e0;     /* خطوط فواصل ناعمة */
        }

        body {
            background: #e9ecef; /* خلفية الصفحة العامة داكنة قليلاً لإبراز الورقة */
            font-family: 'Tajawal', sans-serif;
            font-size: 13px;
            color: var(--corp-dark);
            -webkit-print-color-adjust: exact;
        }

        .invoice-box {
            max-width: 850px; /* عرض A4 القياسي */
            margin: 40px auto;
            padding: 40px 50px;
            background: #fff;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05); /* ظل ناعم جداً */
            border-radius: 0; /* حواف حادة تعطي طابع رسمي أكثر */
        }

        /* HEADER SECTION */
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

        /* CLIENT INFO SECTION */
        .info-section {
            margin-bottom: 40px;
        }
        
        .info-box {
            padding: 0;
        }

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

        /* TABLE STYLING - THE PRO PART */
        .corp-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .corp-table thead th {
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            color: var(--corp-dark);
            text-transform: uppercase;
            padding: 12px 8px;
            border-bottom: 2px solid var(--corp-dark); /* خط سميك تحت العناوين */
            background-color: #fff; /* خلفية بيضاء كلاسيكية */
        }

        .corp-table tbody td {
            padding: 12px 8px;
            border-bottom: 1px solid var(--border-line);
            font-size: 13px;
            color: #333;
        }

        .corp-table tbody tr:last-child td {
            border-bottom: 2px solid var(--corp-dark); /* إغلاق الجدول بخط قوي */
        }

        /* أرقام مالية متوازنة */
        .num-font {
            font-family: 'Courier Prime', 'Courier New', monospace; /* خط موحد العرض للأرقام */
            font-weight: 600;
            letter-spacing: -0.5px;
        }

        .balance-running {
            font-weight: 700;
            color: var(--corp-dark);
            background-color: #fcfcfc;
        }

        /* TOTALS SECTION */
        .summary-box {
            background-color: var(--corp-light);
            padding: 20px;
            border-right: 4px solid var(--corp-blue); /* لمسة جمالية */
            float: left; /* لليسار في العربية */
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

        /* PRINT */
        @media print {
            body { background: #fff; }
            .no-print { display: none; }
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
        <div class="brand-header">
            <div class="row align-items-end">
                <div class="col-6">
                    <img src="{{ asset('images/logo.png') }}" style="max-height: 50px; margin-bottom: 10px;" alt="Logo">
                    <div class="small text-muted">شركة الحلول المتقدمة المحدودة</div>
                </div>
                <div class="col-6 text-start">
                    <h1 class="document-title">كشف حساب</h1>
                    <div class="row mt-3">
                        <div class="col-6 text-end"></div>
                        <div class="col-6">
                            <div class="meta-label">تاريخ الإصدار</div>
                            <div class="meta-value" dir="ltr">{{ date('Y-m-d') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-section">
            <div class="row">
                <div class="col-6">
                    <div class="info-box">
                        <span class="info-title">بيانات العميل (Bill To)</span>
                        <h5 class="fw-bold mb-1">{{ $client->name }}</h5>
                        @if($client->phone)
                            <p class="mb-0 text-muted small">هاتف: <span dir="ltr">{{ $client->phone }}</span></p>
                        @endif
                        <p class="mb-0 text-muted small">العنوان: المملكة العربية السعودية، الرياض</p> </div>
                </div>
                <div class="col-6 text-start">
                    <div class="info-box ps-5">
                        <span class="info-title">ملخص الحساب</span>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small text-muted">عدد الحركات:</span>
                            <span class="fw-bold small">{{ $transactions->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="small text-muted">العملة:</span>
                            <span class="fw-bold small">ريال سعودي (SAR)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <table class="corp-table text-center">
            <thead>
                <tr>
                    <th width="15%" class="text-end ps-3">التاريخ</th>
                    <th width="40%" class="text-end">الوصف / البيان</th>
                    <th width="15%">مدين (Debit)</th>
                    <th width="15%">دائن (Credit)</th>
                    <th width="15%">الرصيد (Balance)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-end ps-3 text-muted" colspan="2">الرصيد الافتتاحي (Opening Balance)</td>
                    <td>-</td>
                    <td>-</td>
                    <td class="balance-running num-font">0.00</td>
                </tr>

                @forelse($transactions as $trans)
                    <tr>
                        <td class="text-end ps-3">
                            <div dir="ltr" class="fw-bold">{{ $trans->created_at->format('Y-m-d') }}</div>
                        </td>
                        
                        <td class="text-end">
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
                            <small class="ms-1" style="font-size: 9px; color: #888;">
                                {{ $trans->balance >= 0 ? 'DR' : 'CR' }}
                            </small>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-5 text-center text-muted">
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
                    <li>1. يعتبر هذا الكشف صحيحاً ومعتمداً ما لم يردنا اعتراض خطي خلال 15 يوماً من تاريخه.</li>
                    <li>2. جميع المبالغ المذكورة بالريال السعودي.</li>
                </ul>
            </div>
            <div class="col-5">
                @if($transactions->count() > 0)
                <div class="summary-box">
                    <div class="mb-2 text-muted small">الرصيد النهائي (Closing Balance)</div>
                    <h3 class="fw-bold mb-0 num-font text-dark" dir="ltr">
                        {{ number_format(abs($transactions->last()->balance), 2) }} <span style="font-size: 14px">SAR</span>
                    </h3>
                    <div class="mt-2 pt-2 border-top border-secondary small">
                        الحالة: 
                        <span class="fw-bold {{ $transactions->last()->balance >= 0 ? 'text-danger' : 'text-success' }}">
                            {{ $transactions->last()->balance >= 0 ? 'مستحق للدفع' : 'رصيد دائن' }}
                        </span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="corp-footer">
            <p class="mb-1">اسم الشركة - العنوان الرسمي - رقم السجل التجاري: 1010101010</p>
            <p>This document is system generated and requires no signature.</p>
        </div>
    </div>
</div>

</body>
</html>