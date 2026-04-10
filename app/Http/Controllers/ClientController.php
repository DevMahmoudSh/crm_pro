<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Client; // تأكد أن هذا السطر موجود
use Yajra\DataTables\Facades\DataTables; // <-- هذا السطر ضروري جداً جداً

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::latest()->get(); // استخدام latest() لترتيب الأحدث أولاً
        return view('clients.index', compact('clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:clients,name',
            'phone' => 'required|string|max:20',
        ]);

        Client::create($request->all());

        return redirect()->back()->with('success', 'تم إضافة الزبون');
    }

    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $client->update($request->all());

        return redirect()->back()->with('success', 'تم التحديث');
    }

    // --- هنا كان الخطأ وتم تصحيحه ---
    public function destroy($id)
    {
        // استبدلنا User بـ Client
        Client::findOrFail($id)->delete();
        
        return redirect()->back()->with('success', 'تم حذف الزبون');
    }
    
    // دالة العرض (Show) لعرض البروفايل (إذا كنت تحتاجها)
    public function show($id)
    {
        $client = Client::findOrFail($id);
        return view('clients.show', compact('client'));
    }
    // public function getClientsForSelect2(Request $request)
    // {
    //     $search = $request->search; // كلمة البحث التي يكتبها المستخدم

    //     $clients = Client::query()
    //         ->when($search, function ($query) use ($search) {
    //             // البحث بالاسم أو الهاتف
    //             $query->where('name', 'LIKE', "%{$search}%")
    //                 ->orWhere('phone', 'LIKE', "%{$search}%");
    //         })
    //         ->select('id', 'name', 'phone') // نختار الحقول المطلوبة فقط
    //         ->simplePaginate(5); // نجلب 10 نتائج فقط في كل مرة

    //     // تحويل البيانات للصيغة التي يفهمها Select2
    //     $formatted_clients = $clients->map(function ($client) {
    //         return [
    //             'id' => $client->id,
    //             'text' => $client->name . ' - (' . ($client->phone ?? 'لا يوجد رقم') . ')'
    //         ];
    //     });

    //     return response()->json([
    //         'results' => $formatted_clients,
    //         'pagination' => [
    //             'more' => $clients->hasMorePages() // هل توجد صفحات أخرى؟
    //         ]
    //     ]);
    // }
    public function getClientsForSelect2(Request $request)
{
    $search = $request->search;

    $clients = Client::query()
        ->when($search, function ($query) use ($search) {
            // 1. Split the search string into individual words
            $words = array_filter(explode(' ', $search));

            // 2. Wrap in a where() closure to group the logic (important for OR queries)
            $query->where(function ($subQuery) use ($words) {
                foreach ($words as $word) {
                    // This ensures every word must exist in either the name OR the phone
                    $subQuery->where(function ($wordQuery) use ($word) {
                        $wordQuery->where('name', 'LIKE', "%{$word}%")
                                  ->orWhere('phone', 'LIKE', "%{$word}%");
                    });
                }
            });
        })
        ->select('id', 'name', 'phone')
        ->simplePaginate(10); // increased to 10 for better UX

    $formatted_clients = $clients->map(function ($client) {
        return [
            'id' => $client->id,
            'text' => $client->name . ' - (' . ($client->phone ?? 'لا يوجد رقم') . ')'
        ];
    });

    return response()->json([
        'results' => $formatted_clients,
        'pagination' => [
            'more' => $clients->hasMorePages()
        ]
    ]);
}

    // public function statement($id)
    // {
    //     $client = Client::findOrFail($id);

    //     // 1. جلب الطلبات (Debit - عليه)
    //     $orders = $client->orders()
    //         ->select('id', 'created_at', 'total_amount as amount', 'details')
    //         ->get()
    //         ->map(function ($order) {
    //             $order->type = 'order'; // تمييز النوع
    //             return $order;
    //         });

    //     // 2. جلب المدفوعات (Credit - له)
    //     $payments = $client->payments()
    //         ->select('id', 'created_at', 'amount', 'notes', 'method')
    //         ->get()
    //         ->map(function ($payment) {
    //             $payment->type = 'payment'; // تمييز النوع
    //             return $payment;
    //         });

    //     // 3. دمج المصفوفتين وترتيبهم حسب التاريخ (الأقدم فالأحدث)
    //     $transactions = $orders->merge($payments)->sortBy('created_at');

    //     return view('clients.statement', compact('client', 'transactions'));
    // }
    public function statement($id)
    {
        $client = Client::findOrFail($id);

        // 1. جلب الطلبات (Debit - عليه)
        $orders = $client->orders()
            ->select('id', 'created_at', 'total_amount as debit', 'details')
            ->get()
            ->map(function ($order) {
                $order->type = 'order';
                $order->credit = 0; // لا يوجد دفع في سطر الطلب
                $order->description = "طلب رقم #" . $order->id . " (" . $order->details . ")";
                return $order;
            });

        // 2. جلب المدفوعات (Credit - له)
        $payments = $client->payments()
            ->select('id', 'created_at', 'amount as credit', 'notes', 'method')
            ->get()
            ->map(function ($payment) {
                $payment->type = 'payment';
                $payment->debit = 0; // لا يوجد مديونية في سطر الدفع
                $payment->description = "دفعة مالية - " . $payment->method . " (" . $payment->notes . ")";
                return $payment;
            });

        // 3. الدمج باستخدام values() لتجنب تضارب الـ IDs وترتيبهم
        $transactions = $orders->concat($payments)->sortBy('created_at')->values();

        // 4. حساب الرصيد التراكمي (Running Balance)
        $runningBalance = 0;
        foreach ($transactions as $transaction) {
            $runningBalance += ($transaction->debit - $transaction->credit);
            $transaction->balance = $runningBalance;
        }

        return view('clients.statement', compact('client', 'transactions'));
    }
    public function clientsData(Request $request)
    {
        if ($request->ajax()) {
            $data = Client::select('*'); // جلب كل الزبائن

            return DataTables::of($data)
                ->addIndexColumn() // هذه الدالة تولد الترقيم التلقائي # (DT_RowIndex)
                ->editColumn('phone', function ($row) {
                    return $row->phone ?? '-';
                })
                ->editColumn('created_at', function ($row) {
                    return '<span dir="ltr" class="small text-muted">' . $row->created_at->format('h:i | Y-m-d') . '</span>';
                })
                ->addColumn('action', function ($row) {
                    // زر التعديل
                    $editBtn = '<button type="button" class="btn btn-sm btn-outline-info edit-btn"
                                    data-id="' . $row->id . '"
                                    data-name="' . htmlspecialchars($row->name) . '"
                                    data-phone="' . htmlspecialchars($row->phone) . '">
                                    <i class="fas fa-edit"></i>
                                </button>';

                    // زر الحذف
                    $deleteUrl = route('clients.destroy', $row->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');
                    $deleteBtn = '<form action="' . $deleteUrl . '" method="POST" class="d-inline delete-form">
                                    ' . $csrf . ' ' . $method . '
                                    <button type="button" class="btn btn-sm btn-outline-danger confirm-delete-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>';

                    return '<div class="d-flex justify-content-center gap-2">' . $editBtn . $deleteBtn . '</div>';
                })
                ->rawColumns(['created_at', 'action']) // للسماح بعرض الـ HTML داخل الجدول
                ->make(true);
        }
    }
}