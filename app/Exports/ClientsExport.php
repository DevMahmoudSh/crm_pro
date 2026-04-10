<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
// ... (أي استخدامات أخرى لديك مثل WithHeadings أو WithMapping)

class ClientsBalanceExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Client::withSum('orders', 'total_amount')
            ->withSum('payments', 'amount')
            // استخدمنا DESC للترتيب التنازلي بناءً على الرصيد
            ->orderByRaw('(IFNULL(orders_sum_total_amount, 0) - IFNULL(payments_sum_amount, 0)) DESC')
            ->get();
    }
    
    // ... (باقي الدوال إن وجدت مثل headings() أو map() اتركها كما هي)
}