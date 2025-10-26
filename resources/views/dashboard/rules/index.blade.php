@extends('layouts.master')
@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        /* CSS for end_date container */
        .end_date-container {
            width: 50px;
            height: 50px;
            position: relative;
            overflow: hidden;
            border-radius: 50%;
        }

        /* CSS for the circular end_date */
        .end_date-container img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">الأدوار /</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">جميع
                    الأدوار</span>
            </div>
        </div>

    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session()->has('Add'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('Add') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('delete'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('delete') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session()->has('edit'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('edit') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Main Statistics Cards -->
    <div class="row row-sm">
        <!-- total role -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card stats-card overflow-hidden sales-card bg-primary-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2">
                    <div class="">
                        <h6 class="mb-3 tx-15 text-white">إجمالي الأدوار</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white"> {{ $totalRoles ?? 0 }}
                                </h4>
                                <p class="mb-0 tx-12 text-white op-7">

                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- most popular role -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card stats-card overflow-hidden sales-card bg-success-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2">
                    <div class="">
                        <h6 class="mb-3 tx-15 text-white">أكثر الأدوار</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $topRole->name ?? '-' }}</h4>
                                <p class="mb-0 tx-12 text-white op-7">

                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- second best role -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card stats-card overflow-hidden sales-card bg-warning-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2">
                    <div class="">
                        <h6 class="mb-3 tx-15 text-white">ثاني أكثر الأدوار</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $secondTopRole->name ?? '-' }}</h4>
                                <p class="mb-0 tx-12 text-white op-7">
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- best role -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card stats-card overflow-hidden sales-card bg-danger-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2">
                    <div class="">
                        <h6 class="mb-3 tx-15 text-white">افضل الأدوار</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ $adminRole->name ?? '-' }}</h4>
                                <p class="mb-0 tx-12 text-white op-7">

                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- زر فتح المودال -->
    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#create_modal">
        <i class="las la-plus"></i> إضافة دور جديدة
    </button>


    <!-- row -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-md-nowrap" id="example1">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0">#</th>
                                    <th class="border-bottom-0">الدور</th>
                                    <th class="border-bottom-0">الكود</th>
                                    <th class="border-bottom-0">تاريخ الانشاء</th>
                                    <th class="border-bottom-0">العمليات</th>

                                </tr>

                            </thead>
                            <tbody>
                                @foreach ($rules as $index => $rule)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $rule->name }}</td>
                                        <td>{{ $rule->guard_name }}</td>

                                        <td>{{ $rule->created_at->locale('ar')->diffForHumans() }}</td>

                                        <td>
                                            <div class="d-flex">
                                                <a href="javascript:void(0);" class="btn btn-sm btn-info analysis-btn"
                                                    data-id="{{ $rule->id }}">
                                                    <i class="las la-chart-bar"></i>
                                                </a>


                                                <a class=" btn btn-sm btn-primary mx-1"
                                                    href="{{ route('rules.show', $rule->id) }}">
                                                    <i class="las la-eye"></i></a>
                                                <a class="modal-effect btn btn-sm btn-info mx-1"
                                                    data-effect="effect-scale" data-toggle="modal" href="#edit_modal"
                                                    data-id="{{ $rule->id }}" data-name="{{ $rule->name }}"
                                                    data-code="{{ $rule->guard_name  }}">
                                                    <i class="las la-pen"></i>
                                                </a>
                                                <a class="modal-effect btn btn-sm btn-danger" data-effect="effect-scale"
                                                    data-id="{{ $rule->id }}" data-name="{{ $rule->name }}"
                                                    data-toggle="modal" href="#delete_modal">
                                                    <i class="las la-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <!-- مودال إنشاء دور جديدة -->
    <div class="modal fade" id="create_modal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('rules.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">إضافة دور جديد</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>الاسم</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>الحارس (Guard)</label>
                            <select name="guard_name" class="form-control @error('guard_name') is-invalid @enderror">
                                <option value="" disabled selected>اختر الحارس</option>
                                <option value="web" {{ old('guard_name') == 'web' ? 'selected' : '' }}>web</option>
                                <option value="api" {{ old('guard_name') == 'api' ? 'selected' : '' }}>api</option>
                            </select>

                            @error('guard_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                        <button type="submit" class="btn btn-primary">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

        <!-- مودال تعديل شركة -->
    <div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editCompanyForm" action="{{ route('rules.updateRole') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">تعديل الدور</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">

                        <div class="form-group">
                            <label>الاسم</label>
                            <input type="text" name="name" id="edit_name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <span class="text-danger d-none" id="error-edit-name">هذا الحقل مطلوب</span>
                        </div>

                          <div class="form-group">
                            <label>الحارس (Guard)</label>
                            <select name="guard_name" class="form-control @error('guard_name') is-invalid @enderror">
                                <option value="" disabled selected>اختر الحارس</option>
                                <option value="web" {{ old('guard_name') == 'web' ? 'selected' : '' }}>web</option>
                                <option value="api" {{ old('guard_name') == 'api' ? 'selected' : '' }}>api</option>
                            </select>

                            @error('guard_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- مودال حذف شركة -->
    <div class="modal fade" id="delete_modal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('rules.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">حذف الدور</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="delete_id">
                        <p>هل أنت متأكد من حذف الدور: <strong id="delete_name"></strong>؟</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger">حذف</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Analysis Modal -->
    <div class="modal fade" id="analysisModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تحليل الدور / الصلاحية</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="analysisContent">
                    <p class="text-center">جاري التحميل...</p>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function() {

            // فتح المودال بعد Validation Error
            @if ($errors->any())
                @if (session('modal') === 'create')
                    $('#create_modal').modal('show');
                @elseif (session('modal') === 'edit')
                    $('#edit_modal').modal('show');
                    // ملء بيانات الفورم بعد Validation Error
                    $('#edit_id').val("{{ old('id') }}");
                    $('#edit_name').val("{{ old('name') }}");
                    $('#edit_code').val("{{ old('code') }}");
                @endif
            @endif

            // فتح مودال التعديل وملء البيانات عند الضغط على زر التعديل
            $('#edit_modal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                $('#edit_id').val(button.data('id'));
                $('#edit_name').val(button.data('name'));
                $('select[name="guard_name"]').val(button.data('code'));

            });

            // Validation قبل إرسال فورم التعديل
            $('#editCompanyForm').on('submit', function(e) {
                let valid = true;
                ['name', 'code'].forEach(function(field) {
                    let input = $('#edit_' + field);
                    let error = $('#error-edit-' + field);

                    if (input.val().trim() === '') {
                        input.addClass('is-invalid');
                        error.removeClass('d-none');
                        valid = false;
                    } else {
                        input.removeClass('is-invalid');
                        error.addClass('d-none');
                    }
                });
                if (!valid) e.preventDefault();
            });

            // إزالة خطأ الفورم فور الكتابة
            $('#edit_modal input').on('input', function() {
                let input = $(this);
                let error = input.siblings('span.text-danger');
                if (input.val().trim() !== '') {
                    input.removeClass('is-invalid');
                    error.addClass('d-none');
                }
            });

            // فتح مودال الحذف وملء الاسم
            $('#delete_modal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                $('#delete_id').val(button.data('id'));
                $('#delete_name').text(button.data('name')); // الاسم يظهر هنا
            });

        });
    </script>

    <!--- Script لتحليل البيانات -->
    <script>
        $(document).on('click', '.analysis-btn', function() {
            loadRoleAnalysis($(this).data('id'), 1);
        });

        function loadRoleAnalysis(roleId, page = 1) {
            $('#analysisContent').html('<p class="text-center">جاري التحميل...</p>');
            $('#analysisModal').modal('show');

            $.ajax({
                url: `/dashboard/rules/${roleId}/analysis?page=${page}`,
                method: 'GET',
                success: function(res) {
                    if (!res.users.length) {
                        $('#analysisContent').html(
                            '<p class="text-center text-muted">لا يوجد مستخدمين لديهم هذه الصلاحية.</p>'
                        );
                        return;
                    }

                    let html = `
            <h5>الدور: ${res.role}</h5>
            <p>عدد المستخدمين: ${res.total}</p>
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="thead-dark">
                        <tr>
                            <th>المستخدم</th>
                            <th>الإيميل</th>
                            <th>تاريخ الإسناد</th>
                        </tr>
                    </thead>
                    <tbody>`;

                    res.users.forEach(user => {
                        html += `
                <tr>
                    <td>${user.name}</td>
                    <td>${user.email}</td>
                    <td>${user.assigned_at || '-'}</td>
                </tr>`;
                    });

                    html += `</tbody></table></div>`;

                    // Pagination Buttons
                    html += `<div class="text-center mt-3">`;
                    if (res.links.prev) {
                        html +=
                            `<button class="btn btn-secondary m-1" onclick="loadRoleAnalysis(${roleId}, ${res.current_page - 1})">السابق</button>`;
                    }
                    if (res.links.next) {
                        html +=
                            `<button class="btn btn-primary m-1" onclick="loadRoleAnalysis(${roleId}, ${res.current_page + 1})">التالي</button>`;
                    }
                    html += `</div>`;

                    $('#analysisContent').html(html);
                },
                error: function() {
                    $('#analysisContent').html(
                        '<p class="text-danger text-center">حدث خطأ أثناء جلب البيانات.</p>'
                    );
                }
            });
        }
    </script>


    <script src="{{ URL::asset('assets/plugins/amazeui-datetimepicker/js/amazeui.datetimepicker.min.js') }}"></script>
    <!-- Internal Data tables -->
    <script src="{{ URL::asset('assets/js/modal.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/vfs_fonts.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
    <!--Internal  Datatable js -->
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
@endsection
