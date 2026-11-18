@extends('layouts.app')
@section('title', 'Consulting')

@section('content')
<section class="page-head">
    <div class="container">
        <div class="eyebrow eyebrow--light">Consulting</div>
        <h1>Help preparing for certification</h1>
        <p>Our consultants work alongside your team from the first assessment to the certification audit.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="ways">
            <div class="way"><h3>Gap analysis</h3><p>We compare how you work today with what the standard requires and list what is missing.</p></div>
            <div class="way"><h3>Building the system</h3><p>We help you write and put in place the policies, processes and records the standard asks for.</p></div>
            <div class="way"><h3>Internal audits</h3><p>We audit your management system and report the findings to your management.</p></div>
            <div class="way"><h3>Readiness check</h3><p>We review the system before the certification body arrives, so there are no surprises on the day.</p></div>
        </div>
        <p class="section__cta"><a class="btn btn--primary" href="{{ route('contact', ['subject' => 'Consulting enquiry', 'interest' => 'Consulting']) }}">Speak to a consultant</a></p>
    </div>
</section>
@endsection
