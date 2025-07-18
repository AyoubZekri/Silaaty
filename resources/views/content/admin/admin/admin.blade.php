@extends('layouts/contentNavbarLayout')

@section('title', __('Users'))

@section('content')

    <h4 class="fw-bold py-3 mb-3 row justify-content-between">
        <div class="col-md-auto">
            <span class="text-muted fw-light">{{ __('Admin') }} /</span> {{ __('Browse Admin') }}
        </div>
        <div class="col-md-auto">
            <button type="button" class="btn btn-primary" id="create">
                <span class="tf-icons bx bx-plus"></span>{{ __('Create Admin') }}
            </button>
        </div>
    </h4>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <div class="table-responsive text-nowrap">
            <div class="table-header row justify-content-between">
                <h5 class="col-md-auto">{{ __('Admin table') }}</h5>
            </div>
            <table class="table" id="laravel_datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Created at') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @include('content.admin.admin.create')
    @include('content.admin.admin.update')
@endsection


@section('page-script')
    <script>
        $(document).ready(function () {
            load_data();

            function load_data() {
                var table = $('#laravel_datatable').DataTable({
                    language: {!! file_get_contents(base_path('lang/' . session('locale', 'en') . '/datatable.json')) !!},
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    pageLength: 10,

                    ajax: {
                        url: "{{ route('admin-list') }}",
                        type: 'GET',
                        // error: function (xhr, error, thrown) {
                        //     let message = 'Something went wrong while loading the table.';

                        //     if (xhr.responseJSON && xhr.responseJSON.message) {
                        //         message = xhr.responseJSON.message;
                        //     }

                        //     toastr.error(message, 'Error');
                        // }
                    },

                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'name', name: 'name' },
                        { data: 'email', name: 'email' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'Status', name: 'Status' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });
            }

            $('#create').on('click', function () {
                $('#create-form')[0].reset();
                $('#create-modal').modal('show');
            });

            $('#create-submit').on('click', function () {
                $("#create-modal").modal("hide");

                var formData = new FormData($("#create-form")[0]);

                $.ajax({
                    url: "{{ url('/admin/store') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    data: formData,
                    dataType: 'JSON',
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.status) {
                            Swal.fire({
                                title: "{{ __('Success') }}",
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                "{{ __('Error') }}",
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function (data) {
                        var errors = data.responseJSON;
                        Swal.fire(
                            "{{ __('Error') }}",
                            errors.message,
                            'error'
                        );
                    }
                });
            });

            $('#update').on('click', function () {
                $('#update-form')[0].reset();
                $('#update-modal').modal('show');
            });




            $(document.body).on('click', '.update', function () {

                var id = $(this).data('id');
                $("#id").val(id);

                $.ajax({
                    url: '{{ url('admin/get') }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    data: {
                        id: id
                    },
                    dataType: 'JSON',
                    success: function (response) {
                        if (response.status) {
                            $('#name').val(response.data.name);
                            $('#email').val(response.data.email);
                            $("#update-modal").modal("show");
                        }
                    }
                });
            });


            $('#update-submit').on('click', function () {

                $("#update-modal").modal("hide");

                var formdata = new FormData($("#update-form")[0]);

                $.ajax({
                    url: "{{ url('/admin/update') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    data: formdata,
                    dataType: 'JSON',
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.status == 1) {
                            Swal.fire({
                                title: "{{ __('Success') }}",
                                text: "{{ __('success') }}",
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                "{{ __('Error') }}",
                                response.message,
                                'error'
                            );
                        }
                    },
                    error: function (data) {
                        var errors = data.responseJSON;
                        Swal.fire(
                            "{{ __('Error') }}",
                            errors.message,
                            'error'
                        );
                        // Render the errors with js ...
                    }
                });
            });

            $(document.body).on('click', '.delete', function () {

                var id = $(this).data('id');

                Swal.fire({
                    title: "{{ __('Warning') }}",
                    text: "{{ __('Are you sure?') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "{{ __('Yes') }}",
                    cancelButtonText: "{{ __('No') }}"
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: "{{ url('user/delete') }}",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: 'POST',
                            data: {
                                id: id
                            },
                            dataType: 'JSON',
                            success: function (response) {
                                if (response.status) {

                                    Swal.fire(
                                        "{{ __('Success') }}",
                                        "{{ __('success') }}",
                                        'success'
                                    ).then((result) => {
                                        location.reload();
                                    });
                                }
                            }
                        });


                    }
                })
            });

        });
    </script>
@endsection
