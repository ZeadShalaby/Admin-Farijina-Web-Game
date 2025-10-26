@extends('layouts.master')


@section('css')
    <link href="{{ URL::asset('assets/plugins/fileuploads/css/fileupload.css') }}" rel="stylesheet" type="text/css" />
    <!---Internal Fancy uploader css-->
    <link href="{{ URL::asset('assets/plugins/fancyuploder/fancy_fileupload.css') }}" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
        .hidden-section {
            display: none;
        }
    </style>
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
    </style>
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

{{-- <h2 class="mt-3">ألسندات المتحركة</h2> --}}

<div class="tab">

    <button class="tablinks" onclick="openTab(event, 'Tab1')" id="defaultOpen">
        اعدادات عامه

    </button>
    {{-- <button class="tablinks" onclick="openTab(event, 'Tab2')" id="defaultOpen">
        الهديا عند الشراء لاول مره

    </button>
    <button class="tablinks" onclick="openTab(event, 'Tab3')"> هديه عند شراء بقيمه محدده </button> --}}

</div>


<form action="{{ route('settings.updateGift') }}" method="POST" enctype="multipart/form-data">

    {{ method_field('post') }}
    {{ csrf_field() }}


    <div id="Tab1" class="tabcontent">
        <div class="card-body">
            <div class="row row-sm">
                {{-- <div class="col-lg">
                    <p class="mg-b-10">عدد اللفات لكل مستخدم</p>
                    <input class="form-control" placeholder="عدد اللفات لكل مستخدم" type="text" name="number_play"
                        value="{{ $gift->number_play }}" hidden>
                </div> --}}
                <div class="row row-sm">
                    <p class="mg-b-10">القسم الرئيسي</p>
                    <select id="confirmation_message" name="confirmation_message" class="form-control SlectBox">

                        <option value="email" {{ $gift->confirmation_message == 'email' ? 'selected' : '' }}> تفعيل
                            الرسائل عن
                            طريق البريد
                            الاكتروني </option>
                        <option value="sms" {{ $gift->confirmation_message == 'sms' ? 'selected' : '' }}> تفعيل
                            الرسائل عن
                            SMS (Twillo) </option>


                    </select>
                </div>
                <input type="text" value="{{ $gift->id }}" name="id" hidden>



            </div>
        </div>
        <div class="col-md-12 col-xl-12 col-xs-12 col-sm-12">
            <div class="card">
                <div class="card-body">


                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div>
                                    <h6 class="card-title mb-1">رفع الصور للفوتر</h6>
                                </div>

                                <br>

                                <div class="row mb-4">
                                    <div class="col-sm-12 col-md-4">
                                        <label for="image" class="col-form-label">صورة للفوتر</label>
                                        <input type="file" class="dropify"
                                            data-default-file="{{ URL::asset($gift->image_footer1) }}"
                                            data-height="200" name="image" enctype="multipart/form-data" />
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>






    {{-- <form action="{{ route('settings.updateGift') }}" method="POST">

        {{ method_field('post') }}
        {{ csrf_field() }}


        <div id="Tab2" class="tabcontent">
            <div class="card-body">
                <div class="row row-sm">
                    <div class="col-lg">
                        <p class="mg-b-10">اسم الهديه</p>
                        <input class="form-control" placeholder="الاسم" type="text" name="first_order"
                            value="{{ $gift->first_order }}">
                    </div>
                    <input type="text" value="{{ $gift->id }}" name="id" hidden>
                    <div class="col-lg">
                        <p class="mg-b-10">مبلغ الخصم</p>
                        <input class="form-control" placeholder="القيمه"type="text" name="first_order_price"
                            value="{{ $gift->first_order_price }}">
                    </div>


                </div>
            </div>

        </div>




        <div id="Tab3" class="tabcontent">
            <div class="card-body">
                <div class="row row-sm">
                    <div class="col-lg">
                        <p class="mg-b-10">اسم الهديه</p>
                        <input class="form-control" placeholder="الاسم" type="text" name="buying_specified_value"
                            value="{{ $gift->buying_specified_value }}">
                    </div>
                    <div class="col-lg">
                        <p class="mg-b-10">المبلغ المحدد الذي اذا اشتري به المستخدم يحصل علي الهديه</p>
                        <input class="form-control" placeholder="القيمه"type="text" name="specified_value_price"
                            value="{{ $gift->specified_value_price }}">
                    </div>
                    <div class="col-lg">
                        <p class="mg-b-10">مبلغ الخصم</p>
                        <input class="form-control" placeholder="القيمه"type="text"
                            name="buying_specified_value_price" value="{{ $gift->buying_specified_value_price }}">
                    </div>


                </div>
            </div>
        </div> --}}

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
