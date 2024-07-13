@extends('layouts/layoutMaster')

@section('title', 'Fullcalendar - Apps')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/fullcalendar/fullcalendar.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/quill/editor.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/app-calendar.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/fullcalendar/fullcalendar.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

@section('page-script')
    <script>
        const userId = {{ Auth::id() }};
    </script>
    @vite(['resources/assets/js/app-calendar-events.js', 'resources/assets/js/app-calendar.js', 'resources/assets/js/forms-selects.js'])
@endsection

@section('content')
    <div class="card app-calendar-wrapper">
        <div class="row g-0">
            <!-- Calendar Sidebar -->
            <div class="col app-calendar-sidebar" id="app-calendar-sidebar">
                <div class="border-bottom p-4 my-sm-0 mb-3">
                    <div class="d-grid">
                        <button class="btn btn-primary btn-toggle-sidebar" data-bs-toggle="offcanvas"
                            data-bs-target="#addEventSidebar" aria-controls="addEventSidebar">
                            <i class="ti ti-plus me-1"></i>
                            <span class="align-middle">Add Event</span>
                        </button>
                    </div>
                </div>
                <div class="p-3">
                    <!-- inline calendar (flatpicker) -->
                    <div class="inline-calendar"></div>

                    <hr class="container-m-nx mb-4 mt-3">

                    <!-- Filter -->
                    <div class="mb-3 ms-3">
                        <small class="text-small text-muted text-uppercase align-middle">Filter</small>
                    </div>
                    <div class="mb-3 select2-primary">
                        <label class="form-label" for="filterPatients">Select Patients</label>
                        <select class="select2 form-select form-select-lg" data-allow-clear="true" id="filterPatients"
                            name="filterPatients[]" multiple>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                            @endforeach
                        </select>

                    </div>
                    <div class="mb-3 select2-primary">
                        <label class="form-label" for="filterDoctors">Select doctors</label>
                        <select class="select2 form-select form-select-lg" data-allow-clear="true" id="filterDoctors"
                            name="filterDoctors[]" multiple>
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                            @endforeach
                        </select>

                    </div>


                    <div class="form-check mb-2 ms-3">
                        <input class="form-check-input select-all" type="checkbox" id="selectAll" data-value="all" checked>
                        <label class="form-check-label" for="selectAll">View All</label>
                    </div>

                    <div class="app-calendar-events-filter ms-3">
                        <div class="form-check form-check-danger mb-2">
                            <input class="form-check-input input-filter" type="checkbox" id="select-Examination"
                                data-value="Examination" checked>
                            <label class="form-check-label" for="select-Examination">Examination</label>
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input input-filter" type="checkbox" id="select-Consultation"
                                data-value="Consultation" checked>
                            <label class="form-check-label" for="select-Consultation">Consultation</label>
                        </div>
                        <div class="form-check form-check-warning mb-2">
                            <input class="form-check-input input-filter" type="checkbox" id="select-Follow-up"
                                data-value="Follow-up" checked>
                            <label class="form-check-label" for="select-Follow-up">Follow-up</label>
                        </div>
                        <div class="form-check form-check-success mb-2">
                            <input class="form-check-input input-filter" type="checkbox" id="select-Procedure"
                                data-value="Procedure" checked>
                            <label class="form-check-label" for="select-Procedure">Procedure</label>
                        </div>
                        <div class="form-check form-check-info">
                            <input class="form-check-input input-filter" type="checkbox" id="select-Other"
                                data-value="Other" checked>
                            <label class="form-check-label" for="select-Other">Other</label>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Calendar Sidebar -->

            <!-- Calendar & Modal -->
            <div class="col app-calendar-content">
                <div class="card shadow-none border-0">
                    <div class="card-body pb-0">
                        <!-- FullCalendar -->
                        <div id="calendar"></div>
                    </div>
                </div>
                <div class="app-overlay"></div>
                <!-- FullCalendar Offcanvas -->
                <div class="offcanvas offcanvas-end event-sidebar" tabindex="-1" id="addEventSidebar"
                    aria-labelledby="addEventSidebarLabel">
                    <div class="offcanvas-header my-1">
                        <h5 class="offcanvas-title" id="addEventSidebarLabel">Add Event</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body pt-0">
                        <form class="event-form pt-0" id="eventForm" onsubmit="return false">
                            <div class="mb-3">
                                <label class="form-label" for="eventTitle">Title</label>
                                <input type="text" class="form-control" id="eventTitle" name="eventTitle"
                                    placeholder="Event Title" />
                                <span class="error-message alert-danger" id="eventTitle-error"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="eventLabel">Label</label>
                                <select class="select2 select-event-label form-select" id="eventLabel" name="eventLabel">
                                    <option value="Consultation" selected>Consultation</option>
                                    <option value="Examination">Examination</option>
                                    <option value="Follow-up">Follow-up</option>
                                    <option value="Procedure">Procedure</option>
                                    <option value="Other">Other</option>
                                </select>
                                <span class="error-message alert-danger" id="eventLabel-error"></span>

                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="eventStartDate">Start Date</label>
                                <input type="text" class="form-control" id="eventStartDate" name="eventStartDate"
                                    placeholder="Start Date" />
                                <span class="error-message alert-danger" id="eventStartDate-error"></span>

                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="eventEndDate">End Date</label>
                                <input type="text" class="form-control" id="eventEndDate" name="eventEndDate"
                                    placeholder="End Date" />
                                <span class="error-message alert-danger" id="eventEndDate-error"></span>

                            </div>
                            {{-- <div class="mb-3">
                                <label class="switch">
                                    <input type="checkbox" class="switch-input allDay-switch" />
                                    <span class="switch-toggle-slider">
                                        <span class="switch-on"></span>
                                        <span class="switch-off"></span>
                                    </span>
                                    <span class="switch-label">All Day</span>
                                </label>
                            </div> --}}
                            {{-- <div class="mb-3">
                                <label class="form-label" for="eventURL">Event URL</label>
                                <input type="url" class="form-control" id="eventURL" name="eventURL"
                                    placeholder="https://www.google.com" />
                            </div> --}}

                            {{-- <div class="mb-3 select2-primary">
                                <label class="form-label" for="eventDoctors">Select Doctor</label>
                                <select class="select2 select-event-doctors form-select" id="eventDoctors"
                                    name="eventDoctors">
                                    @foreach ($doctors as $doctor)
                                        <option value="{{ $doctor->id }}">{{ $doctor->name }}
                                        </option>
                                    @endforeach

                                </select>
                                <span class="error-message alert-danger" id="eventDoctors-error"></span>

                            </div> --}}
                            <div class="mb-3">
                                <label class="form-label" for="eventDoctors">Select Doctor</label>
                                <input type="text" class="form-control" id="eventDoctors" name="eventDoctors"
                                    placeholder="Enter Doctor's Name" autocomplete="off" value="{{ Auth::id() }}"
                                    disabled>
                                <span class="error-message alert-danger" id="eventDoctors-error"></span>
                            </div>

                            <div class="mb-3 select2-primary">
                                <label class="form-label" for="eventPatients">Select Patient</label>
                                <select class="select2 select-event-patients form-select" id="eventPatients"
                                    name="eventPatients">
                                    @foreach ($patients as $patient)
                                        <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                                    @endforeach

                                </select>
                                <span class="error-message alert-danger" id="eventPatients-error"></span>

                            </div>
                            {{-- <div class="mb-3">
                                <label class="form-label" for="eventLocation">Location</label>
                                <input type="text" class="form-control" id="eventLocation" name="eventLocation"
                                    placeholder="Enter Location" />
                            </div> --}}
                            <div class="mb-3">
                                <label class="form-label" for="eventDescription">Description</label>
                                <textarea class="form-control" name="eventDescription" id="eventDescription"></textarea>
                                <span class="error-message alert-danger" id="eventDescription-error"></span>

                            </div>
                            <div class="mb-3 d-flex justify-content-sm-between justify-content-start my-4">
                                <div>

                                    <button type="submit" class="btn btn-primary btn-add-event me-sm-3 me-1">Add</button>
                                    <button type="reset" class="btn btn-label-secondary btn-cancel me-sm-0 me-1"
                                        data-bs-dismiss="offcanvas">Cancel</button>
                                </div>
                                <div><button class="btn btn-label-danger btn-delete-event d-none">Delete</button></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Calendar & Modal -->
        </div>
    </div>

@endsection
