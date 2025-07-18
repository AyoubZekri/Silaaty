@extends('layouts/contentNavbarLayout')

@section('title', __('Users'))

@section('content')

    <h4 class="fw-bold py-3 mb-3 row justify-content-between">
        <div class="col-md-auto">
            <span class="text-muted fw-light">{{ __('Report') }} /</span> {{ __('Browse Report') }}
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
            <div class="table-header row justify-content-between">
                <h5 class="col-md-auto">{{ __('Report table') }}</h5>
            </div>
            <table class="table" id="laravel_datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('report') }}</th>
                        <th>{{ __('Created at') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @include('content.users.create')
    @include('content.users.update')
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
                        url: "{{ route('Report-list') }}",
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
                        { data: 'user.name', name: 'name' },
                        { data: 'user.email', name: 'email' },
                        { data: 'report', name: 'report' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]

                });
            }

            $(document.body).on('click', '.update', function () {

                var id = $(this).data('id');
                $("#id").val(id);

                $.ajax({
                    url: '{{ url('user/get') }}',
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
                    url: "{{ url('user/update') }}",
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

            $(document).on('click', '.delete', function () {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: "لا يمكن التراجع عن هذه العملية!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/Report/delete/' + id,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.status) {
                                    Swal.fire('تم الحذف!', response.message, 'success').then(() => {
                                        $('#laravel_datatable').DataTable().ajax.reload();
                                    });
                                } else {
                                    Swal.fire('خطأ', response.message, 'error');
                                }
                            },
                            error: function (xhr) {
                                let msg = xhr.responseJSON?.message || 'حدث خطأ أثناء الحذف.';
                                Swal.fire('خطأ', msg, 'error');
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
