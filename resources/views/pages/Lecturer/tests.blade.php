@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Due Dates for Tests</h1>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Test Name</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tests as $test)
                <tr>
                    <td>{{ $test->name }}</td>
                    <td>{{ $test->due_date }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
