@extends('layouts.master')


@section('css')
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
@section('title')
    الدول
@stop
@endsection
@section('page-header')
<!-- breadcrumb -->
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">الاعدادات /</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">الدول و
                الضرائب
            </span>
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
<!-- row -->
<div class="row">

    <div class="col-xl-12">
        <div class="card">
            <div class="col-sm-6 col-md-4 col-xl-3">
                <a class="modal-effect btn btn-outline-primary btn-block" data-effect="effect-scale" data-toggle="modal"
                    href="#modaldemo8">اضافة دوله</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-md-nowrap" id="example1">
                        <thead>
                            <tr>
                                <th class="wd-15p border-bottom-0">#</th>
                                <th class="wd-15p border-bottom-0">اسم الدوله</th>
                                <th class="wd-15p border-bottom-0">قيمة الشحن لكل كيلو</th>

                                <th class="wd-20p border-bottom-0">العمليات</th>
                            </tr>
                        </thead>
                        <tbody>

                            @php
                                $i = 0;
                            @endphp

                            @foreach ($countries as $country)
                                @php
                                    $i++;
                                @endphp
                                <tr>
                                    <td>{{ $i }}</td>
                                    </td>
                                    <td>{{ $country->name }}</td>
                                    <td>{{ $country->country_tax }}</td>
                                    <td>

                                        <a class="modal-effect btn btn-sm btn-info" data-effect="effect-scale"
                                            data-id="{{ $country->id }}" data-name="{{ $country->name }}"
                                            data-country_tax="{{ $country->country_tax }}"
                                            data-code="{{ $country->code }}"
                                            data-exchange_rate="{{ $country->exchange_rate }}" data-toggle="modal"
                                            href="#exampleModal2" title="تعديل"><i class="las la-pen"></i></a>



                                        <a class="modal-effect btn btn-sm btn-danger" data-effect="effect-scale"
                                            data-id="{{ $country->id }}" data-name="{{ $country->name }}"
                                            data-toggle="modal" href="#modaldemo9" title="حذف"><i
                                                class="las la-trash"></i></a>

                                    </td>
                                </tr>
                            @endforeach



                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modaldemo8">
        <div class="modal-dialog" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">اضافة دوله</h6><button aria-label="Close" class="close"
                        data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('countries.store') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}


                        <div class="form-group">
                            <strong>اسم الدوله بالعربيه</strong>
                            <select name="name" id="countries" class="form-control mb-1">
                                {{-- <option value="">اسم الدوله بالعربيه</option> --}}
                                @foreach ($countriesList as $country)
                                    <option value="{{ $country->id }}-{{ $country->name_en }}">
                                        {{ $country->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>


                        <input type="hidden" id="latitude" name="latitude" value="">
                        <input type="hidden" id="longitude" name="longitude" value="">
                        <div class="form-group">
                            <label for="exampleInputEmail1">كود العمله</label>
                            <input type="text" class="form-control" id="code" name="code"
                                placeholder="USD" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">فرق العمله</label>
                            <input type="number" class="form-control" id="exchange_rate" name="exchange_rate"
                                placeholder="30" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">قيمة الشحن لكل كيلو</label>
                            <input type="number" class="form-control" id="country_tax" name="country_tax" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">تاكيد</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <!-- row closed -->
    <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">تعديل الدوله</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('countries.update') }}" method="post" enctype="multipart/form-data">

                        {{ csrf_field() }}
                        <input type="hidden" name="id" id="id" value="">

                        <div class="form-group">
                            <label for="exampleInputEmail1">اسم الدوله</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="name" required readonly>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">كود العمله</label>
                            <input type="text" class="form-control" id="code" name="code"
                                placeholder="USD" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">فرق العمله</label>
                            <input type="number" class="form-control" id="exchange_rate" name="exchange_rate"
                                placeholder="30" required>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">قيمة الشحن لكل كيلو</label>
                            <input type="number" class="form-control" id="country_tax" name="country_tax" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">تاكيد</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- delete -->
    <div class="modal" id="modaldemo9">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">حذف الدوله</h6><button aria-label="Close" class="close"
                        data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="{{ route('countries.destroy') }}"method="post">
                    {{ method_field('post') }}
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <p>هل انت متاكد من عملية الحذف ؟</p><br>
                        <input type="hidden" name="id" id="id" value="">
                        <input class="form-control" name="name" id="name" type="text" readonly>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                        <button type="submit" class="btn btn-danger">تاكيد</button>
                    </div>
            </div>
            </form>
        </div>
    </div>

</div>
<!-- Container closed -->
</div>
<!-- main-content closed -->
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('#exampleModal2').on('show.bs.modal', function(event) {
            var id = $(event.relatedTarget).data('id');
            // ... (your existing code for filling other fields)

            // Find the option corresponding to the retrieved id
            var selectedOption = $("#country_ar option[value='" + id + "']");

            // Set the selected option
            selectedOption.prop('selected', true);
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var name = button.data('name')
            var country_tax = button.data('country_tax')
            var code = button.data('code')
            var exchange_rate = button.data('exchange_rate')

            var modal = $(this)
            modal.find('.modal-body #id').val(id)
            modal.find('.modal-body #name').val(name)
            modal.find('.modal-body #country_tax').val(country_tax);
            modal.find('.modal-body #code').val(code)
            modal.find('.modal-body #exchange_rate').val(exchange_rate);

        })
    });
</script>
<script>
    $('#modaldemo9').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id = button.data('id')
        var name = button.data('name')
        var country_tax = button.data('country_tax')
        var modal = $(this)
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #name').val(name);
        modal.find('.modal-body #country_tax').val(country_tax);
    })
</script>





<script src="{{ asset('js/select2.min.js') }}"></script>
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> --}}
<script>
    $('#countries').change(function() {
        var country = $(this).val();
        var parts = country.split("-");
        var id = parts[0]; // المعرف
        var name_en = parts[1]; // اسم الدولة باللغة الإنجليزية

        console.log("المعرف: " + id);
        console.log("اسم الدولة بالإنجليزية: " + name_en);
        $.ajax({
            url: 'https://restcountries.com/v2/name/' + name_en,
            method: 'GET',
            success: function(data) {
                var lat = data[0].latlng[0];
                var lng = data[0].latlng[1];
                console.log('Latitude: ' + lat + ', Longitude: ' + lng);

                $('#latitude').val(lat);
                $('#longitude').val(lng);
            },
            error: function() {
                console.log('Error retrieving data');
            }
        });
    });
</script>

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
