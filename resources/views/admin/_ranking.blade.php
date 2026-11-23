{{-- A ranked list with a bar per row: title, how many were sold, and the revenue. --}}
@if ($rows->isEmpty())
    <p class="muted">{{ $empty }}</p>
@else
    <ol class="ranking">
        @foreach ($rows as $row)
            <li>
                <div class="ranking__head">
                    <span>{{ $row['title'] }}</span>
                    <strong>US${{ number_format($row['revenue'], 2) }}</strong>
                </div>
                <div class="ranking__bar"><span style="width: {{ max(2, round($row['revenue'] / $max * 100)) }}%"></span></div>
                <div class="muted">{{ $row['count'] }} {{ $row['count'] === 1 ? $unit : $unit . 's' }}</div>
            </li>
        @endforeach
    </ol>
@endif
