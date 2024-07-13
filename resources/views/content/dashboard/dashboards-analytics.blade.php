@extends('layouts/layoutMaster')

@section('title', 'Dashboard')

@section('vendor-style')
    @vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss', 'resources/assets/vendor/libs/swiper/swiper.scss', 'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss'])
@endsection

@section('page-style')
    <!-- Page -->
    @vite(['resources/assets/vendor/scss/pages/cards-advance.scss'])
@endsection

@section('vendor-script')
    @vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js', 'resources/assets/vendor/libs/swiper/swiper.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'])
@endsection

@section('page-script')
    @vite(['resources/assets/js/dashboards-analytics.js', 'resources/assets/js/charts-apex-1.js'])
@endsection

@section('content')


    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Dashboard</span>
    </h4>
    <!-- Card Border Shadow -->
    <div class="row">
        @hasrole('SuperAdmin')
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-primary"><i
                                        class="fa-solid fa-users fa-2x"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $usersCount }}</h4>
                        </div>
                        <p class="mb-1">Total users</p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-warning"><i
                                        class="fa-solid fa-hospital fa-2x"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $hospitalsCount }}</h4>
                        </div>
                        <p class="mb-1">Total hospitals</p>

                    </div>
                </div>
            </div>
        @endhasrole
        @hasrole('hospital')
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="fa-solid fa-building fa-2x"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $departmentsCount }}</h4>
                        </div>
                        <p class="mb-1">Total departments</p>
                    </div>
                </div>
            </div>
        @endhasrole
        @hasanyrole('SuperAdmin|hospital|department')
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-danger"><i
                                        class="fa-solid fa-user-nurse fa-2x"></i></span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $doctorsCount }}</h4>
                        </div>
                        <p class="mb-1">Total doctors</p>

                    </div>
                </div>
            </div>
        @endhasanyrole
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-info">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-info"><i
                                    class="fa-solid fa-bed-pulse fa-2x"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{ $patientsCount }}</h4>
                    </div>
                    <p class="mb-1">Total patients</p>

                </div>
            </div>
        </div>
        @hasanyrole('hospital|department')
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="fa-solid fa-calendar fa-2x"></i>

                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $totalReservationsCount }}</h4>
                        </div>
                        <p class="mb-1">Total appointments</p>

                    </div>
                </div>
            </div>
        @endhasanyrole
        @hasrole('department')
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="fa-solid fa-calendar-check fa-2x"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $doctorWithMostReservationsCount }}</h4>
                        </div>
                        <p class="mb-1">{{ $doctorWithMostReservationsName }}:most appointments </p>

                    </div>
                </div>
            </div>
        @endhasrole
        @hasrole('doctor')
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="fa-solid fa-calendar fa-2x"></i>

                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $totalReservationsCount }}</h4>
                        </div>
                        <p class="mb-1">Total appointments</p>

                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="fa-solid fa-calendar-check fa-2x"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $patientWithMostReservationsCount }}</h4>
                        </div>
                        <p class="mb-1">{{ $patientWithMostReservationsName }}:most appointments </p>

                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mb-4">
                <div class="card card-border-shadow-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 pb-1">
                            <div class="avatar me-2">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="fa-solid fa-calendar-week fa-2x"></i>
                                </span>
                            </div>
                            <h4 class="ms-1 mb-0">{{ $totalReservationsCurrentMonth }}</h4>
                        </div>
                        <p class="mb-1">Appointments this month</p>

                    </div>
                </div>
            </div>
        @endhasrole

    </div>
    <!--/ Card Border Shadow -->
    <div class="row">
        <!-- Donut Chart gender -->
        <div class="col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">Patients by Gender</h5>
                        <small class="text-muted">Distribution of patients by gender</small>
                    </div>
                    {{-- <div class="dropdown d-none d-sm-flex">
                    <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown"
                        aria-expanded="false"><i class="ti ti-calendar"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Today</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Yesterday</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 7 Days</a>
                        </li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 30 Days</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Current Month</a>
                        </li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last Month</a>
                        </li>
                    </ul>
                </div> --}}
                </div>
                <div class="card-body">
                    <div id="donutChart"></div>
                </div>
            </div>
        </div>
        <!-- /Donut Chart -->
        <!-- Donut Chart patient disease-->
        <div class="col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">Patients by disease</h5>
                        <small class="text-muted">Distribution by disease</small>

                    </div>
                    <div class="dropdown d-none d-sm-flex">
                        <select class="form-label" id="diseaseSelect">
                            @foreach ($diseases as $disease)
                                <option value="{{ $disease->id }}">{{ $disease->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class="dropdown d-none d-sm-flex">
                      <button type="button" class="btn dropdown-toggle px-0" data-bs-toggle="dropdown"
                          aria-expanded="false"><i class="ti ti-calendar"></i></button>
                      <ul class="dropdown-menu dropdown-menu-end">
                          <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Today</a></li>
                          <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Yesterday</a></li>
                          <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 7 Days</a>
                          </li>
                          <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 30 Days</a>
                          </li>
                          <li>
                              <hr class="dropdown-divider">
                          </li>
                          <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Current Month</a>
                          </li>
                          <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last Month</a>
                          </li>
                      </ul>
                  </div> --}}
                </div>
                <div class="card-body">
                    <div id="donutChart2"></div>
                </div>
            </div>
        </div>
        <!-- /Donut Chart -->
        <!-- Donut Chart gender -->
        <div class="col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">Patients by Blood Type</h5>
                        <small class="text-muted">Distribution of patients by Blood Type</small>
                    </div>

                </div>
                <div class="card-body">
                    <div id="donutChartBlood"></div>
                </div>
            </div>
        </div>
        <!-- Bar Chart -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-md-center align-items-start">
                    <h5 class="card-title mb-0">Total patient by disease</h5>

                </div>
                <div class="card-body">
                    <div id="barChart"></div>
                </div>
            </div>
        </div>
        <!-- /Bar Chart -->

        <!-- Line Area Chart -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <h5 class="card-title mb-0">Patients visit</h5>
                        <small class="text-muted">by label</small>
                    </div>
                    <div class="dropdown">
                        <div id="filters">
                            <label for="filterSelect">Select Filter:</label>
                            <select id="filterSelect">
                                <option value="by_month">By Month</option>
                                <option value="last_30_days">Last 30 Days</option>
                                <option value="last_7_days">Last 7 Days</option>
                                <option value="by_year">By Year</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <div id="lineAreaChart"></div>
                </div>
            </div>
        </div>
        <!-- /Line Area Chart -->
    </div>

@endsection
