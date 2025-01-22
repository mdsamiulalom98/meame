<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Transaction;
use App\Models\Supplier;
use App\Models\Customer;
use InvalidArgumentException;
use DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user == 'supplier') {
            $users = DB::table('suppliers')->select('id', 'name', 'phone')->get();
        } else {
            $users = DB::table('customers')->select('id', 'name', 'phone')->get();
        }

        $data = Transaction::where(['user' => $request->user])->with('customer', 'supplier')->paginate(100);
        return view('backEnd.payment.index', compact('data', 'users'));
    }
    public function create(Request $request)
    {
        if (!in_array($request->user, ['customer', 'supplier'])) {
            throw new InvalidArgumentException('Invalid user type');
        }

        $table = $request->user === 'customer' ? 'customers' : 'suppliers';
        $users = DB::table($table)->select('id', 'name', 'phone', 'due')->get();

        return view('backEnd.payment.create', compact('users'));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'user_id' => 'required',
            'amount' => 'required',
            'method' => 'required',
        ]);

        $input = $request->all();
        $input['type'] = 'payment';
        $input['user'] = $request->user;
        Transaction::create($input);

        if ($request->user == 'supplier') {
            $supplier = Supplier::find($request->user_id);
            $supplier->amount += $request->amount;
            $supplier->paid += $request->amount;
            $supplier->due -= $request->amount;
            $supplier->save();
        } else {
            $customer = Customer::find($request->user_id);
            $customer->amount += $request->amount;
            $customer->paid += $request->amount;
            $customer->due -= $request->amount;
            $customer->save();
        }

        Toastr::success('Success', 'Data insert successfully');
        return redirect()->route('admin.payment.index', ['user' => $request->user]);
    }
    public function edit($id)
    {
        $edit_data = Transaction::find($id);
        if (!in_array($edit_data->user, ['customer', 'supplier'])) {
            throw new InvalidArgumentException('Invalid user type');
        }

        $table = $edit_data->user === 'customer' ? 'customers' : 'suppliers';
        $users = DB::table($table)->select('id', 'name', 'phone', 'due')->get();
        return view('backEnd.payment.edit', compact('users', 'edit_data'));
    }
    public function update(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'user_id' => 'required',
            'amount' => 'required',
            'method' => 'required',
        ]);

        $update_data = Transaction::find($request->id);
        $input = $request->all();
        $input['type'] = 'payment';
        $update_data->update($input);
        Toastr::success('Success', 'Data update successfully');
        return redirect()->route('admin.payment.index');
    }

    public function user_select(Request $request) {
        $table = $request->user === 'customer' ? 'customers' : 'suppliers';
        $user = DB::table($table)->where('id', $request->id)->select('id', 'name', 'phone', 'due')->first();
        $due = $user->due ?? 0;
        Session::put('old_due', $due);
        return response()->json($user);
    }
}
