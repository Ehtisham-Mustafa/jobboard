<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\JobType;
use App\Models\BoardJob;
class HomeController extends Controller
{
    public function index() {

        $categories = Category::where('status',1)->orderBy('name','ASC')->take(8)->get();

        $newCategories = Category::where('status',1)->orderBy('name','ASC')->get();

        $featuredJobs = BoardJob::where('status',1)
                        ->orderBy('created_at','DESC')
                        ->with('jobType')
                        ->where('isFeatured',1)->take(6)->get();

        $latestJobs = BoardJob::where('status',1)
                        ->with('jobType')
                        ->orderBy('created_at','DESC')
                        ->take(6)->get();



        return view('front.home',[
            'categories' => $categories,
            'featuredJobs' => $featuredJobs,
            'latestJobs' => $latestJobs,
            'newCategories' => $newCategories
        ]);
    }
}
