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
                <h4 class="content-title mb-0 my-auto">القسائم /</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">جميع القسائم</span>
            </div>
        </div>
        <!-- زرار الداونلود -->
        <div class="my-auto">
            <!-- زرار تصدير Excel -->
            <button class="btn btn-success d-flex align-items-center gap-2" data-bs-toggle="modal"
                data-bs-target="#exportModal">
                <i class="fas fa-file-excel ml-1"></i>
                استيراد من Excel
            </button>

        </div>
    </div>

    <!-- مودال اختيار المدة -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="exportForm" action="{{ route('export.coupons') }}" method="GET" target="_blank">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exportModalLabel">تحديد فترة القسائم</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="start" class="form-label">من تاريخ</label>
                            <input type="date" class="form-control" name="start" id="start">
                        </div>
                        <div class="mb-3">
                            <label for="end" class="form-label">إلى تاريخ</label>
                            <input type="date" class="form-control" name="end" id="end">
                        </div>
                        <div class="mb-3">
                            <label for="created_by" class="form-label">تم اضافه ف وقت (اختياري)</label>
                            <input type="time" class="form-control" name="created_at" id="created_at"
                                placeholder="ادخل وقت الاضافه ">
                        </div>
                        <div class="mb-3">
                            <label for="company_id" class="form-label">الشركة (اختياري)</label>
                            <select class="form-control" name="company_id" id="company_id">
                                <option value="">جميع الشركات</option>
                                @foreach ($companys as $company)
                                    <option value="{{ $company->id }}" data-name="{{ $company->name }}">
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="company_name" id="company_name">

                        </div>
                        <div class="mb-3">
                            <label for="batch" class="form-label">مجموعة القسائم (اختياري)</label>
                            <select class="form-control" name="batch" id="batch">
                                <option value="">اختر مجموعة القسائم</option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">تحميل</button>
                    </div>
                </div>
            </form>
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
                                    <th class="border-bottom-0">الشركه</th>
                                    <th class="border-bottom-0">مجموعة القسائم</th>
                                    <th class="border-bottom-0">كود القسيمه</th>
                                    <th class="border-bottom-0">نوع القسيمه</th>
                                    <th class="border-bottom-0">قيمة القسيمه</th>
                                    <th class="border-bottom-0">نوع الخصم</th>
                                    <th class="border-bottom-0">عدد الألعاب</th>
                                    <th class="border-bottom-0">حد الاستخدام</th>
                                    <th class="border-bottom-0">حد المستخدم</th>
                                    <th class="border-bottom-0">تاريخ البدايه</th>
                                    <th class="border-bottom-0">تاريخ النهايه</th>
                                    <!--- Analyses--->
                                    <th class="border-bottom-0">إجمالي مرات الاستخدام</th>
                                    <th class="border-bottom-0">عدد المستخدمين المختلفين</th>
                                    <th class="border-bottom-0">عدد الاستخدامات المتبقية</th>
                                    <th class="border-bottom-0">نسبة الاستهلاك (%)</th>
                                    <th class="border-bottom-0">قائم / منتهي الصلاحية</th>

                                    <th class="border-bottom-0">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($coupons as $index => $coupon)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            {{ $coupon->company ? $coupon->company->name : '-' }}
                                        </td>
                                        <td> {{ $coupon->batch ? $coupon->batch : '-' }}
                                        </td>
                                        <td>{{ $coupon->code }}</td>
                                        <td>{{ $coupon->type === 'discount' ? 'خصم' : 'ألعاب مجانية' }}</td>
                                        <td>{{ $coupon->type === 'discount' ? $coupon->value : '-' }}</td>
                                        <td>
                                            @if ($coupon->type === 'discount')
                                                @switch($coupon->discount_type)
                                                    @case('percentage')
                                                        نسبة مئوية
                                                    @break

                                                    @case('fixed')
                                                        مبلغ ثابت
                                                    @break

                                                    @case('free_shipping')
                                                        شحن مجاني
                                                    @break

                                                    @case('bogo')
                                                        اشتري واحد واحصل على واحد
                                                    @break
                                                @endswitch
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $coupon->type === 'free_games' ? $coupon->total_games : '-' }}</td>
                                        <td>{{ $coupon->usage_limit }}</td>
                                        <td>{{ $coupon->usage_per_user }}</td>
                                        <td>{{ $coupon->start_date }}</td>
                                        <td>{{ $coupon->end_date }}</td>
                                        <td>{{ $coupon->total_usage }}</td>
                                        <td>{{ $coupon->unique_users }}</td>
                                        <td>{{ $coupon->remaining_usage }}</td>
                                        <td>{{ $coupon->usage_percentage }}</td>
                                        <td style="color: {{ $coupon->is_expired ? 'red' : 'green' }}">
                                            {{ $coupon->is_expired ? 'منتهي الصلاحية' : 'قائم' }}</td>

                                        <td>
                                            <div class="d-flex">
                                                <a class="modal-effect btn btn-sm btn-info mx-1"
                                                    data-effect="effect-scale" data-toggle="modal" href="#edit_modal"
                                                    data-id="{{ $coupon->id }}" data-code="{{ $coupon->code }}"
                                                    data-type="{{ $coupon->type }}" data-value="{{ $coupon->value }}"
                                                    data-discount_type="{{ $coupon->discount_type }}"
                                                    data-total_games="{{ $coupon->total_games }}"
                                                    data-usage_limit="{{ $coupon->usage_limit }}"
                                                    data-usage_per_user="{{ $coupon->usage_per_user }}"
                                                    data-start_date="{{ $coupon->start_date }}"
                                                    data-end_date="{{ $coupon->end_date }}"
                                                    data-user_id="{{ $coupon->user_id }}">
                                                    <i class="las la-pen"></i>
                                                </a>
                                                <a class="modal-effect btn btn-sm btn-danger" data-effect="effect-scale"
                                                    data-id="{{ $coupon->id }}" data-code="{{ $coupon->code }}"
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

        <!-- Edit Modal -->
        <div class="modal fade" id="edit_modal">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">تعديل القسيمة</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('coupons.update') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="id" id="id">
                            <input type="hidden" name="user_id" id="user_id">

                            <div class="form-group">
                                <label>كود القسيمة</label>
                                <input type="text" class="form-control" name="code" id="code" required>
                            </div>

                            <div class="form-group">
                                <label>نوع القسيمة</label>
                                <select class="form-control" name="type" id="type" required>
                                    <option value="discount">خصم</option>
                                    <option value="free_games">ألعاب مجانية</option>
                                </select>
                            </div>

                            <div class="discount-fields">
                                <div class="form-group">
                                    <label>قيمة الخصم</label>
                                    <input type="number" class="form-control" name="value" id="value">
                                </div>

                                <div class="form-group">
                                    <label>نوع الخصم</label>
                                    <select class="form-control" name="discount_type" id="discount_type">
                                        <option value="percentage">نسبة مئوية</option>
                                        <option value="fixed">مبلغ ثابت</option>
                                        <option value="free_shipping">شحن مجاني</option>
                                        <option value="bogo">اشتري واحد واحصل على واحد</option>
                                    </select>
                                </div>
                            </div>

                            <div class="free-games-fields" style="display:none;">
                                <div class="form-group">
                                    <label>عدد الألعاب</label>
                                    <input type="number" class="form-control" name="total_games" id="total_games">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>حد الاستخدام</label>
                                <input type="number" class="form-control" name="usage_limit" id="usage_limit" required>
                            </div>

                            <div class="form-group">
                                <label>حد المستخدم</label>
                                <input type="number" class="form-control" name="usage_per_user" id="usage_per_user"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>تاريخ البداية</label>
                                <input type="datetime-local" class="form-control" name="start_date" id="start_date"
                                    required>
                            </div>

                            <div class="form-group">
                                <label>تاريخ النهاية</label>
                                <input type="datetime-local" class="form-control" name="end_date" id="end_date"
                                    required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="delete_modal">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">حذف القسيمة</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('coupons.destroy') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="id" id="delete_id">
                            <p>هل أنت متأكد من حذف القسيمة: <span id="delete_code"></span>؟</p>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">حذف</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        $(document).ready(function() {
            // Toggle fields based on coupon type
            $('#type').on('change', function() {
                if (this.value === 'discount') {
                    $('.discount-fields').show();
                    $('.free-games-fields').hide();
                } else {
                    $('.discount-fields').hide();
                    $('.free-games-fields').show();
                }
            });

            // Edit modal
            $('#edit_modal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);

                modal.find('#id').val(button.data('id'));
                modal.find('#code').val(button.data('code'));
                modal.find('#type').val(button.data('type'));
                modal.find('#value').val(button.data('value'));
                modal.find('#discount_type').val(button.data('discount_type'));
                modal.find('#total_games').val(button.data('total_games'));
                modal.find('#usage_limit').val(button.data('usage_limit'));
                modal.find('#usage_per_user').val(button.data('usage_per_user'));
                modal.find('#start_date').val(button.data('start_date'));
                modal.find('#end_date').val(button.data('end_date'));
                modal.find('#user_id').val(button.data('user_id'));

                if (button.data('type') === 'discount') {
                    $('.discount-fields').show();
                    $('.free-games-fields').hide();
                } else {
                    $('.discount-fields').hide();
                    $('.free-games-fields').show();
                }
            });

            // Delete modal
            $('#delete_modal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);

                modal.find('#delete_id').val(button.data('id'));
                modal.find('#delete_code').text(button.data('code'));
            });
        });
        // company name send in request
        document.getElementById('company_id').addEventListener('change', function() {
            let selectedOption = this.options[this.selectedIndex];
            document.getElementById('company_name').value = selectedOption.getAttribute('data-name') || '';
        });


        function previewend_date(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#preview').attr('src', e.target.result).show();
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
        document.querySelector("#end_date").addEventListener("change", function() {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector("#preview").setAttribute("src", e.target.result);
                document.querySelector("#preview").style.display = "block";
            };
            reader.readAsDataURL(this.files[0]);
        });

        document.querySelector("#end_date").addEventListener("change", function() {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector("#preview").setAttribute("src", e.target.result);
                document.querySelector("#preview").style.display = "block";
            };
            reader.readAsDataURL(this.files[0]);
        });
    </script>
    <script>
        document.getElementById('exportForm').addEventListener('submit', function(e) {
            let company = document.getElementById('company_id').value.trim();
            let start = document.getElementById('start').value.trim();
            let end = document.getElementById('end').value.trim();
            let batch = document.getElementById('batch').value.trim();

            // شرط: لازم يختار شركة أو Start+End
            if (!company && !(start && end) && !batch) {
                e.preventDefault();
                alert("يجب اختيار شركة أو تحديد فترة (من - إلى)");
                return;
            }

            // شرط: لو اختار batch لازم يختار شركة كمان
            if (batch && !company) {
                e.preventDefault();
                alert("لازم تختار شركة مع مجموعة القسائم");
                return;
            }

            // شرط: لو اختار start لازم end والعكس
            if ((start && !end) || (!start && end)) {
                e.preventDefault();
                alert("لازم تدخل من تاريخ و إلى تاريخ مع بعض");
                return;
            }
        });
    </script>
   <!---  ارجاع جميع الباتشات الخاصه بهذه الشركه  --->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let companySelect = document.getElementById("company_id");
            let batchSelect = document.getElementById("batch");

            companySelect.addEventListener("change", function () {
                let companyId = this.value;
                
                // فضّي الـ batches القديمة
                batchSelect.innerHTML = '<option value="">اختر مجموعة القسائم</option>';

                if (companyId) {
                                    console.log(companyId);

                    fetch(`/dashboard/coupons/batches?company_id=${companyId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status && data.batches.length > 0) {
                                data.batches.forEach(batch => {
                                    let option = document.createElement("option");
                                    option.value = batch;
                                    option.textContent = batch;
                                    batchSelect.appendChild(option);
                                });
                            }
                        })
                        .catch(error => {
                            console.error("Error fetching batches:", error);
                        });
                }
            });
        });
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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JS + Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection
