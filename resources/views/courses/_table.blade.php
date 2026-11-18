<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Course</th>
                <th scope="col">Delivery</th>
                <th scope="col">Start date</th>
                <th scope="col">Fee</th>
                <th scope="col"><span class="sr-only">Action</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($courses as $course)
                <tr>
                    <td><a class="table__title" href="{{ route('courses.show', $course) }}">{{ $course->title }}</a></td>
                    <td>{{ $course->delivery }}</td>
                    <td>{{ $course->startLabel() }}</td>
                    <td>{{ $course->feeLabel() }}</td>
                    <td class="table__action"><a class="text-link" href="{{ route('contact', ['subject' => 'Enrol: ' . $course->title, 'interest' => 'A training course']) }}">Enrol</a></td>
                </tr>
            @empty
                <tr><td colspan="5">Course dates will be published here soon.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
