@extends('layouts.master')


@section('css')
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--- Internal Select2 css-->
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/amazeui-datetimepicker/css/amazeui.datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/jquery-simple-datetimepicker/jquery.simple-dtpicker.css') }}"
        rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/pickerjs/picker.min.css') }}" rel="stylesheet">

    <link href="{{ URL::asset('assets/plugins/spectrum-colorpicker/spectrum.css') }}" rel="stylesheet">
@section('title')
    اعدادات الهدايا
@stop
<style>
    .mg-r-10 {
        margin-right: 10px;
    }

    #quillEditor {
        direction: rtl;
        text-align: right;
    }

    body {
        font-family: Arial;
    }

    .tab {
        overflow: hidden;
        border-bottom: 1px solid #ccc;
        background-color: #fff;
    }

    .tab button {
        background-color: inherit;
        float: right;
        border: none;
        outline: navajowhite;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
        font-size: 15px;

    }

    .tab button:hover {
        background-color: #ddd;
        outline: navajowhite
    }

    .tab button.active {
        background-color: #0428c4;
        color: #fff;
        outline: navajowhite
    }

    .tabcontent {
        display: none;
        padding: 6px 12px;
        /* border: 1px solid #ccc; */
        border-top: none;
        animation: fadeEffect 1s;
        /* margin-top: 20px; */
        background-color: #fff;
        padding: 15px
    }

    @keyframes fadeEffect {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }
</style>
<style>
    .editor {
        width: 400px;
        margin: 0 auto;
    }

    textarea {
        width: 100%;
        height: 200px;
        font-size: 16px;
        font-weight: normal;
    }

    .controls {
        margin-top: 10px;
    }

    .font-size {
        width: 100px;
    }

    .font-weight {
        width: 100px;
    }

    .preview {
        margin-top: 20px;
    }
</style>




@endsection
{{-- ection('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">الاقسام /</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">فئة العقار</span>
						</div>
					</div>
			
				</div>
				<!-- breadcrumb -->
@endsection --}}
@section('content')

<br>
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

<h2 class="mt-3">ألسندات المتحركة</h2>

<div class="tab">
    {{-- <button class="tablinks" onclick="openTab(event, 'Tab1')" id="defaultOpen">
        باي بال (paypal)

    </button>
    <button class="tablinks" onclick="openTab(event, 'Tab2')"> استريب (stripe)</button> --}}
    <button class="tablinks" onclick="openTab(event, 'Tab1')"> بايموب (paymob)</button>
</div>




<form action="{{ route('gateways.update') }}" method="POST">

    {{ method_field('post') }}
    {{ csrf_field() }}


    {{-- <div id="Tab1" class="tabcontent">
        <div class="card-body">
            <div class="row row-sm">
                <div class="col-lg">
                    <p class="mg-b-10">(PAYPAL_SANDBOX_API_USERNAME) اسم المستخدم</p>
                    <input class="form-control" placeholder="ادخل القيمه" type="text"
                        name="PAYPAL_SANDBOX_API_USERNAME" value="{{ $paymentgateway->PAYPAL_SANDBOX_API_USERNAME }}">
                </div>
                <div class="col-lg">
                    <p class="mg-b-10">(PAYPAL_SANDBOX_API_PASSWORD) كلمة السر</p>
                    <input class="form-control" placeholder="ادخل القيمه"type="text" name="PAYPAL_SANDBOX_API_PASSWORD"
                        value="{{ $paymentgateway->PAYPAL_SANDBOX_API_PASSWORD }}">
                </div>


            </div>



            <br><br><br>


            <div class="row row-sm">


             
                <div class="col-lg">
                    <p class="mg-b-10">(PAYPAL_SANDBOX_API_CERTIFICATE) شهادة واجهة برمجة تطبيقات</p>
                    <input class="form-control" placeholder="ادخل القيمه" type="text"
                        name="PAYPAL_SANDBOX_API_CERTIFICATE"
                        value="{{ $paymentgateway->PAYPAL_SANDBOX_API_CERTIFICATE }}">
                </div>
                <div class="col-lg">
                    <p class="mg-b-10">(PAYPAL_CURRENCY) العمله</p>
                    <input class="form-control" placeholder=" ادخل القيمه" type="text" name="PAYPAL_CURRENCY"
                        value="{{ $paymentgateway->PAYPAL_CURRENCY }}">
                </div>
            </div>

            <br><br><br>

            <div class="row row-sm">



                <div class="col-lg">
                    <p class="mg-b-10">(PAYPAL_MODE) وضع بايبال</p>
                    <input class="form-control" placeholder="ادخل القيمه" type="text" name="PAYPAL_MODE"
                        value="{{ $paymentgateway->PAYPAL_MODE }}">
                </div>
                <div class="col-lg">
                    <p class="mg-b-10">(CLAIENT_ID) معرف العميل</p>
                    <input class="form-control" placeholder=" ادخل القيمه" type="text" name="CLAIENT_ID"
                        value="{{ $paymentgateway->CLAIENT_ID }}">
                </div>
            </div>

            <br><br><br>

            <div class="row row-sm">


                <div class="col-lg">
                    <p class="mg-b-10">(PAYPAL_SANDBOX_API_SECRET) المفتاح السري</p>
                    <input class="form-control" placeholder="ادخل القيمه" type="text"
                        name="PAYPAL_SANDBOX_API_SECRET" value="{{ $paymentgateway->PAYPAL_SANDBOX_API_SECRET }}">
                </div>
                <div class="col-lg">
                    <p class="mg-b-10">(PAYPAL_PUBLIC_KEY) المفتاح العام</p>
                    <input class="form-control" placeholder="ادخل القيمه" type="text" name="PAYPAL_PUBLIC_KEY"
                        value="{{ $paymentgateway->PAYPAL_PUBLIC_KEY }}">
                </div>

            </div>



        </div>

    </div> --}}




    {{-- <div id="Tab2" class="tabcontent">
        <div class="card-body">
            <div class="row row-sm">


                <div class="col-lg">
                    <p class="mg-b-10">(STRIPE_PUBLISHABLE_KEY) المفتاح العام</p>
                    <input class="form-control" placeholder="ادخل القيمه" type="text"
                        name="STRIPE_PUBLISHABLE_KEY" value="{{ $paymentgateway->STRIPE_PUBLISHABLE_KEY }}">
                </div>
                <div class="col-lg">
                    <p class="mg-b-10">(STRIPE_SECRET_KEY) المفتاح السري</p>
                    <input class="form-control" placeholder="ادخل القيمه" type="text" name="STRIPE_SECRET_KEY"
                        value="{{ $paymentgateway->STRIPE_SECRET_KEY }}">
                </div>

            </div>
        </div>
    </div> --}}
    <div id="Tab1" class="tabcontent">
        <div class="card-body">
            <div class="row row-sm">


                <div class="col-lg">
                    <p class="mg-b-10">(PayMob_Username) اسم المستخدم</p>
                    <input class="form-control" placeholder="ادخل القيمه" type="text" name="PayMob_Username"
                        value="{{ $paymentgateway->PayMob_Username }}">
                </div>
                <div class="col-lg">
                    <p class="mg-b-10">(PayMob_Password) كلمة المرور</p>
                    <input class="form-control" placeholder="ادخل القيمه" type="text" name="PayMob_Password"
                        value="{{ $paymentgateway->PayMob_Password }}">
                </div>

            </div>






            <div class="row row-sm">


                <div class="col-lg">
                    <p class="mg-b-10">(PayMob_Integration_Id) المفتاح التكامل</p>
                    <input class="form-control" placeholder="ادخل القيمه" type="text" name="PayMob_Integration_Id"
                        value="{{ $paymentgateway->PayMob_Integration_Id }}">
                </div>
                <div class="col-lg">
                    <p class="mg-b-10">(PayMob_HMAC) </p>
                    <input class="form-control" placeholder="ادخل القيمه" type="text" name="PayMob_HMAC"
                        value="{{ $paymentgateway->PayMob_HMAC }}">
                </div>

            </div>





        </div>
    </div>
    <div class="card-footer text-left">
        <button type="submit" class="btn btn-primary waves-effect waves-light">تحديث</button>
    </div>


</form>



@endsection

@section('js')


<script>
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // Get the element with id="defaultOpen" and click on it
    document.getElementById("defaultOpen").click();
</script>
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

<script>
    var textInput = document.getElementById('text-input');
    var fontSizeInput = document.getElementById('font-size');
    var fontWeightInput = document.getElementById('font-weight');
    var textPreview = document.getElementById('text-preview');

    textInput.addEventListener('input', updatePreview);
    fontSizeInput.addEventListener('input', updatePreview);
    fontWeightInput.addEventListener('input', updatePreview);

    function updatePreview() {
        var fontSize = fontSizeInput.value + 'px';
        var fontWeight = fontWeightInput.value;
        var text = textInput.value;

        textPreview.style.fontSize = fontSize;
        textPreview.style.fontWeight = fontWeight;
        textPreview.textContent = text;
    }
</script>
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
<!--Internal quill js -->
{{-- <script src="{{ URL::asset('assets/plugins/quill/quill.min.js') }}"></script> --}}
<!-- Internal form-elements js -->
<script src="{{ URL::asset('assets/js/form-elements.js') }}"></script>
<!-- Internal Form-editor js -->
<script src="{{ URL::asset('assets/js/form-editor.js') }}"></script>

@endsection
