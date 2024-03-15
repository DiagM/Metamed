@extends('layouts/layoutMaster')

@section('title', 'User View - Pages')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss'])
@endsection

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-user-view.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js'])
@endsection

@section('page-script')
    @vite(['resources/js/laravel-MedicalFile.js', 'resources/assets/js/app-user-view.js', 'resources/assets/js/modal-edit-user.js'])
@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">User / View /</span> Account
    </h4>
    <div class="row">
        <!-- User Sidebar -->
        <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
            <!-- User Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="user-avatar-section">
                        <div class=" d-flex align-items-center flex-column">

                            <div class="avatar-wrapper"></div>

                            <div class="user-info text-center">
                                <h4 class="mb-2">{{ $user->name }}</h4>
                                <span class="badge bg-label-secondary mt-1">Patient</span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-around flex-wrap mt-3 pt-3 pb-4 border-bottom">
                        <div class="d-flex align-items-start me-4 mt-3 gap-2">
                            <span class="badge bg-label-primary p-2 rounded"><i class='ti ti-checkbox ti-sm'></i></span>
                            <div>
                                <p class="mb-0 fw-medium">1.23k</p>
                                <small>Tasks Done</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-start mt-3 gap-2">
                            <span class="badge bg-label-primary p-2 rounded"><i class='ti ti-briefcase ti-sm'></i></span>
                            <div>
                                <p class="mb-0 fw-medium">568</p>
                                <small>Projects Done</small>
                            </div>
                        </div>
                    </div>
                    <p class="mt-4 small text-uppercase text-muted">Details</p>
                    <div class="info-container">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <span class="fw-medium me-1">Username:</span>
                                <span>{{ $user->name }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">Email:</span>
                                <span>{{ $user->email }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">Status:</span>
                                <span class="badge bg-label-success">Active</span>
                            </li>

                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">Licence Number:</span>
                                <span>{{ $user->license_number }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">Contact:</span>
                                <span>{{ $user->contact }}</span>
                            </li>
                            <li class="mb-2 pt-1">
                                <span class="fw-medium me-1">Date of birth:</span>
                                <span>{{ $user->date_of_birth }}</span>
                            </li>
                            <li class="pt-1">
                                <span class="fw-medium me-1">Gender:</span>
                                <span>{{ $user->gender }}</span>
                            </li>
                            <li class="pt-1">
                                <span class="fw-medium me-1">Address:</span>
                                <span>{{ $user->address }}</span>
                            </li>
                        </ul>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:;" class="btn btn-primary me-3" data-bs-target="#editUser"
                                data-bs-toggle="modal">Edit</a>
                            <a href="javascript:;" class="btn btn-label-danger suspend-user">Suspended</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /User Card -->
            <!-- Plan Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <span class="badge bg-label-primary">Standard</span>
                        <div class="d-flex justify-content-center">
                            <sup class="h6 pricing-currency mt-3 mb-0 me-1 text-primary fw-normal">$</sup>
                            <h1 class="mb-0 text-primary">99</h1>
                            <sub class="h6 pricing-duration mt-auto mb-2 text-muted fw-normal">/month</sub>
                        </div>
                    </div>
                    <ul class="ps-3 g-2 my-3">
                        <li class="mb-2">10 Users</li>
                        <li class="mb-2">Up to 10 GB storage</li>
                        <li>Basic Support</li>
                    </ul>
                    <div class="d-flex justify-content-between align-items-center mb-1 fw-medium text-heading">
                        <span>Days</span>
                        <span>65% Completed</span>
                    </div>
                    <div class="progress mb-1" style="height: 8px;">
                        <div class="progress-bar" role="progressbar" style="width: 65%;" aria-valuenow="65"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span>4 days remaining</span>
                    <div class="d-grid w-100 mt-4">
                        <button class="btn btn-primary" data-bs-target="#upgradePlanModal" data-bs-toggle="modal">Upgrade
                            Plan</button>
                    </div>
                </div>
            </div>
            <!-- /Plan Card -->
        </div>
        <!--/ User Sidebar -->


        <!-- User Content -->
        <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
            <!-- User Pills -->
            <ul class="nav nav-pills flex-column flex-md-row mb-4">
                <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i
                            class="ti ti-user-check ti-xs me-1"></i>Account</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('app/user/view/security') }}"><i
                            class="ti ti-lock ti-xs me-1"></i>Security</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('app/user/view/billing') }}"><i
                            class="ti ti-currency-dollar ti-xs me-1"></i>Billing & Plans</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('app/user/view/notifications') }}"><i
                            class="ti ti-bell ti-xs me-1"></i>Notifications</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ url('app/user/view/connections') }}"><i
                            class="ti ti-link ti-xs me-1"></i>Connections</a></li>
            </ul>
            <!--/ User Pills -->

            <!-- Project table -->
            <div class="card mb-4">
                <h5 class="card-header">User's Projects List</h5>
                <div class="table-responsive mb-3">
                    <table class="table datatable-project border-top">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Id</th>
                                <th>File Name</th>
                                <th class="text-nowrap">Description</th>
                                <th>Created_at</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- /Project table -->

            <!-- Activity Timeline -->
            <div class="card mb-4">
                <h5 class="card-header">User Activity Timeline</h5>
                <div class="card-body pb-0">
                    <ul class="timeline mb-0">
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-primary"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0">12 Invoices have been paid</h6>
                                    <small class="text-muted">12 min ago</small>
                                </div>
                                <p class="mb-2">Invoices have been paid to the company</p>
                                <div class="d-flex">
                                    <a href="javascript:void(0)" class="me-3">
                                        <img src="{{ asset('assets/img/icons/misc/pdf.png') }}" alt="PDF image"
                                            width="15" class="me-2">
                                        <span class="fw-medium text-heading">invoices.pdf</span>
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-warning"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0">Client Meeting</h6>
                                    <small class="text-muted">45 min ago</small>
                                </div>
                                <p class="mb-2">Project meeting with john @10:15am</p>
                                <div class="d-flex flex-wrap">
                                    <div class="avatar me-3">
                                        <img src="{{ asset('assets/img/avatars/3.png') }}" alt="Avatar"
                                            class="rounded-circle" />
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Lester McCarthy (Client)</h6>
                                        <small>CEO of {{ config('variables.creatorName') }}</small>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item timeline-item-transparent">
                            <span class="timeline-point timeline-point-info"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0">Create a new project for client</h6>
                                    <small class="text-muted">2 Day Ago</small>
                                </div>
                                <p class="mb-2">5 team members in a project</p>
                                <div class="d-flex align-items-center avatar-group">
                                    <div class="avatar pull-up" data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                        data-bs-placement="top" title="Vinnie Mostowy">
                                        <img src="{{ asset('assets/img/avatars/5.png') }}" alt="Avatar"
                                            class="rounded-circle">
                                    </div>
                                    <div class="avatar pull-up" data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                        data-bs-placement="top" title="Marrie Patty">
                                        <img src="{{ asset('assets/img/avatars/12.png') }}" alt="Avatar"
                                            class="rounded-circle">
                                    </div>
                                    <div class="avatar pull-up" data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                        data-bs-placement="top" title="Jimmy Jackson">
                                        <img src="{{ asset('assets/img/avatars/9.png') }}" alt="Avatar"
                                            class="rounded-circle">
                                    </div>
                                    <div class="avatar pull-up" data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                        data-bs-placement="top" title="Kristine Gill">
                                        <img src="{{ asset('assets/img/avatars/6.png') }}" alt="Avatar"
                                            class="rounded-circle">
                                    </div>
                                    <div class="avatar pull-up" data-bs-toggle="tooltip" data-popup="tooltip-custom"
                                        data-bs-placement="top" title="Nelson Wilson">
                                        <img src="{{ asset('assets/img/avatars/4.png') }}" alt="Avatar"
                                            class="rounded-circle">
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="timeline-item timeline-item-transparent border-transparent">
                            <span class="timeline-point timeline-point-success"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-1">
                                    <h6 class="mb-0">Design Review</h6>
                                    <small class="text-muted">5 days Ago</small>
                                </div>
                                <p class="mb-0">Weekly review of freshly prepared design for our new app.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- /Activity Timeline -->

            <!-- Invoice table -->
            {{-- <div class="card mb-4">
                <div class="table-responsive mb-3">
                    <table class="table datatable-invoice border-top">
                        <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th><i class='ti ti-trending-up text-secondary'></i></th>
                                <th>Total</th>
                                <th>Issued Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div> --}}
            <!-- /Invoice table -->
        </div>
        <!--/ User Content -->
    </div>

    <!-- Offcanvas to add new medical file -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddMedicalFile"
        aria-labelledby="offcanvasAddMedicalFileLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasAddMedicalFileLabel" class="offcanvas-title">Add Medical File</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0">
            <form class="add-new-medical-file pt-0" id="addNewMedicalFileForm">
                <input type="hidden" name="id" id="medical_file_id">

                <div class="mb-3">
                    <label class="form-label" for="add-medical-file-name">File Name</label>
                    <input type="text" class="form-control" id="add-medical-file-name" placeholder="File Name"
                        name="file_name" aria-label="File Name" />
                </div>
                <div class="mb-3">
                    <label class="form-label" for="add-medical-file-description">Description</label>
                    <input type="text" id="add-medical-file-description" class="form-control"
                        placeholder="Description" aria-label="Description" name="description" />
                </div>

                <div class="mb-3">
                    <label class="form-label" for="add-medical-file">Upload File</label>
                    <input type="file" id="add-medical-file" name="medical_file" class="form-control"
                        aria-label="Upload File" />
                </div>
                <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
            </form>
        </div>
    </div>




    <!-- Modal -->
    @include('_partials/_modals/modal-edit-user')
    @include('_partials/_modals/modal-upgrade-plan')
    <!-- /Modal -->
    <script>
        window.userData = {!! json_encode(['id' => $user->id, 'name' => $user->name]) !!};
    </script>


@endsection
