@extends('layouts/contentNavbarLayout')

@section('title', __('Users'))

@section('content')

    <h4 class="fw-bold py-3 mb-3 row justify-content-between">
        <div class="col-md-auto">
            <span class="text-muted fw-light">{{ __('Users') }} /</span> {{ __('Browse users') }}
        </div>
        {{-- <div class="col-md-auto">
            <button type="button" class="btn btn-primary" id="create">
                <span class="tf-icons bx bx-plus"></span>{{ __('Create user') }}
            </button>
        </div> --}}
    </h4>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <div class="table-responsive text-nowrap">
            <div class="table-header row justify-content-between align-items-center px-3 pt-3">
                <h5 class="col-md-auto m-0">{{ __('Users table') }}</h5>

            </div>

            <table class="table" id="laravel_datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Created at') }}</th>
                        <th>{{ __('experiment_at') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @include('content.admin.user.date')
@endsection


@section('page-script')
    <script>
        $(document).ready(function () {
            let table = $('#laravel_datatable').DataTable({
                language: {!! file_get_contents(base_path('lang/' . session('locale', 'en') . '/datatable.json')) !!},
                responsive: true,
                processing: true,
                serverSide: true,
                pageLength: 10,
                searchDelay: 0,
                ajax: {
                    url: "{{ route('user-list') }}",
                    type: 'GET'
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'date_experiment', name: 'date_experiment' },
                    { data: 'Status', name: 'Status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            $('#userSearch').on('input', function () {
                table.search(this.value).draw();
            });

            $(document).on('click', '.make-experiment', function () {
                const userId = $(this).data('id');
                $('#experiment_user_id').val(userId);
                $('#expires_at').val('');
                $('#experimentModal').modal('show');
            });

            // حفظ مدة التجريبي
            $('#experimentForm').submit(function (e) {
                e.preventDefault();
                const userId = $('#experiment_user_id').val();
                const expiresAt = $('#expires_at').val();
                if (!expiresAt) { alert('يرجى اختيار تاريخ نهاية المدة'); return; }

                $.ajax({
                    url: '/users/' + userId + '/make-experiment',
                    method: 'POST',
                    data: {
                        expires_at: expiresAt,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        $('#experimentModal').modal('hide');
                        alert('تم تحويل المستخدم لحساب تجريبي حتى ' + expiresAt);
                        $('#laravel_datatable').DataTable().ajax.reload(null, false);
                    },
                    error: function (err) {
                        alert('فشل التفعيل');
                        console.error(err);
                    }
                });
            });

            // تفعيل/إيقاف
            $(document.body).on('click', '.update', function () {
                let id = $(this).data('id');
                $.ajax({
                    url: '/user/Activation/' + id,
                    type: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        alert(response.message);
                        if (response.status) $('#laravel_datatable').DataTable().ajax.reload(null, false);
                    },
                    error: function () { alert('خطأ في الاتصال بالسيرفر'); }
                });
            });

            // حذف
            $(document.body).on('click', '.delete', function () {
                var id = $(this).data('id');
                Swal.fire({
                    title: "تحذير",
                    text: "هل أنت متأكد من حذف المستخدم؟",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "نعم",
                    cancelButtonText: "لا"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/user/delete',
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            data: { id: id },
                            success: function (response) {
                                if (response.status) {
                                    Swal.fire("تم", "تم حذف المستخدم بنجاح", "success");
                                    $('#laravel_datatable').DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire("خطأ", response.message || "فشل الحذف", "error");
                                }
                            },
                            error: function () {
                                Swal.fire("خطأ", "حدث خطأ في الاتصال بالسيرفر", "error");
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
