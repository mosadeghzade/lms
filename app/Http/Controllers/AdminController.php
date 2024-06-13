<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    //
    public function   AdminDashboard(){

        return view('admin.index');
    }


    public function AdminLogout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('admin/login');
    }//End Method


    public function AdminLogin(){

        return view('admin.admin_login');
    }


    public function AdminProfile(){
    $id = Auth::user()->id;
    $ProfileData =  User::find($id);

    return view('admin.admin_profile_view',compact('ProfileData'));

    }//end method


    public function AdminProfileStore(Request $request){

        $id = Auth::user()->id;
        $data =  User::find($id);
        $data->username = $request->username;
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address  = $request->address;

        if($request->file('photo')){
            $file=$request->file('photo');
            @unlink(public_path('upload/admin_images/'.$data->photo));
            $filename =date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('upload/admin_images'),$filename);
            $data['photo']=$filename;
        }
        
        $data->save();
        $notification=array(

            'message'=>'Admin Profile Updated Successfully',
            'alert-type'=>'success'
        );

    
        return redirect()->back()->with($notification);
    
        }//end method


        public function AdminChangePassword(){


            $id = Auth::user()->id;
            $ProfileData =  User::find($id);

            return view('admin.admin_change_password',compact('ProfileData'));
        }




        public function AdminUpdatePassword(Request $request){


        //Validation
            $request->validate([
                'old_password' => 'required',
                'new_password' => 'required|confirmed'
                
            ]);

            //Match The Old Pass
            if(!Hash::check($request->old_password,auth::user()->password)){
                $notification=array(
    
                    'message'=>'Old Password Does not match',
                    'alert-type'=>'error'
                );
                return back()->with($notification);
            }
            //Update New Password

            User::whereId(auth()->user()->id)->update([
                'password'=>Hash::make($request->new_password)
            ]);
         
            $notification=array(
    
                'message'=>'Password Change Successfully',
                'alert-type'=>'success'
            );
            
            return back()->with($notification);
        
            }//end method
    
        
}
