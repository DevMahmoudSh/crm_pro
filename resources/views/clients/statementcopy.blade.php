<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>كشف حساب - {{ $client->name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        body { background: #f8f9fa; font-family: 'Tajawal', sans-serif; }
        .invoice-box {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            background: #fff;
        }
        /* إخفاء الأزرار عند الطباعة */
        @media print {
            .no-print { display: none; }
            .invoice-box { box-shadow: none; border: 0; }
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div class="d-flex justify-content-center mb-3 no-print">
        <button onclick="window.print()" class="btn btn-primary me-2">
            <i class="fa fa-print"></i> طباعة الكشف
        </button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">عودة</a>
    </div>

    <div class="invoice-box">
        <div class="row mb-4 border-bottom pb-3">
            <div class="col-6">
                <h2 class="fw-bold text-primary">كشف حساب</h2>
                <p class="text-muted mb-0">Statement of Account</p>
            </div>
            <div class="col-6 text-start">
                <h5 class="fw-bold">{{ $client->name }}</h5>
                <p class="mb-0">هاتف: {{ $client->phone }}</p>
                <small class="text-muted">تاريخ ووقت الطباعة: {{ date('H:i | Y-m-d') }}</small>
            </div>
        </div>

        <table class="table">
    <thead>
        <tr>
            <th>التاريخ</th>
            <th>البيان</th>
            <th>مدين (عليه)</th>
            <th>دائن (له)</th>
            <th>الرصيد التراكمي</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transactions as $transaction)
        <tr class="{{ $transaction->type == 'order' ? 'table-danger' : 'table-success' }}">
            <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
            <td>{{ $transaction->description }}</td>
            <td>{{ number_format($transaction->debit, 2) }}</td>
            <td>{{ number_format($transaction->credit, 2) }}</td>
            <td><strong>{{ number_format($transaction->balance, 2) }}</strong></td>
        </tr>
        @endforeach
    </tbody>
</table>
        
        <div class="mt-5 pt-4 text-center text-muted border-top">
            <p>شكراً لتعاملكم معنا</p>
        </div>
    </div>
</div>

</body>
</html>