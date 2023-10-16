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
                            <a href="#basic-info" class="nav-link active" data-toggle="tab">Personal Information</a>
                        </li>
                        <li class="nav-item">
                            <a href="#sponsor-info" class="nav-link" data-toggle="tab">Sponsor Information</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{--Basic Info--}}
                        <div class="tab-pane fade show active" id="basic-info">
                            <table class="table table-bordered">
                                <tbody>
                                <tr>
                                    <td class="font-weight-bold">Gender</td>
                                    <td>{{ $user->gender }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">NRC</td>
                                    <td>{{ $user->nrc }}</td>
                                </tr>
                                @if($user->email)
                                    <tr>
                                        <td class="font-weight-bold">Email</td>
                                        <td>{{$user->email }}</td>
                                    </tr>
                                @endif
                                @if($user->personalinfo)
                                    <tr>
                                        <td class="font-weight-bold">Date of Birth</td>
                                        <td>{{ date($user->personalinfo->dob ) }}</td>
                                    </tr>
                                @endif
                                @if($user->personalinfo)
                                    <tr>
                                        <td class="font-weight-bold">Phone</td>
                                        <td>{{$user->personalinfo->mobile.' / '.$user->personalinfo->telephone }}</td>
                                    </tr>
                                @endif
                                @if($user->personalinfo)
                                    <tr>
                                        <td class="font-weight-bold">Marital Status</td>
                                        <td>{{$user->personalinfo->marital_status }}</td>
                                    </tr>
                                @endif
                                @if($user->personalinfo)
                                    <tr>
                                        <td class="font-weight-bold">Street</td>
                                        <td>{{ $user->personalinfo->street_main }}</td>
                                    </tr>
                                @endif
                                @if($user->personalinfo)
                                    <tr>
                                        <td class="font-weight-bold">Nationality</td>
                                        <td>{{$user->personalinfo->nationality }}</td>
                                    </tr>
                                @endif
                                    @if($user->personalinfo)
                                        <tr>
                                            <td class="font-weight-bold">Province</td>
                                            <td>{{$user->personalinfo->province_state }}</td>
                                        </tr>
                                    @endif
                                    @if($user->personalinfo)
                                        <tr>
                                            <td class="font-weight-bold">Nationality</td>
                                            <td>{{$user->personalinfo->nationality }}</td>
                                        </tr>
                                    @endif

                                @if($user->user_type == 'teacher')
                                    <tr>
                                        <td class="font-weight-bold">My Subjects</td>
                                        <td>
                                            @foreach(Qs::findTeacherSubjects($user->id) as $sub)
                                                <span> - {{ $sub->name.' ('.$sub->my_class->name.')' }}</span><br>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endif

                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane fade show" id="sponsor-info">
                            <table class="table table-bordered">
                                <tbody>
                                @if($user->nextofkin)
                                <tr>
                                    <td class="font-weight-bold">Name</td>
                                    <td>{{ $user->nextofkin->full_name }}</td>
                                </tr>
                                @endif
                                @if($user->nextofkin)
                                <tr>
                                    <td class="font-weight-bold">Relationship</td>
                                    <td>{{ $user->nextofkin->relationship }}</td>
                                </tr>
                                @endif
                                @if($user->nextofkin)
                                <tr>
                                    <td class="font-weight-bold">Phone</td>
                                    <td>{{ $user->nextofkin->phone }}</td>
                                </tr>
                                @endif
                                @if($user->nextofkin)
                                    <tr>
                                        <td class="font-weight-bold">City</td>
                                        <td>{{$user->nextofkin->city }}</td>
                                    </tr>
                                @endif
                                @if($user->nextofkin)
                                    <tr>
                                        <td class="font-weight-bold">Province</td>
                                        <td>{{ $user->nextofkin->province }}</td>
                                    </tr>
                                @endif
                                @if($user->nextofkin)
                                    <tr>
                                        <td class="font-weight-bold">Country</td>
                                        <td>{{ $user->nextofkin->country  }}</td>
                                    </tr>
                                @endif

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
