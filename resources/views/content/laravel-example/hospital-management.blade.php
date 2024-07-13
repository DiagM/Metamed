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
    @vite(['resources/js/laravel-hospital-management.js'])
@endsection
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

@section('content')

    {{-- <div class="row g-4 mb-4">
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
    </div> --}}
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
        <!-- Offcanvas to add new hospital -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddHospital"
            aria-labelledby="offcanvasAddHospitalLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddHospitalLabel" class="offcanvas-title">Add Hospital</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0">
                <form class="add-new-hospital pt-0" id="addNewHospitalForm">
                    <input type="hidden" name="id" id="hospital_id">
                    <div class="mb-3">
                        <label class="form-label" for="add-hospital-name">Hospital Name</label>
                        <input type="text" class="form-control" id="add-hospital-name" placeholder="Hospital Name"
                            name="name" aria-label="Hospital Name" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-hospital-email">Email</label>
                        <input type="email" id="add-hospital-email" class="form-control"
                            placeholder="hospital@example.com" aria-label="hospital@example.com" name="email" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-hospital-contact">Contact</label>
                        <input type="text" id="add-hospital-contact" class="form-control phone-mask"
                            placeholder="+1 (609) 988-44-11" aria-label="hospital@example.com" name="contact" />
                    </div>
                    <!-- Leaflet map container -->
                    <div id="map" style="height: 300px;"></div>
                    <input type="hidden" id="latitude" name="latitude">
                    <input type="hidden" id="longitude" name="longitude">

                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>


    </div>
    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script>
        var map = L.map('map').setView([36.75, 3.04], 13);


        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        L.Control.geocoder().addTo(map)

        var marker;

        map.on('click', function(e) {
            if (marker) {
                map.removeLayer(marker);
            }
            marker = L.marker(e.latlng).addTo(map);
            document.getElementById('latitude').value = e.latlng.lat;
            document.getElementById('longitude').value = e.latlng.lng;
        });
    </script>

@endsection
