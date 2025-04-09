<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Gloudemans\Shoppingcart\Facades\Cart;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\WarehouseTransfer;
use App\Models\PurchaseCategory;
use App\Models\PurchaseDetails;
use App\Models\WarehouseStock;
use App\Models\Transaction;
use App\Models\Warehouse;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $data = Purchase::with('supplier');
        if ($request->supplier_id) {
            $data = $data->where(['supplier_id' => $request->supplier_id]);
        }
        $data = $data->paginate(50);
        $suppliers = Supplier::select('id', 'name', 'phone')->get();

        return view('backEnd.purchase.index', compact('data', 'suppliers'));
    }
    public function create()
    {
        $products = Product::select('id', 'name', 'new_price', 'purchase_price', 'product_code', 'stock')->where(['status' => 1])->get();
        $suppliers = Supplier::select('id', 'name')->where(['status' => 1])->get();
        $warehouses = Warehouse::select('id', 'name')->get();
        $pur_categories = PurchaseCategory::select('id', 'name')->get();
        Cart::instance('purchase')->destroy();
        $cartinfo = Cart::instance('purchase')->content();
        Session::forget('purchase_discount');
        Session::forget('product_discount');
        Session::forget('shipping');
        Session::forget('discount');
        Session::forget('paid');
        Session::forget('warehouse_id');
        return view('backEnd.purchase.create', compact('products', 'cartinfo', 'suppliers', 'warehouses', 'pur_categories'));
    }
    public function cart_add(Request $request)
    {
        $product = DB::table('products')->where(['id' => $request->id])->select('id', 'slug', 'name', 'new_price', 'old_price', 'purchase_price', 'product_code')->first();
        $image = DB::table('productimages')->where('product_id', $request->id)->select('product_id', 'image')->first();
        $warehouse_id = Session::get('warehouse_id', 0);
        $warehouse_stock = WarehouseStock::where(['product_id' => $request->id, 'warehouse_id' => $warehouse_id])->first();
        $qty = 1;
        $cartinfo = Cart::instance('purchase')->add([
            'id' => $product->id,
            'name' => $product->name,
            'qty' => $qty,
            'price' => $product->purchase_price,
            'options' => [
                'image' => $image->image,
                'old_price' => $product->old_price,
                'new_price' => $product->new_price,
                'warehouse_stock'=> $warehouse_stock->stock ?? 0
            ],
        ]);
        return response()->json(compact('cartinfo'));
    }
    public function cart_content()
    {
        $cartinfo = Cart::instance('purchase')->content();
        return view('backEnd.purchase.cart_content', compact('cartinfo'));
    }
    public function cart_details()
    {
        $cartinfo = Cart::instance('purchase')->content();
        $discount = 0;
        foreach ($cartinfo as $cart) {
            $discount += $cart->options->product_discount * $cart->qty;
        }
        Session::put('product_discount', $discount);
        return view('backEnd.purchase.cart_details', compact('cartinfo'));
    }
    public function cart_increment(Request $request)
    {
        $qty = $request->qty + 1;
        $cartinfo = Cart::instance('purchase')->update($request->id, $qty);
        return response()->json($cartinfo);
    }
    public function cart_decrement(Request $request)
    {
        $qty = $request->qty - 1;
        $cartinfo = Cart::instance('purchase')->update($request->id, $qty);
        return response()->json($cartinfo);
    }
    public function cart_remove(Request $request)
    {
        $remove = Cart::instance('purchase')->remove($request->id);
        $cartinfo = Cart::instance('purchase')->content();
        return response()->json($cartinfo);
    }
    public function product_discount(Request $request)
    {
        $discount = $request->discount;
        $cart = Cart::instance('purchase')->content()->where('rowId', $request->id)->first();
        $cartinfo = Cart::instance('purchase')->update($request->id, [
            'options' => [
                'slug' => $cart->options->slug,
                'image' => $cart->options->image,
                'old_price' => $cart->options->old_price,
                'new_price' => $cart->options->new_price,
                'product_discount' => $request->discount,
            ],
        ]);
        return response()->json($cartinfo);
    }
    public function purchase_paid(Request $request)
    {
        $amount = $request->amount;
        Session::put('paid', $amount);
        return response()->json($amount);
    }

    public function product_quantity(Request $request)
    {
        $quantity = $request->quantity;
        $cartinfo = Cart::instance('purchase')->update($request->id, $quantity);
        return response()->json($cartinfo);
    }
    public function cart_clear(Request $request)
    {
        $cartinfo = Cart::instance('purchase')->destroy();
        Session::forget('pos_shipping');
        Session::forget('pos_discount');
        Session::forget('product_discount');
        return redirect()->back();
    }

    public function purchase_store(Request $request)
    {
        $this->validate($request, [
            'supplier_id' => 'required',
        ]);

        if (Cart::instance('purchase')->count() <= 0) {
            Toastr::error('Your shopping empty', 'Failed!');
            return redirect()->back();
        }
        $subtotal = Cart::instance('purchase')->subtotal();
        $subtotal = str_replace(',', '', $subtotal);
        $subtotal = str_replace('.00', '', $subtotal);
        $shipping = Session::get('shipping');
        $discount = Session::get('purchase_discount') + Session::get('product_discount');

        // order data save
        $last_id = Purchase::max('id');
        $purchase = new Purchase();
        $purchase->invoice_id = str_pad(($last_id ? $last_id + 1 : 1), 6, '0', STR_PAD_LEFT);
        $purchase->amount = ($subtotal + $shipping) - $discount;
        $purchase->discount = $discount ?? 0;
        $purchase->category_id = $request->category_id;
        $purchase->supplier_id = $request->supplier_id;
        $purchase->warehouse_id = $request->warehouse_id;
        $purchase->quantity = Cart::instance('purchase')->count();
        $purchase->paid = $request->paid;
        $purchase->category_id = $request->category_id;
        $purchase->status = 'final';
        $purchase->due = (($subtotal + $shipping) - $discount) - $request->paid;
        $purchase->created_at = $request->order_date;
        $purchase->save();

        // supplier data save
        $supplier = Supplier::find($request->supplier_id);
        $supplier->amount += $purchase->amount;
        $supplier->paid += $purchase->paid;
        $supplier->due += $purchase->due;
        $supplier->save();

        $transaction = new Transaction();
        $transaction->title = 'Purchase payment';
        $transaction->type = 'purchase';
        $transaction->user = 'supplier';
        $transaction->ref_id = $purchase->invoice_id;
        $transaction->user_id = $request->supplier_id;
        $transaction->amount = $purchase->paid;
        $transaction->method = 'cash';
        $transaction->save();

        // purchase details data save
        foreach (Cart::instance('purchase')->content() as $cart) {
            $purchase_details = new PurchaseDetails();
            $purchase_details->purchase_id = $purchase->id;
            $purchase_details->product_id = $cart->id;
            $purchase_details->purchase_price = $cart->price;
            $purchase_details->new_price = $cart->options->new_price;
            $purchase_details->old_price = $cart->options->old_price;
            $purchase_details->product_discount = $cart->options->product_discount;
            $purchase_details->quantity = $cart->qty;
            $purchase_details->save();

            // product update
            $product_update = Product::select('id', 'stock')->where('id', $cart->id)->first();
            $product_update->stock += $cart->qty;
            $product_update->save();

            // warehouse data save
            $warehousestock = WarehouseStock::firstOrCreate(
                [
                    'product_id' => $cart->id,
                    'warehouse_id' => $request->warehouse_id
                ],
                [
                    'product_id' => $cart->id,
                    'warehouse_id' => $request->warehouse_id,
                    'stock' => $cart->qty,
                ]
            );
            // If the record already existed, increment the stock
            if (!$warehousestock->wasRecentlyCreated) {
                $warehousestock->stock += $cart->qty;
            }

            $warehousestock->save();

            WarehouseTransfer::create([
                'product_id' => $cart->id,
                'stock' => $cart->qty,
                'from' => 0,
                'to' => $request->warehouse_id,
            ]);

            // Update warehouse products field
            $warehouse = Warehouse::select('id', 'stock', 'purchase', 'products')->where('id', $request->warehouse_id)->first();
            $warehouse->stock += $cart->qty;
            $warehouse->purchase += $cart->qty;
            $warehouse->products += $warehousestock->wasRecentlyCreated ? 1 : 0;
            $warehouse->save();
        }
        Cart::instance('purchase')->destroy();
        Session::forget('paid');
        Session::forget('purchase_discount');
        Session::forget('product_discount');
        Session::forget('shipping');
        Toastr::success('Thanks, Your purchase place successfully', 'Success!');
        return redirect('admin/purchase/manage');
    }
    public function purchase_edit($invoice_id)
    {
        $products = Product::select('id', 'name', 'product_code', 'purchase_price', 'stock')->where(['status' => 1])->get();
        $suppliers = Supplier::select('id', 'name')->where(['status' => 1])->get();
        $purchase = Purchase::where('invoice_id', $invoice_id)->first();
        $warehouses = Warehouse::select('id', 'name')->get();
        $pur_categories = PurchaseCategory::select('id', 'name')->get();
        Session::put('paid', $purchase->paid);
        Session::put('warehouse_id', $purchase->warehouse_id ?? 0);
        $warehouse_id = Session::get('warehouse_id');
        $cartinfo = Cart::instance('purchase')->destroy();
        Session::put('product_discount', $purchase->discount);
        $purchasedetails = PurchaseDetails::where('purchase_id', $purchase->id)->get();
        //return $purchasedetails;
        foreach ($purchasedetails as $purdetails) {
            $image = DB::table('productimages')->select('image', 'product_id')->where('product_id', $purdetails->product_id)->first();
            $product = WarehouseStock::where(['product_id' => $purdetails->product_id, 'warehouse_id' => $warehouse_id])->first();
            $cartinfo = Cart::instance('purchase')->add([
                'id' => $purdetails->product_id,
                'name' => $purdetails->product->name,
                'qty' => $purdetails->quantity,
                'price' => $purdetails->purchase_price,
                'options' => [
                    'image' => $image->image,
                    'old_price' => $purdetails->old_price,
                    'new_price' => $purdetails->new_price,
                    'purchase_price' => $purdetails->purchase_price,
                    'product_discount' => $purdetails->product_discount,
                    'pid' => $purdetails->purchase_id,
                    'warehouse_stock'=> $product->stock ?? 0
                ],
            ]);
        }

        $cartinfo = Cart::instance('purchase')->content();
        return view('backEnd.purchase.edit', compact('products', 'cartinfo', 'purchase', 'suppliers', 'warehouses', 'pur_categories'));
    }
    public function purchase_update(Request $request)
    {
        if (Cart::instance('purchase')->count() <= 0) {
            Toastr::error('Your shopping empty', 'Failed!');
            return redirect()->back();
        }
        $subtotal = Cart::instance('purchase')->subtotal();
        $subtotal = str_replace(',', '', $subtotal);
        $subtotal = str_replace('.00', '', $subtotal);
        $shipping = Session::get('shipping');
        $discount = Session::get('purchase_discount') + Session::get('product_discount');

        // order data save
        $purchase = Purchase::where('id', $request->id)->first();
        $purchase->amount = ($subtotal + $shipping) - $discount;
        $purchase->discount = $discount ? $discount : 0;
        $purchase->category_id = $request->category_id;
        $purchase->supplier_id = $request->supplier_id;
        $purchase->warehouse_id = $request->warehouse_id;
        $purchase->quantity = Cart::instance('purchase')->count();
        $purchase->paid = $request->paid;
        $purchase->status = 'final';
        $purchase->due = (($subtotal + $shipping) - $discount) - $request->paid;
        $purchase->created_at = $request->order_date;
        $purchase->save();

        // purchase details data save
        foreach (Cart::instance('purchase')->content() as $cart) {
            $exits = PurchaseDetails::where('id', $cart->options->pid)->first();
            if ($exits) {
                $purchase_details = PurchaseDetails::where('id', $cart->options->pid)->first();
                $purchase_details->purchase_price = $cart->price;
                $purchase_details->new_price = $cart->options->new_price;
                $purchase_details->old_price = $cart->options->old_price;
                $purchase_details->product_discount = $cart->options->product_discount;
                $purchase_details->quantity = $cart->qty;
                $purchase_details->save();
            } else {
                $purchase_details = new PurchaseDetails();
                $purchase_details->purchase_id = $purchase->id;
                $purchase_details->product_name = $cart->name;
                $purchase_details->purchase_price = $cart->price;
                $purchase_details->product_discount = $cart->options->product_discount;
                $purchase_details->quantity = $cart->qty;
                $purchase_details->product_id = $cart->id;
                $purchase_details->new_price = $cart->options->new_price;
                $purchase_details->old_price = $cart->options->old_price;
                $purchase_details->save();
            }
            // product update
            $product_update = Product::select('id', 'stock')->where('id', $cart->id)->first();
            $product_update->stock -= $purchase_details->quantity;
            $product_update->stock += $cart->qty;
            $product_update->save();

            // warehouse data save
            $warehousestock = WarehouseStock::firstOrCreate(
                [
                    'product_id' => $cart->id,
                    'warehouse_id' => $request->warehouse_id
                ],
                [
                    // Additional fields to set if creating a new record
                    'product_id' => $cart->id,
                    'warehouse_id' => $request->warehouse_id,
                    'stock' => $cart->qty,
                ]
            );
            $warehousestock->stock += $cart->qty;
            $warehousestock->save();

            // // product stock
            // $stock_exits = ProductStock::where('product_id', $cart->id)->first();
            // if ($stock_exits) {
            //     $stock_exits->quantity -= $purchase_details->quantity;
            //     $stock_exits->quantity += $cart->qty;
            //     $stock_exits->save();
            // } else {
            //     $stock_store = new ProductStock();
            //     $stock_store->product_id = $cart->id;
            //     $stock_store->purchase_price = $cart->price;
            //     $stock_store->retail_price = $cart->options->retail_price;
            //     $stock_store->whole_price = $cart->options->whole_price;
            //     $stock_store->quantity = $cart->qty;
            //     $stock_store->save();
            // }
        }
        Cart::instance('purchase')->destroy();
        Session::forget('paid');
        Session::forget('purchase_discount');
        Session::forget('product_discount');
        Session::forget('shipping');
        Toastr::success('Thanks, Your purchase update successfully', 'Success!');
        return redirect()->back();

    }
    public function invoice($id)
    {
        $purchase = Purchase::where(['id' => $id])->with('purchasedetails', 'supplier')->firstOrFail();
        return view('backEnd.purchase.invoice', compact('purchase'));
    }

    public function purchase_summary(Request $request)
    {
        $suppliers = DB::table('suppliers')->select('id', 'name')->where(['status' => 1])->get();
        $data = Purchase::with('supplier');
        if ($request->keyword) {
            $data = $data->orWhere('invoice_id', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->supplier_id) {
            $data = $data->where('supplier_id', $request->supplier_id);
        }
        if ($request->start_date && $request->end_date) {
            $data = $data->whereBetween('updated_at', [$request->start_date, $request->end_date]);
        }
        $data = $data->paginate(100);
        return view('backEnd.reports.purchase_summary', compact('data', 'suppliers'));
    }
    public function purchase_details(Request $request)
    {
        $suppliers = DB::table('suppliers')->select('id', 'name')->where(['status' => 1])->get();
        $data = PurchaseDetails::with('purchase');
        if ($request->keyword) {
            $data = $data->orWhere('invoice_id', 'LIKE', '%' . $request->keyword . "%")->orwhereHas('supplier', function ($query) use ($request) {
                $query->where('phone', $request->keyword);
            });
        }
        if ($request->supplier_id) {
            $data = $data->whereHas('supplier', function ($query) use ($request) {
                $query->where('supplier_id', $request->supplier_id);
            });
        }
        if ($request->start_date && $request->end_date) {
            $data = $data->whereBetween('updated_at', [$request->start_date, $request->end_date]);
        }
        $total_purchase = $data->sum(DB::raw('purchase_price * quantity'));
        $total_item = $data->sum('quantity');
        $data = $data->paginate(100);
        return view('backEnd.reports.purchase_details', compact('data', 'suppliers', 'total_purchase', 'total_item'));
    }
    public function supplier_ledger(Request $request)
    {
        $show_data = Supplier::when($request->keyword, function ($query, $keyword) {
            $query->where('phone', $keyword)->orWhere('name', 'LIKE', "%$keyword%");
        })->paginate(20);

        return view('backEnd.reports.supplier_ledger', compact('show_data'));
    }

    public function purchase_select_warehouse(Request $request)
    {
        Session::put('warehouse_id', $request->id);
        $warehouse_id = Session::get('warehouse_id');
        $carts = Cart::instance('purchase')->content();

        foreach ($carts as $cart) {
            $product = WarehouseStock::where(['product_id' => $cart->id, 'warehouse_id' => $warehouse_id])->first();
            Cart::instance('purchase')->update($cart->rowId, [
                'options' => [
                    'slug' => $cart->options->slug,
                    'image' => $cart->options->image,
                    'old_price' => $cart->options->old_price,
                    'new_price' => $cart->options->new_price,
                    'product_discount' => $cart->options->discount ?? 0,
                    'warehouse_stock'=> $product->stock ?? 0
                ],
            ]);
        }

        return response()->json(['warehouse_id' => $warehouse_id]);
    }
}
