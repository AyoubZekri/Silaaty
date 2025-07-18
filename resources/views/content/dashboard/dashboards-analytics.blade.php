@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
    <script src="{{asset('assets/js/dashboards-analytics.js')}}"></script>
    <script>
        $(document).ready(function () {
            $.ajax({
                url: "{{ route('dashboard.user-stats') }}",
                method: 'GET',
                success: function (data) {
                    $('#totalUsers').text(data.totalUsers);
                    $('#totalIncome').text('$' + data.totalIncome);
                    $('#activatedUsers').text(data.activatedUsers);
                    $('#experimentUsers').text(data.experimentUsers);
                    $('#notActivatedUsers').text(data.notActivatedUsers);
                    $('#nisab').text(data.nisab);

                },
                error: function (xhr) {
                    console.error('Error fetching stats:', xhr);
                }
            });
        });
    </script>

@endsection


@section('content')
        <div class="row">
          <div class="col-12 mb-4">
            <div class="card">
              <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                  <div class="card-body">
                    <h5 class="card-title text-primary">Congratulations John! 🎉</h5>
                    <p class="mb-4">You have done <span class="fw-bold">72%</span> more sales today. Check your new badge in your profile.</p>

                    <a href="{{ url('tldr') }}" class="btn btn-sm btn-outline-primary">View Badges</a>
                  </div>
                </div>
                <div class="col-sm-5 text-center text-sm-left">
                  <div class="card-body pb-0 px-0 px-md-4">
                    <img src="{{asset('assets/img/illustrations/man-with-laptop-light.png')}}" height="140" alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png" data-app-light-img="illustrations/man-with-laptop-light.png">
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>


        <div class="row">
          <!-- Order Statistics -->
          <div class="col-12  mb-4">
            <div class="card h-100">
              <div class="card-header d-flex align-items-center justify-content-between pb-0">
                <div class="card-title mb-0">
                  <h5 class="m-0 me-2">User Statistics</h5>
                  <small class="text-muted">42.82k Total Sales</small>
                </div>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
        data-bs-target="#editThresholdModal">
        <i class="bx bx-edit-alt"></i>
    </button>
                </div>
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <div class="d-flex flex-column align-items-center gap-1">
                    <h2 class="mb-2" id="totalUsers">8,258</h2>
                    <span>Total User</span>
                  </div>
                  <div id="orderStatisticsChart"></div>
                </div>
                <ul class="p-0 m-0">
                    <li class="d-flex align-items-center mb-3">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-primary"><i class='bx bx-money'></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Income</h6>
                                <small class="text-muted">User, Activation</small>
                            </div>
                            <div class="user-progress">
                                <small id="totalIncome" class="fw-semibold">0$</small>
                            </div>
                        </div>
                    </li>

                    <li class="d-flex align-items-center mb-3">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-success"><i class='bx bx-user-check'></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">User Activation</h6>
                                <small class="text-muted">User, Activation</small>
                            </div>
                            <div class="user-progress">
                                <small id="activatedUsers" class="fw-semibold">0</small>
                            </div>
                        </div>
                    </li>

                    <li class="d-flex align-items-center mb-3">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-info"><i class='bx bx-flask'></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">User The Experiment</h6>
                                <small class="text-muted">User, Experiment</small>
                            </div>
                            <div class="user-progress">
                                <small id="experimentUsers" class="fw-semibold">0</small>
                            </div>
                        </div>
                    </li>

                    <li class="d-flex align-items-center mb-3">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-secondary"><i class='bx bx-user-x'></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">User Not Activation</h6>
                                <small class="text-muted">User, Not Activation</small>
                            </div>
                            <div class="user-progress">
                                <small id="notActivatedUsers" class="fw-semibold">0</small>
                            </div>
                        </div>
                    </li>

                    <li class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-warning"><i class='bx bx-money'></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Nisab</h6>
                                <small class="text-muted">ZAKAT, Nisab</small>
                            </div>
                            <div class="user-progress">
                                <small id="nisab" class="fw-semibold">0</small>
                            </div>
                        </div>
                    </li>
                </ul>
                                </div>
            </div>
          </div>
        </div>




        <!-- Modal -->
        <div class="modal fade" id="editThresholdModal" tabindex="-1" aria-labelledby="editThresholdModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('nisab.update') }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editThresholdModalLabel">تعديل نصاب الزكاة</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="threshold" class="form-label">النصاب الجديد</label>
                                <input type="number" step="0.01" class="form-control" id="threshold" name="threshold" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

@endsection

