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
    
        if (empty($doctorBillingData)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No billing data found for the selected doctor.',
                'data' => []
            ]);
        }
    
        return response()->json([
            'status' => 'success',
            'data' => $doctorBillingData,
        ]);
    }

    public function getCumulativeSalesReport(Request $request){
        $cumulativeReport = [
            'total_sales_amount' => 0,
            'cash_payments' => 0,
            'card_payments' => 0,
            'online_payments' => 0,
            'transactions' => []
        ];

        $cashSources = [
            'God' => [],
            'Doctor' => [],
            'Staff' => [],
            'Diary' => []
        ];

        $staffBillingData = DB::table('staff_billing')
            ->leftJoin('staff_product_billing', 'staff_billing.id', '=', 'staff_product_billing.cb_id')
            ->leftJoin('product', 'staff_product_billing.productId', '=', 'product.id')
            ->select('staff_billing.*', 'product.product_name', 'product.hsn_code')
            ->get();

        $customerBillingData = DB::table('customer_billing')
            ->leftJoin('customer_product_billing', 'customer_billing.id', '=', 'customer_product_billing.cb_id')
            ->leftJoin('product', 'customer_product_billing.productId', '=', 'product.id')
            ->select('customer_billing.*', 'product.product_name', 'product.hsn_code')
            ->get();

        foreach ($staffBillingData as $billing) {
            $paymentType = strtolower($billing->paymentType);
            $cumulativeReport['total_sales_amount'] += $billing->total_amt;

            if ($paymentType === 'cash') {
                $cumulativeReport['cash_payments'] += $billing->total_amt;
                $cashSources['Doctor'][] = $billing; 
            }elseif ($paymentType === 'offline') {
                $cumulativeReport['card_payments'] += $billing->total_amt;
            }elseif ($paymentType === 'card') {
                $cumulativeReport['card_payments'] += $billing->total_amt;
            } elseif ($paymentType === 'online') {
                $cumulativeReport['online_payments'] += $billing->total_amt;
            }

            $cumulativeReport['transactions'][] = [
                'date' => $billing->billing_date,
                'invoiceNo' => $billing->invoiceNo,
                'staff_name' => $billing->staff_name,
                'staff_phone' => $billing->staff_phone,
                'doctor_name' => $billing->doctor_name,
                // 'doctor_mobile' => $billing->doctor_mobile,
                'sales_amount' => $billing->total_amt,
                'payment_type' => $billing->paymentType,
                'product_name' => $billing->product_name,
                'hsn_code' => $billing->hsn_code
            ];
        }

        foreach ($customerBillingData as $billing) {
            $paymentType = strtolower($billing->paymentType);
            $cumulativeReport['total_sales_amount'] += $billing->total_amt;

            if ($paymentType === 'cash') {
                $cumulativeReport['cash_payments'] += $billing->total_amt;
                $cashSources['Diary'][] = $billing; 
            } else if ($paymentType === 'offline') {
                $cumulativeReport['cash_payments'] += $billing->total_amt;
            }elseif ($paymentType === 'card') {
                $cumulativeReport['card_payments'] += $billing->total_amt;
            } elseif ($paymentType === 'online') {
                $cumulativeReport['online_payments'] += $billing->total_amt;
            }

            $cumulativeReport['transactions'][] = [
                'date' => $billing->billing_date,
                'invoiceNo' => $billing->invoiceNo,
                'customer_name' => $billing->customer_name,
                'customer_phone' => $billing->customer_phone,
                'doctor_name' => $billing->doctor_name,
                // 'doctor_mobile' => $billing->doctor_mobile,
                'sales_amount' => $billing->total_amt,
                'payment_type' => $billing->paymentType,
                'product_name' => $billing->product_name,
                'hsn_code' => $billing->hsn_code
            ];
        }

        $cumulativeReport['cash_sources'] = $cashSources;

        return response()->json([
            'status' => 'success',
            'data' => $cumulativeReport
        ]);
    }

    public function expiryReport(){
        try {
            $PurchaseStockEntryList = DB::table('purchase_stock_entry')
                ->join('product', 'purchase_stock_entry.product_id', '=', 'product.id')
                ->select('purchase_stock_entry.*', 'product.product_name as product_name')
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
            ->select('cb.billing_date', 'p.hsn_code', 'p.gst', DB::raw('SUM(cb.total_amt) as total_amount'))
            ->groupBy('cb.billing_date', 'p.hsn_code', 'p.gst');
    
        // Fetch the billing data from staff_product_billing
        $staffBills = DB::table('staff_product_billing as cpb')
            ->join('staff_billing as cb', 'cpb.cb_id', '=', 'cb.id')
            ->join('product as p', 'cpb.productId', '=', 'p.id')
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
            $stockData = DB::table('purchase_stock_entry as pse')
                ->join('product as p', 'pse.product_id', '=', 'p.id')
                ->join('brand as b', 'pse.brand_id', '=', 'b.id')
                ->join('category as c', 'pse.category_id', '=', 'c.id')
                ->join('sub_category as sc', 'pse.sub_category_id', '=', 'sc.id')
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
        $zeroStockProducts = DB::table('purchase_stock_entry')
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

    public function monthlyEarnings()
    {
        $currentMonth = now()->format('Y-m');

        $customerSales = DB::table('customer_billing')
            ->whereYear('billing_date', now()->year)
            ->whereMonth('billing_date', now()->month)
            ->sum('total_amt');

        $staffSales = DB::table('staff_billing')
            ->whereYear('billing_date', now()->year)
            ->whereMonth('billing_date', now()->month)
            ->sum('total_amt');

        $totalEarnings = $customerSales + $staffSales;

        return response()->json([
            'status' => 'success',
            'data' => $totalEarnings
        ]);
    }
}
