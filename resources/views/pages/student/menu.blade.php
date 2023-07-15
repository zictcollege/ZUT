<li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['tt.index', 'ttr.edit', 'ttr.show', 'ttr.manage']) ? 'nav-item-expanded nav-item-open' : '' }} ">
    <a href="#" class="nav-link"><i class="icon-graduation2"></i> <span> Finances</span></a>
    <ul class="nav nav-group-sub" data-submenu-title="Manage Academics">
        <li class="nav-item"><a href="{{ route('calendar') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['calendar']) ? 'active' : '' }}">Invoices</a></li>
        <li class="nav-item"><a href="{{ route('intakes.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['intakes.index','intakes.edit']) ? 'active' : '' }}">Statements</a></li>
    </ul>
</li>
<li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['tt.index', 'ttr.edit', 'ttr.show', 'ttr.manage']) ? 'nav-item-expanded nav-item-open' : '' }} ">
    <a href="#" class="nav-link"><i class="icon-graduation2"></i> <span> Examinations</span></a>
    <ul class="nav nav-group-sub" data-submenu-title="Manage Academics">
        <li class="nav-item"><a href="{{ route('calendar') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['calendar']) ? 'active' : '' }}">Results</a></li>
        <li class="nav-item"><a href="{{ route('intakes.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['intakes.index','intakes.edit']) ? 'active' : '' }}">CA Results</a></li>
    </ul>
</li>
<li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['tt.index', 'ttr.edit', 'ttr.show', 'ttr.manage']) ? 'nav-item-expanded nav-item-open' : '' }} ">
    <a href="#" class="nav-link"><i class="icon-graduation2"></i> <span> Account Settings</span></a>
    <ul class="nav nav-group-sub" data-submenu-title="Manage Academics">
        <li class="nav-item"><a href="{{ route('calendar') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['calendar']) ? 'active' : '' }}">Change Password</a></li>
        <li class="nav-item"><a href="{{ route('intakes.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['intakes.index','intakes.edit']) ? 'active' : '' }}">Intakes</a></li>
    </ul>
</li>
<li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['tt.index', 'ttr.edit', 'ttr.show', 'ttr.manage']) ? 'nav-item-expanded nav-item-open' : '' }} ">
    <a href="#" class="nav-link"><i class="icon-graduation2"></i> <span> Time Tables</span></a>
    <ul class="nav nav-group-sub" data-submenu-title="Manage Academics">
        <li class="nav-item"><a href="{{ route('calendar') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['calendar']) ? 'active' : '' }}">Exam Timetable</a></li>
        <li class="nav-item"><a href="{{ route('intakes.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['intakes.index','intakes.edit']) ? 'active' : '' }}">Student Time table</a></li>
    </ul>
</li>
<li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['tt.index', 'ttr.edit', 'ttr.show', 'ttr.manage']) ? 'nav-item-expanded nav-item-open' : '' }} ">
    <a href="#" class="nav-link"><i class="icon-graduation2"></i> <span> E Learning</span></a>
    <ul class="nav nav-group-sub" data-submenu-title="Manage Academics">
        <li class="nav-item"><a href="{{ route('calendar') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['calendar']) ? 'active' : '' }}">Modal</a></li>
        <li class="nav-item"><a href="{{ route('intakes.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['intakes.index','intakes.edit']) ? 'active' : '' }}">Intakes</a></li>
    </ul>
</li>
<li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['tt.index', 'ttr.edit', 'ttr.show', 'ttr.manage']) ? 'nav-item-expanded nav-item-open' : '' }} ">
    <a href="#" class="nav-link"><i class="icon-graduation2"></i> <span> Registration</span></a>
    <ul class="nav nav-group-sub" data-submenu-title="Manage Academics">
        <li class="nav-item"><a href="{{ route('calendar') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['calendar']) ? 'active' : '' }}">Register</a></li>
        <li class="nav-item"><a href="{{ route('intakes.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['intakes.index','intakes.edit']) ? 'active' : '' }}">Intakes</a></li>
    </ul>
</li>
<li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['tt.index', 'ttr.edit', 'ttr.show', 'ttr.manage']) ? 'nav-item-expanded nav-item-open' : '' }} ">
    <a href="#" class="nav-link"><i class="icon-graduation2"></i> <span> Accommodation</span></a>
    <ul class="nav nav-group-sub" data-submenu-title="Manage Academics">
        <li class="nav-item"><a href="{{ route('calendar') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['calendar']) ? 'active' : '' }}">Application</a></li>
        <li class="nav-item"><a href="{{ route('intakes.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['intakes.index','intakes.edit']) ? 'active' : '' }}">Intakes</a></li>
    </ul>
</li>

<li class="nav-item nav-item-submenu {{ in_array(Route::currentRouteName(), ['tt.index', 'ttr.edit', 'ttr.show', 'ttr.manage']) ? 'nav-item-expanded nav-item-open' : '' }} ">
    <a href="#" class="nav-link"><i class="icon-graduation2"></i> <span> Application</span></a>
    <ul class="nav nav-group-sub" data-submenu-title="Manage Academics">
        <li class="nav-item"><a href="{{ route('calendar') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['calendar']) ? 'active' : '' }}">Change Study Mode</a></li>
        <li class="nav-item"><a href="{{ route('intakes.index') }}" class="nav-link {{ in_array(Route::currentRouteName(), ['intakes.index','intakes.edit']) ? 'active' : '' }}">Apply in new Program</a></li>
    </ul>
</li>

{{--Marksheet--}}
<li class="nav-item">
    <a href="{{ "#" }}" class="nav-link {{ in_array(Route::currentRouteName(), ['marks.show', 'marks.year_selector', 'pins.enter']) ? 'active' : '' }}"><i class="icon-book"></i> Marksheet</a>
</li>
