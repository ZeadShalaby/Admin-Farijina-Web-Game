@extends('layouts.master2')
@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!-- Internal Select2 css -->
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .settings-container {
            position: relative;
            /* This allows nested absolute positioning */
            /* Other styles as needed */
        }

        .logout-position {
            position: absolute;
            top: 22%;
            /* Adjust as needed */
            right: 10px;
            /* Adjust as needed */
            /* Add other styles or positioning properties as necessary */
        }

        @media (max-width: 991px) {
            .logout-position {
                top: 17.8%;
                /* Adjusted positioning for smaller screens */
                right: 7%;
                /* Adjusted positioning for smaller screens */
                transform: translateX(50%);
                /* Centering the button horizontally */
            }
        }



        @media (max-width: 753px) {
            .logout-position {
                top: 14.8%;
                /* Adjusted positioning for smaller screens */
                right: 15%;
                /* Adjusted positioning for smaller screens */
                transform: translateX(50%);
                /* Centering the button horizontally */
            }
        }
    </style>
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


    <div class="settings-container">
        <form action="{{ route('setting.store') }}"method="post" enctype="multipart/form-data">
            {{ method_field('post') }}
            {{ csrf_field() }}
            <!-- row -->
            <p></p>
            <div class="row row-sm">

                <!-- Col -->
                <div class="col-lg-4">
                    <div class="card mg-b-20">
                        <div class="card-body">
                            <div class="pl-0">
                                <div class="main-profile-overview">
                                    <div class="main-img-user profile-user">
                                        <img id="profile-image" alt=""
                                            src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png">
                                        <label for="image-upload" class="fas fa-camera profile-edit"></label>
                                        <input id="image-upload" type="file" style="display: none;" name="image">
                                    </div>

                                    <div class="main-profile-social-list">
                                    </div>
                                    <hr class="mg-y-30">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Col -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">


                            <div class="mb-4 main-content-label">الاسم</div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">اسم المتجر</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" placeholder="UserName" value=""
                                            name="username" id="username">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4 main-content-label">معلومات الاتصال</div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">البريد الاكتروني<i>(مطلوب)</i></label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" placeholder="Email" value=""
                                            name="email" id="email">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">هاتف المتجر</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" placeholder="phone number" value=""
                                            name="phone" id="phone">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">عنوان المتجر</label>
                                    </div>
                                    <div class="col-md-9">
                                        <textarea class="form-control" name="address" id="address" rows="2" placeholder="Address"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4 main-content-label">المعلومات التواصل الاجتماعي</div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">تويتر</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" placeholder="twitter" value=""
                                            name="twitter" id="twitter">
                                    </div>
                                </div>
                            </div>
                            <input type="text" value="{{ Auth::user()->id }}" name="user_id" hidden>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">فيسبوك</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" placeholder="facebook" value=""
                                            name="facebook" id="facebook">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">انستقرام</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" placeholder="google" value=""
                                            name="google" id="google">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">لينكدن</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" placeholder="linkedin" value=""
                                            name="linkedin" id="linkedin">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label">جيتهاب</label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" placeholder="github" value=""
                                            name="github" id="github">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4 main-content-label">معلومات اضافيه عن المتجر</div>
                            <div class="form-group ">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label" name="example" id="example">الوصف</label>
                                    </div>
                                    <div class="col-md-9">
                                        <textarea class="form-control" name="aboutcompany" rows="4" placeholder=""></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card-footer text-left">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">تاكيد</button>
                        </div>

                    </div>
                </div>
                <!-- /Col -->
            </div>
    </div>
    <!-- Container closed -->
    </div>
    </form>
    <div class="logout-position">
        <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary waves-effect waves-light">تسجيل الخروج</button>
        </form>
    </div>

    </div>

@endsection
@section('js')
    <script>
        document.getElementById('image-upload').addEventListener('change', function(e) {
            var file = e.target.files[0];
            var reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('profile-image').src = event.target.result;
            };
            reader.readAsDataURL(file);
        });
    </script>
    <!--Internal  Chart.bundle js -->
    <script src="{{ URL::asset('assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>
    <!-- Internal Select2.min js -->
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/select2.js') }}"></script>
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
@endsection
