<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>كشف حساب - {{ $client->name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        body { background: #f8f9fa; font-family: 'Tajawal', sans-serif; }
        .invoice-box {
            max-width: 900px;
            margin: 30px auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            background: #fff;
            border-radius: 8px;
        }
        @media print {
            .no-print { display: none; }
            .invoice-box { box-shadow: none; border: 0; margin: 0; padding: 0; }
        }
    </style>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Changa:wght@200..800&family=Noto+Nastaliq+Urdu:wght@400..700&display=swap');
        .noto-nastaliq-urdu-<uniquifier> {
            font-family: "Noto Nastaliq Urdu", serif;
            font-optical-sizing: auto;
            font-weight: <weight>;
            font-style: normal;
        }
        .changa-<uniquifier> {
            font-family: "Changa", sans-serif;
            font-optical-sizing: auto;
            font-weight: <weight>;
            font-style: normal;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="d-flex justify-content-center mb-4 no-print">
        <button onclick="window.print()" class="btn btn-primary me-2 shadow-sm">
            <i class="fa fa-print"></i> طباعة الكشف
        </button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary shadow-sm">عودة</a>
    </div>

    <div class="invoice-box">
        <div class="row mb-4 border-bottom pb-3 align-items-center">
            <div class="col-4">
                <h2 class="fw-bold text-primary mb-1" style="font-family: 'Cairo'">كشف حساب عميل</h2>
                <p class="text-muted small mb-0">Customer Statement of Account</p>
            </div>
            <div class="col-4 text-start" style="font-family: 'Arial';font-size: 22px">
                <h4 class="fw-bold text-dark" style="font-family: 'Noto Nastaliq Urdu';font-size: 18px; padding: 3px">{{ $client->name }}</h4>
                @if($client->phone)
                    <p class="mb-0 text-muted" style="font-family: 'Changa';font-size: 18px; margin-top: 20px"> هاتف: <span dir="ltr">{{ $client->phone }}</span></p>
                @endif
                <small class="text-muted d-block mt-1" style="font-family: 'Changa';font-size: 14px">تاريخ الإصدار: {{ date('Y-m-d') }}</small>
            </div>
            <div class="col-4">
                <img width="100%" src="{{ asset('images/logo.png') }}" alt="Company Logo">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="15%">التاريخ</th>
                        <th width="40%">البيان / التفاصيل</th>
                        <th width="15%">مدين (عليه)</th>
                        <th width="15%">دائن (له)</th>
                        <th width="15%">الرصيد</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-secondary">
                        <td colspan="4" class="text-end fw-bold">الرصيد الافتتاحي</td>
                        <td class="fw-bold">0.00</td>
                    </tr>

                    @forelse($transactions as $trans)
                        <tr>
                            <td dir="ltr" class="text-nowrap">
                                {{ $trans->created_at->format('Y-m-d') }}
                                <br>
                                <small class="text-muted">{{ $trans->created_at->format('H:i A') }}</small>
                            </td>

                            <td class="text-end">
                                {{ $trans->description }}
                            </td>

                            <td class="text-danger fw-bold">
                                {{ $trans->debit > 0 ? number_format($trans->debit, 2) : '-' }}
                            </td>

                            <td class="text-success fw-bold">
                                {{ $trans->credit > 0 ? number_format($trans->credit, 2) : '-' }}
                            </td>

                            <td class="fw-bold" style="background-color: #fcfcfc;">
                                {{ number_format(abs($trans->balance), 2) }}
                                <br>
                                <span class="badge {{ $trans->balance >= 0 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success' }} rounded-pill border border-0">
                                    {{ $trans->balance >= 0 ? 'عليه' : 'له' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5 text-muted">
                                لا توجد حركات مالية مسجلة لهذا العميل.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                
                @if($transactions->count() > 0)
                <tfoot class="table-light border-top-3">
                    <tr>
                        <td colspan="4" class="text-end fw-bold fs-5 py-3">الرصيد النهائي المستحق:</td>
                        <td class="fw-bold fs-5 py-3 {{ $transactions->last()->balance >= 0 ? 'text-danger' : 'text-success' }}">
                            {{ number_format(abs($transactions->last()->balance), 2) }}
                            <small style="font-size: 0.7rem">
                                {{ $transactions->last()->balance >= 0 ? 'عليه' : 'له' }}
                            </small>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <div class="mt-5 pt-3 text-center text-muted border-top small">
            <p class="mb-1">نقر بصحة الرصيد المذكور أعلاه ما لم يردنا اعتراض خلال 15 يوماً.</p>
            <p>Generated by System</p>
        </div>
    </div>
</div>

</body>
</html>