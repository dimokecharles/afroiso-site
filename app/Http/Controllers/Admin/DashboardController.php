<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enquiry;
use App\Models\Publication;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'unread' => Enquiry::where('is_read', false)->count(),
            'enquiries' => Enquiry::latest()->limit(8)->get(),
            'courseCount' => Course::count(),
            'publicationCount' => Publication::count(),
            'certificateCount' => Certificate::count(),
        ]);
    }
}
