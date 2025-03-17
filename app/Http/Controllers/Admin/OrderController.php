<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Brian2694\Toastr\Facades\Toastr;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Models\ExpenseCategories;
use App\Models\ShippingCharge;
use App\Models\WarehouseStock;
use App\Models\Childcategory;
use App\Models\OrderCategory;
use App\Models\AssetCategory;
use App\Models\OrderDetails;
use App\Models\Subcategory;
use App\Models\OrderStatus;
use App\Models\Transaction;
use App\Models\Courierapi;
use App\Models\Warehouse;
use App\Models\Customer;
use App\Models\Shipping;
use App\Models\Category;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Asset;
use App\Models\Order;
use App\Models\User;

class OrderController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:order-list|order-create|order-edit|order-delete', ['only' => ['index', 'order_store', 'order_edit']]);
        $this->middleware('permission:order-create', ['only' => ['order_store', 'order_create']]);
        $this->middleware('permission:order-edit', ['only' => ['order_edit', 'order_update']]);
        $this->middleware('permission:order-delete', ['only' => ['destroy']]);
        $this->middleware('permission:order-invoice', ['only' => ['invoice']]);
        $this->middleware('permission:order-process', ['only' => ['process', 'order_process']]);
        $this->middleware('permission:order-process', ['only' => ['process']]);
    }
    public function search(Request $request)
    {
        $products = Product::select('id', 'name', 'slug', 'new_price', 'old_price', 'category_id', 'subcategory_id', 'childcategory_id')
            ->where('status', 1)
            ->with('image');

        if ($request->category) {
            $products = $products->where('category_id', $request->category);
        }

        if ($request->subcategory) {
            $products = $products->where('subcategory_id', $request->subcategory);
        }

        if ($request->childcategory) {
            $products = $products->where('childcategory_id', $request->childcategory);
        }

        $products = $products->pluck('name', 'id');
        return response()->json($products);
    }

    public function office_orders() {
        $show_data = Order::where('order_type', 0)->paginate(100);
        // return $show_data;
        $order_status = OrderStatus::where(['slug' => 'delivered'])->withCount('orders')->first();
        $users = User::get();
        $steadfast = Courierapi::where(['status' => 1, 'type' => 'steadfast'])->first();
        $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])->select('id', 'type', 'url', 'token', 'status')->first();
        // pathao courier
        if ($pathao_info) {
            $response = Http::get($pathao_info->url . '/api/v1/countries/1/city-list');
            $pathaocities = $response->json();
            $response2 = Http::withHeaders([
                'Authorization' => 'Bearer ' . $pathao_info->token,
                'Content-Type' => 'application/json',
            ])->get($pathao_info->url . '/api/v1/stores');
            $pathaostore = $response2->json();
        } else {
            $pathaocities = [];
            $pathaostore = [];
        }
        return view('backEnd.order.officeorders', compact('show_data', 'order_status', 'users', 'steadfast', 'pathaostore', 'pathaocities'));
    }
    public function index($slug, Request $request)
    {
        // Order::query()->update(['order_type' => 1]);
        if ($slug == 'all') {
            $order_status = (object) [
                'name' => 'All',
                'orders_count' => Order::where('order_type', 1)->count(),
            ];
            $show_data = Order::where('order_type', 1)->latest()->with('shipping', 'status');
            if ($request->keyword) {
                $show_data = $show_data->where(function ($query) use ($request) {
                    $query->orWhere('invoice_id', 'LIKE', '%' . $request->keyword . '%')
                        ->orWhereHas('shipping', function ($subQuery) use ($request) {
                            $subQuery->where('phone', $request->keyword);
                        });
                });
            }
            $show_data = $show_data->paginate(100);
        } else {
            $order_status = OrderStatus::where('slug', $slug)->withCount('orders')->first();
            $show_data = Order::where(['order_status' => $order_status->id, 'order_type' => 1])->latest()->with('shipping', 'status')->paginate(100);
        }
        $users = User::get();
        $steadfast = Courierapi::where(['status' => 1, 'type' => 'steadfast'])->first();
        $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])->select('id', 'type', 'url', 'token', 'status')->first();
        // pathao courier
        if ($pathao_info) {
            $response = Http::get($pathao_info->url . '/api/v1/countries/1/city-list');
            $pathaocities = $response->json();
            $response2 = Http::withHeaders([
                'Authorization' => 'Bearer ' . $pathao_info->token,
                'Content-Type' => 'application/json',
            ])->get($pathao_info->url . '/api/v1/stores');
            $pathaostore = $response2->json();
        } else {
            $pathaocities = [];
            $pathaostore = [];
        }
        return view('backEnd.order.index', compact('show_data', 'order_status', 'users', 'steadfast', 'pathaostore', 'pathaocities'));
    }

    public function pathaocity(Request $request)
    {
        $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])->select('id', 'type', 'url', 'token', 'status')->first();
        if ($pathao_info) {
            $response = Http::get($pathao_info->url . '/api/v1/cities/' . $request->city_id . '/zone-list');
            $pathaozones = $response->json();
            return response()->json($pathaozones);
        } else {
            return response()->json([]);
        }
    }
    public function pathaozone(Request $request)
    {
        $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])->select('id', 'type', 'url', 'token', 'status')->first();
        if ($pathao_info) {
            $response = Http::get($pathao_info->url . '/api/v1/zones/' . $request->zone_id . '/area-list');
            $pathaoareas = $response->json();
            return response()->json($pathaoareas);
        } else {
            return response()->json([]);
        }
    }

    public function order_pathao(Request $request)
    {
        return "Testing access patho off for fake order place";
        $order = Order::with('shipping')->find($request->id);
        $order_count = OrderDetails::select('order_id')->where('order_id', $order->id)->count();
        // pathao
        $pathao_info = Courierapi::where(['status' => 1, 'type' => 'pathao'])->select('id', 'type', 'url', 'token', 'status')->first();
        if ($pathao_info) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $pathao_info->token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($pathao_info->url . '/api/v1/orders', [
                'store_id' => $request->pathaostore,
                'merchant_order_id' => $order->invoice_id,
                'sender_name' => 'Test',
                'sender_phone' => $order->shipping ? $order->shipping->phone : '',
                'recipient_name' => $order->shipping ? $order->shipping->name : '',
                'recipient_phone' => $order->shipping ? $order->shipping->phone : '',
                'recipient_address' => $order->shipping ? $order->shipping->address : '',
                'recipient_city' => $request->pathaocity,
                'recipient_zone' => $request->pathaozone,
                'recipient_area' => $request->pathaoarea,
                'delivery_type' => 48,
                'item_type' => 2,
                'special_instruction' => 'Special note- product must be check after delivery',
                'item_quantity' => $order_count,
                'item_weight' => 0.5,
                'amount_to_collect' => round($order->amount),
                'item_description' => 'Special note- product must be check after delivery',
            ]);
        }
        if ($response->status() == '200') {
            $order->order_status = 4;
            $order->save();
            Toastr::success('order send to pathao successfully');
            return redirect()->back();
        } else {
            Toastr::error($response['message'], 'Courier Order Faild');
            return redirect()->back();
        }
    }

    public function invoice($invoice_id)
    {
        $order = Order::where(['invoice_id' => $invoice_id])->with('orderdetails', 'payment', 'shipping', 'customer')->firstOrFail();
        return view('backEnd.order.invoice', compact('order'));
    }

    public function process($invoice_id)
    {
        $data = Order::where(['invoice_id' => $invoice_id])->select('id', 'invoice_id', 'order_status')->with('orderdetails')->first();
        $shippingcharge = ShippingCharge::where('status', 1)->get();
        $warehouses = Warehouse::where('status', 1)->get();
        return view('backEnd.order.process', compact('data', 'shippingcharge', 'warehouses'));
    }

    public function order_process(Request $request)
    {
        $link = OrderStatus::find($request->status)->slug;
        $order = Order::find($request->id);
        $courier = $order->order_status;
        $order->order_status = $request->status;
        $order->admin_note = $request->admin_note;
        $order->save();

        $shipping_update = Shipping::where('order_id', $order->id)->first();
        $shippingfee = ShippingCharge::find($request->area);
        if ($shippingfee->name != $request->area) {
            if ($order->shipping_charge > $shippingfee->amount) {
                $total = $order->amount + ($shippingfee->amount - $order->shipping_charge);
                $order->shipping_charge = $shippingfee->amount;
                $order->amount = $total;
                $order->save();
            } else {
                $total = $order->amount + ($shippingfee->amount - $order->shipping_charge);
                $order->shipping_charge = $shippingfee->amount;
                $order->amount = $total;
                $order->save();
            }
        }

        $shipping_update->name = $request->name;
        $shipping_update->phone = $request->phone;
        $shipping_update->address = $request->address;
        $shipping_update->area = $shippingfee->name;
        $shipping_update->save();

        if ($request->status == 4 && $courier != 4) {
            $courier_info = Courierapi::where(['status' => 1, 'type' => 'steadfast'])->first();
            if ($courier_info) {
                $consignmentData = [
                    'invoice' => $order->invoice_id,
                    'recipient_name' => $order->shipping ? $order->shipping->name : 'InboxHat',
                    'recipient_phone' => $order->shipping ? $order->shipping->phone : '01750578495',
                    'recipient_address' => $order->shipping ? $order->shipping->address : '01750578495',
                    'cod_amount' => $order->amount
                ];
                $client = new Client();
                $response = $client->post('$courier_info->url', [
                    'json' => $consignmentData,
                    'headers' => [
                        'Api-Key' => '$courier_info->api_key',
                        'Secret-Key' => '$courier_info->secret_key',
                        'Accept' => 'application/json',
                    ],
                ]);

                $responseData = json_decode($response->getBody(), true);
            } else {
                return "ok";
            }

        }
        if ($request->status == 5) {
            $orders_details = OrderDetails::select('id', 'order_id', 'product_id', 'qty')->where('order_id', $order->id)->get();

            foreach ($orders_details as $order_details) {
                $product = Product::select('id', 'stock')->find($order_details->product_id);
                $product->stock -= $order_details->qty;
                $product->save();

                $warehousestock = WarehouseStock::where([
                    'warehouse_id' => $request->warehouse_id,
                    'product_id' => $order_details->product_id
                ])->first();

                if ($warehousestock) {
                    if ($warehousestock->stock >= $order_details->qty) {
                        $warehousestock->stock -= $order_details->qty;
                        $warehousestock->sold += $order_details->qty;
                        $warehousestock->save();
                    } else {
                        continue;
                    }
                } else {
                    continue;
                }
            }

            $order->paid += $order->amount;
            $order->due += 0;
            $order->save();

            $customer = Customer::find($order->customer_id);
            $customer->amount += $order->amount;
            $customer->paid += $order->amount;
            $customer->save();

            $transaction = new Transaction();
            $transaction->title = 'Purchase payment';
            $transaction->type = 'purchase';
            $transaction->user = 'supplier';
            $transaction->ref_id = $order->invoice_id;
            $transaction->user_id = $customer->id;
            $transaction->amount = $order->amount;
            $transaction->method = 'cash';
            $transaction->save();
        }
        if ($request->status == 7) {

            $orders_details = OrderDetails::select('id', 'order_id', 'product_id', 'qty')->where('order_id', $order->id)->get();
            foreach ($orders_details as $order_details) {
                $product = Product::select('id', 'stock')->find($order_details->product_id);
                $product->stock += $order_details->qty;
                $product->save();
            }

        }
        Toastr::success('Success', 'Order status change successfully');
        return redirect('admin/order/' . $link);
    }

    public function order_return(Request $request)
    {
        $order_detail = OrderDetails::select('id', 'order_id', 'product_id', 'sale_price', 'qty', 'is_return')->where('id', $request->id)->first();
        $order_detail->is_return = 1;
        $order_detail->save();

        $order = Order::find($order_detail->order_id);
        if ($order->order_status == 5) {
            $order->amount -= $order_detail->sale_price;
            $order->paid -= $order_detail->sale_price;
            $order->save();
        }

        if ($order->order_status == 5) {
            $product = Product::select('id', 'name', 'stock')->find($order_detail->product_id);
            $product->stock += $order_detail->qty;
            $product->save();
        }

        Toastr::success('Success', 'Order item return successfully');
        return back();
    }

    public function order_replace(Request $request)
    {
        $order_detail = OrderDetails::select('id', 'order_id', 'product_id', 'sale_price', 'qty', 'is_return')->where('id', $request->id)->first();
        $order_detail->is_replace = 1;
        $order_detail->save();

        Toastr::success('Success', 'Order item replace successfully');
        return back();
    }
    public function destroy(Request $request)
    {
        Order::where('id', $request->id)->delete();
        OrderDetails::where('order_id', $request->id)->delete();
        Shipping::where('order_id', $request->id)->delete();
        Payment::where('order_id', $request->id)->delete();
        Toastr::success('Success', 'Order delete success successfully');
        return redirect()->back();
    }
    public function order_assign(Request $request)
    {
        Order::whereIn('id', $request->input('order_ids'))->update(['user_id' => $request->user_id]);
        return response()->json(['status' => 'success', 'message' => 'Order user id assign']);
    }
    public function order_status(Request $request)
    {
        $orders = Order::whereIn('id', $request->input('order_ids'))->update(['order_status' => $request->order_status]);

        $orders = Order::whereIn('id', $request->input('order_ids'))->get();
        if ($request->order_status == 5) {
            foreach ($orders as $order) {
                $orders_details = OrderDetails::select('id', 'order_id', 'product_id', 'qty')->where('order_id', $order->id)->get();
                foreach ($orders_details as $order_details) {
                    $product = Product::select('id', 'stock')->find($order_details->product_id);
                    $product->stock -= $order_details->qty;
                    $product->save();
                }

                $order->paid += $order->amount;
                $order->due += 0;
                $order->save();

                $customer = Customer::find($order->customer_id);
                $customer->amount += $order->amount;
                $customer->paid += $order->amount;
                $customer->save();

                $transaction = new Transaction();
                $transaction->title = 'Purchase payment';
                $transaction->type = 'purchase';
                $transaction->user = 'supplier';
                $transaction->ref_id = $order->invoice_id;
                $transaction->user_id = $customer->id;
                $transaction->amount = $order->amount;
                $transaction->method = 'cash';
                $transaction->save();
            }
        }

        if ($request->order_status == 7) {
            foreach ($orders as $order) {
                $orders_details = OrderDetails::select('id', 'order_id', 'product_id', 'qty')->where('order_id', $order->id)->get();
                foreach ($orders_details as $order_details) {
                    $product = Product::select('id', 'stock')->find($order_details->product_id);
                    $product->stock += $order_details->qty;
                    $product->save();
                }
            }
        }
        return response()->json(['status' => 'success', 'message' => 'Order status change successfully']);

    }

    public function bulk_destroy(Request $request)
    {
        $orders_id = $request->order_ids;
        foreach ($orders_id as $order_id) {
            $order = Order::where('id', $order_id)->delete();
            $order_details = OrderDetails::where('order_id', $order_id)->delete();
            $shipping = Shipping::where('order_id', $order_id)->delete();
            $payment = Payment::where('order_id', $order_id)->delete();
        }
        return response()->json(['status' => 'success', 'message' => 'Order delete successfully']);
    }
    public function order_print(Request $request)
    {
        $orders = Order::whereIn('id', $request->input('order_ids'))->with('orderdetails', 'payment', 'shipping', 'customer')->get();
        $view = view('backEnd.order.print', ['orders' => $orders])->render();
        return response()->json(['status' => 'success', 'view' => $view]);
    }
    public function bulk_courier($slug, Request $request)
    {
        $courier_info = Courierapi::where(['status' => 1, 'type' => $slug])->first();
        if ($courier_info) {
            $orders_id = $request->order_ids;
            foreach ($orders_id as $order_id) {
                $order = Order::find($order_id);
                $courier = $order->order_status;
                if ($request->status == 4 && $courier != 4) {
                    $consignmentData = [
                        'invoice' => $order->invoice_id,
                        'recipient_name' => $order->shipping ? $order->shipping->name : 'InboxHat',
                        'recipient_phone' => $order->shipping ? $order->shipping->phone : '01750578495',
                        'recipient_address' => $order->shipping ? $order->shipping->address : '01750578495',
                        'cod_amount' => $order->amount
                    ];
                    $client = new Client();
                    $response = $client->post('$courier_info->url', [
                        'json' => $consignmentData,
                        'headers' => [
                            'Api-Key' => '$courier_info->api_key',
                            'Secret-Key' => '$courier_info->secret_key',
                            'Accept' => 'application/json',
                        ],
                    ]);

                    $responseData = json_decode($response->getBody(), true);
                    if ($responseData['status'] == 200) {
                        $message = 'Your order place to courier successfully';
                        $status = 'success';
                        $order->order_status = 4;
                        $order->save();
                    } else {
                        $message = 'Your order place to courier failed';
                        $status = 'failed';
                    }
                    return response()->json(['status' => $status, 'message' => $message]);
                }

            }
        } else {
            return "stop";
        }
    }
    public function order_create()
    {
        $categories = Category::get();
        $products = Product::select('id', 'name', 'new_price', 'product_code')->where(['status' => 1])->get();
        Cart::instance('pos_shopping')->destroy();
        $cartinfo = Cart::instance('pos_shopping')->content();
        $shippingcharge = ShippingCharge::where('status', 1)->get();
        $ordercategory = OrderCategory::where('status', 1)->get();
        $customers = Customer::where('status', 'active')->get();
        $warehouses = Warehouse::where('status', 1)->get();
        return view('backEnd.order.create', compact('categories', 'products', 'cartinfo', 'shippingcharge', 'ordercategory', 'customers', 'warehouses'));
    }

    public function order_store(Request $request)
    {
        if ($request->guest_customer) {
            $this->validate($request, [
                'guest_customer' => 'required',
            ]);
            $customer = Customer::find($request->guest_customer);
            $area = ShippingCharge::where('pos', 1)->first();
            $name = $customer->name;
            $phone = $customer->phone;
            $address = $area->name;
            $area = $area->id;

        } else {
            $this->validate($request, [
                'name' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'area' => 'required',
            ]);
            $name = $request->name;
            $phone = $request->phone;
            $address = $request->address;
            $area = $request->area;
        }

        if (Cart::instance('pos_shopping')->count() <= 0) {
            Toastr::error('Your shopping empty', 'Failed!');
            return redirect()->back();
        }

        $subtotal = Cart::instance('pos_shopping')->subtotal();
        $subtotal = str_replace(',', '', $subtotal);
        $subtotal = str_replace('.00', '', $subtotal);
        $discount = Session::get('pos_discount') + Session::get('product_discount');
        $additional_shipping = Session::get('additional_shipping') ?? 0;
        if ($request->area == 3) {
            $shippingfee = 0;
            $shippingarea = 'Pos Area';
        } else {
            $shipping = ShippingCharge::find($request->area);
            $shippingfee = $shipping->amount;
            $shippingarea = $shipping->name;
        }

        $exits_customer = Customer::where('phone', $phone)->select('phone', 'id')->first();
        if ($exits_customer) {
            $customer_id = $exits_customer->id;
        } else {
            $password = rand(111111, 999999);
            $store = new Customer();
            $store->name = $name;
            $store->slug = $name;
            $store->phone = $phone;
            $store->password = bcrypt($password);
            $store->verify = 1;
            $store->status = 'active';
            $store->save();
            $customer_id = $store->id;
        }

        // order data save
        $order = new Order();
        $order->invoice_id = rand(11111, 99999);
        $order->amount = ($subtotal + $shippingfee + $additional_shipping) - $discount;
        $order->discount = $discount ?? 0;
        $order->shipping_charge = $shippingfee;
        $order->additional_shipping = $additional_shipping;
        $order->customer_id = $customer_id;
        $order->paid = $request->paid;
        $order->due = $order->amount - $request->paid;
        $order->order_status = 5;
        $order->order_type = 0;
        $order->category_id = $request->category_id;
        $order->warehouse_id = $request->warehouse_id;
        $order->admin_note = $request->admin_note;
        $order->created_at = $request->order_date;
        $order->save();

        // shipping data save
        $shipping = new Shipping();
        $shipping->order_id = $order->id;
        $shipping->customer_id = $customer_id;
        $shipping->name = $name;
        $shipping->phone = $phone;
        $shipping->address = $address;
        $shipping->area = $shippingarea;
        $shipping->save();

        // payment data save
        $payment = new Payment();
        $payment->order_id = $order->id;
        $payment->customer_id = $customer_id;
        $payment->payment_method = 'Cash On Delivery';
        $payment->amount = $order->amount;
        $payment->payment_status = 'pending';
        $payment->save();

        // order details data save
        foreach (Cart::instance('pos_shopping')->content() as $cart) {
            $order_details = new OrderDetails();
            $order_details->order_id = $order->id;
            $order_details->product_id = $cart->id;
            $order_details->product_name = $cart->name;
            $order_details->purchase_price = $cart->options->purchase_price;
            $order_details->product_discount = $cart->options->product_discount;
            $order_details->sale_price = $cart->price;
            $order_details->qty = $cart->qty;
            $order_details->save();
        }

        $customer = Customer::find($customer_id);
        $customer->amount += $order->amount;
        $customer->paid += $request->paid;
        $customer->due += $customer->amount - $customer->paid;
        $customer->save();

        $transaction = new Transaction();
        $transaction->title = 'order payment';
        $transaction->type = 'payment';
        $transaction->user = 'customer';
        $transaction->ref_id = $order->invoice_id;
        $transaction->user_id = $customer_id;
        $transaction->amount = $request->paid;
        $transaction->method = 'cash';
        $transaction->save();

        Cart::instance('pos_shopping')->destroy();
        Session::forget('pos_shipping');
        Session::forget('pos_discount');
        Session::forget('product_discount');
        Session::forget('cpaid');
        Session::forget('additional_shipping');
        Session::forget('old_due');

        Toastr::success('Thanks, Your order place successfully', 'Success!');
        return redirect()->route('admin.office.orders');
    }
    public function cart_add(Request $request)
    {
        $product = Product::select('id', 'name', 'stock', 'new_price', 'old_price', 'purchase_price', 'slug')->where(['id' => $request->id])->first();
        $qty = 1;
        $cartinfo = Cart::instance('pos_shopping')->add([
            'id' => $product->id,
            'name' => $product->name,
            'qty' => $qty,
            'price' => $product->new_price,
            'options' => [
                'slug' => $product->slug,
                'image' => $product->image->image,
                'old_price' => $product->old_price,
                'purchase_price' => $product->purchase_price,
                'product_discount' => 0,
            ],
        ]);
        return response()->json(compact('cartinfo'));
    }
    public function cart_content()
    {
        $cartinfo = Cart::instance('pos_shopping')->content();
        return view('backEnd.order.cart_content', compact('cartinfo'));
    }
    public function cart_details()
    {
        $cartinfo = Cart::instance('pos_shopping')->content();
        $discount = 0;
        foreach ($cartinfo as $cart) {
            $discount += $cart->options->product_discount * $cart->qty;
        }
        Session::put('product_discount', $discount);
        return view('backEnd.order.cart_details', compact('cartinfo'));
    }
    public function cart_increment(Request $request)
    {
        $qty = $request->qty + 1;
        $cartinfo = Cart::instance('pos_shopping')->update($request->id, $qty);
        return response()->json($cartinfo);
    }
    public function cart_decrement(Request $request)
    {
        $qty = $request->qty - 1;
        $cartinfo = Cart::instance('pos_shopping')->update($request->id, $qty);
        return response()->json($cartinfo);
    }
    public function cart_remove(Request $request)
    {
        Cart::instance('pos_shopping')->remove($request->id);
        $cartinfo = Cart::instance('pos_shopping')->content();
        return response()->json($cartinfo);
    }
    public function product_discount(Request $request)
    {
        $discount = $request->discount;
        $cart = Cart::instance('pos_shopping')->content()->where('rowId', $request->id)->first();
        $cartinfo = Cart::instance('pos_shopping')->update($request->id, [
            'options' => [
                'slug' => $cart->options->slug,
                'image' => $cart->options->image,
                'old_price' => $cart->options->old_price,
                'purchase_price' => $cart->options->purchase_price,
                'product_discount' => $request->discount,
                'details_id' => $cart->options->details_id
            ],
        ]);
        return response()->json($cartinfo);
    }
    public function product_quantity(Request $request)
    {
        $quantity = $request->quantity;
        $cartinfo = Cart::instance('pos_shopping')->update($request->id, $quantity);
        return response()->json($cartinfo);
    }
    public function cart_shipping(Request $request)
    {
        $shipping = ShippingCharge::where(['status' => 1, 'id' => $request->id])->first()->amount;
        Session::put('pos_shipping', $shipping);
        return response()->json($shipping);
    }

    public function cart_clear(Request $request)
    {
        Cart::instance('pos_shopping')->destroy();
        Session::forget('pos_shipping');
        Session::forget('pos_discount');
        Session::forget('product_discount');
        return redirect()->back();
    }
    public function order_edit($invoice_id)
    {
        $products = Product::select('id', 'name', 'new_price', 'product_code')->where(['status' => 1])->get();
        $shippingcharge = ShippingCharge::where('status', 1)->get();
        $ordercategory = OrderCategory::where('status', 1)->get();
        $order = Order::where('invoice_id', $invoice_id)->first();
        $cartinfo = Cart::instance('pos_shopping')->destroy();
        $shippinginfo = Shipping::where('order_id', $order->id)->first();
        Session::put('product_discount', $order->discount);
        Session::put('pos_shipping', $order->shipping_charge);
        Session::put('cpaid', $order->paid);
        Session::put('additional_shipping', $order->additional_shipping);
        Session::put('old_due', $order->customer->due ?? 0);
        $orderdetails = OrderDetails::where('order_id', $order->id)->get();
        foreach ($orderdetails as $ordetails) {
            $cartinfo = Cart::instance('pos_shopping')->add([
                'id' => $ordetails->product_id,
                'name' => $ordetails->product_name,
                'qty' => $ordetails->qty,
                'price' => $ordetails->sale_price,
                'options' => [
                    'image' => $ordetails->image->image,
                    'purchase_price' => $ordetails->purchase_price,
                    'product_discount' => $ordetails->product_discount,
                    'details_id' => $ordetails->id,
                ],
            ]);
        }
        $cartinfo = Cart::instance('pos_shopping')->content();
        $customers = Customer::where('status', 'active')->get();
        $warehouses = Warehouse::where('status', 1)->get();
        $categories = Category::where('status', 1)->get();
        return view('backEnd.order.edit', compact('products', 'cartinfo', 'shippingcharge', 'shippinginfo', 'order', 'ordercategory', 'customers', 'warehouses', 'categories'));
    }

    public function order_update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'area' => 'required',
        ]);

        if (Cart::instance('pos_shopping')->count() <= 0) {
            Toastr::error('Your shopping empty', 'Failed!');
            return redirect()->back();
        }

        $subtotal = Cart::instance('pos_shopping')->subtotal();
        $subtotal = str_replace(',', '', $subtotal);
        $subtotal = str_replace('.00', '', $subtotal);
        $discount = Session::get('pos_discount') + Session::get('product_discount');
        $additional_shipping = Session::get('additional_shipping') ?? 0;
        if ($request->area == 3) {
            $shippingfee = 0;
            $shippingarea = 'Pos Area';
        } else {
            $shipping = ShippingCharge::find($request->area);
            $shippingfee = $shipping->amount;
            $shippingarea = $shipping->name;
        }

        $exits_customer = Customer::where('phone', $request->phone)->select('phone', 'id')->first();
        if ($exits_customer) {
            $customer_id = $exits_customer->id;
        } else {
            $password = rand(111111, 999999);
            $store = new Customer();
            $store->name = $request->name;
            $store->slug = $request->name;
            $store->phone = $request->phone;
            $store->password = bcrypt($password);
            $store->verify = 1;
            $store->status = 'active';
            $store->save();
            $customer_id = $store->id;
        }

        // order data save
        $order = Order::where('id', $request->order_id)->first();
        $order->amount = ($subtotal + $shippingfee) - ($discount);
        $order->paid = $request->paid;
        $order->due = $order->amount - $request->paid;
        $order->discount = $discount ? $discount : 0;
        $order->additional_shipping = $additional_shipping;
        $order->shipping_charge = $shippingfee;
        $order->customer_id = $customer_id;
        $order->category_id = $request->category_id;
        $order->order_status = 1;
        $order->admin_note = $request->admin_note;
        $order->save();

        $shipping = Shipping::updateOrCreate(
            ['order_id' => $request->order_id],  // Search criteria
            [
                'order_id' => $order->id,
                'customer_id' => $customer_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'area' => $shippingarea
            ]  // Data to update or create
        );

        // payment data save
        Payment::updateOrCreate(
            ['order_id' => $request->order_id],
            [
                'order_id' => $order->id,
                'customer_id' => $customer_id,
                'payment_method' => 'Cash On Delivery',
                'amount' => $order->amount,
                'payment_status' => 'pending',
            ]
        );

        // order details data save
        foreach ($order->orderdetails as $orderdetail) {
            $item = Cart::instance('pos_shopping')->content()->where('id', $orderdetail->product_id)->first();
            if (!$item) {
                $orderdetail->delete();
            }
        }
        foreach (Cart::instance('pos_shopping')->content() as $cart) {
            $exits = OrderDetails::where('id', $cart->options->details_id)->first();
            if ($exits) {
                $order_details = OrderDetails::find($exits->id);
                $order_details->product_discount = $cart->options->product_discount;
                $order_details->sale_price = $cart->price;
                $order_details->qty = $cart->qty;
                $order_details->save();
            } else {
                $order_details = new OrderDetails();
                $order_details->order_id = $order->id;
                $order_details->product_id = $cart->id;
                $order_details->product_name = $cart->name;
                $order_details->purchase_price = $cart->options->purchase_price;
                $order_details->product_discount = $cart->options->product_discount;
                $order_details->sale_price = $cart->price;
                $order_details->qty = $cart->qty;
                $order_details->save();
            }
        }

        Cart::instance('pos_shopping')->destroy();
        Session::forget('pos_shipping');
        Session::forget('pos_discount');
        Session::forget('product_discount');
        Session::forget('old_due');
        Session::forget('additional_shipping');
        Toastr::success('Thanks, Your order place successfully', 'Success!');
        return redirect('admin/order/pending');
    }

    public function order_report(Request $request)
    {
        $sale_category = OrderCategory::select('id', 'name')->where(['status' => 1])->get();
        $orders = OrderDetails::with('shipping', 'order')->whereHas('order', function ($query) {
            $query->where('order_status', 5);
        });
        if ($request->category_id) {
            $orders = $orders->whereHas('order', function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            });
        }
        if ($request->keyword) {
            $orders = $orders->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->start_date && $request->end_date) {
            $orders = $orders->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
        $total_purchases = $orders->sum(\DB::raw('purchase_price * qty'));
        $total_item = $orders->sum('qty');
        $total_sales = $orders->sum(\DB::raw('sale_price * qty'));
        $orders = $orders->paginate(100);
        return view('backEnd.reports.order', compact('orders', 'total_purchases', 'total_item', 'total_sales', 'sale_category'));
    }
    public function return_report(Request $request)
    {
        $orders = OrderDetails::where('is_return', '=', 1)->with('shipping', 'order')->whereHas('order', function ($query) {
            $query->where('order_status', 5);
        });
        if ($request->keyword) {
            $orders = $orders->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->start_date && $request->end_date) {
            $orders = $orders->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
        $total_purchases = $orders->sum(\DB::raw('purchase_price * qty'));
        $total_item = $orders->sum('qty');
        $total_sales = $orders->sum(\DB::raw('sale_price * qty'));
        $orders = $orders->paginate(100);
        return view('backEnd.reports.return_report', compact('orders', 'total_purchases', 'total_item', 'total_sales'));
    }
    public function replace_report(Request $request)
    {
        $orders = OrderDetails::where('is_replace', '=', 1)->with('shipping', 'order')->whereHas('order', function ($query) {
            $query->where('order_status', 5);
        });
        if ($request->keyword) {
            $orders = $orders->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->start_date && $request->end_date) {
            $orders = $orders->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
        $total_purchases = $orders->sum(\DB::raw('purchase_price * qty'));
        $total_item = $orders->sum('qty');
        $total_sales = $orders->sum(\DB::raw('sale_price * qty'));
        $orders = $orders->paginate(100);
        return view('backEnd.reports.replace_report', compact('orders', 'total_purchases', 'total_item', 'total_sales'));
    }
    public function stock_report(Request $request)
    {
        $products = Product::select('id', 'name', 'new_price', 'stock')
            ->where('status', 1);
        if ($request->keyword) {
            $products = $products->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->category_id) {
            $products = $products->where('category_id', $request->category_id);
        }
        if ($request->subcategory_id) {
            $products = $products->where('subcategory_id', $request->subcategory_id);
        }
        if ($request->childcategory_id) {
            $products = $products->where('childcategory_id', $request->childcategory_id);
        }
        if ($request->warehouse_id) {
            $products = $products->whereHas('stocks', function ($query) use ($request) {
                $query->where('warehouse_id', $request->warehouse_id);
            });
        }
        if ($request->start_date && $request->end_date) {
            $products = $products->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
        $total_purchase = $products->sum(\DB::raw('purchase_price * stock'));
        $total_stock = $products->sum('stock');
        $total_price = $products->sum(\DB::raw('new_price * stock'));
        $products = $products->paginate(100);
        // return $products;
        $categories = Category::where('status', 1)->get();
        $subcategories = [];
        if($request->category_id) {
            $subcategories = Subcategory::where('category_id', $request->category_id)->get();
        }
        $childcategories = [];
        if($request->subcategory_id) {
            $childcategories = Childcategory::where('subcategory_id', $request->subcategory_id)->get();
        }
        $warehouses = Warehouse::where('status', 1)->get();
        return view('backEnd.reports.stock', compact('products', 'categories', 'total_purchase', 'total_stock', 'total_price', 'subcategories', 'childcategories', 'warehouses'));
    }
    public function warehouse_report(Request $request)
    {
        $stocks = WarehouseStock::query();

        if ($request->warehouse_id) {
            $stocks = $stocks->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->product_id) {
            $stocks = $stocks->where('product_id', $request->product_id);
        }

        if ($request->start_date && $request->end_date) {
            $stocks = $stocks->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
        // $total_purchase = $stocks->sum(\DB::raw('purchase_price * stock'));
        // $total_stock = $stocks->sum('stock');
        // $total_price = $stocks->sum(\DB::raw('new_price * stock'));
        $stocks = $stocks->paginate(100);
        $products = Product::where('status', 1)->get();
        $warehouses = Warehouse::where('status', 1)->get();

        return view('backEnd.reports.warehouse', compact('stocks','products', 'warehouses'));
    }
    public function expense_report(Request $request)
    {
        $data = Expense::where('status', 1);
        if ($request->keyword) {
            $data = $data->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->warehouse_id) {
            $data = $data->where('warehouse_id', $request->warehouse_id);
        }
        if ($request->category_id) {
            $data = $data->where('expense_cat_id', $request->category_id);
        }
        if ($request->subcategory_id) {
            $data = $data->where('subcategory_id', $request->subcategory_id);
        }
        if ($request->start_date && $request->end_date) {
            $data = $data->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
        $data = $data->paginate(100);
        $categories = ExpenseCategories::where('status', 1)->get();

        $warehouses = Warehouse::where('status', 1)->get();
        return view('backEnd.reports.expense', compact('data', 'categories', 'warehouses'));
    }
    public function asset_report(Request $request)
    {
        $data = Asset::where('status', 1);
        if ($request->keyword) {
            $data = $data->where('name', 'LIKE', '%' . $request->keyword . "%");
        }
        if ($request->category_id) {
            $data = $data->where('category_id', $request->category_id);
        }
        if ($request->start_date && $request->end_date) {
            $data = $data->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }
        $data = $data->paginate(100);
        $assetcategory = AssetCategory::where('status', 1)->get();
        return view('backEnd.reports.asset', compact('data', 'assetcategory'));
    }
    public function loss_profit(Request $request)
    {
        if ($request->start_date && $request->end_date) {
            $total_expense = Expense::where('status', 1)->whereBetween('created_at', [$request->start_date, $request->end_date])->sum('amount');
            $total_purchase = OrderDetails::whereHas('order', function ($query) use ($request) {
                $query->where('order_status', 5)
                    ->whereBetween('created_at', [$request->start_date, $request->end_date]);
            })->sum(\DB::raw('purchase_price * qty'));

            $total_sales = OrderDetails::whereHas('order', function ($query) use ($request) {
                $query->where('order_status', 5)
                    ->whereBetween('created_at', [$request->start_date, $request->end_date]);
            })->sum(\DB::raw('sale_price * qty'));
        } else {
            $total_expense = Expense::where('status', 1)->sum('amount');
            $total_purchase = OrderDetails::whereHas('order', function ($query) {
                $query->where('order_status', 5);
            })->sum(\DB::raw('purchase_price * qty'));

            $total_sales = OrderDetails::whereHas('order', function ($query) {
                $query->where('order_status', 5);
            })->sum(\DB::raw('sale_price * qty'));
        }

        return view('backEnd.reports.loss_profit', compact('total_expense', 'total_purchase', 'total_sales'));
    }
    public function order_paid(Request $request)
    {
        $amount = $request->amount;

        Session::put('cpaid', $amount);
        return response()->json($amount);
    }
    public function additional_shipping(Request $request)
    {
        $amount = $request->amount;
        Session::put('additional_shipping', $amount);
        return response()->json($amount);
    }
    public function customer_select(Request $request) {
        $customer = Customer::where('id', $request->id)->first();
        $due = $customer->due ?? 0;
        Session::put('old_due', $due);
        return response()->json($customer);
    }
}
