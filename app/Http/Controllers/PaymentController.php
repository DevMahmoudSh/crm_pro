<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables; 

class PaymentController extends Controller
{
    public function index()
    {
        $clients = Client::withSum('orders', 'total_amount')
                         ->withSum('payments', 'amount')
                         ->with(['latestOrder', 'latestPayment'])
                         ->get();

        return view('payments.index', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount'    => 'required|numeric|min:0.1',
            'method'    => 'required|in:cash,wallet,app',
            'notes'     => 'nullable|string|max:255',
        ]);

        Payment::create($request->all());

        return redirect()->back()->with('success', 'تم تسجيل الدفعة بنجاح');
    }

    public function allPaymentsHistory()
    {
        $payments = Payment::with('client')->latest()->get();
        $clients = Client::withSum('orders', 'total_amount')
                         ->withSum('payments', 'amount')
                         ->with(['latestOrder', 'latestPayment'])
                         ->get();

        return view('payments.history', compact('payments', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,wallet,app',
            'notes'  => 'nullable|string|max:255',
        ]);

        $payment = Payment::findOrFail($id);
        $payment->update($request->only('amount', 'method', 'notes'));

        return back()->with('success', 'تم تحديث بيانات الدفعة بنجاح');
    }

    public function destroy($id)
    {
        Payment::findOrFail($id)->delete();
        return back()->with('success', 'تم حذف الدفعة بنجاح');
    }

    /**
     * جلب بيانات سجل المدفوعات التفصيلي
     */
    public function historyData(Request $request)
    {
        if ($request->ajax()) {
            $data = Payment::with('client')->select('payments.*');

            return DataTables::of($data)
                ->filterColumn('client.name', function($query, $keyword) {
                    $query->whereHas('client', function($q) use ($keyword) {
                        $q->where('name', 'LIKE', "%{$keyword}%")
                          ->orWhere('phone', 'LIKE', "%{$keyword}%");
                    });
                })
                ->filterColumn('method', function($query, $keyword) {
                    $query->where(function($q) use ($keyword) {
                        if (mb_strpos('كاش', $keyword) !== false) $q->orWhere('method', 'cash');
                        if (mb_strpos('محفظة', $keyword) !== false) $q->orWhere('method', 'wallet');
                        if (mb_strpos('تطبيق', $keyword) !== false) $q->orWhere('method', 'app');
                        $q->orWhere('method', 'LIKE', "%{$keyword}%");
                    });
                })
                ->addColumn('client_info', function ($row) {
                    $name = $row->client->name ?? 'عميل غير معروف';
                    $phone = $row->client->phone ?? '';
                    return '<div class="fw-bold">' . $name . '</div><small class="text-muted ms-1">(' . $phone . ')</small>';
                })
                ->addColumn('method_badge', function ($row) {
                    $methodClass = ['wallet' => 'bg-primary', 'cash' => 'bg-info text-dark', 'app' => 'bg-raspberry text-white'][$row->method] ?? 'bg-secondary';
                    $methodName = ['wallet' => 'محفظة', 'cash' => 'كاش', 'app' => 'تطبيق'][$row->method] ?? $row->method;
                    return '<span class="badge ' . $methodClass . '" style="font-size: 14px">' . $methodName . '</span>';
                })
                ->editColumn('amount', function ($row) {
                    return '<span class="text-success fw-bold">' . number_format($row->amount, 2) . '</span>';
                })
                ->editColumn('created_at', function ($row) {
                    return '<span dir="ltr" class="small">' . $row->created_at->format('Y-m-d H:i') . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $clientName = $row->client->name ?? 'غير معروف';
                    $editBtn = '<button class="btn btn-outline-primary btn-action edit-payment-btn" 
                                    data-id="'.$row->id.'" 
                                    data-amount="'.$row->amount.'" 
                                    data-method="'.$row->method.'" 
                                    data-notes="'.htmlspecialchars($row->notes ?? '').'" 
                                    data-client-name="'.htmlspecialchars($clientName).'">
                                    <i class="fas fa-edit"></i>
                                </button>';
                    
                    $deleteUrl = route('payments.destroy', $row->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');
                    $deleteBtn = '<form action="'.$deleteUrl.'" method="POST" class="d-inline delete-form">
                                    '.$csrf.' '.$method.'
                                    <button type="button" class="btn btn-outline-danger btn-action confirm-delete-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                  </form>';

                    return '<div class="d-flex justify-content-center gap-2">' . $editBtn . $deleteBtn . '</div>';
                })
                ->rawColumns(['client_info', 'method_badge', 'amount', 'created_at', 'action'])
                ->make(true);
        }
    }

    /**
     * جلب ملخص أرصدة العملاء
     */
    public function summaryData(Request $request)
    {
        if ($request->ajax()) {
            $data = Client::withSum('orders', 'total_amount')
                          ->withSum('payments', 'amount')
                          ->with(['latestOrder', 'latestPayment']);

            return DataTables::of($data)
                ->filterColumn('name', function($query, $keyword) {
                    $query->where('name', 'LIKE', "%{$keyword}%")
                          ->orWhere('phone', 'LIKE', "%{$keyword}%");
                })
                ->addColumn('client_info', function ($row) {
                    return '<div class="d-flex align-items-center justify-content-center">
                                <div class="bg-light rounded-circle p-2 me-2 text-dark"><i class="fas fa-user-tie"></i></div>
                                <span class="fw-bold">' . $row->name . '</span>
                                <small class="text-muted ms-1">(' . $row->phone . ')</small>
                            </div>';
                })
                ->addColumn('total_orders', function ($row) {
                    $total = $row->orders_sum_total_amount ?? 0;
                    return '<span class="text-danger fw-bold">' . number_format($total, 2) . '</span>';
                })
                ->addColumn('total_payments', function ($row) {
                    $total = $row->payments_sum_amount ?? 0;
                    return '<span class="text-success fw-bold">' . number_format($total, 2) . '</span>';
                })
                ->addColumn('balance_badge', function ($row) {
                    $orders = $row->orders_sum_total_amount ?? 0;
                    $payments = $row->payments_sum_amount ?? 0;
                    $balance = $orders - $payments; // حساب الرصيد (الطلبات - المدفوعات)

                    // إذا كان الرصيد أكبر من صفر (العميل عليه ديون) -> أحمر احترافي
                    if ($balance > 0) {
                        return '<span class="badge rounded-pill px-3 py-2" style="background-color: #b47171; color: rgb(255, 255, 255); border: 1px solid #dc3545; font-size: 13px; font-weight: bold; min-width: 80px;">' . number_format($balance, 2) . '</span>';
                    } 
                    // إذا كان الرصيد أقل من صفر (العميل له رصيد إضافي) -> أخضر احترافي
                    elseif ($balance < 0) {
                        return '<span class="badge rounded-pill px-3 py-2" style="background-color: #eafbee; color: #198754; border: 1px solid #198754; font-size: 13px; font-weight: bold; min-width: 80px;">' . number_format(abs($balance), 2) . '</span>';
                    } 
                    // إذا كان الرصيد صفر
                    else {
                        return '<span class="badge rounded-pill px-3 py-2 bg-light text-dark border" style="font-size: 13px; font-weight: bold; min-width: 80px;">0.00</span>';
                    }
                })
                // إضافة منطق الترتيب (Sorting) الخاص بـ DataTables للرصيد
                ->orderColumn('balance', function ($query, $order) {
                    // الترتيب باستخدام ناتج طرح (إجمالي الطلبات - إجمالي المدفوعات)
                    // $query->orderByRaw('(IFNULL(orders_sum_total_amount, 0) - IFNULL(payments_sum_amount, 0)) ' . $order);
                    $query->orderByRaw('(COALESCE((SELECT SUM(total_amount) FROM orders WHERE orders.client_id = clients.id), 0) - COALESCE((SELECT SUM(amount) FROM payments WHERE payments.client_id = clients.id), 0)) DESC');
                })
                ->addColumn('last_transaction', function ($row) {
                    $last_order = $row->latestOrder ? $row->latestOrder->created_at : null;
                    $last_payment = $row->latestPayment ? $row->latestPayment->created_at : null;
                    $last_trans = ($last_order > $last_payment) ? $last_order : ($last_payment ?? $last_order);
                    
                    return $last_trans ? '<span dir="ltr" class="text-secondary small">' . $last_trans->format('Y:m:d H:i') . '</span>' : '--';
                })
                ->addColumn('action', function ($row) {
                    $url = route('clients.statement', $row->id);
                    return '<a href="'.$url.'" class="btn btn-sm btn-outline-secondary" title="كشف حساب" target="_blank"><i class="fas fa-file-invoice"></i></a>';
                })
                ->rawColumns(['client_info', 'total_orders', 'total_payments', 'balance_badge', 'last_transaction', 'action'])
                ->make(true);
        }
    }
}