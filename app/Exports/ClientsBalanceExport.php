<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientsBalanceExport implements 
    FromCollection, 
    WithHeadings, 
    WithMapping, 
    ShouldAutoSize,
    WithStyles,
    WithEvents
{
    public function collection()
    {
        return Client::withSum('orders', 'total_amount')
            ->withSum('payments', 'amount')
            ->get() // نجلب البيانات
            ->sortByDesc(function ($client) {
                // الترتيب تنازلياً بناءً على الرصيد الحالي
                return ($client->orders_sum_total_amount ?? 0) - ($client->payments_sum_amount ?? 0);
            })
            ->values(); // إعادة تعيين المفاتيح لضمان توافقها مع الإكسل
    }

    public function headings(): array
    {
        return [
            'اسم العميل',
            'رقم الهاتف',
            'إجمالي الطلبات',
            'إجمالي المدفوعات',
            'الرصيد الحالي',
            'آخر معاملة'
        ];
    }

    public function map($client): array
    {
        $total_orders = $client->orders_sum_total_amount ?? 0;
        $total_payments = $client->payments_sum_amount ?? 0;
        $balance = $total_payments - $total_orders;

        $last_order_date = $client->latestOrder?->created_at;
        $last_payment_date = $client->latestPayment?->created_at;

        $last_transaction = $last_order_date > $last_payment_date
            ? $last_order_date
            : ($last_payment_date ?? $last_order_date);

        return [
            $client->name,
            $client->phone,
            $total_orders,
            $total_payments,
            $balance,
            $last_transaction ? $last_transaction->format('Y-m-d H:i') : '--',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [ // تنسيق الهيدر
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet;

                // جعل الاتجاه من اليمين لليسار
                $sheet->getDelegate()->setRightToLeft(true);

                // تلوين الهيدر
                $sheet->getStyle('A1:F1')->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => ['rgb' => '198754'],
                    ],
                    'font' => [
                        'color' => ['rgb' => 'FFFFFF'],
                        'bold' => true,
                    ],
                ]);

                // تلوين الرصيد (أحمر لو سالب)
                $highestRow = $sheet->getHighestRow();

                for ($row = 2; $row <= $highestRow; $row++) {
                    $balance = $sheet->getCell('E' . $row)->getValue();

                    if ($balance < 0) {
                        $sheet->getStyle('E' . $row)->applyFromArray([
                            'font' => ['color' => ['rgb' => 'DC3545']],
                        ]);
                    } elseif ($balance > 0) {
                        $sheet->getStyle('E' . $row)->applyFromArray([
                            'font' => ['color' => ['rgb' => '198754']],
                        ]);
                    }
                }
            },
        ];
    }
}