<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CmsController extends Controller
{
    /**
     * Show the CMS dashboard page
     */
    public function dashboard(): View
    {
        return view('cms.dashboard');
    }
}
