<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Brian2694\Toastr\Facades\Toastr;
use DB;
class CollectionController extends Controller
{
    public function index(){
     $customers = DB::table('customers')->select('id','name','phone')->get();
      $data = Transaction::where(['type'=>'sell'])->with('customer')->paginate(100);
       return view('backEnd.collection.index',compact('data','customers'));
    }
    public function create(){
        $customers = DB::table('customers')->select('id','name','phone')->get();
       return view('backEnd.collection.create',compact('customers'));
    }
    public function store(Request $request){
        $this->validate($request, [
            'title' => 'required',
            'user_id' => 'required',
            'amount' => 'required',
            'method' => 'required',
        ]);

        $input = $request->all();
        $input['type'] = 'sell';
        Transaction::create($input);
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('admin.collection.index');
    }
    public function edit($id){
        $customers = DB::table('customers')->select('id','name','phone')->get();
        $edit_data = Transaction::find($id);
       return view('backEnd.collection.edit',compact('customers','edit_data'));
    }
    public function update(Request $request){
        $this->validate($request, [
            'title' => 'required',
            'user_id' => 'required',
            'amount' => 'required',
            'method' => 'required',
        ]);

        $update_data = Transaction::find($request->id);
        $input = $request->all();
        $input['type'] = 'sell';
        $update_data->update($input);
        Toastr::success('Success','Data update successfully');
        return redirect()->route('admin.collection.index');
    }
}
