<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Brian2694\Toastr\Facades\Toastr;
use Intervention\Image\Facades\Image;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\IpBlock;

class CustomerManageController extends Controller
{
    public function index(Request $request)
    {
        $show_data = Customer::when($request->keyword, function ($query, $keyword) {
            $query->where('phone', $keyword)
                ->orWhere('name', $keyword);
        })->paginate(20);

        return view('backEnd.customer.index', compact('show_data'));
    }

    public function create()
    {
        return view('backEnd.customer.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required|unique:customers',
            'email' => 'required',
            'address' => 'required',
        ]);

        $last_id = Customer::orderBy('id', 'desc')->first();
        $last_id = $last_id ? $last_id->id + 1 : 1;

        $input = $request->all();
        $input['slug'] = strtolower(preg_replace('/\s+/', '-', $request->name));
        $input['slug'] = str_replace('/', '', $input['slug']);
        $input['slug'] = strtolower(Str::slug($input['slug'] . '-' . $last_id));
        $input['status'] = $request->status ? 'active' : 'pending';
        $input['password'] = bcrypt($request->password);

        // new image
        $image = $request->file('image');
        if ($image) {
            // image with intervention
            $name = time() . '-' . $image->getClientOriginalName();
            $name = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $name);
            $name = strtolower(preg_replace('/\s+/', '-', $name));
            $uploadpath = 'public/uploads/customer/';
            $imageUrl = $uploadpath . $name;
            $img = Image::make($image->getRealPath());
            $img->encode('webp', 90);
            $width = 100;
            $height = 100;
            $img->height() > $img->width() ? $width = null : $height = null;
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($imageUrl);
            $input['image'] = $imageUrl;
        }
        // dd($input);
        Customer::create($input);
        Toastr::success('Success', 'Data insert successfully');
        return redirect()->route('customers.index');
    }

    public function edit($id)
    {
        $edit_data = Customer::find($id);
        return view('backEnd.customer.edit', compact('edit_data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'address' => 'required',
        ]);

        $input = $request->except('hidden_id');
        $update_data = Customer::find($request->hidden_id);

        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        // new image
        $image = $request->file('image');
        if ($image) {
            // image with intervention
            $name = time() . '-' . $image->getClientOriginalName();
            $name = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp', $name);
            $name = strtolower(preg_replace('/\s+/', '-', $name));
            $uploadpath = 'public/uploads/customer/';
            $imageUrl = $uploadpath . $name;
            $img = Image::make($image->getRealPath());
            $img->encode('webp', 90);
            $width = 100;
            $height = 100;
            $img->height() > $img->width() ? $width = null : $height = null;
            $img->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($imageUrl);
            $input['image'] = $imageUrl;
            File::delete($update_data->image);
        } else {
            $input['image'] = $update_data->image;
        }
        $input['slug'] = strtolower(preg_replace('/\s+/', '-', $request->name));
        $input['slug'] = str_replace('/', '', $input['slug']);
        $input['slug'] = strtolower(Str::slug($input['slug'] . '-' . $request->hidden_id));
        $input['status'] = $request->status ? 'active' : 'pending';
        $update_data->update($input);

        Toastr::success('Success', 'Data update successfully');
        return redirect()->route('customers.index');
    }

    public function inactive(Request $request)
    {
        $inactive = Customer::find($request->hidden_id);
        $inactive->status = 'inactive';
        $inactive->save();
        Toastr::success('Success', 'Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Customer::find($request->hidden_id);
        $active->status = 'active';
        $active->save();
        Toastr::success('Success', 'Data active successfully');
        return redirect()->back();
    }
    public function profile(Request $request)
    {
        $profile = Customer::with('orders')->find($request->id);
        $transaction = Transaction::where(['user' => 'customer', 'user_id' => $profile->id])->orderBy('id', 'asc')->get();
        return view('backEnd.customer.profile', compact('profile', 'transaction'));
    }
    public function adminlog(Request $request)
    {
        $customer = Customer::find($request->hidden_id);
        Auth::guard('customer')->loginUsingId($customer->id);
        return redirect()->route('customer.account');
    }
    public function ip_block(Request $request)
    {
        $data = IpBlock::get();
        return view('backEnd.reports.ipblock', compact('data'));
    }
    public function ipblock_store(Request $request)
    {

        $store_data = new IpBlock();
        $store_data->ip_no = $request->ip_no;
        $store_data->reason = $request->reason;
        $store_data->save();
        Toastr::success('Success', 'IP address add successfully');
        return redirect()->back();
    }
    public function ipblock_update(Request $request)
    {
        $update_data = IpBlock::find($request->id);
        $update_data->ip_no = $request->ip_no;
        $update_data->reason = $request->reason;
        $update_data->save();
        Toastr::success('Success', 'IP address update successfully');
        return redirect()->back();
    }
    public function ipblock_destroy(Request $request)
    {
        $delete_data = IpBlock::find($request->id)->delete();
        Toastr::success('Success', 'IP address delete successfully');
        return redirect()->back();
    }
}
