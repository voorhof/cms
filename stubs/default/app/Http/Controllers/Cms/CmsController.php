<?php

namespace App\Http\Controllers\Cms;

use Illuminate\View\View;

class CmsController extends BaseCmsController
{
    /**
     * Show the CMS dashboard page
     */
    public function dashboard(): View
    {
        return view('cms.dashboard');
    }
}
