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
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        form {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
        }

        .col-md-3,
        .col-md-2 {
            padding: 10px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: border 0.3s;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s, transform 0.2s;
        }

        .btn-primary {
            background-color: #007bff;
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #ffffff;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        .mt-2 {
            margin-top: 10px;
        }

        .mt-4 {
            margin-top: 20px;
        }

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

        .card {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn {
            border-radius: 5px;
            padding: 8px 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .progress {
            height: 20px;
            border-radius: 10px;
            background-color: #e9ecef;
        }

        .progress-bar {
            background-color: #28a745;
            border-radius: 10px;
        }

        #filePreview {
            transition: all 0.3s ease;
        }

        #filePreview .card {
            border: 1px dashed #28a745;
        }

        .alert {
            border-radius: 8px;
        }

        .fa-file-excel {
            margin-right: 10px;
        }

        .bg-red {
            background-color: #f8d7da;
            /* Red */
        }

        .bg-yellow {
            background-color: #fff3cd;
            /* Yellow */
        }
    </style>
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">العماليات /</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">
                    الفئات</span>
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
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="card-title mb-0">إدارة الفئات</h4>
                                    <div class="d-flex gap-2">
                                        <!-- Add Category Button -->
                                        <a class="btn btn-primary" data-effect="effect-scale" data-toggle="modal"
                                            href="#modaldemo8">
                                            <i class="fas fa-plus-circle ml-1"></i>
                                            إضافة فئة جديدة
                                        </a>

                                        <!-- Import Excel Button -->
                                        <button class="btn btn-success" type="button"
                                            onclick="document.getElementById('excel_file').click();">
                                            <i class="fas fa-file-excel ml-1"></i>
                                            استيراد من Excel
                                        </button>
                                    </div>
                                </div>

                                <!-- Import Form with Progress Bar -->
                                <form id="importForm" action="{{ route('categories.import') }}" method="POST"
                                    enctype="multipart/form-data" class="d-none">
                                    @csrf
                                    <input type="file" id="excel_file" name="excel_file" accept=".xlsx, .xls, .csv"
                                        class="d-none" onchange="handleFileSelect(this)">
                                  <input  type="hidden" name="mode" value={{$mode}}>
                                </form>

                                <!-- File Upload Preview -->
                                <div id="filePreview" class="d-none">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="fas fa-file-excel text-success fa-2x"></i>
                                                    <span id="fileName" class="ml-2"></span>
                                                </div>
                                                <div>
                                                    <button type="button" class="btn btn-success btn-sm"
                                                        onclick="submitForm()">
                                                        <i class="fas fa-upload ml-1"></i>
                                                        رفع الملف
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="cancelUpload()">
                                                        <i class="fas fa-times ml-1"></i>
                                                        إلغاء
                                                    </button>
                                                </div>
                                            </div>
                                            <!-- Progress Bar -->
                                            <div class="progress mt-3 d-none" id="uploadProgress">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                    role="progressbar" style="width: 0%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Alert Messages -->
                                @if ($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                @if (session('success'))
                                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                                        {{ session('success') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('categories.noWordCategory',$mode) }}" method="GET">
                            <div class="row">
                                <!-- Title Filter -->
                                <div class="col-md-3">
                                    <label>عنوان الفئة</label>
                                    <input type="text" name="title" class="form-control"
                                        value="{{ request('title') }}">
                                </div>

                                <!-- Type Filter -->
                                <div class="col-md-2">
                                    <label>النوع</label>
                                    <select name="type" class="form-control">
                                        <option value="">الكل</option>
                                        <option value="normal" {{ request('type') == 'normal' ? 'selected' : '' }}>عادي
                                        </option>
                                        <option value="premium" {{ request('type') == 'premium' ? 'selected' : '' }}>مميز
                                        </option>
                                    </select>
                                </div>

                                <!-- Status Filter -->
                                <div class="col-md-2">
                                    <label>الحالة</label>
                                    <select name="is_active" class="form-control">
                                        <option value="">الكل</option>
                                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط
                                        </option>
                                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط
                                        </option>
                                    </select>
                                </div>

                                <!-- Views Filter -->
                                <div class="col-md-2">
                                    <label>المشاهدات</label>
                                    <select name="views" class="form-control">
                                        <option value="">الكل</option>
                                        <option value="most_viewed"
                                            {{ request('views') == 'most_viewed' ? 'selected' : '' }}>الأكثر مشاهدة
                                        </option>
                                        <option value="least_viewed"
                                            {{ request('views') == 'least_viewed' ? 'selected' : '' }}>الأقل مشاهدة
                                        </option>
                                    </select>
                                </div>

                                <!-- Questions Count Filter -->
                                <div class="col-md-2">
                                    <label>عدد الأسئلة</label>
                                    <select name="questions_count" class="form-control">
                                        <option value="">الكل</option>
                                        <option value="200"
                                            {{ request('questions_count') == '200' ? 'selected' : '' }}>200 نقطة</option>
                                        <option value="400"
                                            {{ request('questions_count') == '400' ? 'selected' : '' }}>400 نقطة</option>
                                        <option value="600"
                                            {{ request('questions_count') == '600' ? 'selected' : '' }}>600 نقطة</option>
                                    </select>
                                </div>

                                <!-- Date Range Filter -->
                                <div class="col-md-3">
                                    <label>تاريخ الإنشاء</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ request('start_date') }}">
                                    <input type="date" name="end_date" class="form-control mt-2"
                                        value="{{ request('end_date') }}">
                                </div>

                                <!-- End At Filter -->
                                <div class="col-md-2">
                                    <label>تاريخ الانتهاء</label>
                                    <input type="date" name="end_at" class="form-control"
                                        value="{{ request('end_at') }}">
                                </div>

                                <!-- Submit Button -->
                                <div class="col-md-2 mt-4">
                                    <button type="submit" class="btn btn-primary">تصفية</button>
                                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">إعادة تعيين</a>
                                </div>
                            </div>
                        </form>


                        <div class="table-responsive">
                            <table class="table text-md-nowrap" id="example1">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>العنوان</th>
                                        <th>النوع</th>
                                        <th>الصوره</th>
                                        <th>الحالة</th>
                                        <th>قريبا</th>
                                        <th>تحت التطوير</th>
                                        <th>المشاهدات</th>
                                        <th>أسئلة 200</th>
                                        <th>أسئلة 400</th>
                                        <th>أسئلة 600</th>
                                        <th>وقت 200</th>
                                        <th>وقت 400</th>
                                        <th>وقت 600</th>
                                        <th>التحذير</th>
                                        {{-- <th>اسم المستخدم</th> --}}
                                        {{-- <th>عدد الالعاب</th> --}}
                                        {{-- <th>الالعاب المحتمله</th> --}}
                                        <th>تاريخ الإنشاء</th>
                                        <th>العمليات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $index => $category)
                                        <tr class="bg-{{ $category->warning }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $category->title }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $category->type == 'premium' ? 'success' : 'primary' }}">
                                                    {{ $category->type }}
                                                </span>
                                            </td>

                                            <td>
                                                @if ($category->image)
                                                    <a href="#" data-toggle="modal"
                                                        data-target="#qModal{{ $index }}">
                                                        <div class="image-container"
                                                            style="width: 30px; height: 30px; 
                                                                    border-radius: 50%; 
                                                                    overflow: hidden; 
                                                                    border: 2px solid #28a745; /* أخضر - تقدر تغير اللون */
                                                                    display: flex; 
                                                                    align-items: center; 
                                                                    justify-content: center;">
                                                            <img src="{{ asset($category->image) }}" alt="Category Image"
                                                                style="width: 100%; height: 100%; object-fit: cover;">
                                                        </div>
                                                    </a>
                                                @else
                                                    <label>لا يتوفر صوره</label>
                                                @endif



                                                <div class="modal fade" id="qModal{{ $index }}" tabindex="-1"
                                                    role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content position-relative">

                                                            <div class="modal-body text-center">
                                                                <!-- أيقونة التحميل -->
                                                                <a href="{{ asset($category->image) }}"
                                                                    download="category-{{ $index }}.jpg"
                                                                    class="btn btn-light btn-sm position-absolute"
                                                                    style="top: 10px; left: 10px; border-radius: 50%;">
                                                                    <i class="fas fa-download"></i>
                                                                </a>

                                                                <!-- الصورة -->
                                                                <img src="{{ asset($category->image) }}"
                                                                    class="img-fluid" alt="Preview">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>



                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $category->is_active ? 'success' : 'danger' }}">
                                                    {{ $category->is_active ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $category->is_almost ? 'warning' : 'secondary' }}">
                                                    {{ $category->is_almost ? 'قريبا' : 'لا' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $category->is_draft ? 'teal' : 'secondary' }}">
                                                    {{ $category->is_draft ? 'تحت التطوير' : 'لا' }}
                                                </span>
                                            </td>
                                            <td>{{ $category->views }}</td>
                                            <td>{{ $category->questionsByPoints(200) }}</td>
                                            <td>{{ $category->questionsByPoints(400) }}</td>
                                            <td>{{ $category->questionsByPoints(600) }}</td>
                                            <td>{{ $category->timer_200 }} ث</td>
                                            <td>{{ $category->timer_400 }} ث</td>
                                            <td>{{ $category->timer_600 }} ث</td>
                                            <td>
                                                @if ($category->warning == 'red')
                                                    <span class="badge badge-danger">تحذير: لا توجد أسئلة كافية</span>
                                                @elseif($category->warning == 'yellow')
                                                    <span class="badge badge-warning">تحذير: قرب نفاد الأسئلة</span>
                                                @else
                                                    <span class="badge badge-success">لا يوجد تحذير</span>
                                                @endif
                                            </td>
                                            {{-- <td>{{ $category->maxViewingUserName }}</td> --}}
                                            {{-- <td>{{ $category->maxGamesViewed }}</td> --}}
                                            {{-- <td>{{ $category->totalPossibleGames }}</td> --}}
                                            <td>{{ $category->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-primary btn-sm d-flex align-items-center mx-1"
                                                        data-effect="effect-scale" data-toggle="modal"
                                                        data-target="#exampleModal2" data-id="{{ $category->id }}"
                                                        data-name="{{ $category->title }}"
                                                        data-description="{{ $category->description }}"
                                                        data-type="{{ $category->type }}"
                                                        data-end_at="{{ $category->end_at ? $category->end_at : '' }}"
                                                        data-is_active="{{ $category->is_active }}"
                                                        data-is_almost="{{ $category->is_almost }}" {{-- data-image="{{ $category->image }}" --}}
                                                        data-is_draft="{{ $category->is_draft }}"
                                                        data-timer_200="{{ $category->timer_200 }}"
                                                        data-timer_400="{{ $category->timer_400 }}"
                                                        data-timer_600="{{ $category->timer_600 }}">

                                                        <i class="las la-edit ms-1"></i>
                                                        تعديل
                                                    </button>
                                                    <button class="btn btn-danger btn-sm d-flex align-items-center mx-1"
                                                        data-effect="effect-scale" data-toggle="modal"
                                                        data-target="#modaldemo9" data-id="{{ $category->id }}"
                                                        data-name="{{ $category->title }}">
                                                        <i class="las la-trash ms-1"></i>
                                                        حذف
                                                    </button>
                                                    <button class="btn btn-danger btn-sm d-flex align-items-center mx-1"
                                                        onclick="window.location='{{ route('categories.show', $category->id) }}'">
                                                        <i class="si si-chart ms-1"></i>
                                                        احصائيات
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $categories->links() }}

                        </div>
                    </div>
                </div>
            </div>
            <!-- Edit Modal -->
            <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">تعديل الفئة</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('categories.update', 0) }}" method="post"
                                enctype="multipart/form-data">
                                {{ method_field('PUT') }}
                                {{ csrf_field() }}
                                <input type="hidden" name="id" id="id" value="">
                                <div class="form-group">
                                    <label for="title">العنوان</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                <div class="form-group">
                                    <label for="description">الوصف</label>
                                    <textarea class="form-control" id="description" name="description"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="image">الصورة</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                </div>
                                <div class="form-group">
                                    <label for="type">النوع</label>
                                    <select class="form-control" id="type" name="type">
                                        <option value="normal">عادي</option>
                                        <option value="premium">مميز</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="end_at">تاريخ الانتهاء</label>
                                    <input type="datetime-local" class="form-control" id="end_at" name="end_at">
                                </div>
                              <div class="form-group">
                                    <label for="title">وقت نقاط 200</label>
                                    <input type="number" class="form-control" id="timer_200" name="timer_200" required>
                                </div>
                              <div class="form-group">
                                    <label for="title">وقت نقاط 400</label>
                                    <input type="number" class="form-control" id="timer_400" name="timer_400" required>
                                </div>
                              <div class="form-group">
                                    <label for="title">وقت نقاط 600</label>
                                    <input type="number" class="form-control" id="timer_600" name="timer_600" required>
                                </div>
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" id="is_active" name="is_active" value="1"> نشط
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" id="is_almost" name="is_almost" value="1"> قريبا
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" id="is_draft" name="is_draft" value="1"> تحت
                                        التطوير
                                    </label>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">تأكيد</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Delete Modal -->
            <div class="modal" id="modaldemo9">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content modal-content-demo">
                        <div class="modal-header">
                            <h6 class="modal-title">حذف الفئة</h6>
                            <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('categories.destroy', 0) }}" method="post">
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


            <div class="modal" id="modaldemo8">
                <div class="modal-dialog" role="document">
                    <div class="modal-content modal-content-demo">
                        <div class="modal-header">
                            <h6 class="modal-title">اضافة فئة جديدة</h6>
                            <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('categories.store') }}" method="post" enctype="multipart/form-data">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <label for="title">العنوان</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>

                                <div class="form-group">
                                    <label for="description">الوصف</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="image">الصورة</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                </div>

                                <div class="form-group">
                                    <label for="type">النوع</label>
                                    <select class="form-control" id="type" name="type">
                                        <option value="normal">عادي</option>
                                        <option value="premium">مميز</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="end_at">تاريخ الانتهاء</label>
                                    <input type="datetime-local" class="form-control" id="end_at" name="end_at">
                                </div>

                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="is_active" value="1" checked> نشط
                                    </label>
                                </div>
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="is_almost" value="1"> قريبا
                                        <input type="hidden" name="mode" value="{{ $mode }}">
                                    </label>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">تأكيد</button>
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">اغلاق</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>




        </div>

    @endsection

    @section('js')
        <script>
            $('#exampleModal2').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                // Set the fields based on the data attributes
                modal.find('#id').val(button.data('id'));
                modal.find('#title').val(button.data('name')); // use data('name') instead of data('title')
                modal.find('#description').val(button.data('description'));
                modal.find('#type').val(button.data('type'));
                // modal.find('#image').val(button.data('image'));
                modal.find('#timer_200').val(button.data('timer_200')); // use data('timer_200') instead of data('timer_200')
                modal.find('#timer_400').val(button.data('timer_400')); // use data('timer_400') instead of data('timer_400')
                modal.find('#timer_600').val(button.data('timer_600')); // use data('timer_600') instead of data('timer_600')

                modal.find('#end_at').val(button.data('end_at'));
                modal.find('#is_active').prop('checked', button.data('is_active') == 1);
                modal.find('#is_almost').prop('checked', button.data('is_almost') == 1);
                modal.find('#is_draft').prop('checked', button.data('is_draft') == 1);
            });
            $('#modaldemo9').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget)
                var id = button.data('id')
                var name = button.data('name')
                var modal = $(this)
                modal.find('.modal-body #id').val(id);
                modal.find('.modal-body #name').val(name);
            })

            function handleFileSelect(input) {
                const file = input.files[0];
                if (file) {
                    // Show file preview
                    document.getElementById('filePreview').classList.remove('d-none');
                    document.getElementById('fileName').textContent = file.name;
                }
            }

            function submitForm() {
                const form = document.getElementById('importForm');
                const progressBar = document.getElementById('uploadProgress');
                const progressBarInner = progressBar.querySelector('.progress-bar');

                // Show progress bar
                progressBar.classList.remove('d-none');

                // Create FormData object
                const formData = new FormData(form);

                // Send AJAX request
                $.ajax({
                    url: form.action,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percent = Math.round((e.loaded / e.total) * 100);
                                progressBarInner.style.width = percent + '%';
                                progressBarInner.textContent = percent + '%';
                            }
                        });
                        return xhr;
                    },
                    success: function(response) {
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الرفع بنجاح',
                            text: 'تم استيراد البيانات بنجاح',
                            confirmButtonText: 'حسناً'
                        }).then((result) => {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        // Show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: 'حدث خطأ أثناء رفع الملف',
                            confirmButtonText: 'حسناً'
                        });
                        cancelUpload();
                    }
                });
            }

            function cancelUpload() {
                // Reset form and hide preview
                document.getElementById('importForm').reset();
                document.getElementById('filePreview').classList.add('d-none');
                document.getElementById('uploadProgress').classList.add('d-none');
            }
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    @endsection
