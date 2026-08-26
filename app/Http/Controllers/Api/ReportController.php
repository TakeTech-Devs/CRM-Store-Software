<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function storeWiseMonthlySales(Request $request){
        $month = $request->input('month');
        $storeId = $request->input('store_name');
        \Log::info('Month: ' . $month);
        \Log::info('Store ID: ' . $storeId);

        $salesData = StoreAssign::select(
                'created_at as billing_date',
                'assign_bill_number as invoice_no',
                'store_id',
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                'total as amount'
            )
            ->where('store_id', $storeId)
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$month])
            ->get();

        if ($salesData->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No assignment found for the selected store and month.',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $salesData,
        ]);
    }

    public function getDoctors(){
        $doctors = DB::table('doctor')->select('id', 'name')->get();

        if ($doctors->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No doctors found.',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $doctors
        ], 200);
    }

    public function doctorWiseReport(Request $request){
        $doctorId = $request->input('doctor_name');
    
        $doctorDetails = DB::table('doctor')
            ->where('id', $doctorId) 
            ->select('name as doctor_name', 'phone as doctor_mobile') 
            ->first();
    
        $doctorBillingData = [];
    
        $staffBilling = DB::table('staff_billing')
            ->where('doctor_name', $doctorId)
            ->where('total_amt', '>', 0)
            ->get();
    
        foreach ($staffBilling as $billing) {
            $product = DB::table('staff_product_billing')
                ->join('product', 'staff_product_billing.productId', '=', 'product.id')
                ->where('staff_product_billing.cb_id', $billing->id)
                ->select('product.product_name', 'product.hsn_code')
                ->first();
    
            $staffPhone = DB::table('staff')
                ->where('id', $billing->staff_phone) 
                ->select('phone as staff_phone')
                ->first();
    
            $doctorBillingData[] = [
                'doctor_name' => $doctorDetails->doctor_name,
                'doctor_mobile' => $doctorDetails->doctor_mobile,
                'billing_date' => $billing->billing_date,
                'invoiceNo' => $billing->invoiceNo,
                'staff_name' => $billing->staff_name,
                'staff_phone' => $staffPhone->staff_phone ?? null, 
                'paymentType' => $billing->paymentType,
                'total_amt' => $billing->total_amt,
                'product_name' => $product->product_name ?? null, 
                'hsn_code' => $product->hsn_code ?? null, 
            ];
        }
    
        $customerBilling = DB::table('customer_billing')
            ->where('doctor_name', $doctorId)
            ->where('total_amt', '>', 0)
            ->get();
    
        foreach ($customerBilling as $billing) {
            $product = DB::table('customer_product_billing')
                ->join('product', 'customer_product_billing.productId', '=', 'product.id')
                ->where('customer_product_billing.cb_id', $billing->id)
                ->select('product.product_name', 'product.hsn_code')
                ->first();
    
            $customerPhone = DB::table('customer')
                ->where('id', $billing->customer_phone) 
                ->select('phone as customer_phone')
                ->first();
    
            $doctorBillingData[] = [
                'doctor_name' => $doctorDetails->doctor_name,
                'doctor_mobile' => $doctorDetails->doctor_mobile,
                'billing_date' => $billing->billing_date,
                'invoiceNo' => $billing->invoiceNo,
                'customer_name' => $billing->customer_name,
                'customer_phone' => $customerPhone->customer_phone ?? null, 
                'paymentType' => $billing->paymentType,
                'total_amt' => $billing->total_amt,
                'product_name' => $product->product_name ?? null, 
                'hsn_code' => $product->hsn_code ?? null, 
            ];
        }
    
        // Summary + monthly trend for the cards/chart — computed straight from the
        // billing header tables (not the line-item loops above), since a bill with
        // several product lines would otherwise get its total_amt counted once per
        // line item instead of once per bill.
        $storeMetaId = session('storeId');
        $store = DB::table('store')->where('store_meta_id', $storeMetaId)->first();
        $storeId = $store?->id;

        $billStats = function ($from = null, $to = null) use ($doctorId, $storeId) {
            $customerQuery = DB::table('customer_billing')->where('doctor_name', $doctorId)->where('total_amt', '>', 0);
            $staffQuery = DB::table('staff_billing')->where('doctor_name', $doctorId)->where('total_amt', '>', 0);
            if ($storeId) {
                $customerQuery->where('store_id', $storeId);
                $staffQuery->where('store_id', $storeId);
            }
            if ($from && $to) {
                $customerQuery->whereBetween('billing_date', [$from, $to]);
                $staffQuery->whereBetween('billing_date', [$from, $to]);
            }
            $customerRows = $customerQuery->get(['customer_phone', 'total_amt']);
            $staffRows = $staffQuery->get(['staff_phone', 'total_amt']);

            $patientCount = $customerRows->pluck('customer_phone')
                ->merge($staffRows->pluck('staff_phone'))
                ->filter()
                ->unique()
                ->count();

            return [
                'patient_count' => $patientCount,
                'bill_amount' => round($customerRows->sum('total_amt') + $staffRows->sum('total_amt'), 2),
            ];
        };

        $summary = $billStats();

        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $label = $m->format('M Y');
            $mStart = $m->startOfMonth()->toDateString();
            $mEnd   = $m->endOfMonth()->toDateString();
            $stats = $billStats($mStart, $mEnd);
            $monthlyTrend[] = [
                'month' => $label,
                'patient_count' => $stats['patient_count'],
                'bill_amount' => $stats['bill_amount'],
            ];
        }

        if (empty($doctorBillingData)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No billing data found for the selected doctor.',
                'data' => [],
                'summary' => $summary,
                'trend' => $monthlyTrend,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $doctorBillingData,
            'summary' => $summary,
            'trend' => $monthlyTrend,
        ]);
    }

    public function getCumulativeSalesReport(Request $request){
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        $cumulativeReport = [
            'total_sales_amount' => 0,
            'cash_payments'      => 0,
            'card_payments'      => 0,
            'online_payments'    => 0,
            'transactions'       => []
        ];

        // A bill fully reduced to nothing via a same-day edit still exists as a
        // record (rewrite-in-place, not deleted) but represents no real sale —
        // exclude it from reporting, same as every other report below.
        $staffQuery = DB::table('staff_billing')->where('total_amt', '>', 0);
        if ($startDate) $staffQuery->where('billing_date', '>=', $startDate);
        if ($endDate)   $staffQuery->where('billing_date', '<=', $endDate);
        $staffBillingData = $staffQuery->get();

        $customerQuery = DB::table('customer_billing')->where('total_amt', '>', 0);
        if ($startDate) $customerQuery->where('billing_date', '>=', $startDate);
        if ($endDate)   $customerQuery->where('billing_date', '<=', $endDate);
        $customerBillingData = $customerQuery->get();

        foreach ($staffBillingData as $billing) {
            $paymentType = strtolower($billing->paymentType);
            $amt = (float) $billing->total_amt;
            $cumulativeReport['total_sales_amount'] += $amt;

            if ($paymentType === 'cash') {
                $cumulativeReport['cash_payments'] += $amt;
            } elseif ($paymentType === 'card') {
                $cumulativeReport['card_payments'] += $amt;
            } elseif ($paymentType === 'online') {
                $cumulativeReport['online_payments'] += $amt;
            }

            $cumulativeReport['transactions'][] = [
                'type'        => 'Staff',
                'date'        => $billing->billing_date,
                'invoiceNo'   => $billing->invoiceNo,
                'name'        => $billing->staff_name,
                'phone'       => $billing->staff_phone,
                'sales_amount'=> $amt,
                'payment_type'=> $billing->paymentType,
            ];
        }

        foreach ($customerBillingData as $billing) {
            $paymentType = strtolower($billing->paymentType);
            $amt = (float) $billing->total_amt;
            $cumulativeReport['total_sales_amount'] += $amt;

            if ($paymentType === 'cash') {
                $cumulativeReport['cash_payments'] += $amt;
            } elseif ($paymentType === 'card') {
                $cumulativeReport['card_payments'] += $amt;
            } elseif ($paymentType === 'online') {
                $cumulativeReport['online_payments'] += $amt;
            }

            $cumulativeReport['transactions'][] = [
                'type'        => 'Customer',
                'date'        => $billing->billing_date,
                'invoiceNo'   => $billing->invoiceNo,
                'name'        => $billing->customer_name,
                'phone'       => $billing->customer_phone,
                'sales_amount'=> $amt,
                'payment_type'=> $billing->paymentType,
            ];
        }

        // Sort all transactions by date descending
        usort($cumulativeReport['transactions'], fn($a, $b) => strcmp($b['date'], $a['date']));

        return response()->json([
            'status' => 'success',
            'data'   => $cumulativeReport
        ]);
    }

    public function expiryReport(){
        try {
            $PurchaseStockEntryList = DB::table('purchase_request')
                ->join('product', 'purchase_request.product_id', '=', 'product.id')
                ->select('purchase_request.*', 'product.product_name as product_name')
                ->get();
    
            return response()->json([
                'status' => 200,
                'data' => $PurchaseStockEntryList
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching the data.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    
    public function gstReport(Request $request) {
        // Fetch the billing data from customer_product_billing
        $customerBills = DB::table('customer_product_billing as cpb')
            ->join('customer_billing as cb', 'cpb.cb_id', '=', 'cb.id')
            ->join('product as p', 'cpb.productId', '=', 'p.id')
            ->where('cb.total_amt', '>', 0)
            ->select('cb.billing_date', 'p.hsn_code', 'p.gst', DB::raw('SUM(cb.total_amt) as total_amount'))
            ->groupBy('cb.billing_date', 'p.hsn_code', 'p.gst');

        // Fetch the billing data from staff_product_billing
        $staffBills = DB::table('staff_product_billing as cpb')
            ->join('staff_billing as cb', 'cpb.cb_id', '=', 'cb.id')
            ->join('product as p', 'cpb.productId', '=', 'p.id')
            ->where('cb.total_amt', '>', 0)
            ->select('cb.billing_date', 'p.hsn_code', 'p.gst', DB::raw('SUM(cb.total_amt) as total_amount'))
            ->groupBy('cb.billing_date', 'p.hsn_code', 'p.gst');
    
        // Combine both queries using union
        $bills = $customerBills->union($staffBills)->get();
    
        // Map over the combined results to calculate GST
        $gstReport = $bills->map(function ($bill) {
            // Log the values for debugging
            \Log::info('Bill Data', [
                'billing_date' => $bill->billing_date,
                'hsn_code' => $bill->hsn_code,
                'gst' => $bill->gst,
                'total_amount' => $bill->total_amount,
            ]);
    
            // Ensure values are numeric to avoid NaN
            $cgst = ($bill->gst ?? 0) / 2;
            $sgst = ($bill->gst ?? 0) / 2;
            $totalGst = (($bill->total_amount ?? 0) * ($bill->gst ?? 0)) / 100;
    
            return [
                'billing_date' => $bill->billing_date,
                'hsn_code' => $bill->hsn_code,
                'gst' => $bill->gst,
                'cgst' => number_format($cgst, 2),
                'sgst' => number_format($sgst, 2),
                'total_gst' => number_format($totalGst, 2),
                'total_amount' => number_format($bill->total_amount, 2),
            ];
        });
    
        return response()->json([
            'status' => 'success',
            'data' => $gstReport,
        ]);
    }

    public function stockReport(){
        try {
            $stockData = DB::table('purchase_request as pse')
                ->join('product as p', 'pse.product_id', '=', 'p.id')
                ->join('brand as b', 'pse.brand_id', '=', 'b.id')
                ->join('category as c', 'p.category_id', '=', 'c.id')
                ->join('sub_category as sc', 'p.sub_category_id', '=', 'sc.id')
                ->join('pack as pk', 'pse.pack_id', '=', 'pk.id')
                ->join('price as pr', 'pse.price_id', '=', 'pr.id')
                ->select(
                    'p.product_name',
                    'b.brand_name',
                    'c.category_name',
                    'sc.sub_category_name',
                    'pk.pack_name',
                    'pr.price_name',
                    DB::raw('SUM(pse.qty) as qty'),
                    'pse.exp_date',
                    'p.hsn_code',
                    'p.gst',
                    DB::raw('MIN(pse.created_at) as created_at')
                )
                ->groupBy(
                    'p.product_name',
                    'b.brand_name',
                    'c.category_name',
                    'sc.sub_category_name',
                    'pk.pack_name',
                    'pr.price_name',
                    'pse.exp_date',
                    'p.hsn_code',
                    'p.gst'
                )
                ->orderBy('p.product_name', 'asc')
                ->get();

            return response()->json([
                'status' => 200,
                'data' => $stockData
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while fetching the stock data.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    

    public function yesterdaySale()
    {
        $yesterday = now()->subDay()->toDateString();

        $customerSales = DB::table('customer_billing')
            ->whereDate('billing_date', $yesterday)
            ->sum('total_amt');

        $staffSales = DB::table('staff_billing')
            ->whereDate('billing_date', $yesterday)
            ->sum('total_amt');

        $totalSales = $customerSales + $staffSales;

        return response()->json([
            'status' => 'success',
            'data' => $totalSales
        ]);
    }

    public function todaySale()
    {
        $today = now()->toDateString();

        $customerSales = DB::table('customer_billing')
            ->whereDate('billing_date', $today)
            ->sum('total_amt');

        $staffSales = DB::table('staff_billing')
            ->whereDate('billing_date', $today)
            ->sum('total_amt');

        $totalSales = $customerSales + $staffSales;

        return response()->json([
            'status' => 'success',
            'data' => $totalSales
        ]);
    }

    public function zeroStockMedicine()
    {
        $zeroStockProducts = DB::table('purchase_request')
            ->select('product_id')
            ->groupBy('product_id')
            ->havingRaw('SUM(qty) = 0')
            ->get();

        $productNames = [];
        foreach ($zeroStockProducts as $product) {
            $productName = DB::table('product')->where('id', $product->product_id)->value('product_name');
            if ($productName) {
                $productNames[] = $productName;
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'count' => count($productNames),
                'products' => $productNames
            ]
        ]);
    }

    public function monthlyEarnings(Request $request)
    {
        $storeMetaId = session('storeId');
        $store = DB::table('store')->where('store_meta_id', $storeMetaId)->first();
        $storeId = $store?->id;

        $query = function ($table) use ($storeId) {
            $q = DB::table($table)
                ->whereYear('billing_date', now()->year)
                ->whereMonth('billing_date', now()->month);
            if ($storeId) $q->where('store_id', $storeId);
            return $q->sum('total_amt');
        };

        $totalEarnings = $query('customer_billing') + $query('staff_billing');

        return response()->json([
            'status' => 'success',
            'data' => $totalEarnings
        ]);
    }

    public function analyticsData(Request $request)
    {
        $storeMetaId = session('storeId');
        $store = DB::table('store')->where('store_meta_id', $storeMetaId)->first();
        $storeId = $store?->id;

        $today = now()->toDateString();
        $weekStart = now()->startOfWeek()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        // Sales overview — customer + staff combined
        $salesQuery = function ($from, $to) use ($storeId) {
            $c = DB::table('customer_billing')
                ->where('store_id', $storeId)
                ->whereBetween('billing_date', [$from, $to])
                ->sum('total_amt');
            $s = DB::table('staff_billing')
                ->where('store_id', $storeId)
                ->whereBetween('billing_date', [$from, $to])
                ->sum('total_amt');
            return round($c + $s, 2);
        };

        // Use whereYear/whereMonth for the month total to avoid timezone edge cases
        $monthCustomer = DB::table('customer_billing')
            ->where('store_id', $storeId)
            ->whereYear('billing_date', now()->year)
            ->whereMonth('billing_date', now()->month)
            ->sum('total_amt');
        $monthStaff = DB::table('staff_billing')
            ->where('store_id', $storeId)
            ->whereYear('billing_date', now()->year)
            ->whereMonth('billing_date', now()->month)
            ->sum('total_amt');

        $salesOverview = [
            'today'  => $salesQuery($today, $today),
            'week'   => $salesQuery($weekStart, $today),
            'month'  => round($monthCustomer + $monthStaff, 2),
        ];

        // Monthly trend — last 6 months
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = now()->subMonths($i);
            $label = $m->format('M Y');
            $mStart = $m->startOfMonth()->toDateString();
            $mEnd   = $m->endOfMonth()->toDateString();
            $monthlyTrend[] = [
                'month' => $label,
                'amount' => $salesQuery($mStart, $mEnd),
            ];
        }

        // Top 5 products by qty sold (regular)
        $topProducts = DB::table('customer_product_billing as cpb')
            ->join('product', 'cpb.productId', '=', 'product.id')
            ->join('customer_billing as cb', 'cpb.cb_id', '=', 'cb.id')
            ->where('cb.store_id', $storeId)
            ->selectRaw('product.product_name, SUM(cpb.qty) as total_qty')
            ->groupBy('product.product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Payment breakdown
        $paymentBreakdown = DB::table('customer_billing')
            ->where('store_id', $storeId)
            ->where('total_amt', '>', 0)
            ->selectRaw('paymentType, COUNT(*) as count, SUM(total_amt) as total')
            ->groupBy('paymentType')
            ->get();

        // Doctor-wise sales (top 5)
        $doctorSales = DB::table('customer_billing as cb')
            ->join('doctor', 'cb.doctor_name', '=', 'doctor.id')
            ->where('cb.store_id', $storeId)
            ->where('cb.total_amt', '>', 0)
            ->whereNotNull('cb.doctor_name')
            ->selectRaw('doctor.name as doctor_name, COUNT(*) as bill_count, SUM(cb.total_amt) as total_amt')
            ->groupBy('doctor.name')
            ->orderByDesc('total_amt')
            ->limit(5)
            ->get();

        // Inhouse vs Regular (by qty sold)
        $regularQty = DB::table('customer_product_billing as cpb')
            ->join('customer_billing as cb', 'cpb.cb_id', '=', 'cb.id')
            ->where('cb.store_id', $storeId)
            ->whereNotNull('cpb.productId')
            ->sum('cpb.qty');

        $inhouseQty = DB::table('customer_product_billing as cpb')
            ->join('customer_billing as cb', 'cpb.cb_id', '=', 'cb.id')
            ->where('cb.store_id', $storeId)
            ->whereNotNull('cpb.inhouse_product_id')
            ->sum('cpb.qty');

        // Expiry track — medicines expiring in next 90 days or already expired
        $expiryTrack = DB::table('purchase_request as pr')
            ->join('store_assign', 'pr.store_assign_id', '=', 'store_assign.id')
            ->join('product', 'pr.product_id', '=', 'product.id')
            ->join('pack', 'pr.pack_id', '=', 'pack.id')
            ->where('store_assign.store_id', $storeId)
            ->where('pr.qty', '>', 0)
            ->whereNotNull('pr.exp_date')
            ->where('pr.exp_date', '<=', now()->addDays(90)->toDateString())
            ->select('product.product_name', 'pack.pack_name', 'pr.qty', DB::raw('pr.exp_date as expiry_date'))
            ->orderBy('pr.exp_date')
            ->limit(20)
            ->get();

        return response()->json([
            'status' => 200,
            'data' => [
                'sales_overview'   => $salesOverview,
                'monthly_trend'    => $monthlyTrend,
                'top_products'     => $topProducts,
                'payment_breakdown'=> $paymentBreakdown,
                'doctor_sales'     => $doctorSales,
                'inhouse_vs_regular' => ['regular' => (int)$regularQty, 'inhouse' => (int)$inhouseQty],
                'expiry_track'     => $expiryTrack,
            ]
        ]);
    }
}
