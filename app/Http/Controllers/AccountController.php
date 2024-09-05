<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class AccountController extends Controller
{
    public function registration(){
        return view('front.account.registration');
    }
    public function process_registration(Request $request){
        $validator=Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:6|same:confirm_password',
            'confirm_password'=>'required|min:6',

        ]);
        if($validator->passes()){
            $user=new User();
            $user->name=$request->name;
            $user->email=$request->email;
            $user->password=Hash::make($request->password);
            $user->save();
            session()->flash('success','You have registered successfully.');

            return response()->json(['status'=>true,'error'=>[]]);

        }
        else{
        return response()->json(['status'=>false,'error'=>$validator->errors()]);
        }

    }
    public function login(){
        return view('front.account.login');

    }
    public function authenticate(Request $request){
        $validator=Validator::make($request->all(),[
           'email'=>'required|email',
            'password'=>'required'
        ]);
        if($validator->passes()){
            if(Auth::attempt([
                'email'=>$request->email,
                'password'=>$request->password])){
                return redirect()->route('account.profile');
            }else{
                return redirect()->route('account.login')
                    ->with('error','Either Email/Password is incorrect');
            }

        }
        else{
   return redirect()->route('account.login')
       ->withErrors($validator)
       ->withInput($request->only('email'));
        }
    }
    public function profile(){
        $id=Auth::id();
        $user=User::where('id',$id)->first();
        return view('front.account.profile',compact('user'));
    }
    public function updateProfile(Request $request){
        $id=Auth::id();
                $validator=Validator::make($request->all(),[
                    'name'=>'required|min:5',
                    'email'=>'required|email|unique:users,email,'.$id.',id',
                ]);
                if($validator->passes()){
                    $user=User::find($id);
                    $user->name=$request->name;
                    $user->email=$request->email;
                    $user->mobile=$request->phone;
                    $user->designation=$request->designation;
                    $user->save();
                    Session()->flash('success','Profile updated successfully.');
                    return response()->json(['status'=>true,'errors'=>[]]);
                }else{
                    return response()->json(['status'=>false,'errors'=>$validator->errors()]);
                }
    }
    public function logout()
    {
        Auth::logout();
        return redirect()->route('account.login');
    }

    public function updateProfilePic(Request $request){
        $id=Auth::id();
        $validator=Validator::make($request->all(),[
            'image'=>'required|image',
        ]);

        if($validator->passes()){
            $image=$request->image;
            $ext=$image->getClientOriginalExtension();
            $imageName=$id.'-'.time().'.'.$ext;
            $image->move(public_path('profile_pic'),$imageName);
            //create a small thumbnail
            $source_path=public_path('profile_pic/'.$imageName);
            $manager = new ImageManager(Driver::class);
            $image = $manager->read($source_path);
            $image->cover(150, 150);
            $image->toPng()->save(public_path('profile_pic/thumb/'.$imageName));
            //end thumbnail code

            //Delete old profile pic
            File::delete(public_path('profile_pic/thumb/'.Auth::user()->image));
            File::delete(public_path('profile_pic/'.Auth::user()->image));

            User::where('id',$id)->update(['image'=>$imageName]);
            Session()->flash('success','Profile picture updated successfully.');

          return response()->json(['status'=>true,'errors'=>[]]);
        }
        else{
            return response()->json(['status'=>false,'errors'=>$validator->errors()]);
        }
    }
}

