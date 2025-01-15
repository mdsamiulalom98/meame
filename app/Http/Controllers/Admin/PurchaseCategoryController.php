<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\PurchaseCategory;
class PurchaseCategoryController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:purchase-category-list|purchase-category-create|purchase-category-edit|purchase-category-delete', ['only' => ['index','store']]);
         $this->middleware('permission:purchase-category-create', ['only' => ['create','store']]);
         $this->middleware('permission:purchase-category-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:purchase-category-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = PurchaseCategory::orderBy('id','DESC')->get();
        return view('backEnd.purchase.category.index',compact('data'));
    }
    public function create()
    {
        return view('backEnd.purchase.category.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);
        $input = $request->all();
        PurchaseCategory::create($input);
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('purchase.categories.index');
    }
    
    public function edit($id)
    {
        $edit_data = PurchaseCategory::find($id);
        return view('backEnd.purchase.category.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $update_data = PurchaseCategory::find($request->id);
        $input = $request->all();
        $input['status'] = $request->status?1:0;
        $update_data->update($input);
        Toastr::success('Success','Data update successfully');
        return redirect()->route('purchase.categories.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = PurchaseCategory::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = PurchaseCategory::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = PurchaseCategory::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}