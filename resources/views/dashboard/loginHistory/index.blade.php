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
                <h4 class="content-title mb-0 my-auto">الشركات /</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">جميع
                    الشركات</span>
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
        <!-- آخر تسجيل دخول -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card stats-card overflow-hidden sales-card bg-primary-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2">
                    <div class="">
                        <h6 class="mb-3 tx-15 text-white">آخر تسجيل دخول</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-18 font-weight-bold mb-1 text-white">
                                    <a href="{{ $mapsUrl }}" target="_blank" class="text-white">
                                        <i class="fas fa-map-marker-alt" style="color: rgb(0, 255, 0)"></i>
                                    </a>
                                    {{ trim(($recentLogins->location['city'] ?? '') . ', ' . ($recentLogins->location['state'] ?? '') . ', ' . ($recentLogins->location['country'] ?? 'غير معروف'), ', ') }}

                                </h4>
                                <p class="mb-0 tx-12 text-white op-7">
                                    {{ $recentLogins->first()->login_at->locale('ar')->diffForHumans() ?? '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- عدد مرات الدخول -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card stats-card overflow-hidden sales-card bg-success-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2">
                    <div class="">
                        <h6 class="mb-3 tx-15 text-white">عدد مرات الدخول (إجمالي)</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-18 font-weight-bold mb-1 text-white">
                                    {{ number_format($totalLogins) }}
                                </h4>
                                <p class="mb-0 tx-12 text-white op-7">إجمالي محاولات الدخول</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الأماكن الأكثر تسجيل دخول -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card stats-card overflow-hidden sales-card bg-warning-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2">
                    <div class="">
                        <h6 class="mb-3 tx-15 text-white">الأماكن الأكثر تسجيل دخول منها</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                @php
                                    $topLocation = $topLocations->keys()->first();
                                    $count = $topLocations->first();
                                @endphp
                                <h4 class="tx-18 font-weight-bold mb-1 text-white">
                                    {{ $topLocation ?? 'غير معروف' }}
                                </h4>
                                <p class="mb-0 tx-12 text-white op-7">
                                    عدد الدخول: {{ number_format($count ?? 0) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الجلسات النشطة -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card stats-card overflow-hidden sales-card bg-danger-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2">
                    <div class="">
                        <h6 class="mb-3 tx-15 text-white">جلسات نشطة الآن</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-18 font-weight-bold mb-1 text-white">
                                    {{ number_format($onlineUsers) }}
                                </h4>
                                <p class="mb-0 tx-12 text-white op-7">عدد الإداريين المتصلين حالياً</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- row -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-md-nowrap" id="example1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>البريد الإلكتروني</th>
                                    <th>الموقع</th>
                                    <th>وقت الدخول</th>
                                    <th>وقت الخروج</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loginHistory as $index => $history)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $history->admin_email }}</td>

                                        <td>
                                            <span style="color: #007bff">{{ $history->formatted_location }} </span>
                                            @if ($history->maps_url)
                                                <a href="{{ $history->maps_url }}" target="_blank" title="عرض على الخريطة"
                                                   >
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </a>
                                            @endif
                                        </td>


                                        <td>{{ $history->login_at ? $history->login_at->locale('ar')->diffForHumans() : '-' }}
                                        </td>
                                        <td>{{ $history->logout_at ? $history->logout_at->locale('ar')->diffForHumans() : 'لم يسجل خروج' }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $history->status === 'online' ? 'badge-success' : 'badge-secondary' }}">
                                                {{ $history->status === 'online' ? 'متصل' : 'غير متصل' }}
                                            </span>
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





@endsection

@section('js')
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
