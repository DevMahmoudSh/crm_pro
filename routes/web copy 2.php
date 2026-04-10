<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController; // Ensure you have a LoginController

// 1. Guest Routes (Publicly accessible)
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

// 2. Protected Routes (Must be logged in)
Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return redirect('/dashboard');
    });

    Route::resource('dashboard', DashboardController::class);

    // --- Clients ---
    Route::resource('clients', ClientController::class);
    Route::get('/ajax/clients', [ClientController::class, 'getClientsForSelect2'])->name('clients.select2');
    Route::get('/clients/{id}/statement', [ClientController::class, 'statement'])->name('clients.statement');

    // --- Orders ---
    Route::get('/orders/archive', [OrderController::class, 'archive'])->name('orders.archive');
    Route::resource('orders', OrderController::class);

    // --- Payments ---
    Route::get('/payments/history', [PaymentController::class, 'allPaymentsHistory'])->name('payments.history');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    Route::resource('payments', PaymentController::class)->only(['store', 'destroy']);

    // --- Logout ---
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});

use App\Exports\ClientsBalanceExport;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/clients/balance/export', function () {
    return Excel::download(new ClientsBalanceExport, 'ملخص_ارصدة_العملاء.xlsx');
})->name('clients.balance.export');

//download statment as pdf
//Route::get('/client/{id}/pdf', [ClientController::class, 'downloadPdf'])->name('client.download.pdf');


// use App\Models\Client; // تأكد من استدعاء المودل
// use Carbon\Carbon;     // تأكد من استدعاء Carbon

// Route::get('/import-clients', function () {
    
//     $jsonData = '[
//         {
//           "name": "ياسر شحادة حسين",
//           "phone": "0599601638",
//           "createdAt": 1763378817479,
//           "id": "mi327vlzdoj0ujp6tmf"
//         },
//         {
//           "name": "رمزي ايمن السعافين",
//           "phone": "0597109463",
//           "createdAt": 1763378955129,
//           "id": "mi32attlr6fb8eyudln"
//         }
//     ]';

//     $clients = json_decode($jsonData, true);

//     foreach ($clients as $item) {
//         $date = Carbon::createFromTimestampMs($item['createdAt']);

//         Client::updateOrCreate(
//             ['name' => $item['name']], 
//             [
//                 'phone'      => $item['phone'],
//                 'created_at' => $date,
//                 'updated_at' => now(),
//             ]
//         );
//     }

//     return "تم إضافة البيانات بنجاح!";
// });


// use Illuminate\Support\Facades\File;
// use App\Models\Client;
// use App\Models\Order;
// use Carbon\Carbon;

// Route::get('/import-from-files', function () {
    
//     // ==========================================
//     // 1. استيراد العملاء (من ملف clients.json)
//     // ==========================================
    
//     // تحديد مسار الملف
//     $clientsPath = storage_path('app/clients.json');

//     // التأكد من وجود الملف
//     if (!File::exists($clientsPath)) {
//         return "خطأ: ملف العملاء غير موجود في المسار: " . $clientsPath;
//     }

//     // قراءة محتوى الملف
//     $clientsJson = File::get($clientsPath);
//     $clientsData = json_decode($clientsJson, true);

//     // التحقق من صحة الجيسون
//     if (is_null($clientsData)) {
//         return "خطأ في صيغة ملف clients.json: " . json_last_error_msg();
//     }

//     // بدء التخزين
//     foreach ($clientsData as $c) {
//         $clientDate = Carbon::createFromTimestampMs($c['createdAt']);
        
//         Client::updateOrCreate(
//             ['name' => $c['name']], 
//             [
//                 'phone'       => $c['phone'],
//                 'external_id' => $c['id'],
//                 'created_at'  => $clientDate,
//                 'updated_at'  => now(),
//             ]
//         );
//     }

//     // ==========================================
//     // 2. استيراد الطلبات (من ملف orders.json)
//     // ==========================================

//     $ordersPath = storage_path('app/orders.json');

//     if (!File::exists($ordersPath)) {
//         return "خطأ: ملف الطلبات غير موجود في المسار: " . $ordersPath;
//     }

//     $ordersJson = File::get($ordersPath);
//     $ordersData = json_decode($ordersJson, true);

//     if (is_null($ordersData)) {
//         return "خطأ في صيغة ملف orders.json: " . json_last_error_msg();
//     }

//     foreach ($ordersData as $o) {
//         // البحث عن العميل
//         $client = Client::where('external_id', $o['clientId'])->first();

//         if ($client) {
//             $orderDate = Carbon::createFromTimestampMs($o['createdAt']);

//             Order::updateOrCreate(
//                 ['external_order_id' => $o['id']], 
//                 [
//                     'client_id'      => $client->id,
//                     'details'        => $o['details'],
//                     'total_amount'         => $o['amount'],
//                     'payment_method' => $o['paymentMethod'],
//                     'payment_status' => $o['paymentStatus'],
//                     'stage'          => $o['orderStage'],
//                     'created_at'     => $orderDate,
//                     'updated_at'     => now(),
//                 ]
//             );
//         }
//     }

//     return "تم قراءة الملفات واستيراد البيانات بنجاح!";
// });