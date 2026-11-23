{{-- Homepage "Upcoming courses": next-intake banner with a countdown, then one row per course. --}}
@php
    $today = now()->startOfDay();
    $nextStart = $courses->pluck('start_date')->filter(fn ($date) => $date && $date->gte($today))->sort()->first();
    $daysLeft = $nextStart ? (int) $today->diffInDays($nextStart) : null;
    $rowIcons = ['9001' => 'fa-award', '14001' => 'fa-leaf', '45001' => 'fa-helmet-safety', '27001' => 'fa-shield-halved', 'food' => 'fa-utensils', 'auditor' => 'fa-clipboard-check'];
@endphp

<div class="intake">
    <div class="intake__text">
        <div class="eyebrow eyebrow--light">Next intake</div>
        <div class="intake__date">{{ $nextStart ? $nextStart->format('j F Y') : 'Dates to be announced' }}</div>
        <p>All courses are delivered by e-learning. Enrol now to secure your place.</p>
    </div>
    @if ($daysLeft !== null)
        <div class="intake__count">
            @if ($daysLeft > 0)
                {{-- Live countdown to midnight, Harare time, on the start date. The numbers below show first and are then kept up to date by public/js/site.js. --}}
                <div class="countdown" data-countdown="{{ $nextStart->format('Y-m-d') }}T00:00:00+02:00">
                    <span class="sr-only">{{ $daysLeft }} {{ $daysLeft === 1 ? 'day' : 'days' }} until classes start</span>
                    <div class="countdown__units" aria-hidden="true">
                        <div class="countdown__unit"><strong data-unit="days">{{ $daysLeft }}</strong><span>Days</span></div>
                        <div class="countdown__unit"><strong data-unit="hours">00</strong><span>Hours</span></div>
                        <div class="countdown__unit"><strong data-unit="minutes">00</strong><span>Minutes</span></div>
                        <div class="countdown__unit"><strong data-unit="seconds">00</strong><span>Seconds</span></div>
                    </div>
                    <div class="countdown__label">until classes start</div>
                </div>
            @else
                <div class="intake__label intake__label--today">Classes start today</div>
            @endif
        </div>
    @endif
</div>

<div class="rows">
    @forelse ($courses as $course)
        @php
            $icon = 'fa-certificate';
            foreach ($rowIcons as $keyword => $class) {
                if (stripos($course->code . ' ' . $course->title, (string) $keyword) !== false) {
                    $icon = $class;
                    break;
                }
            }
        @endphp
        <div class="row">
            <div class="row__icon"><i class="fa-solid {{ $icon }}" aria-hidden="true"></i></div>
            <div class="row__name">
                <a href="{{ route('courses.show', $course) }}">{{ $course->title }}</a>
                @if ($course->category)
                    <span>{{ $course->category }}</span>
                @endif
            </div>
            <div class="row__date"><i class="fa-regular fa-calendar" aria-hidden="true"></i>{{ $course->start_date ? $course->start_date->format('j M Y') : 'To be announced' }}</div>
            <div class="row__delivery"><i class="fa-solid fa-laptop" aria-hidden="true"></i>{{ $course->delivery }}</div>
            <div class="row__fee">{{ $course->feeLabel() }}</div>
            <a class="btn btn--accent btn--cut row__enrol" href="{{ $course->fee !== null ? route('enrol.create', $course) : route('contact', ['subject' => 'Enrol: ' . $course->title, 'interest' => 'A training course']) }}">Enrol<span class="sr-only"> on {{ $course->title }}</span></a>
        </div>
    @empty
        <div class="row">Course dates will be published here soon.</div>
    @endforelse
</div>
