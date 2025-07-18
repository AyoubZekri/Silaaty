@extends('layouts/contentNavbarLayout')

@section('title', __('Users'))

@section('content')

    <h4 class="fw-bold py-3 mb-3 row justify-content-between">
        <div class="col-md-auto">
            <span class="text-muted fw-light">{{ __('Users') }} /</span> {{ __('Browse users') }}
        </div>
        <div class="col-md-auto">
            <button type="button" class="btn btn-primary" id="create">
                <span class="tf-icons bx bx-plus"></span>{{ __('Create user') }}
            </button>
        </div>
    </h4>
    <div class="Serch">
        <form method="GET">
            <label for="Search">
                {{-- <button>
                    <i class="fas fa-search"></i>
                </button> --}}
                <input type="TEXT" id="searchQuery" name="query" placeholder="{{ __('lang.39') }}..." required>
            </label>
        </form>
    </div>

    <!-- Card Layout for Users -->
    <div class="row pt-3" id="user-cards">

    </div>

    @include('content.users.create')
    @include('content.users.update')
@endsection


@section('page-script')
    <script>
        $(document).ready(function() {
            load_data();

            function load_data() {
                // Fetch users data
                $.ajax({
                    url: "{{ url('user/list') }}",
                    type: 'GET',
                    dataType: 'JSON',
                    success: function(response) {
                        let users = response
                            .data; // Assuming response contains a 'data' array with user data
                        let cardsHtml = '';

                        users.forEach(function(user) {
                            cardsHtml += `
    <!-- Card 1 -->
    <div class="col-md-4 mb-4"> <!-- Added margin-bottom for spacing -->
        <div class="card p-3">
            <div class="card-body">
                <!-- User info -->
                <div class="d-flex justify-content-start align-items-center mb-3">
                    <a>
                        <div class="avatar me-3">
                            <img src="https://dashboard.agropole.site/assets/img/avatars/avatar.png" alt="Avatar" class="rounded-circle">
                        </div>
                    </a>
                    <div class="d-flex flex-column">
                        <a href="https://dashboard.agropole.site/user/803/stocks" class="text-body text-nowrap">
                            <h6 class="mb-0">dist</h6>
                        </a>
                        <span>Distributeur</span>
                    </div>
                </div>

                <!-- Coordinates -->
                <h6 class="mb-1">Coordonnées</h6>
                <p class="mb-1 text-fit" title="dist@gmail.com"><i class="bx bxs-envelope"></i> dist@gmail.com</p>
                <p class="mb-1 text-fit" title="0666666666"><i class="bx bxs-phone"></i> 0666666666</p>
                <p class="mb-0 text-fit" title="El Oued/Hassani Abdelkrim">
                    <i class="bx bxs-map"></i>
                    <a href="https://maps.google.com/?q=33.3624592,6.8579441">El Oued/Hassani Abdelkrim</a>
                </p>
            </div>
        </div>
    </div>



                            `;
                        });

                        $('#user-cards').html(cardsHtml); // Insert cards into the page
                    },
                    error: function() {
                        Swal.fire(
                            "{{ __('Error') }}",
                            "{{ __('Unable to fetch users') }}",
                            'error'
                        );
                    }
                });
            }

            $('#create').on('click', function() {
                $('#create-modal').modal('show');
            });

            $('#create-submit').on('click', function() {

                $("#create-modal").modal("hide");

                var formdata = new FormData($("#create-form")[0]);

                $.ajax({
                    url: "{{ url('user/create') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    data: formdata,
                    dataType: 'JSON',
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.status == 1) {
                            Swal.fire({
                                title: "{{ __('Success') }}",
                                text: "{{ __('User created successfully') }}",
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
                    error: function(data) {
                        var errors = data.responseJSON;
                        Swal.fire(
                            "{{ __('Error') }}",
                            errors.message,
                            'error'
                        );
                    }
                });
            });

            $(document.body).on('click', '.update', function() {
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
                    success: function(response) {
                        if (response.status) {
                            $('#name').val(response.data.name);
                            $('#email').val(response.data.email);
                            $("#update-modal").modal("show");
                        }
                    }
                });
            });

            $('#update-submit').on('click', function() {
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
                    success: function(response) {
                        if (response.status == 1) {
                            Swal.fire({
                                title: "{{ __('Success') }}",
                                text: "{{ __('User updated successfully') }}",
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
                    error: function(data) {
                        var errors = data.responseJSON;
                        Swal.fire(
                            "{{ __('Error') }}",
                            errors.message,
                            'error'
                        );
                    }
                });
            });

            $(document.body).on('click', '.delete', function() {
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
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire(
                                        "{{ __('Success') }}",
                                        "{{ __('User deleted successfully') }}",
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
