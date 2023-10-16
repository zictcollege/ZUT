@extends('layouts.master')
@section('page_title', 'Programs - ')
@section('content')
    @php
        use App\Helpers\Qs;
    @endphp

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header header-elements-inline">
                    <h6 class="card-title">Enrollments Information</h6>
                    {!! Qs::getPanelOptions() !!}
                </div>

                <div class="card-body">

                    <ul class="nav nav-tabs nav-tabs-highlight">
                        @foreach($enrollments as $en)
                            <li class="nav-item">
                                <a href="#info{{ $en['key'] }}" class="nav-link academic-toggle">{{ $en['code'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="tab-content">
                        @foreach($enrollments as $en)
                            <div class="tab-pane fade show" id="info{{ $en['key'] }}">
                                <button class="btn btn-primary justify-content-end float-right"> Download Summary</button>
                                <table class="table table-hover table-striped-columns">
                                    <thead>
                                    <tr>
{{--                                        <th>S/N</th>--}}
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($en['classes'] as $class)
                                        <tr>
{{--                                            <td>{{ $loop->iteration }}</td>--}}
                                            <td>{{ $class['course_code'] }}</td>
                                            <td>{{ $class['course_name'] }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" id="launchModalButton">
        Launch static backdrop modal
    </button>

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="remove"></a>
                    </div>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeModalButton" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary">Understood</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const launchModalButton = document.getElementById('launchModalButton');
            const closeModalButton = document.getElementById('closeModalButton');
            const staticBackdropModal = new bootstrap.Modal(document.getElementById('staticBackdrop'));

            launchModalButton.addEventListener('click', function () {
                staticBackdropModal.show();
            });
            closeModalButton.addEventListener('click', function () {
                staticBackdropModal.hide();
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const navLinks = document.querySelectorAll('.academic-toggle');
            const tabContents = document.querySelectorAll('.tab-pane');

            navLinks.forEach(link => {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    navLinks.forEach(navLink => navLink.classList.remove('active'));
                    tabContents.forEach(tabContent => tabContent.classList.remove('active', 'show'));

                    this.classList.add('active');
                    const target = this.getAttribute('href');
                    document.querySelector(target).classList.add('active', 'show');
                });
            });
        });
    </script>
@endsection
