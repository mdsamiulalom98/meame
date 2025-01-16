<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\ExpenseSubcategory;
use App\Models\ExpenseCategories;

class ExpenseSubcategoryController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:expensesubcategory-list|expensesubcategory-create|expensesubcategory-edit|expensesubcategory-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:expensesubcategory-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:expensesubcategory-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:expensesubcategory-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = ExpenseSubcategory::orderBy('id', 'DESC')->get();
        // return $data;
        return view('backEnd.expensesubcategory.index', compact('data'));
    }
    public function create()
    {
        $categories = ExpenseCategories::orderBy('id', 'DESC')->select('id', 'name')->get();
        return view('backEnd.expensesubcategory.create', compact('categories'));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);

        $input = $request->all();
        ExpenseSubcategory::create($input);
        Toastr::success('Success', 'Data insert successfully');
        return redirect()->route('expensesubcategories.index');
    }

    public function edit($id)
    {
        $edit_data = ExpenseSubcategory::find($id);
        $categories = ExpenseCategories::select('id', 'name')->get();
        return view('backEnd.expensesubcategory.edit', compact('edit_data', 'categories'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $update_data = ExpenseSubcategory::find($request->id);
        $input = $request->all();
        $input['status'] = $request->status ? 1 : 0;

        $update_data->update($input);

        Toastr::success('Success', 'Data update successfully');
        return redirect()->route('expensesubcategories.index');
    }

    public function inactive(Request $request)
    {
        $inactive = ExpenseSubcategory::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success', 'Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = ExpenseSubcategory::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success', 'Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = ExpenseSubcategory::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success', 'Data delete successfully');
        return redirect()->back();
    }
}
