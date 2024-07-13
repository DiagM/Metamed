@extends('layouts/layoutMaster')

@section('title', 'User Management - Crud App')

<!-- Vendor Styles -->
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
    @vite(['resources/js/laravel-department-management.js'])
@endsection

@section('content')

    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>departments</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2">{{ $totalUser }}</h3>
                                <small class="text-success">(100%)</small>
                            </div>
                            <small>Total departments</small>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="fa-solid fa-building-user fa-2x"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Most doctors:</span>
                            @if ($mostDoctorsDepartment)
                                <div class="d-flex align-items-end mt-2">
                                    <h3 class="mb-0 me-2">{{ $mostDoctorsDepartment->name }}</h3>
                                    <small class="text-success">({{ $mostDoctorsDepartmentCount }})</small>
                                </div>
                            @endif
                            <small>Total doctors</small>
                        </div>
                        <span class="badge bg-label-success rounded p-2">
                            <i class="fa-solid fa-arrow-up fa-2x"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Least doctors</span>
                            @if ($leastDoctorsDepartment)
                                <div class="d-flex align-items-end mt-2">
                                    <h3 class="mb-0 me-2">{{ $leastDoctorsDepartment->name }}</h3>
                                    <small class="text-success">({{ $leastDoctorsDepartmentCount }})</small>
                                </div>
                            @endif
                            <small>Total doctors</small>
                        </div>
                        <span class="badge bg-label-danger rounded p-2">
                            <i class="fa-solid fa-arrow-down fa-2x"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Verification Pending</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2"></h3>
                                <small class="text-danger">(+6%)</small>
                            </div>
                            <small>Recent analytics</small>
                        </div>
                        <span class="badge bg-label-warning rounded p-2">
                            <i class="ti ti-user-circle ti-sm"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
    <!-- Users List Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Search Filter</h5>

        </div>


        <div class="card-datatable table-responsive">
            <table class="datatables-users table">
                <thead class="border-top">
                    <tr>
                        <th></th>
                        <th>Id</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Created_at</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
        <!-- Offcanvas to add new department -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAdddepartment"
            aria-labelledby="offcanvasAdddepartmentLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasAdddepartmentLabel" class="offcanvas-title">Add department</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0">
                <form class="add-new-department pt-0" id="addNewdepartmentForm">
                    <input type="hidden" name="id" id="department_id">
                    <div class="mb-3">
                        <label class="form-label" for="add-department-name">department Name</label>
                        <input type="text" class="form-control" id="add-department-name" placeholder="department Name"
                            name="name" aria-label="department Name" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-department-email">Email</label>
                        <input type="email" id="add-department-email" class="form-control"
                            placeholder="department@example.com" aria-label="department@example.com" name="email" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-department-contact">Contact</label>
                        <input type="text" id="add-department-contact" class="form-control phone-mask"
                            placeholder="+1 (609) 988-44-11" aria-label="department@example.com" name="contact" />
                    </div>

                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>


    </div>
@endsection
