@php
    use App\Helpers\Qs;
@endphp
<div class="sidebar sidebar-dark sidebar-main sidebar-expand-md">

    <!-- Sidebar mobile toggler -->
    <div class="sidebar-mobile-toggler text-center">
        <a href="#" class="sidebar-mobile-main-toggle">
            <i class="icon-arrow-left8"></i>
        </a>
        Navigation
        <a href="#" class="sidebar-mobile-expand">
            <i class="icon-screen-full"></i>
            <i class="icon-screen-normal"></i>
        </a>
    </div>
    <!-- /sidebar mobile toggler -->

    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- User menu -->
        <div class="sidebar-user">
            <div class="card-body">
                <div class="media">
                    <div class="mr-3">
                        <a href="{{ "#" }}"><img src="{{ Auth::user()->photo }}" width="38" height="38" class="rounded-circle" alt="photo"></a>
                    </div>

                    <div class="media-body">
                        <div class="media-title font-weight-semibold">{{ Auth::user()->name }}</div>
                        <div class="font-size-xs opacity-50">
                            <i class="icon-user font-size-sm"></i> &nbsp;{{ ucwords(str_replace('_', ' ', Auth::user()->user_type)) }}
                        </div>
                    </div>

                    <div class="ml-3 align-self-center">
                        <a href="{{ "#" }}" class="text-white"><i class="icon-cog3"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <!-- /user menu -->

        <!-- Main navigation -->
        <div class="card card-sidebar-mobile">
            <ul class="nav nav-sidebar" data-nav-type="accordion">

                <!-- Main -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ (Route::is('dashboard')) ? 'active' : '' }}">
                        <i class="icon-home4"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                {{--Academics--}}
                @if(true)
                    <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['calendar', 'intakes.index', 'academic.show', 'update','index.prerequisite','edit.prerequisite','show.prerequisite']) ? 'nav-item-expanded nav-item-open' : '' }} ">
                        <a href="#" class="nav-link"><i class="icon-graduation2"></i> <span> Academics</span></a>

                        <ul class="nav nav-group-sub" data-submenu-title="Manage Academics">

                        {{--Timetables--}}
                            <li class="nav-item"><a href="{{ route('calendar') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['calendar']) ? 'active' : '' }}">Academic Periods</a></li>
                            <li class="nav-item"><a href="{{ route('intakes.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['intakes.index','intakes.edit']) ? 'active' : '' }}">Intakes</a></li>
                            <li class="nav-item"><a href="{{ route('index.prerequisite') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['index.prerequisite','edit.prerequisite','show.prerequisite']) ? 'active' : '' }}">Prerequisites</a></li>
                            <li class="nav-item"><a href="{{ "#" }}" class="nav-link {{ in_array(Route::currentRouteName(), ['tt.index']) ? 'active' : '' }}">Notification</a></li>
                        </ul>
                    </li>
                    @endif

                {{--Administrative--}}
                @if(true)
                    <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['payments.index', 'payments.create', 'payments.invoice', 'payments.receipts', 'payments.edit', 'payments.manage', 'payments.show',]) ? 'nav-item-expanded nav-item-open' : '' }} ">
                        <a href="#" class="nav-link"><i class="icon-office"></i> <span> Administrative</span></a>

                        <ul class="nav nav-group-sub" data-submenu-title="Administrative">

                            {{--Payments--}}
                            @if(true)
                            <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['payments.index', 'payments.create', 'payments.edit', 'payments.manage', 'payments.show', 'payments.invoice']) ? 'nav-item-expanded' : '' }}">

                                <a href="#" class="nav-link {{ in_array(Route::currentRouteName(), ['payments.index', 'payments.edit', 'payments.create', 'payments.manage', 'payments.show', 'payments.invoice']) ? 'active' : '' }}">Payments</a>

                                <ul class="nav nav-group-sub">
                                    <li class="nav-item"><a href="{{ "#" }}" class="nav-link {{ Route::is('payments.create') ? 'active' : '' }}">Create Payment</a></li>
                                    <li class="nav-item"><a href="{{ "#" }}" class="nav-link {{ in_array(Route::currentRouteName(), ['payments.index', 'payments.edit', 'payments.show']) ? 'active' : '' }}">Manage Payments</a></li>
                                    <li class="nav-item"><a href="{{ "#"}}" class="nav-link {{ in_array(Route::currentRouteName(), ['payments.manage', 'payments.invoice', 'payments.receipts']) ? 'active' : '' }}">Student Payments</a></li>

                                </ul>

                            </li>
                            @endif
                        </ul>
                    </li>
                @endif

                {{--Manage Students--}}
                @if(true)
                    <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['students.create', 'students.list','students.lists', 'students.edit', 'students.show', 'students.enrollments', 'students.promotion_manage', 'students.graduated']) ? 'nav-item-expanded nav-item-open' : '' }} ">
                        <a href="#" class="nav-link"><i class="icon-users"></i> <span> Students</span></a>

                        <ul class="nav nav-group-sub" data-submenu-title="Manage Students">
                            {{--Admit Student--}}
                            @if(true)
                                <li class="nav-item">
                                    <a href="{{ route('students.create') }}"
                                       class="nav-link {{ (Route::is('students.create')) ? 'active' : '' }}">Admit Student</a>
                                </li>
                            @endif
                            {{--Student Information--}}
                            <li class="nav-item">
                                <a href="{{ route('students.list') }}"
                                   class="nav-link {{ (Route::is('students.list')) ? 'active' : '' }}">Student Information</a>
                            </li>

                            @if(true)

                            {{--Student Promotion--}}
                            <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['students.enrollments', 'students.promotion_manage']) ? 'nav-item-expanded' : '' }}"><a href="#" class="nav-link {{ in_array(Route::currentRouteName(), ['students.enrollments', 'students.promotion_manage' ]) ? 'active' : '' }}">Student Enrollment</a>
                            <ul class="nav nav-group-sub">
                                <li class="nav-item"><a href="{{ "#" }}" class="nav-link {{ in_array(Route::currentRouteName(), ['students.enrollments']) ? 'active' : '' }}">Students registration</a></li>
                                <li class="nav-item"><a href="{{ "#" }}" class="nav-link {{ in_array(Route::currentRouteName(), ['students.promotion_manage']) ? 'active' : '' }}">Manage Registrations</a></li>
                            </ul>

                            </li>

                            {{--Student Graduated--}}
                            <li class="nav-item"><a href="{{ "#"  }}" class="nav-link {{ in_array(Route::currentRouteName(), ['students.graduated' ]) ? 'active' : '' }}">Students Graduated</a></li>
                                @endif

                        </ul>
                    </li>
                @endif

                @if(true)
                    {{--Manage Users--}}
                    <li class="nav-item">
                        <a href="{{ route('users.index')  }}" class="nav-link {{ in_array(Route::currentRouteName(), ['users.index', 'users.show', 'users.edit']) ? 'active' : '' }}"><i class="icon-users4"></i> <span> Users</span></a>
                    </li>

                    {{--Manage Classes--}}
                    <li class="nav-item">
                        <a href="{{ route('classes.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['classes.index','classes.edit']) ? 'active' : '' }}"><i class="icon-windows2"></i> <span> Classes</span></a>
                    </li>

                    {{--Manage Study modes--}}
                    <li class="nav-item">
                        <a href="{{ route('studymodes.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['studymodes.index','studymodes.edit']) ? 'active' : '' }}"><i class="icon-home9"></i> <span>Study Modes</span></a>
                    </li>
                    {{-- Academic MANAGEMENT--}}
                    <li class="nav-item">
                        <a href="{{ route('periodtypes.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['periodtypes.index','periodtypes.edit']) ? 'active' : '' }}"><i class="icon-home9"></i> <span>Academic Period Types</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('fees.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['fees.index','fees.edit']) ? 'active' : '' }}"><i class="icon-home9"></i> <span>Fees</span></a>
                    </li>
                    {{--Manage Departments--}}
                    <li class="nav-item">
                        <a href="{{ route('departments.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['departments.index','departments.edit',]) ? 'active' : '' }}"><i class="icon-fence"></i> <span>Departments</span></a>
                    </li>
                    {{--Manage programs--}}
                    <li class="nav-item">
                        <a href="{{ route('programs.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['programs.index','programs.edit',]) ? 'active' : '' }}"><i class="icon-fence"></i> <span>Programs</span></a>
                    </li>

                    {{--Manage courses--}}
                    <li class="nav-item">
                        <a href="{{ route('courses.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['courses.index','courses.edit',]) ? 'active' : '' }}"><i class="icon-pin"></i> <span>Courses</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('qualifications.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['qualifications.index','qualifications.edit',]) ? 'active' : '' }}"><i class="icon-pin"></i> <span>Qualifications</span></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('levels.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['levels.index','levels.edit',]) ? 'active' : '' }}"><i class="icon-pin"></i> <span>Course Levels</span></a>
                    </li>
                @endif

                {{--Exam--}}
                @if(true)
                <li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['exams.index', 'exams.edit', 'assessments.index', 'assessments.edit', 'assessments.store', 'classAssessments.index', 'classAssessments.edit', 'classAssessments.show', 'classAssessments.store', 'marks.batch_fix',]) ? 'nav-item-expanded nav-item-open' : '' }} ">
                    <a href="#" class="nav-link"><i class="icon-books"></i> <span> Exams</span></a>

                    <ul class="nav nav-group-sub" data-submenu-title="Manage Exams">
                        @if(true)

                        {{--Exam list--}}
                            <li class="nav-item">
                                <a href="{{ "#"  }}"
                                   class="nav-link {{ (Route::is('exams.index')) ? 'active' : '' }}">Exam Management</a>
                            </li>
                        {{--Assessment Types --}}
                            <li class="nav-item">
                                <a href="{{ route('assessments.index')  }}"
                                   class="nav-link {{ (Route::is('assessments.index')) ? 'active' : '' }}">Assessment Management</a>
                            </li>

                            {{--Grades list--}}
                            <li class="nav-item">
                                    <a href="{{ "#" }}"
                                       class="nav-link {{ in_array(Route::currentRouteName(), ['grades.index', 'grades.edit']) ? 'active' : '' }}">CA</a>
                            </li>

                            {{--Tabulation Sheet--}}
                            <li class="nav-item">
                                <a href="{{ route('classAssessments.index')  }}"
                                   class="nav-link {{ (Route::is('classAssessments.index')) ? 'active' : '' }}">Exam Management</a>
                            </li>

                            {{--Marks Batch Fix--}}
                            <li class="nav-item">
                                <a href="{{ "#" }}" class="nav-link {{ in_array(Route::currentRouteName(), ['marks.batch_fix']) ? 'active' : '' }}">Batch Fix</a>
                            </li>
                        @endif

                        @if(true)
                            {{--Marks Manage--}}
                            <li class="nav-item">
                                <a href="{{"#" }}"
                                   class="nav-link {{ in_array(Route::currentRouteName(), ['marks.index']) ? 'active' : '' }}">Marks</a>
                            </li>

                            {{--Marksheet--}}
                            <li class="nav-item">
                                <a href="{{ "#" }}" class="nav-link {{ in_array(Route::currentRouteName(), ['marks.bulk', 'marks.show']) ? 'active' : '' }}">Marksheet</a>
                            </li>

                            @endif

                    </ul>
                </li>
                @endif


                {{--End Exam--}}

                @include('pages.'.Qs::getUserType().'.menu')

                {{--Manage Account--}}
                <li class="nav-item">
                    <a href="{{ route('my_account')  }}" class="nav-link {{ in_array(Route::currentRouteName(), ['my_account']) ? 'active' : '' }}"><i class="icon-user"></i> <span>My Account</span></a>
                </li>

                </ul>
            </div>
        </div>
</div>