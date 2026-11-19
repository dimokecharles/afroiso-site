@extends('layouts.app')
@section('title', 'ISO Consulting and Certification Support')
@section('description', 'ISO consulting from AfroISO in Harare: gap analysis, help building your management system, internal audits and a readiness check before the certification audit.')

@section('content')
<section class="page-head page-head--about">
    <div class="container">
        <div class="eyebrow eyebrow--light">Consulting</div>
        <h1>From first assessment to certification audit.</h1>
        <p>Our consultants work alongside your team to build a management system that meets the standard and suits the way you work.</p>
    </div>
</section>

{{-- Intro beside a quick-facts panel --}}
<section class="section">
    <div class="container split">
        <div class="split__head">
            <div class="eyebrow">How we help</div>
            <h2>Certification without the guesswork</h2>
        </div>
        <div class="split__body">
            <p>Preparing for certification is easier with someone who knows what the auditor will look for. We assess where you are today, help you close the gaps, and check that you are ready before the certification body arrives.</p>
            <ul class="about__points">
                <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i>Support for organisations of any size</li>
                <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i>Work done with your own team, so the knowledge stays with you</li>
                <li><i class="fa-solid fa-circle-check" aria-hidden="true"></i>Training for your staff through our e-learning courses</li>
            </ul>
        </div>
    </div>
</section>

{{-- The four stages as a timeline --}}
<section class="section section--tint">
    <div class="container">
        <div class="section__head section__head--center">
            <div class="eyebrow">Our process</div>
            <h2>Four stages to certification readiness</h2>
        </div>
        <ol class="timeline">
            <li>
                <div class="timeline__dot"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></div>
                <div class="timeline__body">
                    <span class="timeline__step">Stage 1</span>
                    <h3>Gap analysis</h3>
                    <p>We compare how you work today with what the standard requires and list what is missing.</p>
                </div>
            </li>
            <li>
                <div class="timeline__dot"><i class="fa-solid fa-sitemap" aria-hidden="true"></i></div>
                <div class="timeline__body">
                    <span class="timeline__step">Stage 2</span>
                    <h3>Building the system</h3>
                    <p>We help you write and put in place the policies, processes and records the standard asks for.</p>
                </div>
            </li>
            <li>
                <div class="timeline__dot"><i class="fa-solid fa-clipboard-list" aria-hidden="true"></i></div>
                <div class="timeline__body">
                    <span class="timeline__step">Stage 3</span>
                    <h3>Internal audits</h3>
                    <p>We audit your management system and report the findings to your management.</p>
                </div>
            </li>
            <li>
                <div class="timeline__dot"><i class="fa-solid fa-flag-checkered" aria-hidden="true"></i></div>
                <div class="timeline__body">
                    <span class="timeline__step">Stage 4</span>
                    <h3>Readiness check</h3>
                    <p>We review the system before the certification body arrives, so there are no surprises on the day.</p>
                </div>
            </li>
        </ol>
    </div>
</section>

{{-- Standards we consult on: a navy band of labels --}}
<section class="band">
    <div class="container band__inner">
        <div class="band__text">
            <div class="eyebrow eyebrow--light">Standards we work with</div>
            <h2>One team for every management system</h2>
        </div>
        <ul class="band__tags">
            <li>ISO 9001 Quality</li>
            <li>ISO 14001 Environment</li>
            <li>ISO 45001 Health and Safety</li>
            <li>ISO/IEC 27001 Information Security</li>
            <li>Food Safety and Hygiene</li>
        </ul>
    </div>
</section>

{{-- Call to action --}}
<section class="section">
    <div class="container">
        <div class="cta">
            <div class="cta__text">
                <h2>Tell us where you are today</h2>
                <p>Send us a short note about your organisation and the standard you are working towards. We will reply with the next steps.</p>
            </div>
            <div class="cta__actions">
                <a class="btn btn--accent btn--cut" href="{{ route('contact', ['subject' => 'Consulting enquiry', 'interest' => 'Consulting']) }}">Speak to a consultant<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                <a class="btn btn--light btn--cut" href="{{ route('courses.index') }}">View courses</a>
            </div>
        </div>
    </div>
</section>
@endsection
