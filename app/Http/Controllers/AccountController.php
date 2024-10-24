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
use App\Models\Category;
use App\Models\JobType;
use App\Models\BoardJob;

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
        $request->validate([
                    'name'=>'required|min:5',
                    'email'=>'required|email|unique:users,email,'.$id.',id',
                    'designation'=>'required',
                    'phone'=>'required'
                ]);
            
                $user = User::find($id);

                if ($user) {
                    $user->name = $request->name;
                    $user->email = $request->email;
                    $user->mobile = $request->phone;
                    $user->designation = $request->designation;
                    $user->save();
            
                    session()->flash('success', 'Profile updated successfully.');
            
                    return redirect()->back()->with('success', 'Profile updated successfully.');
                } else {
                    return redirect()->back()->withErrors($request->all);
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
    public function createJob() {

        $categories = Category::orderBy('name','ASC')->where('status',1)->get();

        $jobTypes = JobType::orderBy('name','ASC')->where('status',1)->get();

        return view('front.account.job.create',[
            'categories' =>  $categories,
            'jobTypes' =>  $jobTypes,
        ]);
    }
    public function saveJob(Request $request) {
        // Validate the request
        $request->validate([
            'title' => 'required|min:5|max:200',
            'category' => 'required',
            'jobType' => 'required',
            'vacancy' => 'required|integer',
            'location' => 'required|max:50',
            'description' => 'required',
            'company_name' => 'required|min:3|max:75',
        ]);
    
        try {
            // Create a new job instance and populate it
            $job = new BoardJob();
            $job->title = $request->title;
            $job->category_id = $request->category;
            $job->job_type_id = $request->jobType;
            $job->user_id = Auth::user()->id;
            $job->vacancy = $request->vacancy;
            $job->salary = $request->salary; 
            $job->location = $request->location;
            $job->description = $request->description;
            $job->benefits = $request->benefits; 
            $job->responsibility = $request->responsibility; 
            $job->qualifications = $request->qualifications; 
            $job->keywords = $request->keywords; 
            $job->experience = $request->experience; 
            $job->company_name = $request->company_name;
            $job->company_location = $request->company_location; 
            $job->company_website = $request->website; 
    
            // Save the job
            $job->save();
            session()->flash('success', 'Job added successfully.');
            return redirect()->route('account.myJobs')->with('success', 'Job added successfully.');
        } catch (\Exception $e) {
            // Return with errors if the job could not be created
            return redirect()->back()->withErrors($request->all);
        }
    }
    public function myJobs() {    
        $jobs = BoardJob::where('user_id',Auth::user()->id)->with('jobType')->orderBy('created_at','DESC')->paginate(10);        
        return view('front.account.job.my-jobs',[
            'jobs' => $jobs
        ]);
    } 
    public function editJob(Request $request, $id) {
        
        $categories = Category::orderBy('name','ASC')->where('status',1)->get();
        $jobTypes = JobType::orderBy('name','ASC')->where('status',1)->get();

        $job = BoardJob::where([
            'user_id' => Auth::user()->id,
            'id' => $id
        ])->first();

        if ($job == null) {
            abort(404);
        }

        return view('front.account.job.edit',[
            'categories' => $categories,
            'jobTypes' => $jobTypes,
            'job' => $job,
        ]);
    }
    public function updateJob(Request $request, $id) {

        $request->validate([
            'title' => 'required|min:5|max:200',
            'category' => 'required',
            'jobType' => 'required',
            'vacancy' => 'required|integer',
            'location' => 'required|max:50',
            'description' => 'required',
            'company_name' => 'required|min:3|max:75',          

        ]);

            try{

            $job = BoardJob::find($id);
            $job->title = $request->title;
            $job->category_id = $request->category;
            $job->job_type_id  = $request->jobType;
            $job->user_id = Auth::user()->id;
            $job->vacancy = $request->vacancy;
            $job->salary = $request->salary;
            $job->location = $request->location;
            $job->description = $request->description;
            $job->benefits = $request->benefits;
            $job->responsibility = $request->responsibility;
            $job->qualifications = $request->qualifications;
            $job->keywords = $request->keywords;
            $job->experience = $request->experience;
            $job->company_name = $request->company_name;
            $job->company_location = $request->company_location;
            $job->company_website = $request->website;
            $job->save();

            session()->flash('success','Job updated successfully.');
        
        return redirect()->route('account.myJobs')->with('success', 'Job updated successfully.');
    } catch (\Exception $e) {
        // Return with errors if the job could not be created
        return redirect()->back()->withErrors($request->all);
    }

    }
    public function deleteJob(Request $request) {

        $job = BoardJob::where([
            'user_id' => Auth::user()->id,
            'id' => $request->jobId
        ])->first();


        if ($job == null) {
            session()->flash('error','Either job deleted or not found.');
            return response()->json([
                'status' => true
            ]);
        }

        BoardJob::where('id',$request->jobId)->delete();
        session()->flash('success','Job deleted successfully.');
        return response()->json([
            'status' => true
        ]);

    }

}

