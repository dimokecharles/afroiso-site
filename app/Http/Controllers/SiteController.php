<?php

namespace App\Http\Controllers;

use App\Mail\EnquiryReceived;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enquiry;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SiteController extends Controller
{
    public const INTERESTS = [
        'A training course',
        'Consulting',
        'Ordering a book or magazine',
        'Something else',
    ];

    public function home()
    {
        $courses = Course::published()->get();

        return view('pages.home', [
            'cards' => $courses->whereNotNull('code'),
            'courses' => $courses,
            'publications' => Publication::published()->get(),
        ]);
    }

    public function about()
    {
        return view('pages.about');
    }

    public function consulting()
    {
        return view('pages.consulting');
    }

    public function courses()
    {
        return view('courses.index', ['courses' => Course::published()->get()]);
    }

    public function course(Course $course)
    {
        abort_unless($course->is_published, 404);

        return view('courses.show', ['course' => $course]);
    }

    public function publications()
    {
        return view('pages.publications', ['publications' => Publication::published()->get()]);
    }

    public function verify(Request $request)
    {
        $number = trim((string) $request->query('number', ''));
        $certificate = $number !== '' ? Certificate::where('number', $number)->first() : null;

        return view('pages.verify', [
            'number' => $number,
            'searched' => $number !== '',
            'certificate' => $certificate,
        ]);
    }

    public function contact(Request $request)
    {
        return view('pages.contact', [
            'interests' => self::INTERESTS,
            'subject' => (string) $request->query('subject', ''),
            'interest' => (string) $request->query('interest', ''),
        ]);
    }

    public function sendEnquiry(Request $request)
    {
        // "website" is a hidden field that people never fill in; bots usually do.
        if ($request->filled('website')) {
            return redirect()->route('contact')->with('sent', true);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'organisation' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'interest' => ['nullable', 'string', 'max:80'],
            'subject' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $enquiry = Enquiry::create($data);

        try {
            Mail::to(config('afroiso.office_email'))->send(new EnquiryReceived($enquiry));
        } catch (\Throwable $e) {
            // The enquiry is already saved for the admin panel; only the email failed.
            Log::warning('Enquiry email could not be sent: ' . $e->getMessage());
        }

        return redirect()->route('contact')->with('sent', true);
    }

    /**
     * List of pages for Google and other search engines.
     */
    public function sitemap()
    {
        $pages = [
            ['loc' => route('home'), 'priority' => '1.0'],
            ['loc' => route('courses.index'), 'priority' => '0.9'],
            ['loc' => route('consulting'), 'priority' => '0.8'],
            ['loc' => route('publications'), 'priority' => '0.8'],
            ['loc' => route('about'), 'priority' => '0.7'],
            ['loc' => route('contact'), 'priority' => '0.7'],
            ['loc' => route('verify'), 'priority' => '0.4'],
        ];

        foreach (Course::published()->get() as $course) {
            $pages[] = [
                'loc' => route('courses.show', $course),
                'priority' => '0.9',
                'lastmod' => optional($course->updated_at)->toDateString(),
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($pages as $page) {
            $xml .= '  <url><loc>' . e($page['loc']) . '</loc>';
            if (! empty($page['lastmod'])) {
                $xml .= '<lastmod>' . $page['lastmod'] . '</lastmod>';
            }
            $xml .= '<priority>' . $page['priority'] . '</priority></url>' . "\n";
        }
        $xml .= '</urlset>' . "\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function policy(string $policy)
    {
        $titles = [
            'privacy-policy' => 'Privacy policy',
            'terms' => 'Terms and conditions',
            'refund-policy' => 'Refund policy',
        ];
        abort_unless(isset($titles[$policy]), 404);

        return view('pages.policy', ['title' => $titles[$policy], 'policy' => $policy]);
    }
}
