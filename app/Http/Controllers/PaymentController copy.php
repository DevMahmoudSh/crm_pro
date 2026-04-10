<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
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

        return view('payments.index', compact('clients'));
        //return view('payments.history-payment-list-ready', compact('clients'));

        
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0.1',
            'method' => 'required|in:cash,wallet,app',
        ]);

        Payment::create($request->all());

        return redirect()->back()->with('success', 'تم تسجيل الدفعة بنجاح');
    }
    public function allPaymentsHistory()
    {
        // جلب المدفوعات مع بيانات العميل المرتبط بها (Eager Loading)
        // $payments = Payment::with('client')->latest()->paginate(20);
        $payments = Payment::with('client')->latest()->get();

        // إذا كان ملف الـ Blade يحتاج متغير $clients للفلترة مثلاً
        $clients = Client::withSum('orders', 'total_amount')
                         ->withSum('payments', 'amount')
                         ->with(['latestOrder', 'latestPayment'])
                         ->get();

        // تمرير المتغيرات للملف الموجود في resources/views/payments/index.blade.php
        return view('payments.history', compact('payments', 'clients'));
        //to redirect to statment of all clients
        //return view('payments.history-pay-list-ready', compact('payments', 'clients'));
        //to show payment page with option export to excel
        //return view('payments.history-excel-sup', compact('payments', 'clients'));
    }
    public function update(Request $request, $id)
    {
        // 1. التحقق من البيانات
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,wallet,app',
            'notes'  => 'nullable|string|max:255',
        ]);

        // 2. العثور على الدفعة وتحديثها
        $payment = \App\Models\Payment::findOrFail($id);
        
        $payment->update([
            'amount' => $request->amount,
            'method' => $request->method,
            'notes'  => $request->notes,
        ]);

        // 3. العودة مع رسالة نجاح
        return back()->with('success', 'تم تحديث بيانات الدفعة بنجاح');
    }
    public function destroy($id)
    {
        $payment = \App\Models\Payment::findOrFail($id);
        $payment->delete();

        return back()->with('success', 'تم حذف الدفعة بنجاح');
    }
}