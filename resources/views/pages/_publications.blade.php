<div class="pubs">
    @foreach ($publications as $publication)
        <article class="pub">
            @if ($publication->cover_path)
                <img class="pub__cover" src="{{ asset($publication->cover_path) }}" alt="Cover of {{ $publication->title }}">
            @else
                <div class="pub__cover pub__cover--empty">Cover to follow</div>
            @endif
            <div class="pub__body">
                <div class="eyebrow">{{ $publication->typeLabel() }}</div>
                <h3>{{ $publication->title }}</h3>
                @if ($publication->description)
                    <p>{!! nl2br(e($publication->description)) !!}</p>
                @endif
                <div class="pub__price">{{ $publication->priceLabel() }}</div>
                <a class="btn btn--accent" href="{{ $publication->price !== null ? route('buy.create', $publication) : route('contact', ['subject' => 'Order: ' . $publication->title, 'interest' => 'Ordering a book or magazine']) }}">Buy this {{ strtolower($publication->typeLabel()) }}</a>
            </div>
        </article>
    @endforeach
</div>
