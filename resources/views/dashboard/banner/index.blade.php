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
        /* CSS for image container */
        .image-container {
            width: 50px;
            height: 50px;
            position: relative;
            overflow: hidden;
            border-radius: 50%;
        }

        /* CSS for the circular image */
        .image-container img {
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
                <h4 class="content-title mb-0 my-auto">الاعلانات /</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">جميع
                    الاعلانات</span>
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
                        href="#modaldemo8">اضافة اعلان</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table text-md-nowrap" id="example1">
                            <thead>
                                <tr>
                                    <th class="wd-15p border-bottom-0">#</th>
                                    <th class="wd-15p border-bottom-0">اسم الاعلان</th>
                                    <th class="wd-15p border-bottom-0">صورة الاعلان</th>
                                    <th class="wd-20p border-bottom-0">تاريخ الانشاء</th>
                                    <th class="wd-20p border-bottom-0">العمليات</th>
                                </tr>
                            </thead>
                            <tbody>

                                @php
                                    $i = 0;
                                @endphp

                                @foreach ($banners as $banner)
                                    @php
                                        $i++;
                                    @endphp
                                    <tr>
                                        <td>{{ $i }}</td>
                                        </td>
                                        <td>{{ $banner->name }}</td>
                                        <td>
                                            <div class="image-container">
                                                <img src="{{ asset($banner->image) }}" alt="Avatar Image">
                                            </div>
                                        </td>
                                        <td>{{ $banner->created_at }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <div class="main-toggle main-toggle-success {{ $banner->status == true ? 'on' : '' }} btn-sm ml-2"
                                                    data-banner-id="{{ $banner->id }}">
                                                    <span></span>
                                                </div>
                                                <a class="modal-effect btn btn-sm btn-info btn-sm ml-2"
                                                    data-effect="effect-scale" data-id="{{ $banner->id }}"
                                                    data-name="{{ $banner->name }}" data-name_en="{{ $banner->name_en }}"
                                                    data-status="{{ $banner->status }}"
                                                    data-arrange="{{ $banner->arrange }}"
                                                    data-banner_url="{{ $banner->banner_url }}"
                                                    data-image="{{ $banner->image }}" data-toggle="modal"
                                                    href="#exampleModal2" title="تعديل"><i class="las la-pen">

                                                    </i>
                                                </a>



                                                <a class="modal-effect btn btn-sm btn-danger" data-effect="effect-scale"
                                                    data-id="{{ $banner->id }}" data-name="{{ $banner->name }}"
                                                    data-toggle="modal" href="#modaldemo9" title="حذف"><i
                                                        class="las la-trash"></i></a>
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

        <div class="modal" id="modaldemo8">
            <div class="modal-dialog" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">اضافة اعلان</h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('banners.store') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label for="exampleInputEmail1">اسم الاعلان باللغه العربيه</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            {{-- <div class="form-group">
                                <label for="exampleInputEmail1">اسم الاعلان باللغه الانجليزيه</label>
                                <input type="text" class="form-control" id="name_en" name="name_en" required>
                            </div> --}}

                            <div class="form-group">
                                <label for="exampleInputEmail1">اختار نوع الافته</label>
                                <p class="mg-b-10"></p>
                                <select id="type" name="type" class="form-control SlectBox"
                                    onchange="functionName">
                                    <option value="banner">لافتات</option>
                                    <option value="ads">اعلان</option>
                                </select>
                            </div>


                            <div class="form-group">
                                <label for="exampleInputEmail1">رابط البانر</label>
                                <input type="text" class="form-control" id="banner_url" name="banner_url" required>
                            </div>
                            {{-- <div class="form-group" id="subcat" style="display: display">
                                <label for="exampleInputEmail1">اختار نوع المنتجات</label>
                                <p class="mg-b-10"></p>
                                <select id="type" name="type" class="form-control SlectBox"
                                    onchange="functionName">
                                    <option value="flash_sale"> تخفيضات (falsh sale) </option>
                                    <option value="top_rating">اعلي المنتجات تقيممأ (top rating)</option>
                                    <option value="top_rating">قسم فرعي</option>
                                    <option value="top_rating">المنتجات المضافه حديثا</option>
                                </select>
                            </div>

                            <div class="form-group" id="subcat" style="display: display">
                                <p class="mg-b-10"></p>
                                <select id="sub_categories_id" name="sub_categories_id" class="form-control SlectBox">
                                    <option value="">اختار معرف القسم الفرعي</option>
                                    @foreach ($SubCategorys as $item)
                                        <option value="{{ $item->id }}">{{ $item->name_ar }}</option>
                                    @endforeach


                                </select>
                            </div> --}}


                            <div class="form-group">
                                <label for="exampleInputEmail1">ترتيب الاعلان </label>
                                <input type="number" class="form-control" id="arrange" name="arrange">
                            </div>
                            <div class="form-group">
                                <label for="image">تحميل صوره للاعلان</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="image" name="image"
                                        required>
                                    <label class="custom-file-label" for="image">اختار صوره</label>
                                </div>
                                <img src="#" id="preview"
                                    style="display: none; max-width: 200px; max-height: 200px;">
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
                        <h5 class="modal-title" id="exampleModalLabel">تعديل الاعلان</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('banners.update') }}" method="post" enctype="multipart/form-data">
                            {{ method_field('PUT') }}
                            {{ csrf_field() }}
                            <input type="hidden" name="id" id="id" value="">

                            {{-- <input type="hidden" name="status" id="status" value=""> --}}

                            <div class="form-group">
                                <label for="exampleInputEmail1">اسم الاعلان</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            {{-- <div class="form-group">
                                <label for="exampleInputEmail1">اسم الاعلان باللغه الانجليزيه</label>
                                <input type="text" class="form-control" id="name_en" name="name_en">
                            </div> --}}
                            <div class="form-group">
                                <label for="exampleInputEmail1">ترتيب الاعلان </label>
                                <input type="number" class="form-control" id="arrange" name="arrange">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">رابط البانر</label>
                                <input type="text" class="form-control" id="banner_url" name="banner_url" required>
                            </div>
                            <div class="form-group">
                                <label for="recipient-name" class="col-form-label">صوره الاعلان</label>
                                <input class="form-control" name="image" id="image" type="file"
                                    onchange="displaySelectedImage(event)">
                                <img src="image" id="preview-image" class="img-thumbnail"
                                    style="width: 100px; height: 100px;">
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
                        <h6 class="modal-title">حذف الاعلان</h6>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('banners.destroy', 0) }}" method="post">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <p>هل أنت متأكد من عملية الحذف؟</p><br>
                            <input type="hidden" name="id" id="id" value="">
                            <input class="form-control" name="name" id="name" type="text" readonly>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                            <button type="submit" class="btn btn-danger">تأكيد</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection

@section('js')
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
        const discountField = document.getElementById('type');
        const discountTimeFields = document.getElementById('subcat');

        // دالة لإظهار حقول الوقت
        function showDiscountTimeFields() {
            discountTimeFields.style.display = 'block';
        }

        // دالة لإخفاء حقول الوقت
        function hideDiscountTimeFields() {
            discountTimeFields.style.display = 'none';
        }
        let x = false;
        // ربط حدث تغيير حقل الخصم بدالتي الإظهار والإخفاء
        discountField.addEventListener('change', function() {
            // إذا كان القيمة غير صفرية، أظهر حقول الوقت، وإلا أخفها
            if (x) {
                showDiscountTimeFields();
                x = false;
            } else {
                hideDiscountTimeFields();
                x = true;
            }
        });
    </script>
    <script>
        $('#modaldemo9').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var id = button.data('id')
            var name = button.data('name')
            var modal = $(this)
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #name').val(name);
        })
    </script>


    {{-- <script>
        function imageImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#image').attr('src', e.target.result).show();
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script> --}}

    <script>
        $(document).ready(function() {
            $('.main-toggle').on('click', function() {
                $(this).toggleClass('on');
                var isToggleOn = $(this).hasClass('on');
                var url = '{{ route('banners.update-status') }}';
                var categoryId = $(this).data('banner-id');
                // Retrieve the CSRF token value from the meta tag
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        isToggleOn: isToggleOn,
                        categoryId: categoryId
                    },
                    success: function(response) {
                        console.log(response);
                        // Handle the success response
                    },
                    error: function(error) {
                        console.log(error);
                        // Handle the error response
                    }
                });
            });
        });
    </script>

    <script>
        function displaySelectedImage(event) {
            const fileInput = event.target;
            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    const previewImage = document.getElementById('preview-image');
                    previewImage.src = e.target.result;
                };

                reader.readAsDataURL(fileInput.files[0]);
            }
        }
        document.querySelector("#image").addEventListener("change", function() {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.querySelector("#preview").setAttribute("src", e.target.result);
                document.querySelector("#preview").style.display = "block";
            };
            reader.readAsDataURL(this.files[0]);
        });

        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#preview').attr('src', e.target.result).show();
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    <script>
        const appUrl = "{{ url('/') }}";
        $(document).ready(function() {
            $('#exampleModal2').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget)
                var id = button.data('id')
                var name = button.data('name')
                var name_en = button.data('name_en')
                var banner_url = button.data('banner_url')
                var arrange = button.data('arrange')
                var status = button.data('status')
                var image = button.data('image')
                var modal = $(this)
                modal.find('.modal-body #id').val(id)
                modal.find('.modal-body #name').val(name)
                modal.find('.modal-body #preview-image').attr('src', appUrl + "/" + image)
                modal.find('.modal-body #name_en').val(name_en)
                modal.find('.modal-body #banner_url').val(banner_url)
                modal.find('.modal-body #arrange').val(arrange)
                modal.find('.modal-body #status').val(status)
            })
        });
    </script>
@endsection
