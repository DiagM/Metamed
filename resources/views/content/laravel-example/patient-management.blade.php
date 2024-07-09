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
    @vite(['resources/js/laravel-patient-management.js', 'resources/assets/js/forms-selects.js'])
@endsection

@section('content')

    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span>Patients</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2">{{ $totalPatients }}</h3>
                                <small class="text-success">(100%)</small>
                            </div>
                            <small>Total Patients</small>
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
                            <span>Male</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2">{{ $MalePatients }}</h3>
                            </div>
                            <small>Total male patient</small>
                        </div>
                        <span class="badge bg-label-success rounded p-2">
                            <i class="fas fa-male fa-2x"></i>
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
                            <span>Female</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2">{{ $FemalePatients }}</h3>
                                <small class="text-success"></small>
                            </div>
                            <small>Total female patient</small>
                        </div>
                        <span class="badge bg-label-warning rounded p-2">
                            <i class="fas fa-female fa-2x"></i>
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
                            <span>blood type</span>
                            <div class="d-flex align-items-end mt-2">
                                <h3 class="mb-0 me-2">{{ $mostCommonBloodType }}</h3>
                                <small class="text-danger">({{ $mostCommonBloodTypeCount }})</small>
                            </div>
                            <small>Most Common</small>
                        </div>
                        <span class="badge  bg-label-danger rounded p-2">
                            <i class="fa-solid fa-droplet fa-2x"></i>
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
            <div class="row align-items-end">
                <div class="col-md-6 mb-3" style="width: 200px">
                    <label for="filter-blood-type" class="form-label">Filter by Blood Type:</label>
                    <select id="filter-blood-type" class="form-select">
                        <option value="">All Blood Types</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
                @unlessrole('doctor')
                    <div class="col-md-6 mb-3" style="width: 200px">
                        <label for="filter-doctor-name" class="form-label">Filter by Doctor Name:</label>
                        <select id="filter-doctor-name" class="select2 form-select form-select-lg" data-allow-clear="true">
                            <option value="">All doctors</option>

                            @foreach ($filterdoctors as $doctor)
                                <option value="{{ $doctor->id }}" data-department-id="{{ $doctor->department_id }}">
                                    {{ $doctor->name }}</option>
                            @endforeach

                        </select>
                    </div>
                @endunlessrole
                @hasrole('hospital')
                    <div class="col-md-6 mb-3" style="width: 200px">
                        <label for="filter-department-name" class="form-label">Filter by Department Name:</label>
                        <select id="filter-department-name" class="select2 form-select form-select-lg" data-allow-clear="true">
                            <option value="">All departments</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endrole

                @hasrole('SuperAdmin')
                    <div class="col-md-6 mb-3" style="width: 200px">
                        <label for="filter-hospital-name" class="form-label">Filter by hospital Name:</label>
                        <select id="filter-hospital-name" class="select2 form-select form-select-lg" data-allow-clear="true">
                            <option value="">All hospitals</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endrole
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
                        <th>Licence number</th>
                        <th>Contact</th>
                        <th>Blood type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
        <!-- Offcanvas to add new user -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Patient</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
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
                        <input type="text" id="add-user-email" class="form-control"
                            placeholder="john.doe@example.com" aria-label="john.doe@example.com" name="email" />
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
                    <!-- Newly added fields -->
                    <div class="mb-3">
                        <label class="form-label" for="add-user-date_of_birth">Date of Birth</label>
                        <input type="date" id="add-user-date_of_birth" name="date_of_birth" class="form-control" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-user-gender">Gender</label>
                        <select id="add-user-gender" name="gender" class="form-control">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-user-address">Address</label>
                        <textarea id="add-user-address" name="address" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-user-height">Height</label>
                        <input type="number" min="0" id="add-user-height" name="height" class="form-control"
                            placeholder="e.g. 180 cm" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-user-weight">Weight</label>
                        <input type="number" min="0" id="add-user-weight" name="weight" class="form-control"
                            placeholder="e.g. 75 kg" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-user-blood_type">Blood Type</label>
                        <select id="add-user-blood_type" name="blood_type" class="form-control">
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-user-medical_notes">Medical Notes</label>
                        <textarea id="add-user-medical_notes" name="medical_notes" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="add-user-allergies">Allergies and reactions</label>
                        <textarea id="add-user-allergies" name="allergies" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
                </form>
            </div>
        </div>


    </div>
    <script>
        $(document).ready(function() {
            // Attach change event listener to hospital filter
            const doctorSelect = document.getElementById('filter-doctor-name');
            const hospitals = @json($departments);

            $('#filter-department-name').change(function() {
                const hospitalId = this.value;

                // Clear current doctor options
                doctorSelect.innerHTML = '<option value="">All doctors</option>';

                if (hospitalId) {

                    const selectedHospital = hospitals.find(hospital => hospital.id == hospitalId);
                    selectedHospital.doctorsdepartment.forEach(doctor => {
                        let option = document.createElement('option');
                        option.value = doctor.id;
                        option.textContent = doctor.name;
                        doctorSelect.appendChild(option);
                    });
                } else {
                    // If no hospital is selected, show all doctors
                    @foreach ($filterdoctors as $doctor)
                        {
                            let option = document.createElement('option');
                            option.value = '{{ $doctor->id }}';
                            option.textContent = '{{ $doctor->name }}';
                            doctorSelect.appendChild(option);

                        }
                    @endforeach
                }
            });

            // Ensure the filters are being properly applied
            $('#filter-hospital-name').trigger('change');
        });
    </script>


    @hasrole('SuperAdmin')
        <script>
            $(document).ready(function() {
                // Attach change event listener to hospital filter
                const doctorSelect = document.getElementById('filter-doctor-name');
                const hospitals = @json($departments);

                $('#filter-hospital-name').change(function() {
                    const hospitalId = this.value;

                    // Clear current doctor options
                    doctorSelect.innerHTML = '<option value="">All doctors</option>';

                    if (hospitalId) {

                        const selectedHospital = hospitals.find(hospital => hospital.id == hospitalId);
                        selectedHospital.departments.forEach(department => {
                            department.doctorsdepartment.forEach(doctor => {
                                let option = document.createElement('option');
                                option.value = doctor.id;
                                option.textContent = doctor.name;
                                doctorSelect.appendChild(option);
                            });
                        });
                    } else {
                        // If no hospital is selected, show all doctors
                        @foreach ($filterdoctors as $doctor)
                            {
                                let option = document.createElement('option');
                                option.value = '{{ $doctor->id }}';
                                option.textContent = '{{ $doctor->name }}';
                                doctorSelect.appendChild(option);

                            }
                        @endforeach
                    }
                });

                // Ensure the filters are being properly applied
                $('#filter-hospital-name').trigger('change');
            });
        </script>
    @endhasrole
@endsection
