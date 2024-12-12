<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobsController;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('jobs',[JobsController::class,'index'])->name('jobs');
Route::get('jobs/detail/{id}',[JobsController::class,'detail'])->name('jobDetail');
Route::post('jobs/apply-job',[JobsController::class,'applyJob'])->name('applyJob');
Route::post('save-job',[JobsController::class,'saveJob'])->name('saveJob');

Route::group(['prefix'=>'account'],function(){
    //Guest Route
Route::group(['middleware'=>'guest'],function(){
    Route::get('register', [AccountController::class, 'registration'])->name('account.registration') ;
    Route::post('process-register', [AccountController::class, 'process_registration'])->name('account.process_registration') ;
    Route::get('login', [AccountController::class, 'login'])->name('account.login') ;
    Route::post('authenticate', [AccountController::class, 'authenticate'])->name('account.authenticate') ;

});
//Authenticated Users
    Route::group(['middleware'=>'auth'],function(){
        Route::get('profile', [AccountController::class, 'profile'])->name('account.profile') ;
        Route::post('profile', [AccountController::class, 'updateProfile'])->name('account.updateProfile') ;
        Route::get('logout', [AccountController::class, 'logout'])->name('account.logout') ;
        Route::post('update-profile-pic', [AccountController::class, 'updateProfilePic'])->name('account.updateProfilePic') ;

        //JOb routes

        Route::get('create-job',[AccountController::class,'createJob'])->name('account.createJob');   
        Route::post('create-job',[AccountController::class,'saveJob'])->name('account.saveJob');   
        Route::get('my-jobs',[AccountController::class,'myJobs'])->name('account.myJobs');  
        Route::get('my-jobs/edit/{jobId}',[AccountController::class,'editJob'])->name('account.editJob');  
        Route::post('my-jobs/edit/{jobId}',[AccountController::class,'updateJob'])->name('account.updateJob');   


        Route::post('delete-job',[AccountController::class,'deleteJob'])->name('account.deleteJob');   
        Route::get('/my-job-applications',[AccountController::class,'myJobApplications'])->name('account.myJobApplications');  

        Route::post('/remove-job-application',[AccountController::class,'removeJobs'])->name('account.removeJobs');   
        Route::get('/saved-jobs',[AccountController::class,'savedJobs'])->name('account.savedJobs');  
        Route::post('/remove-saved-job',[AccountController::class,'removeSavedJob'])->name('account.removeSavedJob');   
        Route::post('/update-password',[AccountController::class,'updatePassword'])->name('account.updatePassword');   


    });
});


