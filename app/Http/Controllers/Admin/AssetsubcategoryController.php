<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\AssetSubcategory;
use App\Models\AssetCategory;

class AssetsubcategoryController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:asset-subcategory-list|asset-subcategory-create|asset-subcategory-edit|asset-subcategory-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:asset-subcategory-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:asset-subcategory-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:asset-subcategory-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = AssetSubcategory::orderBy('id', 'DESC')->get();
        return view('backEnd.asset.subcategory.index', compact('data'));
    }
    public function create()
    {
        $categories = AssetCategory::orderBy('id', 'DESC')->select('id', 'name')->get();
        return view('backEnd.asset.subcategory.create', compact('categories'));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);
        $input = $request->all();
        AssetSubcategory::create($input);
        Toastr::success('Success', 'Data insert successfully');
        return redirect()->route('asset.subcategories.index');
    }

    public function edit($id)
    {
        $edit_data = AssetSubcategory::find($id);
        $categories = AssetCategory::orderBy('id', 'DESC')->select('id', 'name')->get();
        return view('backEnd.asset.subcategory.edit', compact('edit_data', 'categories'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $update_data = AssetSubcategory::find($request->id);
        $input = $request->all();
        $input['status'] = $request->status ? 1 : 0;
        $update_data->update($input);
        Toastr::success('Success', 'Data update successfully');
        return redirect()->route('asset.subcategories.index');
    }

    public function inactive(Request $request)
    {
        $inactive = AssetSubcategory::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success', 'Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = AssetSubcategory::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success', 'Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = AssetSubcategory::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success', 'Data delete successfully');
        return redirect()->back();
    }
}
