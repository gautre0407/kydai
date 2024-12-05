<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SlideBanner;
use App\MostView;
use App\HotRelease;
use App\Post;
use App\QuickLink;
use App\Category;
use App\Classes;
use App\Interested;
use App\Options;
use App\Shortcut;
use Illuminate\Support\Facades\DB;
// use Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Session;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        return view('frontend.home');
    }

}
