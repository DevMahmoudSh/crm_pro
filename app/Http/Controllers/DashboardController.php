<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;    // تأكد من وجود مودل Order
use App\Models\Client; // تأكد من وجود مودل Client
use App\Models\Payment;
use Spatie\DbDumper\Databases\MySql;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     // 1. حساب مجموع المدفوعات (Paid)
    //     $totalPaid = Order::where('status', 'Paid')->sum('total_amount');

    //     // 2. حساب مجموع غير المدفوع (Unpaid)
    //     $totalUnpaid = Order::where('status', 'Unpaid')->sum('total_amount');

    //     // 3. عدد الطلبات الكلي
    //     $ordersCount = Order::count();

    //     // 4. عدد الزبائن (إما من جدول الزبائن أو عدد الأسماء الفريدة في الطلبات)
    //     // سنفترض وجود مودل Clients
    //     $clientsCount = Client::count(); 
    //     // أو إذا لم يوجد جدول عملاء: Order::distinct('client_name')->count('customer_name');

    //     // 5. جلب آخر 5 طلبات للعرض في الجدول
    //     $recentOrders = Order::latest()->take(5)->get();

    //     return view('dashboard', compact(
    //         'totalPaid', 
    //         'totalUnpaid', 
    //         'ordersCount', 
    //         'clientsCount', 
    //         'recentOrders'
    //     ));
    // }

    public function index()
    {
        // نجلب العملاء مع:
        // 1. مجموع الطلبات (orders_sum_total_amount)
        // 2. مجموع المدفوعات (payments_sum_amount)
        // 3. آخر طلب وآخر دفعة (لحساب تاريخ آخر معاملة)
        $clients = Client::withSum('orders', 'total_amount')
                         ->withSum('payments', 'amount')
                         ->with(['latestOrder', 'latestPayment'])
                         ->get();

        //count of all orders
        $allOrdersCount = Order::count();
        //count of ready orders
        $readyOrdersCount = Order::where('stage', 'ready')->count();
        //count of pending orders
        $pendingOrdersCount = Order::where('stage', 'pending')->count();
        //count of clients
        $clientsCount = Client::Count();
        //sum of all orders
        $totalPaidbalance = Payment::Sum('amount');

        return view('dashboard', compact(
            'totalPaidbalance',
            'clients',
            'clientsCount',
            'allOrdersCount',
            'readyOrdersCount',
            'pendingOrdersCount'
            ));
    }
    public function backupDatabase()
    {
        try {
            $fileName = 'backup_' . env('DB_DATABASE') . '_' . now()->format('Y-m-d_H-i-s') . '.sql';
            $filePath = storage_path('app/' . $fileName);

            $db = env('DB_DATABASE');
            $user = env('DB_USERNAME', 'root');
            $pass = env('DB_PASSWORD', '');

            // تجهيز كلمة المرور إن وُجدت
            $passwordParam = $pass ? "-p\"{$pass}\"" : "";

            // إعداد الأمر المباشر مع استخدام ميزة result-file المدمجة في MySQL
            // لتجنب أي مشاكل في توجيه الملفات على الويندوز
            $command = "C:\\xampp\\mysql\\bin\\mysqldump.exe -u {$user} {$passwordParam} --result-file=\"{$filePath}\" {$db}";

            // هذه هي الميزة السحرية في Laravel: حقن متغيرات النظام مباشرة في العملية
            // لكي يتجاوز الويندوز خطأ الشبكة (10106)
            $result = Process::env([
                'SystemRoot'  => 'C:\Windows',
                'SystemDrive' => 'C:',
                'WINDIR'      => 'C:\Windows',
            ])->run($command);

            // التحقق مما إذا كانت العملية قد فشلت
            if ($result->failed()) {
                dd('فشلت عملية التصدير. تفاصيل الخطأ: ' . $result->errorOutput());
            }

            // إذا نجحت العملية، قم بتحميل الملف وحذفه من السيرفر لتوفير المساحة
            return response()->download($filePath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            dd('حدث خطأ برمجي: ' . $e->getMessage());
        }
    }
}