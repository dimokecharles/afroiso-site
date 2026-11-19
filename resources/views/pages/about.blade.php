@extends('layouts.app')
@section('title', 'About AfroISO')
@section('description', 'About AfroISO Institute & Consultants: a Harare-based training institute, consultancy and publisher for ISO management system standards. Our vision, mission and who we serve.')

@section('content')
<section class="page-head page-head--about">
    <div class="container">
        <div class="eyebrow eyebrow--light">About the institute</div>
        <h1>A training institute, a consultancy and a publisher under one roof.</h1>
        <p>AfroISO Institute &amp; Consultants helps African professionals and organisations understand, implement and audit ISO management system standards.</p>
    </div>
</section>

{{-- Who we are --}}
<section class="section">
    <div class="container about">
        <div class="about__brand">
            <div class="about__logo">
                <img src="{{ asset('images/logo.jpg') }}" alt="{{ config('afroiso.name') }}">
            </div>
        </div>
        <div class="about__body">
            <div class="eyebrow">Who we are</div>
            <h2>What AfroISO is all about</h2>
            <p>AfroISO Institute &amp; Consultants is based in Harare, Zimbabwe. We teach the ISO management system standards for quality, environment, occupational health and safety, and information security, together with food safety and lead auditor training.</p>
            <p>Our courses are delivered through our e-learning platform, so you can study from anywhere. Our consultants work alongside organisations preparing for certification, and we publish and distribute books and magazines on standards.</p>
            <div class="about__place"><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span>{{ config('afroiso.address') }}</span></div>
        </div>
    </div>
</section>

{{-- Vision and mission: one two-tone band --}}
<section class="duo">
    <div class="duo__half duo__half--navy">
        <div class="duo__inner">
            <div class="duo__label"><i class="fa-solid fa-eye" aria-hidden="true"></i>Our vision</div>
            <p>To be Africa's most trusted institute for management system standards, so that African organisations can compete on quality anywhere in the world.</p>
        </div>
    </div>
    <div class="duo__half duo__half--orange">
        <div class="duo__inner">
            <div class="duo__label"><i class="fa-solid fa-bullseye" aria-hidden="true"></i>Our mission</div>
            <p>To make ISO standards practical and within reach for African professionals and organisations, through e-learning, hands-on consulting and clear publications.</p>
        </div>
    </div>
</section>

{{-- What we do: numbered list --}}
<section class="section">
    <div class="container split">
        <div class="split__head">
            <div class="eyebrow">What we do</div>
            <h2>Three services, one purpose</h2>
        </div>
        <ol class="numbered">
            <li>
                <span class="numbered__no">01</span>
                <div>
                    <h3><a href="{{ route('courses.index') }}">Training</a></h3>
                    <p>E-learning courses in the ISO standards and food safety, up to lead auditor level.</p>
                </div>
            </li>
            <li>
                <span class="numbered__no">02</span>
                <div>
                    <h3><a href="{{ route('consulting') }}">Consulting</a></h3>
                    <p>Gap analysis, help building the management system, internal audits and a readiness check.</p>
                </div>
            </li>
            <li>
                <span class="numbered__no">03</span>
                <div>
                    <h3><a href="{{ route('publications') }}">Publications</a></h3>
                    <p>Books and magazines on standards that we publish and distribute.</p>
                </div>
            </li>
        </ol>
    </div>
</section>

{{-- Who we serve: icon list on a tinted band --}}
<section class="section section--tint">
    <div class="container">
        <div class="section__head section__head--center">
            <div class="eyebrow">Who we serve</div>
            <h2>Built for the people who make standards work</h2>
            <p>Our courses and services are open to anyone interested in ISO, whether you are an individual or an organisation.</p>
        </div>
        <ul class="serve">
            <li><i class="fa-solid fa-user-tie" aria-hidden="true"></i><div><strong>Working professionals</strong><span>Quality, safety, environmental and IT staff who want a recognised skill in management systems.</span></div></li>
            <li><i class="fa-solid fa-user-graduate" aria-hidden="true"></i><div><strong>Students and graduates</strong><span>People starting a career who want to stand out with knowledge of the ISO standards.</span></div></li>
            <li><i class="fa-solid fa-store" aria-hidden="true"></i><div><strong>Small and medium enterprises</strong><span>Businesses building their first management system or preparing for certification.</span></div></li>
            <li><i class="fa-solid fa-building" aria-hidden="true"></i><div><strong>Certified organisations</strong><span>Companies keeping their systems current, including the move to the 2026 editions of ISO 9001 and ISO 14001.</span></div></li>
            <li><i class="fa-solid fa-magnifying-glass-chart" aria-hidden="true"></i><div><strong>Auditors</strong><span>Internal auditors and those working towards lead auditor level.</span></div></li>
            <li><i class="fa-solid fa-utensils" aria-hidden="true"></i><div><strong>Food businesses</strong><span>Food premises and transporters that must meet hygiene regulations in Zimbabwe and South Africa.</span></div></li>
        </ul>
    </div>
</section>

{{-- Call to action --}}
<section class="section">
    <div class="container">
        <div class="cta">
            <div class="cta__text">
                <h2>Ready to start?</h2>
                <p>Choose a course, or tell us what your organisation needs and we will reply with the next steps.</p>
            </div>
            <div class="cta__actions">
                <a class="btn btn--accent btn--cut" href="{{ route('courses.index') }}">View courses<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                <a class="btn btn--light btn--cut" href="{{ route('contact') }}">Talk to our office</a>
            </div>
        </div>
    </div>
</section>
@endsection
