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
    @vite(['resources/js/laravel-user-management.js'])
@endsection

@section('content')

    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Users</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2">{{ $totalUser }}</h3>
                                <small class="text-success">(100%)</small>
                            </div>
                            <small>Total Users</small>
                        </div>
                        <span class="badge bg-label-primary rounded p-2">
                            <i class="ti ti-user ti-sm"></i>
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
                            <span>Verified Users</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2">{{ $verified }}</h3>
                                <small class="text-success">(+95%)</small>
                            </div>
                            <small>Recent analytics </small>
                        </div>
                        <span class="badge bg-label-success rounded p-2">
                            <i class="ti ti-user-check ti-sm"></i>
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
                            <span>Duplicate Users</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2">{{ $userDuplicates }}</h3>
                                <small class="text-success">(0%)</small>
                            </div>
                            <small>Recent analytics</small>
                        </div>
                        <span class="badge bg-label-danger rounded p-2">
                            <i class="ti ti-users ti-sm"></i>
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
                            <span>Verification Pending</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2">{{ $notVerified }}</h3>
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
        </div>
    </div>
    <!-- Users List Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Search Filter</h5>
            <div class="mb-3">
                <label for="filter-department" class="form-label">Filter by Department:</label>
                <select id="filter-department" class="form-select" style="width: 200px">
                    <option value="">All Departments</option>
                    <option value="Cardiology">Cardiology</option>
                    <option value="Orthopedics">Orthopedics</option>
                    <option value="Gynecology">Gynecology</option>
                    <option value="Pediatrics">Pediatrics</option>
                    <option value="Neurology">Neurology</option>
                    <option value="Ophthalmology">Ophthalmology</option>
                    <option value="Oncology">Oncology</option>
                    <option value="Dermatology">Dermatology</option>
                </select>
            </div>
        </div>


        <div class="card-datatable table-responsive">
            <table class="datatables-users table">
                <thead class="border-top">
                    <tr>
                        <th></th>
                        <th>Id</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>License number</th>
                        <th>Department</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
        <!-- Offcanvas to add new user -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add User</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0">
                <form class="add-new-user pt-0" id="addNewUserForm">
                    <input type="hidden" name="id" id="user_id">
                    <div class="mb-3">
                        <label class="form-label" for="add-user-fullname">Full Name</label>
                        <input type="text" class="form-control" id="add-user-fullname" placeholder="John Doe"
                            name="name" aria-label="John Doe" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-user-email">Email</label>
                        <input type="text" id="add-user-email" class="form-control" placeholder="john.doe@example.com"
                            aria-label="john.doe@example.com" name="email" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-user-contact">Contact</label>
                        <input type="text" id="add-user-contact" class="form-control phone-mask"
                            placeholder="+1 (609) 988-44-11" aria-label="john.doe@example.com" name="userContact" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-user-license_number">License number</label>
                        <input type="text" id="add-user-license_number" name="license_number" class="form-control"
                            placeholder="AD576" aria-label="jdoe1" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="department">Department</label>
                        <select id="department" class="select2 form-select" name="department">
                            <option value="" selected disabled>Select Department</option>
                            <option value="Cardiology">Cardiology</option>
                            <option value="Orthopedics">Orthopedics</option>
                            <option value="Gynecology">Gynecology</option>
                            <option value="Pediatrics">Pediatrics</option>
                            <option value="Neurology">Neurology</option>
                            <option value="Ophthalmology">Ophthalmology</option>
                            <option value="Oncology">Oncology</option>
                            <option value="Dermatology">Dermatology</option>
                        </select>
                    </div>


                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>
    </div>
@endsection
