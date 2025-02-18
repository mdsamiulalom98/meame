<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssetCategory;
use App\Models\Asset;
use Toastr;
use File;
use DB;
class AssetController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:asset-list|asset-create|asset-edit|asset-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:asset-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:asset-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:asset-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = Asset::orderBy('id', 'DESC')->with('category')->get();
        return view('backEnd.asset.index', compact('data'));
    }
    public function create()
    {
        $assetcategory = AssetCategory::where('status', 1)->get();
        return view('backEnd.asset.create', compact('assetcategory'));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'category_id' => 'required',
            'name' => 'required',
            'status' => 'required',
        ]);
        $image = $request->file('image');
        if ($image) {
            // image with intervention
            $file = $request->file('image');
            $name = time() . $file->getClientOriginalName();
            $uploadPath = 'public/uploads/asset/';
            $file->move($uploadPath, $name);
            $fileUrl = $uploadPath . $name;
        } else {
            $fileUrl = null;
        }
        $input = $request->all();
        $input['image'] = $fileUrl;
        Asset::create($input);
        Toastr::success('Success', 'Data insert successfully');
        return redirect()->route('asset.index');
    }

    public function edit($id)
    {
        $edit_data = Asset::find($id);
        $assetcategory = AssetCategory::select('id', 'name')->get();
        return view('backEnd.asset.edit', compact('edit_data', 'assetcategory'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'status' => 'required',
        ]);
        $update_data = Asset::find($request->id);
        $input = $request->all();

        $image = $request->file('image');
        if ($image) {
            // image with intervention
            $file = $request->file('image');
            $name = time() . $file->getClientOriginalName();
            $uploadPath = 'public/uploads/asset/';
            $file->move($uploadPath, $name);
            $fileUrl = $uploadPath . $name;
            $input['image'] = $fileUrl;
            File::delete($update_data->image);
        } else {
            $input['image'] = $update_data->image;
        }

        $input['status'] = $request->status ? 1 : 0;

        $update_data->update($input);

        Toastr::success('Success', 'Data update successfully');
        return redirect()->route('asset.index');
    }

    public function inactive(Request $request)
    {
        $inactive = Asset::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success', 'Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Asset::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success', 'Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = Asset::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success', 'Data delete successfully');
        return redirect()->back();
    }
}
