<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Exports\ClientExport;
use App\Http\Controllers\ClientJController;


Route::get('/', function () {
    return redirect('/dashboard');
});

Route::resource('dashboard', DashboardController::class);



// مسارات الزبائن
Route::resource('clients', ClientController::class);
// رابط لجلب العملاء للبحث والتقليب
Route::get('/ajax/clients', [ClientController::class, 'getClientsForSelect2'])->name('clients.select2');

// يفضل وضعه قبل Route::resource إذا وجد
Route::get('/orders/archive', [OrderController::class, 'archive'])->name('orders.archive');
// مسارات الطلبات
Route::resource('orders', OrderController::class);

Route::get('/payments/history', [PaymentController::class, 'allPaymentsHistory'])->name('payments.history');
Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
// مسارات المدفوعات (سنحتاج فقط التخزين والحذف غالباً)
Route::resource('payments', PaymentController::class)->only(['store', 'destroy']);

// 1. مسار عرض صفحة المدفوعات والجدول
Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');

// 2. مسار حفظ الدفعة الجديدة (عند ضغط زر الحفظ في المودال)
Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

// ملاحظة: مسار البحث (AJAX) قمنا بإضافته سابقاً، تأكد أنه موجود:
// Route::get('/ajax/clients', [ClientController::class, 'getClientsForSelect2'])->name('clients.select2');

// رابط كشف الحساب
Route::get('/clients/{id}/statement', [ClientController::class, 'statement'])->name('clients.statement');

// Route::get('/export-excel', [ClientExport::class, 'exportExcel'])->name('export.excel');

// راوت لاستقبال الداتا وتخزينها
// Route::post('/clients/import', [ClientController::class, 'storeClients']);

// راوت بسيط لتجربة الكود (للعرض فقط)
// Route::get('/test-import', [ClientJController::class, 'storeClients']);





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