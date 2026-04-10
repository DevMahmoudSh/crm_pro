<?php

use Mpdf\Mpdf;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
//just for hidden url for db create for render
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Exports\ClientsBalanceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Client;

// ==========================================
// 1. مسارات الزوار (غير مسجلين الدخول)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

// ==========================================
// 2. مسارات النظام (محمية - تتطلب تسجيل دخول)
// ==========================================
Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return redirect('/dashboard');
    });
    // أضف هذا السطر مع مسارات لوحة التحكم (Dashboard)
    Route::get('/dashboard/backup', [DashboardController::class, 'backupDatabase'])->name('dashboard.backup');
    Route::resource('dashboard', DashboardController::class);
    

    // ------------------------------------------
    // مسارات الـ AJAX (يجب أن تكون دائماً فوق الـ Resource)
    // ------------------------------------------
    // أجاكس الزبائن
    Route::get('/clients/data', [ClientController::class, 'clientsData'])->name('clients.data');
    Route::get('/ajax/clients', [ClientController::class, 'getClientsForSelect2'])->name('clients.select2');
    
    // أجاكس المدفوعات
    Route::get('/payments/history-data', [PaymentController::class, 'historyData'])->name('payments.history.data');
    Route::get('/payments/summary-data', [PaymentController::class, 'summaryData'])->name('payments.summary.data');

    // ------------------------------------------
    // مسارات الزبائن (Clients)
    // ------------------------------------------
    Route::get('/clients/balance/export', function () {
        return Excel::download(new ClientsBalanceExport, 'ملخص_ارصدة_العملاء.xlsx');
    })->name('clients.balance.export');

    // مسار الـ PDF (تم التصحيح والدمج هنا)
    Route::get('/clients/balance/pdf', function () {
        // جلب البيانات مع حساب الرصيد والترتيب التصاعدي
        $clients = Client::withSum('orders', 'total_amount')
            ->withSum('payments', 'amount')
            // ->orderByRaw('(IFNULL(orders_sum_total_amount, 0) - IFNULL(payments_sum_amount, 0)) DESC')
            ->orderByRaw('(COALESCE((SELECT SUM(total_amount) FROM orders WHERE orders.client_id = clients.id), 0) - COALESCE((SELECT SUM(amount) FROM payments WHERE payments.client_id = clients.id), 0)) DESC')
            ->get();

        // إعدادات mPDF لدعم اللغة العربية تلقائياً
        $pdf = PDF::loadView('pdf.clients_balance', compact('clients'), [], [
            'mode'             => 'utf-8',
            'format'           => 'A4',
            'autoScriptToLang' => true,
            'autoLangToFont'   => true,
            'autoArabic'       => true,
        ]);

        // يمكنك استخدام stream للعرض أو download للتحميل المباشر
        return $pdf->download('ملخص_أرصدة_العملاء.pdf');
    })->name('clients.balance.pdf');

    Route::get('/clients/{id}/statement', [ClientController::class, 'statement'])->name('clients.statement');
    Route::resource('clients', ClientController::class); // <-- الـ Resource في الأسفل

    // ------------------------------------------
    // مسارات الطلبات (Orders)
    // ------------------------------------------
    Route::get('/orders/data', [OrderController::class, 'ordersData'])->name('orders.data');
    Route::get('/orders/archive', [OrderController::class, 'archive'])->name('orders.archive');
    Route::resource('orders', OrderController::class);

    // ------------------------------------------
    // مسارات المدفوعات (Payments)
    // ------------------------------------------
    Route::get('/payments/history', [PaymentController::class, 'allPaymentsHistory'])->name('payments.history');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    Route::resource('payments', PaymentController::class)->only(['store', 'destroy']);

    // ------------------------------------------
    // تسجيل الخروج
    // ------------------------------------------
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});


// ==========================================
// أكواد استيراد البيانات (موقوفة كتعليق كما كانت لديك)
// ==========================================

/*
use App\Models\Client;
use Carbon\Carbon;
Route::get('/import-clients', function () {
    // كود الاستيراد الخاص بك...
});

use Illuminate\Support\Facades\File;
use App\Models\Order;
Route::get('/import-from-files', function () {
    // كود الاستيراد من الملفات الخاص بك...
});
*/
// Route::get('/setup-db', function () {
//     try {
//         Artisan::call('migrate', ['--force' => true]);
//         return 'تم إنشاء جداول قاعدة البيانات بنجاح! يمكنك الآن العودة للصفحة الرئيسية.';
//     } catch (\Exception $e) {
//         return 'حدث خطأ: ' . $e->getMessage();
//     }
// });