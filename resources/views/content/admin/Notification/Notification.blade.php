@extends('layouts/contentNavbarLayout')

@section('title', __('Users'))

@section('content')

    <h4 class="fw-bold py-3 mb-3 row justify-content-between">
        <div class="col-md-auto">
            <span class="text-muted fw-light">{{ __('Notification') }}
        </div>
        <div class="col-md-auto">
            <button type="button" class="btn btn-primary" id="create">
                <span class="tf-icons bx bx-plus"></span>{{ __('Create Notification') }}
            </button>
        </div>
    </h4>

    <!-- Basic Bootstrap Table -->
    <div class="card">
        <div class="table-responsive text-nowrap">
            <div class="table-header row justify-content-between">
                <h5 class="col-md-auto">{{ __('Notification table') }}</h5>
            </div>
            <table class="table" id="laravel_datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('title') }}</th>
                        <th>{{ __('content') }}</th>
                        <th>{{ __('Created at') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @include('content.admin.Notification.create')
    @include('content.admin.Notification.update')
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
                        url: "{{ route('Notification-list') }}",
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
                        { data: 'title', name: 'title' },
                        { data: 'content', name: 'content' },
                        { data: 'created_at', name: 'created_at' },
                        { data: 'action', name: 'action', orderable: false, searchable: false }
                    ]
                });
            }


            $('#create').on('click', function () {
                $('#create-form')[0].reset();
                $('#create-modal').modal('show');
            });

            $('#create-submit').on('click', function () {
                var formData = new FormData($("#create-form")[0]);

                $.ajax({
                    url: "{{ url('/Notification/Create') }}",
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
                            Swal.fire("{{ __('Success') }}", response.message, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire("{{ __('Error') }}", response.message, 'error');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire("{{ __('Error') }}", xhr.responseJSON?.message || 'خطأ غير متوقع', 'error');
                    }
                });
            });


            $(document).on('click', '.edit', function () {
                const id = $(this).data('id');

                $.ajax({
                    url: '/Notification/edit/' + id,
                    method: 'GET',
                    success: function (response) {
                        if (response.status) {
                            $('#update-id').val(response.data.id);
                            $('#update-title').val(response.data.title);
                            $('#update-content').val(response.data.content);
                            $('#update-modal').modal('show');
                        } else {
                            Swal.fire("خطأ", response.message, "error");
                        }
                    },
                    error: function () {
                        Swal.fire("خطأ", "حدث خطأ أثناء جلب البيانات", "error");
                    }
                });
            });

            $('#update-submit').on('click', function () {
                const formData = new FormData($('#update-form')[0]);

                $.ajax({
                    url: "{{ route('Notification.update') }}",
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        if (response.status) {
                            Swal.fire("نجاح", response.message, "success").then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire("خطأ", response.message, "error");
                        }
                    },
                    error: function (xhr) {
                        let msg = xhr.responseJSON?.message || 'فشل التحديث';
                        Swal.fire("خطأ", msg, "error");
                    }
                });
            });



            $(document).on('click', '.resend', function () {
                var id = $(this).data('id');

                Swal.fire({
                    title: "تأكيد",
                    text: "هل تريد إرسال هذا الإشعار؟",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonText: "نعم",
                    cancelButtonText: "إلغاء"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("notification.resend") }}',
                            method: 'POST',
                            data: { id: id },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                Swal.fire("نجاح", response.message, "success");
                            },
                            error: function (xhr) {
                                let error = xhr.responseJSON?.message || 'حدث خطأ.';
                                Swal.fire("خطأ", error, "error");
                            }
                        });
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
                            url: "{{ url(path: '/Notification/delete') }}/" + id,
                            type: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                if (response.status) {
                                    Swal.fire("{{ __('Success') }}", response.message, 'success').then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire("{{ __('Error') }}", response.message, 'error');
                                }
                            },
                            error: function (xhr) {
                                Swal.fire("{{ __('Error') }}", xhr.responseJSON?.message || 'خطأ أثناء الحذف', 'error');
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
