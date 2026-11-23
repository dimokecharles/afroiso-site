@extends('layouts.admin')
@section('title', 'Website text')

@section('content')
<div class="head"><h1>Website text</h1></div>
@foreach ($groups as $group => $items)
    <div class="panel">
        <h2>{{ $group }}</h2>
        <div class="table-wrap">
            <table class="table--compact">
                <thead><tr><th>Text</th><th>Wording in use</th><th><span class="vh">Actions</span></th></tr></thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item['label'] }}</td>
                            <td><span @class(['tag', 'tag--new' => $item['changed']])>{{ $item['changed'] ? 'Edited here' : ($item['kind'] === 'page' ? 'Built-in draft' : 'Built-in wording') }}</span></td>
                            <td class="actions"><a href="{{ route('admin.texts.edit', $item['key']) }}">Edit</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($group === 'Policies')
            <form method="post" action="{{ route('admin.texts.publish') }}" class="form" style="margin-top: 18px">
                @csrf
                <p><strong>{{ $policiesPublished ? 'The policies are published.' : 'The policies are not published.' }}</strong>
                    {{ $policiesPublished ? 'Visitors see the full text, and search engines may list the pages.' : 'Visitors see a short "being finalised" note. Publish once the wording is approved.' }}</p>
                @if ($lockedByEnv)
                    <p class="muted">Publishing is switched on in this site's settings file, so it cannot be turned off here.</p>
                @else
                    <input type="hidden" name="published" value="{{ $policiesPublished ? '0' : '1' }}">
                    <div class="form__actions"><button class="btn {{ $policiesPublished ? 'btn--plain' : '' }}" type="submit" onclick="return confirm('{{ $policiesPublished ? 'Hide the policies from visitors?' : 'Publish the policies to visitors? Only do this once the wording is approved.' }}')">{{ $policiesPublished ? 'Unpublish the policies' : 'Publish the policies' }}</button></div>
                @endif
            </form>
        @endif
    </div>
@endforeach
@endsection
