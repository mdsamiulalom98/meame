<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseDetails;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\OrderDetails;
use App\Models\Order;
use DB;
class ReportsController extends Controller
{
    
    public function purchase_reports(Request $request){
        $suppliers = DB::table('suppliers')->select('id','name')->where(['status'=>1])->get();
        $pur_category = DB::table('purchase_categories')->select('id','name')->where(['status'=>1])->get();
        $data = PurchaseDetails::with('purchase');
        if ($request->category_id) {
            $data = $data->whereHas('purchase', function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            });
        }
        if ($request->supplier_id) {
            $data = $data->whereHas('purchase.supplier', function ($query) use ($request) {
                $query->where('supplier_id', $request->supplier_id);
            });
        }
        if ($request->start_date && $request->end_date) {
            $data =$data->whereBetween('created_at', [$request->start_date,$request->end_date]);
        }

        $total_purchase = $data->sum(\DB::raw('purchase_price * quantity'));
        $total_item = $data->sum('quantity');
        $data = $data->paginate(100);
        return view('backEnd.reports.purchase',compact('data','suppliers','pur_category','total_purchase','total_item'));
    }
    public function cash_purchase(Request $request)
    {
    	$suppliers = DB::table('suppliers')->select('id','name')->where(['status'=>1])->get();
    	$data = Purchase::where('paid','>','0');
        if ($request->supplier_id) {
            $data = $data->where('supplier_id', $request->supplier_id);
        }
        if ($request->start_date && $request->end_date) {
            $data = $data->whereBetween('created_at', [$request->start_date,$request->end_date]);
        }
        $total_amount = $data->sum(\DB::raw('amount'));
        $total_paid = $data->sum('paid');
        $data = $data->paginate(100);
        return view('backEnd.reports.cash_purchase',compact('suppliers','data','total_amount','total_paid'));
    }
    public function due_purchase(Request $request)
    {
    	$suppliers = DB::table('suppliers')->select('id','name')->where(['status'=>1])->get();
    	$data = Purchase::where('due','>','0');
        if ($request->supplier_id) {
            $data = $data->where('supplier_id', $request->supplier_id);
        }
        if ($request->start_date && $request->end_date) {
            $data = $data->whereBetween('created_at', [$request->start_date,$request->end_date]);
        }
        $total_amount = $data->sum(\DB::raw('amount'));
        $total_due = $data->sum('due');
        $data = $data->paginate(100);
        return view('backEnd.reports.due_purchase',compact('suppliers','data','total_amount','total_due'));
    }
    public function supplier_ledger(Request $request)
    {
        if($request->keyword){
            $show_data = Supplier::orWhere('phone',$request->keyword)->orWhere('name',$request->keyword)->paginate(20);
        }else{
             $show_data = Supplier::paginate(20);
        }
        return view('backEnd.reports.supplier_ledger',compact('show_data'));
    }
    public function due_paid(Request $request){
        if($request->user == 'supplier'){
            $users = DB::table('suppliers')->select('id','name','phone')->get();
        }else{
            $users = DB::table('customers')->select('id','name','phone')->get();
        }
        $data = Transaction::where(['type'=>'payment','user'=>$request->user])->with('customer','supplier')->paginate(100);
        return view('backEnd.reports.due_paid',compact('data','users'));
    }

    public function cash_sales(Request $request)
    {
        $customers = DB::table('customers')->select('id','name')->where(['status'=>1])->get();
        $data = Order::where('paid','>','0');
        if ($request->customer_id) {
            $data = $data->where('customer_id', $request->customer_id);
        }
        if ($request->start_date && $request->end_date) {
            $data = $data->whereBetween('created_at', [$request->start_date,$request->end_date]);
        }
        $total_amount = $data->sum(\DB::raw('amount'));
        $total_paid = $data->sum('paid');
        $data = $data->paginate(100);
        return view('backEnd.reports.cash_sales',compact('customers','data','total_amount','total_paid'));
    }
    public function due_sales(Request $request)
    {
        $customers = DB::table('customers')->select('id','name')->where(['status'=>1])->get();
        $data = Order::where('due','>','0');
        if ($request->customer_id) {
            $data = $data->where('customer_id', $request->customer_id);
        }
        if ($request->start_date && $request->end_date) {
            $data = $data->whereBetween('created_at', [$request->start_date,$request->end_date]);
        }
        $total_amount = $data->sum(\DB::raw('amount'));
        $total_due = $data->sum('due');
        $data = $data->paginate(100);
        return view('backEnd.reports.due_sales',compact('customers','data','total_amount','total_due'));
    }
     public function offer_sales(Request $request){
        $products = DB::table('products')->select('id','name')->where(['status'=>1])->get();
        $data = OrderDetails::with('order');
        if ($request->product_id) {
            $data = $data->where('product_id', $request->product_id);
        }
        if ($request->start_date && $request->end_date) {
            $data =$data->whereBetween('created_at', [$request->start_date,$request->end_date]);
        }

        $total_sales = $data->sum(\DB::raw('sale_price * qty'));
        $total_item = $data->sum('qty');
        $data = $data->paginate(100);

        return view('backEnd.reports.offer_sales',compact('data','products','total_sales','total_item'));
    }
}
