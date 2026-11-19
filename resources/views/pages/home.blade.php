@extends('layouts.app')

@section('content')
<section class="hero">
    @if (file_exists(public_path('images/hero.jpg')))
        <img class="hero__image" src="{{ asset('images/hero.jpg') }}" alt="">
    @endif
    <canvas class="hero__canvas" aria-hidden="true"></canvas>
    <div class="hero__overlay"></div>
    <div class="container hero__inner">
        <div class="hero__text">
            <div class="eyebrow eyebrow--light">ISO training institute and consultancy &middot; Harare</div>
            <h1>Training the people who build and audit management systems.</h1>
            <p>AfroISO Institute &amp; Consultants teaches ISO 9001, ISO 14001, ISO 45001 and ISO/IEC 27001, including lead auditor courses, through our e-learning platform. Our consultants help organisations prepare for certification, and we publish and distribute books and magazines.</p>
            <div class="hero__actions">
                <a class="btn btn--accent" href="{{ route('courses.index') }}">View training courses</a>
                <a class="hero__link" href="{{ route('consulting') }}">Ask about consulting</a>
            </div>
        </div>
        <aside class="notice">
            <div class="eyebrow">Notice</div>
            <h2>ISO 9001:2026 has been published</h2>
            <p>The new edition of the quality management standard was released on 16 September 2026 and replaces ISO 9001:2015. Our quality courses are taught to the 2026 edition.</p>
            <a href="{{ route('courses.index') }}">See our ISO 9001 courses</a>
        </aside>
    </div>
</section>

<section class="section">
    <div class="container about">
        <div class="about__brand">
            <div class="about__logo">
                <img src="{{ asset('images/logo.jpg') }}" alt="{{ config('afroiso.name') }}">
            </div>
        </div>
        <div class="about__body">
            <div class="eyebrow">About the institute</div>
            <h2>A training institute, a consultancy and a publisher under one roof.</h2>
            <p>We are based in Harare and work with organisations and professionals who need to understand, implement or audit an ISO management system.</p>
            <ul class="about__points">
                <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i>E-learning courses, up to lead auditor level</li>
                <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i>Consultants who help you prepare for certification</li>
                <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i>Books and magazines that we publish and distribute</li>
            </ul>
            <div class="about__actions">
                <a class="btn btn--accent btn--cut" href="{{ route('about') }}">Read more about us<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                <a class="btn btn--outline btn--cut" href="{{ route('contact') }}">Talk to our office</a>
            </div>
        </div>
    </div>
</section>

<section class="section section--tint">
    <div class="container">
        <div class="section__head section__head--row">
            <div>
                <div class="eyebrow">Training courses</div>
                <h2>Courses by standard</h2>
                <p>Courses in the ISO management system standards, up to lead auditor level, and in food safety.</p>
            </div>
            <a class="btn btn--accent btn--cut" href="{{ route('courses.index') }}">View all<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        @php
            // Icon for each card, chosen by a keyword in the card heading or course title.
            $cardIcons = ['9001' => 'fa-award', '14001' => 'fa-leaf', '45001' => 'fa-helmet-safety', '27001' => 'fa-shield-halved', 'food' => 'fa-utensils', 'auditor' => 'fa-clipboard-check'];
        @endphp
        <div class="cards">
            @foreach ($cards as $course)
                @php
                    $icon = 'fa-certificate';
                    foreach ($cardIcons as $keyword => $class) {
                        if (stripos($course->code . ' ' . $course->title, (string) $keyword) !== false) {
                            $icon = $class;
                            break;
                        }
                    }
                @endphp
                <article class="card">
                    <div class="card__icon"><i class="fa-solid {{ $icon }}" aria-hidden="true"></i></div>
                    <h3 class="card__title">{{ $course->code }}</h3>
                    <div class="card__category">{{ $course->category }}</div>
                    <p>{{ $course->summary }}</p>
                    <ul class="card__meta">
                        <li><i class="fa-solid fa-laptop" aria-hidden="true"></i>{{ $course->delivery }}</li>
                        <li><i class="fa-regular fa-calendar" aria-hidden="true"></i>{{ $course->startLabel() }}</li>
                        <li class="card__fee">{{ $course->feeLabel() }}</li>
                    </ul>
                    <a class="card__link" href="{{ route('courses.show', $course) }}">View course<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </article>
            @endforeach
            <article class="card card--cta">
                <div class="card__icon"><i class="fa-solid fa-calendar-days" aria-hidden="true"></i></div>
                <h3 class="card__title">All courses and dates</h3>
                <p>Compare the start dates and fees for every course we offer, then enrol online.</p>
                <a class="card__link" href="{{ route('courses.index') }}">View the full list<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </article>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section__head section__head--center">
            <div class="eyebrow">What we do</div>
            <h2>Three ways to work with us</h2>
            <p>Learn the standards, get help putting them in place, or read our publications.</p>
        </div>
        <div class="steps">
            <svg class="steps__line" viewBox="0 0 1000 200" preserveAspectRatio="none" aria-hidden="true">
                <path d="M 240 78 C 300 10, 370 10, 432 78"></path>
                <path d="M 568 128 C 630 196, 700 196, 760 128"></path>
            </svg>
            <div class="step">
                <div class="step__art">
                    <div class="step__tile"><i class="fa-solid fa-laptop" aria-hidden="true"></i></div>
                    <div class="step__badge"><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i></div>
                </div>
                <h3>Training</h3>
                <p>We offer e-learning training to anyone interested in ISO, whether you are an individual or an organisation.</p>
                <a class="text-link" href="{{ route('courses.index') }}">See courses and dates</a>
            </div>
            <div class="step">
                <div class="step__art">
                    <div class="step__tile"><i class="fa-solid fa-clipboard-check" aria-hidden="true"></i></div>
                    <div class="step__badge"><i class="fa-solid fa-comments" aria-hidden="true"></i></div>
                </div>
                <h3>Consulting</h3>
                <p>Gap analysis, help building the management system, internal audits and a readiness check before the certification audit.</p>
                <a class="text-link" href="{{ route('consulting') }}">Speak to a consultant</a>
            </div>
            <div class="step">
                <div class="step__art">
                    <div class="step__tile step__tile--navy"><i class="fa-solid fa-book-open" aria-hidden="true"></i></div>
                    <div class="step__badge step__badge--light"><i class="fa-solid fa-newspaper" aria-hidden="true"></i></div>
                </div>
                <h3>Publications</h3>
                <p>We publish and distribute books and magazines.</p>
                <a class="text-link" href="{{ route('publications') }}">See our publications</a>
            </div>
        </div>
    </div>
</section>

<section class="section section--tint">
    <div class="container">
        <div class="section__head section__head--row">
            <div>
                <div class="eyebrow">Training calendar</div>
                <h2>Upcoming courses</h2>
            </div>
            <a class="text-link" href="{{ route('courses.index') }}">View all courses</a>
        </div>
        @include('courses._intake', ['courses' => $courses])
    </div>
</section>

@if ($publications->isNotEmpty())
<section class="section">
    <div class="container">
        <div class="section__head">
            <div class="eyebrow">Publications</div>
            <h2>Books and magazines</h2>
            <p>AfroISO publishes and distributes books and magazines.</p>
        </div>
        @include('pages._publications', ['publications' => $publications])
    </div>
</section>
@endif

<section class="verify-band">
    <div class="container verify-band__inner">
        <div>
            <h2>Verify a certificate</h2>
            <p>Employers and auditors can confirm that a certificate was issued by AfroISO by entering its number.</p>
        </div>
        <form method="get" action="{{ route('verify') }}" class="verify-form">
            <label for="home-cert">Certificate number</label>
            <div class="verify-form__row">
                <input id="home-cert" name="number" type="text" required>
                <button class="btn btn--accent" type="submit">Verify</button>
            </div>
        </form>
    </div>
</section>
<script src="{{ asset('js/hero.js') }}" defer></script>
@endsection
