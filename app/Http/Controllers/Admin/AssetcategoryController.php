<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\AssetCategory;
class AssetcategoryController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:asset-category-list|asset-category-create|asset-category-edit|asset-category-delete', ['only' => ['index','store']]);
         $this->middleware('permission:asset-category-create', ['only' => ['create','store']]);
         $this->middleware('permission:asset-category-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:asset-category-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = AssetCategory::orderBy('id','DESC')->get();
        return view('backEnd.asset.category.index',compact('data'));
    }
    public function create()
    {
        return view('backEnd.asset.category.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);
        $input = $request->all();
        AssetCategory::create($input);
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('asset.categories.index');
    }
    
    public function edit($id)
    {
        $edit_data = AssetCategory::find($id);
        return view('backEnd.asset.category.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $update_data = AssetCategory::find($request->id);
        $input = $request->all();
        $input['status'] = $request->status?1:0;
        $update_data->update($input);
        Toastr::success('Success','Data update successfully');
        return redirect()->route('asset.categories.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = AssetCategory::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = AssetCategory::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = AssetCategory::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
