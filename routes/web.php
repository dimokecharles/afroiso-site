<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

// Public website
Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/about', [SiteController::class, 'about'])->name('about');
Route::get('/consulting', [SiteController::class, 'consulting'])->name('consulting');
Route::get('/courses', [SiteController::class, 'courses'])->name('courses.index');
Route::permanentRedirect('/courses/lead-auditor', '/courses/internal-auditor');
Route::get('/courses/{course}', [SiteController::class, 'course'])->name('courses.show');
Route::get('/publications', [SiteController::class, 'publications'])->name('publications');
Route::get('/verify', [SiteController::class, 'verify'])->name('verify')->middleware('throttle:30,1');
Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
// Enrol on a course or buy a publication, then pay on the order's private pay page
Route::get('/courses/{course}/enrol', [CheckoutController::class, 'enrol'])->name('enrol.create');
Route::post('/courses/{course}/enrol', [CheckoutController::class, 'storeEnrolment'])->name('enrol.store')->middleware('throttle:10,1');
Route::get('/publications/{publication}/buy', [CheckoutController::class, 'buy'])->name('buy.create');
Route::post('/publications/{publication}/buy', [CheckoutController::class, 'storeOrder'])->name('buy.store')->middleware('throttle:10,1');
Route::get('/pay/{order:token}', [CheckoutController::class, 'show'])->name('pay.show');
Route::post('/pay/{order:token}', [CheckoutController::class, 'pay'])->name('pay.process')->middleware('throttle:20,1');

Route::get('/sitemap.xml', [SiteController::class, 'sitemap'])->name('sitemap');
Route::post('/contact', [SiteController::class, 'sendEnquiry'])->name('contact.send')->middleware('throttle:5,1');
Route::get('/{policy}', [SiteController::class, 'policy'])
    ->whereIn('policy', ['privacy-policy', 'terms', 'refund-policy'])
    ->name('policy');

// Admin panel
Route::prefix('admin')->group(function () {
    Route::get('/login', [Admin\AuthController::class, 'show'])->name('login');
    Route::post('/login', [Admin\AuthController::class, 'login'])->name('login.attempt')->middleware('throttle:10,1');

    Route::middleware('auth')->name('admin.')->group(function () {
        Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', Admin\DashboardController::class)->name('dashboard');
        Route::resource('courses', Admin\CourseController::class)->except('show');
        Route::resource('publications', Admin\PublicationController::class)->except('show');
        Route::resource('certificates', Admin\CertificateController::class)->except('show');
        Route::resource('enquiries', Admin\EnquiryController::class)->only(['index', 'show', 'destroy']);
        Route::resource('orders', Admin\OrderController::class)->only(['index', 'create', 'store', 'show', 'update']);
        Route::post('orders/{order}/send', [Admin\OrderController::class, 'send'])->name('orders.send');
    });
});
