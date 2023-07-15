@extends('layouts.master')
@section('page_title', 'Student Profile - ')
@section('content')
    @php
        use App\Helpers\Qs;
    @endphp
<div class="row">
    <div class="col-md-3 text-center">
        <div class="card">
            <div class="card-body">
                <img style="width: 90%; height:90%" src="{{ 00 }}" alt="photo" class="rounded-circle">
                <br>
                <h3 class="mt-3">{{ 00 }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="card card-collapsed">
            <div class="card-header header-elements-inline">
                <h6 class="card-title">Account</h6>
                {!! Qs::getPanelOptions() !!}
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-highlight">
                    <li class="nav-item">
                        <a href="#" class="nav-link active">{{ 'Account Details' }}</a>
                    </li>
                </ul>

                <div class="tab-content">
                    {{--Basic Info--}}
{{--                    <div class="tab-pane fade show active" id="basic-info">--}}
{{--                        <table class="table table-bordered">--}}
{{--                            <tbody>--}}
{{--                            <tr>--}}
{{--                                <td class="font-weight-bold">Name</td>--}}
{{--                                <td>{{ $sr->user->name }}</td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td class="font-weight-bold">ADM_NO</td>--}}
{{--                                <td>{{ $sr->adm_no }}</td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td class="font-weight-bold">Class</td>--}}
{{--                                <td>{{ $sr->my_class->name.' '.$sr->section->name }}</td>--}}
{{--                            </tr>--}}
{{--                            @if($sr->my_parent_id)--}}
{{--                                <tr>--}}
{{--                                    <td class="font-weight-bold">Parent</td>--}}
{{--                                    <td>--}}
{{--                                        <span><a target="_blank" href="{{ route('users.show', Qs::hash($sr->my_parent_id)) }}">{{ $sr->my_parent->name }}</a></span>--}}
{{--                                    </td>--}}
{{--                                </tr>--}}
{{--                            @endif--}}
{{--                            <tr>--}}
{{--                                <td class="font-weight-bold">Year Admitted</td>--}}
{{--                                <td>{{ $sr->year_admitted }}</td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td class="font-weight-bold">Gender</td>--}}
{{--                                <td>{{ $sr->user->gender }}</td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <td class="font-weight-bold">Address</td>--}}
{{--                                <td>{{ $sr->user->address }}</td>--}}
{{--                            </tr>--}}
{{--                            @if($sr->user->email)--}}
{{--                            <tr>--}}
{{--                                <td class="font-weight-bold">Email</td>--}}
{{--                                <td>{{$sr->user->email }}</td>--}}
{{--                            </tr>--}}
{{--                            @endif--}}
{{--                            @if($sr->user->phone)--}}
{{--                                <tr>--}}
{{--                                    <td class="font-weight-bold">Phone</td>--}}
{{--                                    <td>{{$sr->user->phone.' '.$sr->user->phone2 }}</td>--}}
{{--                                </tr>--}}
{{--                            @endif--}}
{{--                            <tr>--}}
{{--                                <td class="font-weight-bold">Birthday</td>--}}
{{--                                <td>{{$sr->user->dob }}</td>--}}
{{--                            </tr>--}}
{{--                            @if($sr->user->bg_id)--}}
{{--                            <tr>--}}
{{--                                <td class="font-weight-bold">Blood Group</td>--}}
{{--                                <td>{{$sr->user->blood_group->name }}</td>--}}
{{--                            </tr>--}}
{{--                            @endif--}}
{{--                            @if($sr->user->nal_id)--}}
{{--                            <tr>--}}
{{--                                <td class="font-weight-bold">Nationality</td>--}}
{{--                                <td>{{$sr->user->nationality->name }}</td>--}}
{{--                            </tr>--}}
{{--                            @endif--}}
{{--                            @if($sr->user->state_id)--}}
{{--                            <tr>--}}
{{--                                <td class="font-weight-bold">State</td>--}}
{{--                                <td>{{$sr->user->state->name }}</td>--}}
{{--                            </tr>--}}
{{--                            @endif--}}
{{--                            @if($sr->user->lga_id)--}}
{{--                            <tr>--}}
{{--                                <td class="font-weight-bold">LGA</td>--}}
{{--                                <td>{{$sr->user->lga->name }}</td>--}}
{{--                            </tr>--}}
{{--                            @endif--}}
{{--                            @if($sr->dorm_id)--}}
{{--                                <tr>--}}
{{--                                    <td class="font-weight-bold">Dormitory</td>--}}
{{--                                    <td>{{$sr->dorm->name.' '.$sr->dorm_room_no }}</td>--}}
{{--                                </tr>--}}
{{--                            @endif--}}

{{--                            </tbody>--}}
{{--                        </table>--}}
{{--                    </div>--}}

                </div>
            </div>
        </div>

        <div class="card card-collapsed">
            <div class="card-header header-elements-inline">
                <h6 class="card-title">Money</h6>
                {!! Qs::getPanelOptions() !!}
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-highlight">
                    <li class="nav-item">
                        <a href="#" class="nav-link active">{{ 'Financials' }}</a>
                    </li>
                </ul>

                <div class="tab-content">
                    {{--Basic Info--}}
                    {{--                    <div class="tab-pane fade show active" id="basic-info">--}}
                    {{--                        <table class="table table-bordered">--}}
                    {{--                            <tbody>--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Name</td>--}}
                    {{--                                <td>{{ $sr->user->name }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">ADM_NO</td>--}}
                    {{--                                <td>{{ $sr->adm_no }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Class</td>--}}
                    {{--                                <td>{{ $sr->my_class->name.' '.$sr->section->name }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @if($sr->my_parent_id)--}}
                    {{--                                <tr>--}}
                    {{--                                    <td class="font-weight-bold">Parent</td>--}}
                    {{--                                    <td>--}}
                    {{--                                        <span><a target="_blank" href="{{ route('users.show', Qs::hash($sr->my_parent_id)) }}">{{ $sr->my_parent->name }}</a></span>--}}
                    {{--                                    </td>--}}
                    {{--                                </tr>--}}
                    {{--                            @endif--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Year Admitted</td>--}}
                    {{--                                <td>{{ $sr->year_admitted }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Gender</td>--}}
                    {{--                                <td>{{ $sr->user->gender }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Address</td>--}}
                    {{--                                <td>{{ $sr->user->address }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @if($sr->user->email)--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Email</td>--}}
                    {{--                                <td>{{$sr->user->email }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @endif--}}
                    {{--                            @if($sr->user->phone)--}}
                    {{--                                <tr>--}}
                    {{--                                    <td class="font-weight-bold">Phone</td>--}}
                    {{--                                    <td>{{$sr->user->phone.' '.$sr->user->phone2 }}</td>--}}
                    {{--                                </tr>--}}
                    {{--                            @endif--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Birthday</td>--}}
                    {{--                                <td>{{$sr->user->dob }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @if($sr->user->bg_id)--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Blood Group</td>--}}
                    {{--                                <td>{{$sr->user->blood_group->name }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @endif--}}
                    {{--                            @if($sr->user->nal_id)--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Nationality</td>--}}
                    {{--                                <td>{{$sr->user->nationality->name }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @endif--}}
                    {{--                            @if($sr->user->state_id)--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">State</td>--}}
                    {{--                                <td>{{$sr->user->state->name }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @endif--}}
                    {{--                            @if($sr->user->lga_id)--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">LGA</td>--}}
                    {{--                                <td>{{$sr->user->lga->name }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @endif--}}
                    {{--                            @if($sr->dorm_id)--}}
                    {{--                                <tr>--}}
                    {{--                                    <td class="font-weight-bold">Dormitory</td>--}}
                    {{--                                    <td>{{$sr->dorm->name.' '.$sr->dorm_room_no }}</td>--}}
                    {{--                                </tr>--}}
                    {{--                            @endif--}}

                    {{--                            </tbody>--}}
                    {{--                        </table>--}}
                    {{--                    </div>--}}

                </div>
            </div>
        </div>

        <div class="card card-collapsed">
            <div class="card-header header-elements-inline">
                <h6 class="card-title">Registration</h6>
                {!! Qs::getPanelOptions() !!}
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs nav-tabs-highlight">
                    <li class="nav-item">
                        <a href="#" class="nav-link active">{{ 'Registration' }}</a>
                    </li>
                </ul>

                <div class="tab-content">
                    {{--Basic Info--}}
                    {{--                    <div class="tab-pane fade show active" id="basic-info">--}}
                    {{--                        <table class="table table-bordered">--}}
                    {{--                            <tbody>--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Name</td>--}}
                    {{--                                <td>{{ $sr->user->name }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">ADM_NO</td>--}}
                    {{--                                <td>{{ $sr->adm_no }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Class</td>--}}
                    {{--                                <td>{{ $sr->my_class->name.' '.$sr->section->name }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @if($sr->my_parent_id)--}}
                    {{--                                <tr>--}}
                    {{--                                    <td class="font-weight-bold">Parent</td>--}}
                    {{--                                    <td>--}}
                    {{--                                        <span><a target="_blank" href="{{ route('users.show', Qs::hash($sr->my_parent_id)) }}">{{ $sr->my_parent->name }}</a></span>--}}
                    {{--                                    </td>--}}
                    {{--                                </tr>--}}
                    {{--                            @endif--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Year Admitted</td>--}}
                    {{--                                <td>{{ $sr->year_admitted }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Gender</td>--}}
                    {{--                                <td>{{ $sr->user->gender }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Address</td>--}}
                    {{--                                <td>{{ $sr->user->address }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @if($sr->user->email)--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Email</td>--}}
                    {{--                                <td>{{$sr->user->email }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @endif--}}
                    {{--                            @if($sr->user->phone)--}}
                    {{--                                <tr>--}}
                    {{--                                    <td class="font-weight-bold">Phone</td>--}}
                    {{--                                    <td>{{$sr->user->phone.' '.$sr->user->phone2 }}</td>--}}
                    {{--                                </tr>--}}
                    {{--                            @endif--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Birthday</td>--}}
                    {{--                                <td>{{$sr->user->dob }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @if($sr->user->bg_id)--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Blood Group</td>--}}
                    {{--                                <td>{{$sr->user->blood_group->name }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @endif--}}
                    {{--                            @if($sr->user->nal_id)--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">Nationality</td>--}}
                    {{--                                <td>{{$sr->user->nationality->name }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @endif--}}
                    {{--                            @if($sr->user->state_id)--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">State</td>--}}
                    {{--                                <td>{{$sr->user->state->name }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @endif--}}
                    {{--                            @if($sr->user->lga_id)--}}
                    {{--                            <tr>--}}
                    {{--                                <td class="font-weight-bold">LGA</td>--}}
                    {{--                                <td>{{$sr->user->lga->name }}</td>--}}
                    {{--                            </tr>--}}
                    {{--                            @endif--}}
                    {{--                            @if($sr->dorm_id)--}}
                    {{--                                <tr>--}}
                    {{--                                    <td class="font-weight-bold">Dormitory</td>--}}
                    {{--                                    <td>{{$sr->dorm->name.' '.$sr->dorm_room_no }}</td>--}}
                    {{--                                </tr>--}}
                    {{--                            @endif--}}

                    {{--                            </tbody>--}}
                    {{--                        </table>--}}
                    {{--                    </div>--}}

                </div>
            </div>
        </div>
    </div>
</div>


    {{--Student Profile Ends--}}

@endsection
