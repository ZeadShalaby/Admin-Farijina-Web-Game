@extends('layouts.master')
@section('css')
    <!-- Internal Select2 css -->
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--Internal  Datetimepicker-slider css -->
    <link href="{{ URL::asset('assets/plugins/amazeui-datetimepicker/css/amazeui.datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/jquery-simple-datetimepicker/jquery.simple-dtpicker.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/pickerjs/picker.min.css') }}" rel="stylesheet">
    <!-- Internal Spectrum-colorpicker css -->
    <link href="{{ URL::asset('assets/plugins/spectrum-colorpicker/spectrum.css') }}" rel="stylesheet">
    <!---Internal Fileupload css-->
    <link href="{{ URL::asset('assets/plugins/fileuploads/css/fileupload.css') }}" rel="stylesheet" type="text/css" />
    <!---Internal Fancy uploader css-->
    <link href="{{ URL::asset('assets/plugins/fancyuploder/fancy_fileupload.css') }}" rel="stylesheet" />
    <style>
        .file-upload {
            position: relative;
            display: inline-block;
        }

        color-input-group {
            display: flex;
            align-items: center;
        }

        .color-input {
            margin-right: 10px;
        }

        .price-input {
            width: 150px;
        }


        .file-upload input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload label {
            display: inline-block;
            background-color: #3498db;
            color: #fff;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }

        .file-upload label span {
            margin-right: 10px;
        }

        .file-preview {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
        }

        .file-preview img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin-right: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        }



        /*******************/

        .tab-text {
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            padding-bottom: 5px;
            display: inline-block;
            position: relative;
            color: #555;
        }

        /* الخط تحت النص */
        .tab-text::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            height: 3px;
            width: 0%;
            background-color: blue;
            border-radius: 2px;
            transition: width 0.3s ease, left 0.3s ease;
        }

        /* الخط يظهر كامل عند التفعيل */
        .tab-text.active::after {
            width: 100%;
            left: 0;
        }

        .tab-text.active {
            color: #000;
        }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">المنتج</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ اضافة
                    منتج</span>
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


    @if (session()->has('Edit'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('Edit') }}</strong>
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

    @if (session()->has('Error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ session()->get('Error') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif



    <!-- row -->
    {{-- <form action="{{ route('coupons.store') }}" method="post" enctype="multipart/form-data">
        {{ method_field('post') }}
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-12 col-xl-12 col-xs-12 col-sm-12">
                <div class="card">
                    <div class="col-md-12 col-xl-12 col-xs-12 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row row-sm">

                                    <!-- Coupon Code -->
                                    <div class="col-lg">
                                        <p class="mg-b-10">كود القسيمة</p>
                                        <input class="form-control" placeholder="الكود" type="text" name="code"
                                            value="{{ old('code') }}" required>
                                    </div>

                                    <!-- Coupon Type -->
                                    <div class="col-lg">
                                        <p class="mg-b-10">نوع القسيمة</p>
                                        <select class="form-control" id="type" name="type" required>
                                            <option value="discount">خصم</option>
                                            <option value="free_games">ألعاب مجانية</option>
                                        </select>
                                    </div>

                                    <!-- Discount Fields (Conditional) -->
                                    <div class="col-lg" id="discountFields">
                                        <p class="mg-b-10">قيمة الخصم</p>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                            </div>
                                            <input aria-label="Amount (to the nearest dollar)" class="form-control"
                                                placeholder="السعر" type="number" name="value" min="1">
                                            <div class="input-group-append">
                                                <span class="input-group-text">.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-sm">
                                    <!-- Discount Type Fields (Conditional) -->
                                    <div class="col-lg" id="discountTypeFields">
                                        <p class="mg-b-10">نوع الخصم</p>
                                        <select class="form-control" id="discount_type" name="discount_type">
                                            <option value="percentage">نسبة مئوية (%)</option>
                                            <option value="fixed">مبلغ ثابت ($)</option>
                                        </select>
                                    </div>

                                    <!-- Free Games Fields (Conditional) -->
                                    <div class="col-lg" id="totalGamesFields" style="display: none;">
                                        <p class="mg-b-10">عدد الألعاب</p>
                                        <input class="form-control" placeholder="عدد الألعاب" type="number"
                                            name="total_games" min="1">
                                    </div>

                                    <!-- Usage Limit -->
                                    <div class="col-lg">
                                        <p class="mg-b-10">الحد الأقصى للاستخدام</p>
                                        <input class="form-control" placeholder="الحد الأقصى للاستخدام" type="number"
                                            name="usage_limit" min="1" required>
                                    </div>

                                    <!-- Usage Per User -->
                                    <div class="col-lg">
                                        <p class="mg-b-10">الحد الأقصى للاستخدام لكل مستخدم</p>
                                        <input class="form-control" placeholder="الحد الأقصى للاستخدام لكل مستخدم"
                                            type="number" name="usage_per_user" min="1" required>
                                    </div>

                                    <!-- Start Date -->
                                    <div class="col-lg">
                                        <label for="datetimepicker2">وقت البدء</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                                </div>
                                            </div>
                                            <input class="form-control" id="datetimepicker2" type="datetime-local"
                                                name="start_date" value="{{ date('Y-m-d\TH:i') }}" required>
                                        </div>
                                    </div>

                                    <!-- End Date -->
                                    <div class="col-lg">
                                        <label for="datetimepicker1">وقت الانتهاء</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">
                                                    <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                                </div>
                                            </div>
                                            <input class="form-control" id="datetimepicker1" type="datetime-local"
                                                name="end_date" value="{{ date('Y-m-d\TH:i', strtotime('+1 day')) }}"
                                                min="{{ date('Y-m-d\TH:i', strtotime('+1 day')) }}" required>
                                        </div>
                                    </div>

                                    <!-- Hidden User ID -->
                                    <input type="hidden" name="user_id" value="{{ Auth::id() }}">

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-danger"
                        style="width:200px; height:50px; background-color: blue; color:white; margin: 0 auto;">حفظ
                    </button>
                </div>
            </div>
        </div>
    </form> --}}
  <form action="{{ route('coupons.store') }}" method="post" enctype="multipart/form-data">
    {{ method_field('post') }}
    {{ csrf_field() }}

    <!-- Tabs للتبديل بين الفورمين -->
    <div class="row mb-3">
        <div class="col-lg-6 text-center">
            <span id="tabOld" class="tab-text active" onclick="toggleCoupon('old')">الفورم العادي</span>
        </div>
        <div class="col-lg-6 text-center">
            <span id="tabNew" class="tab-text" onclick="toggleCoupon('new')">فورم الشركة والكوبون</span>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-xl-12 col-xs-12 col-sm-12">
            <div class="card">
                <div class="card-body">

                    <div class="row row-sm">

                        <!-- الفورم العادي -->
                        <div id="oldCouponField">
                            <div class="col-lg">
                                <p class="mg-b-10">كود القسيمة</p>
                                <input class="form-control" placeholder="الكود" type="text" name="code"
                                    value="{{ old('code') }}" required>
                            </div>
                        </div>

                        <!-- الفورم الجديد -->
                        <div id="newCouponFields" style=" width:100%; display:flex; gap:15px;">
                            <div class="col-lg-3">
                                <label for="company_id">اسم الشركة</label>
                                <select class="form-control" name="company" id="company_id">
                                    <option value="">اختر الشركة</option>
                                    @foreach ($companys as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                             <div class="col-lg-3">
                                <p class="mg-b-10">مجموعه القسيمة</p>
                                <input class="form-control" placeholder="مجموعه القسيمة" type="test"
                                    name="batch">
                            </div>

                            <div class="col-lg-3">
                                <p class="mg-b-10">عدد الكوبونات</p>
                                <input class="form-control" placeholder="عدد الكوبونات" type="number"
                                    name="coupon_number">
                            </div>
                            <div class="col-lg-2">
                                <p class="mg-b-10">رقم الكوبون</p>
                                <input class="form-control" placeholder="رقم الكوبون" type="number" name="num_code"
                                    value="5">
                            </div>
                        </div>

                        <!-- Coupon Type -->
                        <div class="col-lg">
                            <p class="mg-b-10">نوع القسيمة</p>
                            <select class="form-control" id="type" name="type" required>
                                <option value="discount">خصم</option>
                                <option value="free_games">ألعاب مجانية</option>
                            </select>
                        </div>

                        <!-- Discount Fields -->
                        <div class="col-lg" id="discountFields">
                            <p class="mg-b-10">قيمة الخصم</p>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input class="form-control" placeholder="السعر" type="number" name="value"
                                    min="1">
                                <div class="input-group-append">
                                    <span class="input-group-text">.00</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="row row-sm">

                        <!-- Discount Type Fields -->
                        <div class="col-lg" id="discountTypeFields">
                            <p class="mg-b-10">نوع الخصم</p>
                            <select class="form-control" id="discount_type" name="discount_type">
                                <option value="percentage">نسبة مئوية (%)</option>
                                <option value="fixed">مبلغ ثابت ($)</option>
                            </select>
                        </div>

                        <!-- Free Games Fields -->
                        <div class="col-lg" id="totalGamesFields" style="display: none;">
                            <p class="mg-b-10">عدد الألعاب</p>
                            <input class="form-control" placeholder="عدد الألعاب" type="number" name="total_games"
                                min="1">
                        </div>

                        <!-- Usage Limit -->
                        <div class="col-lg">
                            <p class="mg-b-10">الحد الأقصى للاستخدام</p>
                            <input class="form-control" placeholder="الحد الأقصى للاستخدام" type="number"
                                name="usage_limit" min="1" required>
                        </div>

                        <!-- Usage Per User -->
                        <div class="col-lg">
                            <p class="mg-b-10">الحد الأقصى للاستخدام لكل مستخدم</p>
                            <input class="form-control" placeholder="الحد الأقصى للاستخدام لكل مستخدم" type="number"
                                name="usage_per_user" min="1" required>
                        </div>

                        <!-- Start Date -->
                        <div class="col-lg">
                            <label for="datetimepicker2">وقت البدء</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                    </div>
                                </div>
                                <input class="form-control" id="datetimepicker2" type="datetime-local"
                                    name="start_date" value="{{ date('Y-m-d\TH:i') }}" required>
                            </div>
                        </div>

                        <!-- End Date -->
                        <div class="col-lg">
                            <label for="datetimepicker1">وقت الانتهاء</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                    </div>
                                </div>
                                <input class="form-control" id="datetimepicker1" type="datetime-local"
                                    name="end_date" value="{{ date('Y-m-d\TH:i', strtotime('+1 day')) }}"
                                    min="{{ date('Y-m-d\TH:i', strtotime('+1 day')) }}" required>
                            </div>
                        </div>

                        <!-- Hidden User ID -->
                        <input type="hidden" name="user_id" value="{{ Auth::id() }}">

                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-danger"
                    style="width:200px; height:50px; background-color: blue; color:white; margin: 0 auto;">حفظ</button>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", function() {
    // افتراضي: الفورم القديم ظاهر
    toggleCoupon('old');

    // تغيير الحقول حسب نوع القسيمة
    const typeSelect = document.getElementById('type');
    const discountFields = document.getElementById('discountFields');
    const discountTypeFields = document.getElementById('discountTypeFields');
    const totalGamesFields = document.getElementById('totalGamesFields');

    function handleCouponType() {
        if (typeSelect.value === 'discount') {
            discountFields.style.display = 'block';
            discountTypeFields.style.display = 'block';
            totalGamesFields.style.display = 'none';
        } else {
            discountFields.style.display = 'none';
            discountTypeFields.style.display = 'none';
            totalGamesFields.style.display = 'block';
        }
    }

    typeSelect.addEventListener('change', handleCouponType);
    handleCouponType(); // لتفعيل الحالة الافتراضية
});

function toggleCoupon(type) {
    const oldField = document.getElementById('oldCouponField');
    const newField = document.getElementById('newCouponFields');
    const tabOld = document.getElementById('tabOld');
    const tabNew = document.getElementById('tabNew');

    // حقول الفورم القديم
    const oldCode = oldField.querySelector('input[name="code"]');

    // حقول الفورم الجديد
    const company = newField.querySelector('select[name="company"]');
    const couponNumber = newField.querySelector('input[name="coupon_number"]');
    const numCode = newField.querySelector('input[name="num_code"]');
    const batch = newField.querySelector('input[name="batch"]');

    if (type === 'old') {
        oldField.style.display = 'block';
        newField.style.display = 'none';
        tabOld.classList.add('active');
        tabNew.classList.remove('active');

        // إدارة required
        oldCode.required = true;
        company.required = false;
        couponNumber.required = false;
        numCode.required = false;

        // إعادة القيم للـ null للفورم الجديد
        company.value = null;
        couponNumber.value = null;
        numCode.value = null;

    } else {
        oldField.style.display = 'none';
        newField.style.display = 'flex';
        tabOld.classList.remove('active');
        tabNew.classList.add('active');

        // إدارة required
        oldCode.required = false;
        company.required = true;
        couponNumber.required = true;
        numCode.required = true;

        // إعادة القيم للـ null للفورم القديم
        oldCode.value = null;
    }
}

</script>

    <!-- JavaScript to Handle Conditional Fields -->
    <script>
        document.getElementById('type').addEventListener('change', function() {
            const type = this.value;
            if (type === 'discount') {
                document.getElementById('discountFields').style.display = 'block';
                document.getElementById('discountTypeFields').style.display = 'block';
                document.getElementById('totalGamesFields').style.display = 'none';
            } else {
                document.getElementById('discountFields').style.display = 'none';
                document.getElementById('discountTypeFields').style.display = 'none';
                document.getElementById('totalGamesFields').style.display = 'block';
            }
        });
    </script>
    <!-- row closed -->
@endsection

@section('js')
    {{-- <script>
        $(document).ready(function() {
            $('.main-toggle').on('click', function() {
                console.log('==========================');
                var isActive = $(this).hasClass('main-toggle-success');
                var couponActiveInput = $('#couponActiveInput');

                if (isActive) {
                    $(this).removeClass('main-toggle-success');
                    couponActiveInput.val('1');
                } else {
                    $(this).addClass('main-toggle-success');
                    couponActiveInput.val('0');
                }
            });
        });
    </script> --}}
    <!--Internal  Datepicker js -->
    <script src="{{ URL::asset('assets/plugins/jquery-ui/ui/widgets/datepicker.js') }}"></script>
    <!--Internal  jquery.maskedinput js -->
    <script src="{{ URL::asset('assets/plugins/jquery.maskedinput/jquery.maskedinput.js') }}"></script>
    <!--Internal  spectrum-colorpicker js -->
    <script src="{{ URL::asset('assets/plugins/spectrum-colorpicker/spectrum.js') }}"></script>
    <!-- Internal Select2.min js -->
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <!--Internal Ion.rangeSlider.min js -->
    <script src="{{ URL::asset('assets/plugins/ion-rangeslider/js/ion.rangeSlider.min.js') }}"></script>
    <!--Internal  jquery-simple-datetimepicker js -->
    <script src="{{ URL::asset('assets/plugins/amazeui-datetimepicker/js/amazeui.datetimepicker.min.js') }}"></script>
    <!-- Ionicons js -->
    <script src="{{ URL::asset('assets/plugins/jquery-simple-datetimepicker/jquery.simple-dtpicker.js') }}"></script>
    <!--Internal  pickerjs js -->
    <script src="{{ URL::asset('assets/plugins/pickerjs/picker.min.js') }}"></script>
    <!-- Internal form-elements js -->
    <script src="{{ URL::asset('assets/js/form-elements.js') }}"></script>

    <!--Internal Fileuploads js-->
    <script src="{{ URL::asset('assets/plugins/fileuploads/js/fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fileuploads/js/file-upload.js') }}"></script>
    <!--Internal Fancy uploader js-->
    <script src="{{ URL::asset('assets/plugins/fancyuploder/jquery.ui.widget.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fancyuploder/jquery.fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fancyuploder/jquery.iframe-transport.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fancyuploder/jquery.fancy-fileupload.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/fancyuploder/fancy-uploader.js') }}"></script>
@endsection
