<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Transaction;
use App\Models\Supplier;
use Brian2694\Toastr\Facades\Toastr;
use Intervention\Image\Facades\Image;
use File;
use Str;
class SupplierController extends Controller
{
    function __construct()
    {
         // $this->middleware('permission:supplier-list|supplier-create|supplier-edit|supplier-delete', ['only' => ['index','store']]);
         // $this->middleware('permission:supplier-create', ['only' => ['create','store']]);
         // $this->middleware('permission:supplier-edit', ['only' => ['edit','update']]);
         // $this->middleware('permission:supplier-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = Supplier::orderBy('id','DESC')->get();
        return view('backEnd.supplier.index',compact('data'));
    }
    public function create()
    {
        return view('backEnd.supplier.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);
        // image with intervention 

        $image = $request->file('image');
        if($image){
            $name =  time().'-'.$image->getClientOriginalName();
            $name = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name);
            $name = strtolower(preg_replace('/\s+/', '-', $name));
            $uploadpath = 'public/uploads/supplier/';
            $imageUrl = $uploadpath.$name; 
            $img=Image::make($image->getRealPath());
            $img->encode('webp', 90);
            $width = '';
            $height = '';
            $img->height() > $img->width() ? $width=null : $height=null;
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($imageUrl);
        }else{
            $imageUrl = NULL;
        }

        $input = $request->all();
        $input['slug'] = strtolower(Str::slug($request->name));
        $input['status'] = $request->status?1:0;
        $input['image'] = $imageUrl;
        Supplier::create($input);
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('supplier.index');
    }
    
    public function edit($id)
    {
        $edit_data = Supplier::find($id);
        return view('backEnd.supplier.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $update_data = Supplier::find($request->id);
        $input = $request->all();
        $image = $request->file('image');
        if($image){
            // image with intervention 
            $name =  time().'-'.$image->getClientOriginalName();
            $name = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name);
            $name = strtolower(preg_replace('/\s+/', '-', $name));
            $uploadpath = 'public/uploads/supplier/';
            $imageUrl = $uploadpath.$name; 
            $img=Image::make($image->getRealPath());
            $img->encode('webp', 90);
            $width = '';
            $height = '';
            $img->height() > $img->width() ? $width=null : $height=null;
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($imageUrl);
            $input['image'] = $imageUrl;
            File::delete($update_data->image);
        }else{
            $input['image'] = $update_data->image;
        }
        $input['slug'] = strtolower(Str::slug($request->name));
        $input['status'] = $request->status?1:0;
        $update_data->update($input);

        Toastr::success('Success','Data update successfully');
        return redirect()->route('supplier.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = Supplier::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Supplier::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = Supplier::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
    public function profile(Request $request){
        $profile = Supplier::find($request->id);
        $purchase = Purchase::where(['supplier_id'=>$profile->id])->orderBy('id','asc')->get();
        $transaction = Transaction::where(['user'=>'supplier','user_id'=>$profile->id])->orderBy('id','asc')->get();
        return view('backEnd.supplier.profile',compact('profile','purchase','transaction'));
    }
}
