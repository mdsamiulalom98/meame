<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\WarehouseStock;
use App\Models\Warehouse;
use App\Models\WarehouseTransfer;

class WarehouseController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:warehouse-list|warehouse-create|warehouse-edit|warehouse-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:warehouse-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:warehouse-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:warehouse-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = Warehouse::orderBy('id', 'DESC')->get();
        return view('backEnd.warehouse.index', compact('data'));
    }
    public function create()
    {
        return view('backEnd.warehouse.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);
        $last_id = Warehouse::orderBy('id', 'desc')->select('id')->first();
        $last_id = $last_id ? $last_id->id + 1 : 1;
        $input = $request->all();
        $input['slug'] = strtolower(preg_replace('/[\/\s]+/', '-', $request->name . '-' . $last_id));
        Warehouse::create($input);
        Toastr::success('Success', 'Data insert successfully');
        return redirect()->route('warehouses.index');
    }

    public function edit($id)
    {
        $edit_data = Warehouse::find($id);
        return view('backEnd.warehouse.edit', compact('edit_data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $update_data = Warehouse::find($request->id);
        $input = $request->all();
        $input['slug'] = strtolower(preg_replace('/[\/\s]+/', '-', $request->name . '-' . $update_data->id));
        $input['status'] = $request->status ? 1 : 0;

        $update_data->update($input);

        Toastr::success('Success', 'Data update successfully');
        return redirect()->route('warehouses.index');
    }



    public function inactive(Request $request)
    {
        $inactive = Warehouse::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success', 'Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Warehouse::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success', 'Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = Warehouse::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success', 'Data delete successfully');
        return redirect()->back();
    }
    public function profile(Request $request)
    {
        $profile = Warehouse::find($request->id);
        $profile->stock = $profile->stocks->sum('stock');
        $profile->products = $profile->stocks->count();
        $profile->purchase = $profile->stocks->sum('stock') + $profile->stocks->sum('sold');
        $profile->sold = $profile->stocks->sum('sold');
        $profile->save();
        $instocks = WarehouseStock::where(['warehouse_id' => $profile->id])->where('stock', '>', 0)->orderBy('id', 'asc')->get();
        $totalstocks = WarehouseStock::where('warehouse_id', $profile->id)->orderBy('id', 'DESC')->get();
        $allwarehouses = Warehouse::where('id', '!=', $request->id)->get();
        return view('backEnd.warehouse.profile', compact('profile', 'instocks', 'totalstocks', 'allwarehouses'));
    }

    public function stock_change(Request $request)
    {
        $this->validate($request, [
            'stock' => 'required',
            'from' => 'required',
            'product_id' => 'required',
            'to' => 'required',
        ]);
        $fromWarehouse = WarehouseStock::where('product_id', $request->product_id)
            ->where('warehouse_id', $request->from)
            ->first();
        $toWarehouse = WarehouseStock::where('product_id', $request->product_id)
            ->where('warehouse_id', $request->to)
            ->first();
        if ($fromWarehouse && $fromWarehouse->stock >= $request->stock) {
            $fromWarehouse->stock -= $request->stock;
            $fromWarehouse->save();

            if ($toWarehouse) {
                $toWarehouse->stock += $request->stock;
                $toWarehouse->save();
            } else {
                // If "to" warehouse doesn't exist, create a new entry
                WarehouseStock::create([
                    'product_id' => $request->product_id,
                    'warehouse_id' => $request->to,
                    'stock' => $request->stock,
                ]);
            }
        } else {
            Toastr::error('error', 'Insufficient stock in the selected warehouse!');
            return redirect()->back();
        }


        $input = $request->all();
        WarehouseTransfer::create($input);

        Toastr::success('Success', 'Data updated successfully');
        return redirect()->back();
    }

    public function transfers()
    {
        $data = WarehouseTransfer::get();
        return view('backEnd.warehouse.transfers', compact('data'));
    }
}
