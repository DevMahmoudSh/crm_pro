<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Client;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        // تحديد بداية ونهاية الأسبوع الحالي
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $orders = Order::with('client')
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek]) // فلترة حسب التاريخ
            ->latest()
            ->get();

        $clients = Client::all();
        
        return view('orders.index', compact('orders', 'clients'));
    }

    public function archive()
    {
        $orders = Order::with('client')->latest()->get();
        $clients = Client::all();
        return view('orders.index', compact('orders', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'total_amount' => 'required|numeric',
            'stage' => 'required|in:pending,ready,received',
            'details' => 'nullable|string',
        ]);

        Order::create($request->all());

        return redirect()->back()->with('success', 'تم إضافة الطلب بنجاح');
    }

    // --- هذه هي الدالة التي كانت ناقصة ---
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'total_amount' => 'required|numeric',
            'stage' => 'required|in:pending,ready,received',
            'details' => 'nullable|string',
        ]);

        $order->update($request->all());

        return redirect()->back()->with('success', 'تم تحديث بيانات الطلب');
    }
    // --------------------------------------

    public function destroy($id)
    {
        Order::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'تم حذف الطلب');
    }
    public function ordersData(Request $request)
    {
        if ($request->ajax()) {
            $query = \App\Models\Order::with('client')->select('orders.*');

            if ($request->is_archive == '1') {
                // $query->where('stage', 'received'); 
            } else {
                $query->where('created_at', '>=', Carbon::now()->startOfWeek(Carbon::SATURDAY));
            }

            return DataTables::of($query)
                // --- إضافة فلتر البحث الذكي لحالة الطلب ---
                ->filterColumn('stage', function($query, $keyword) {
                    $query->where(function($q) use ($keyword) {
                        // 1. البحث بالاسم الإنجليزي في قاعدة البيانات (احتياطياً)
                        $q->where('orders.stage', 'LIKE', "%{$keyword}%");

                        // 2. البحث بالترجمة العربية بمرونة عالية
                        if (mb_strpos('قيد الانتظار', $keyword) !== false) {
                            $q->orWhere('orders.stage', 'pending');
                        }
                        if (mb_strpos('جاهز', $keyword) !== false) {
                            $q->orWhere('orders.stage', 'ready');
                        }
                        if (mb_strpos('تم الاستلام', $keyword) !== false) {
                            $q->orWhere('orders.stage', 'received');
                        }
                    });
                })
                // -----------------------------------------
                ->addColumn('id_formatted', function ($row) {
                    return '<span class="fw-bold">#' . $row->id . '</span>';
                })
                ->addColumn('client_info', function ($row) {
                    $name = $row->client->name ?? 'غير معروف';
                    return '<span class="fw-bold text-primary">' . $name . '</span>';
                })
                ->addColumn('details_formatted', function ($row) {
                    $details = Str::limit($row->details, 25) ?: '--';
                    return '<span class="text-muted small">' . $details . '</span>';
                })
                ->addColumn('amount', function ($row) {
                    return '<span class="fw-bold text-dark">$' . number_format($row->total_amount, 2) . '</span>';
                })
                ->addColumn('stage_badge', function ($row) {
                    $stage = $row->stage ?? 'pending';
                    if ($stage == 'pending') {
                        return '<span class="badge bg-warning text-dark px-3 py-2 rounded-pill">قيد الانتظار</span>';
                    } elseif ($stage == 'ready') {
                        return '<span class="badge bg-primary px-3 py-2 rounded-pill">جاهز</span>';
                    } elseif ($stage == 'received') {
                        return '<span class="badge bg-success px-3 py-2 rounded-pill">تم الاستلام</span>';
                    }
                    return $stage;
                })
                ->editColumn('created_at', function ($row) {
                    return '<span dir="ltr" class="text-secondary small">' . $row->created_at->format('Y-m-d | h:i') . '</span>';
                })
                ->addColumn('action', function ($row) {
                    $clientName = $row->client->name ?? '';
                    $clientPhone = $row->client->phone ?? '';
                    $fullClientName = htmlspecialchars($clientName . ' - (' . $clientPhone . ')', ENT_QUOTES, 'UTF-8');
                    $details = htmlspecialchars($row->details ?? '', ENT_QUOTES, 'UTF-8');
                    
                    $editBtn = '<button type="button" class="btn btn-sm btn-outline-info edit-order-btn rounded-circle"
                                    data-id="' . $row->id . '"
                                    data-client-id="' . $row->client_id . '" 
                                    data-client-name="' . $fullClientName . '" 
                                    data-amount="' . $row->total_amount . '"
                                    data-details="' . $details . '"
                                    data-stage="' . $row->stage . '">
                                    <i class="fas fa-edit"></i>
                                </button>';
                    
                    $deleteUrl = route('orders.destroy', $row->id);
                    $csrf = csrf_field();
                    $method = method_field('DELETE');
                    $deleteBtn = '<form action="' . $deleteUrl . '" method="POST" class="d-inline delete-order-form">
                                    ' . $csrf . ' ' . $method . '
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle confirm-delete-order">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                  </form>';

                    return '<div class="d-flex justify-content-center gap-2">' . $editBtn . $deleteBtn . '</div>';
                })
                ->rawColumns(['id_formatted', 'client_info', 'details_formatted', 'amount', 'stage_badge', 'created_at', 'action'])
                ->make(true);
        }
    }
}