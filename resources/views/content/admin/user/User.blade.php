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
                $('#make_experiment_user_id').val(userId);
                $('#make_expires_at').val('');
                $('#makeExperimentModal').modal('show');
            });

            // حفظ مدة الاشتراك
            $('#makeExperimentForm').submit(function (e) {
                e.preventDefault();
                const userId = $('#make_experiment_user_id').val();
                const expiresAt = $('#make_expires_at').val();
                if (!expiresAt) { alert('يرجى اختيار تاريخ نهاية الاشتراك'); return; }

                $.ajax({
                    url: '/users/' + userId + '/make-experiment',
                    method: 'POST',
                    data: {
                        expires_at: expiresAt,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        $('#makeExperimentModal').modal('hide');
                        if(res.status) {
                            alert(res.message);
                        } else {
                            alert(res.message);
                        }
                        $('#laravel_datatable').DataTable().ajax.reload(null, false);
                    },
                    error: function (err) {
                        alert('فشل التفعيل');
                        console.error(err);
                    }
                });
            });


            $(document).on('click', '.experiment', function () {
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
                if (!expiresAt) { alert('يرجى اختيار تاريخ نهاية التجربة'); return; }

                $.ajax({
                    url: '/users/' + userId + '/experiment',
                    method: 'POST',
                    data: {
                        expires_at: expiresAt,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        $('#experimentModal').modal('hide');
                        if(res.status) {
                            alert(res.message);
                        } else {
                            alert(res.message);
                        }
                        $('#laravel_datatable').DataTable().ajax.reload(null, false);
                    },
                    error: function (err) {
                        alert('فشل التفعيل');
                        console.error(err);
                    }
                });
            });


            let currentExperimentUrl = '';
            
            function openGenericExperimentModal(userId, url, title) {
                $('#activation_user_id').val(userId);
                $('#activation_expires_at').val('');
                $('#activationModal .modal-title').text(title);
                currentExperimentUrl = url;
                $('#activationModal').modal('show');
            }

            $(document.body).on('click', '.desktop-admin-experiment', function () {
                openGenericExperimentModal($(this).data('id'), '/users/' + $(this).data('id') + '/desktop-admin-experiment', 'تفعيل فترة اشتراك (سطح المكتب مشرف)');
            });
            $(document.body).on('click', '.desktop-seller-admin-experiment', function () {
                openGenericExperimentModal($(this).data('id'), '/users/' + $(this).data('id') + '/desktop-seller-admin-experiment', 'تفعيل فترة اشتراك (سطح المكتب مشرف وبائع)');
            });
            $(document.body).on('click', '.mobile-pc-admin-experiment', function () {
                openGenericExperimentModal($(this).data('id'), '/users/' + $(this).data('id') + '/mobile-pc-admin-experiment', 'تفعيل فترة (هاتف وكمبيوتر مشرف)');
            });
            $(document.body).on('click', '.mobile-pc-seller-admin-experiment', function () {
                openGenericExperimentModal($(this).data('id'), '/users/' + $(this).data('id') + '/mobile-pc-seller-admin-experiment', 'تفعيل فترة (هاتف وكمبيوتر مشرف وبائع)');
            });

            // تفعيل (مشرف وبائع)
            $(document.body).on('click', '.update', function () {
                const userId = $(this).data('id');
                $('#activation_user_id').val(userId);
                $('#activation_expires_at').val('');
                $('#activationModal .modal-title').text('تفعيل فترة (مشرف وبائع)');
                currentExperimentUrl = '';
                $('#activationModal').modal('show');
            });

            // حفظ تفعيل (مشرف وبائع) أو الفترات الجديدة
            $('#activationForm').submit(function (e) {
                e.preventDefault();
                const userId = $('#activation_user_id').val();
                const expiresAt = $('#activation_expires_at').val();
                if (!expiresAt) { alert('يرجى اختيار تاريخ نهاية التفعيل'); return; }

                const submitUrl = currentExperimentUrl ? currentExperimentUrl : '/user/Activation/' + userId;

                $.ajax({
                    url: submitUrl,
                    method: 'POST',
                    data: {
                        expires_at: expiresAt,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        $('#activationModal').modal('hide');
                        alert(res.message);
                        $('#laravel_datatable').DataTable().ajax.reload(null, false);
                    },
                    error: function (err) {
                        alert('فشل التفعيل');
                        console.error(err);
                    }
                });
            });

            function sendPermanentRequest(url) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        alert(response.message);
                        if (response.status) $('#laravel_datatable').DataTable().ajax.reload(null, false);
                    },
                    error: function () { alert('خطأ في الاتصال بالسيرفر'); }
                });
            }

            $(document.body).on('click', '.desktop-admin-permanent', function () {
                sendPermanentRequest('/users/' + $(this).data('id') + '/desktop-admin-permanent');
            });
            $(document.body).on('click', '.desktop-seller-admin-permanent', function () {
                sendPermanentRequest('/users/' + $(this).data('id') + '/desktop-seller-admin-permanent');
            });
            $(document.body).on('click', '.mobile-pc-admin-permanent', function () {
                sendPermanentRequest('/users/' + $(this).data('id') + '/mobile-pc-admin-permanent');
            });
            $(document.body).on('click', '.mobile-pc-seller-admin-permanent', function () {
                sendPermanentRequest('/users/' + $(this).data('id') + '/mobile-pc-seller-admin-permanent');
            });

            // تفعيل دائم للمشرف
            $(document.body).on('click', '.permanent-supervisor', function () {
                let id = $(this).data('id');
                $.ajax({
                    url: '/users/' + id + '/permanent-supervisor',
                    type: 'POST',
                    data: { _token: $('meta[name="csrf-token"]').attr('content') },
                    success: function (response) {
                        alert(response.message);
                        if (response.status) $('#laravel_datatable').DataTable().ajax.reload(null, false);
                    },
                    error: function () { alert('خطأ في الاتصال بالسيرفر'); }
                });
            });

            // تفعيل دائم للمشرف والبائع
            $(document.body).on('click', '.permanent-seller-supervisor', function () {
                let id = $(this).data('id');
                $.ajax({
                    url: '/users/' + id + '/permanent-seller-supervisor',
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
            // إعدادات البيع
            $(document.body).on('click', '.edit-sell-settings', function () {
                const userId = $(this).data('id');
                const sellType = $(this).data('sell_type') || 1;
                const maxSellers = $(this).data('max_sellers') || 2;
                
                $('#sell_settings_user_id').val(userId);
                $('#sell_type').val(sellType);
                $('#max_sellers').val(maxSellers);
                $('#editSellSettingsModal').modal('show');
            });

            // حفظ إعدادات البيع
            $('#editSellSettingsForm').submit(function (e) {
                e.preventDefault();
                const userId = $('#sell_settings_user_id').val();
                
                $.ajax({
                    url: '/users/' + userId + '/update-sell-settings',
                    method: 'POST',
                    data: {
                        sell_type: $('#sell_type').val(),
                        max_sellers: $('#max_sellers').val(),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (res) {
                        $('#editSellSettingsModal').modal('hide');
                        if (res.status) {
                            alert(res.message);
                            $('#laravel_datatable').DataTable().ajax.reload(null, false);
                        } else {
                            alert(res.message || 'فشل التحديث');
                        }
                    },
                    error: function (err) {
                        alert('حدث خطأ أثناء التحديث');
                        console.error(err);
                    }
                });
            });

            // تغيير نوع الحساب
            $(document.body).on('click', '.change-account-type', function () {
                const userId = $(this).data('id');
                const accountType = $(this).data('type');
                
                if (confirm('هل أنت متأكد من تغيير نوع الحساب؟')) {
                    $.ajax({
                        url: '/users/' + userId + '/change-account-type',
                        method: 'POST',
                        data: {
                            account_type: accountType,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (res) {
                            if (res.status) {
                                alert(res.message);
                                $('#laravel_datatable').DataTable().ajax.reload(null, false);
                            } else {
                                alert(res.message || 'فشل التحديث');
                            }
                        },
                        error: function (err) {
                            alert('حدث خطأ أثناء التحديث');
                            console.error(err);
                        }
                    });
                }
            });

        });
    </script>
@endsection
