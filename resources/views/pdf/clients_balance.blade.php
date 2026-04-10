<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>ملخص أرصدة العملاء</title>
    <style>
        /* إعدادات الخط والاتجاه الأساسية لدعم العربية */
        body { 
            font-family: 'xbriyaz', sans-serif; /* خط مدمج في mPDF يدعم العربية */
            direction: rtl; 
            text-align: right; 
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 20px; 
        }
        th, td { 
            border: 1px solid #dddddd; 
            padding: 10px; 
            text-align: center; 
        }
        th { 
            background-color: #f4f4f4; 
            font-weight: bold; 
            color: #333;
        }
        .text-success { color: #198754; }
        .text-danger { color: #dc3545; }
        .header-title { text-align: center; color: #1e293b; margin-bottom: 5px; }
        .date-text { text-align: center; color: #64748b; font-size: 12px; margin-bottom: 30px; }
    </style>
</head>
<body>

    <h2 class="header-title">تقرير ملخص أرصدة العملاء</h2>
    <p class="date-text">تاريخ الإصدار: {{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>اسم العميل</th>
                <th>رقم الهاتف</th>
                <th>إجمالي الطلبات</th>
                <th>إجمالي المدفوعات</th>
                <th>الرصيد الحالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $client)
                @php
                    $orders = $client->orders_sum_total_amount ?? 0;
                    $payments = $client->payments_sum_amount ?? 0;
                    $balance = $payments - $orders;
                @endphp
                <tr>
                    <td>{{ $client->id }}</td>
                    <td><strong>{{ $client->name }}</strong></td>
                    <td dir="ltr">{{ $client->phone ?? 'لا يوجد' }}</td>
                    <td>{{ number_format($orders, 2) }}</td>
                    <td>{{ number_format($payments, 2) }}</td>
                    <td dir="rtl">
                        @if($balance > 0)
                            <span class="text-success">له: {{ number_format($balance, 2) }}</span>
                        @elseif($balance < 0)
                            <span class="text-danger">عليه: {{ number_format(abs($balance), 2) }}</span>
                        @else
                            0.00
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>