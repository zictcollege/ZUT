@extends('layouts.master')
@section('page_title', 'User Profile - '.$user->name)
@section('content')
    @php
        use App\Helpers\Qs;
    @endphp
    <div class="row">
        <div class="col-md-3 text-center">
            <div class="card">
                <div class="card-body">
                    <img style="width: 90%; height:90%" src="{{ $user->photo }}" alt="photo" class="rounded-circle">
                    <br>
                    <h3 class="mt-3">{{ $user->first_name.' '.$user->middle_name.' '.$user->last_name }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-highlight">
                        <li class="nav-item">
                            <a href="#" class="nav-link active" >{{ $user->first_name.' '.$user->middle_name.' '.$user->last_name }}</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{--Basic Info--}}
                        <div class="tab-pane fade show active" id="basic-info">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td class="font-weight-bold">Name</td>
                                    <td>{{ $user->first_name.' '.$user->middle_name.' '.$user->last_name }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Gender</td>
                                    <td>{{ $user->gender }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">Address</td>
                                    <td>{{ $user->address }}</td>
                                </tr>
                                @if($user->email)
                                    <tr>
                                        <td class="font-weight-bold">Email</td>
                                        <td>{{$user->email }}</td>
                                    </tr>
                                @endif
                                @if($user->username)
                                    <tr>
                                        <td class="font-weight-bold">Username</td>
                                        <td>{{$user->username }}</td>
                                    </tr>
                                @endif
                                @if($user->phone)
                                    <tr>
                                        <td class="font-weight-bold">Phone</td>
                                        <td>{{$user->phone.' '.$user->phone2 }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td class="font-weight-bold">Birthday</td>
                                    <td>{{$user->dob }}</td>
                                </tr>
                                @if($user->nal_id)
                                    <tr>
                                        <td class="font-weight-bold">Nationality</td>
                                        <td>{{$user->nationality->name }}</td>
                                    </tr>
                                @endif
                                @if($user->state_id)
                                    <tr>
                                        <td class="font-weight-bold">NRC</td>
                                        <td>{{$user->NRC}}</td>
                                    </tr>
                                @endif

{{--                                @if($user->user_type == 'teacher')--}}
{{--                                    <tr>--}}
{{--                                        <td class="font-weight-bold">My Subjects</td>--}}
{{--                                        <td>--}}
{{--                                            @foreach(Qs::findTeacherSubjects($user->id) as $sub)--}}
{{--                                                <span> - {{ $sub->name.' ('.$sub->my_class->name.')' }}</span><br>--}}
{{--                                            @endforeach--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
{{--                                @endif--}}

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    {{--User Profile Ends--}}

@endsection
