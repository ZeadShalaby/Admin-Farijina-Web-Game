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
                <h4 class="content-title mb-0 my-auto">العماليات /</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">
                    الاسئله</span>
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
                                    <h4 class="card-title mb-0">إدارة الأسئلة</h4>
                                    <div class="d-flex gap-2">
                                        <!-- Add Question Button -->
                                        <a class="btn btn-primary" data-effect="effect-scale" data-toggle="modal"
                                            href="#modaldemo8">
                                            <i class="fas fa-plus-circle ml-1"></i>
                                            إضافة سؤال جديد
                                        </a>


                                        <button class="btn btn-success" type="button"
                                            onclick="document.getElementById('excel_file').click();">
                                            <i class="fas fa-file-excel ml-1"></i>
                                            استيراد من Excel لاسالة الرعب
                                        </button>
                                    </div>
                                </div>

                                <!-- Import Form -->
                                <form id="importForm" action="{{ route('questionshorror.import') }}" method="POST"
                                    enctype="multipart/form-data" class="d-none">
                                    @csrf
                                    <input type="file" id="excel_file" name="excel_file" accept=".xlsx, .xls, .csv"
                                        class="d-none" onchange="handleFileSelect(this)">
                                </form>

                                <!-- File Preview -->
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
                                            <div class="progress mt-3 d-none" id="uploadProgress">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                    role="progressbar" style="width: 0%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('question_horror.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="بحث..."
                                    value="{{ request()->search }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">بحث</button>

                                <a href="{{ route('question_horror.index', array_merge(request()->only('search'), ['all' => 1])) }}"
                                    class="btn btn-success">عرض الكل</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table text-md-nowrap" id="example1">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>السؤال</th>
                                    <th>الإجابة</th>
                                    <th>النقاط</th>
                                    <th>نوع الرابط</th>
                                    <th>ملف السؤال</th>
                                    <th>ملف الاجابه</th>
                                    <th>الحالة</th>
                                    <th>مجاني</th>
                                    <th>العمليات</th>
                                </tr>
                            </thead>
                            <tbody>

                                @php
                                    $i = 0;
                                @endphp

                                @foreach ($questions as $index => $question)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ Str::limit($question->question, 30) }}</td>
                                        <td>{{ Str::limit($question->answer, 30) }}</td>
                                        <td>{{ $question->points }}</td>
                                        <td>{{ $question->link_type }}</td>
                                    <td>
    @if ($question->link_question)
        <a href="#" data-toggle="modal" data-target="#qModal{{ $index }}">
            <div class="image-container"
                style="width: 30px; height: 30px; 
                    border-radius: 50%; 
                    overflow: hidden; 
                    border: 2px solid #28a745;
                    display: flex; 
                    align-items: center; 
                    justify-content: center;">

                @if (Str::endsWith($question->link_question, ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
                    <img src="{{ asset($question->link_question) }}" alt="Question Image"
                        style="width: 100%; height: 100%; object-fit: cover;">
                @elseif (Str::endsWith($question->link_question, ['.mp3', '.wav', '.ogg']))
                    <i class="fas fa-microphone" style="color: #28a745; font-size: 16px;"></i>
                @else
                    <i class="fas fa-play" style="color: #28a745; font-size: 16px;"></i>
                @endif
            </div>
        </a>
    @else
        <label>لا يتوفر ملف</label>
    @endif

    <div class="modal fade" id="qModal{{ $index }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content position-relative">
                <div class="modal-body text-center">
                    <div class="position-relative d-inline-block">
                        <!-- زر تحميل -->
                        <a href="{{ asset($question->link_question) }}" download
                            class="btn btn-light btn-sm position-absolute"
                            style="top: 10px; left: 10px; border-radius: 50%; z-index: 9999;">
                            <i class="fas fa-download"></i>
                        </a>

                        <!-- عرض الفيديو / الصورة / الصوت -->
                        @if (Str::endsWith($question->link_question, ['.mp4', '.webm', '.ogg']))
                            <video controls class="img-fluid" style="z-index: 1; position: relative;">
                                <source src="{{ asset($question->link_question) }}">
                                متصفحك لا يدعم عرض الفيديو.
                            </video>
                        @elseif (Str::endsWith($question->link_question, ['.mp3', '.wav', '.ogg']))
                            <audio controls style="width: 100%; z-index: 1; position: relative;">
                                <source src="{{ asset($question->link_question) }}">
                                متصفحك لا يدعم تشغيل الصوت.
                            </audio>
                        @else
                            <img src="{{ asset($question->link_question) }}" class="img-fluid" alt="Preview"
                                style="z-index: 1; position: relative;">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</td>

<td>
    @if ($question->link_answer)
        <a href="#" data-toggle="modal" data-target="#aModal{{ $index }}">
            <div class="image-container"
                style="width: 30px; height: 30px; 
                    border-radius: 50%; 
                    overflow: hidden; 
                    border: 2px solid #007bff;
                    display: flex; 
                    align-items: center; 
                    justify-content: center;">

                @if (Str::endsWith($question->link_answer, ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
                    <img src="{{ asset($question->link_answer) }}" alt="Answer Image"
                        style="width: 100%; height: 100%; object-fit: cover;">
                @elseif (Str::endsWith($question->link_answer, ['.mp3', '.wav', '.ogg']))
                    <i class="fas fa-microphone" style="color: #007bff; font-size: 16px;"></i>
                @else
                    <i class="fas fa-play" style="color: #007bff; font-size: 16px;"></i>
                @endif
            </div>
        </a>
    @else
        <label>لا يتوفر ملف</label>
    @endif

    <div class="modal fade" id="aModal{{ $index }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content position-relative">
                <div class="modal-body text-center">
                    <div class="position-relative d-inline-block">
                        <!-- زر تحميل -->
                        <a href="{{ asset($question->link_answer) }}" download
                            class="btn btn-light btn-sm position-absolute"
                            style="top: 10px; left: 10px; border-radius: 50%; z-index: 9999;">
                            <i class="fas fa-download"></i>
                        </a>

                        <!-- عرض الفيديو / الصورة / الصوت -->
                        @if (Str::endsWith($question->link_answer, ['.mp4', '.webm', '.ogg']))
                            <video controls class="img-fluid" style="z-index: 1; position: relative;">
                                <source src="{{ asset($question->link_answer) }}">
                                متصفحك لا يدعم عرض الفيديو.
                            </video>
                        @elseif (Str::endsWith($question->link_answer, ['.mp3', '.wav', '.ogg']))
                            <audio controls style="width: 100%; z-index: 1; position: relative;">
                                <source src="{{ asset($question->link_answer) }}">
                                متصفحك لا يدعم تشغيل الصوت.
                            </audio>
                        @else
                            <img src="{{ asset($question->link_answer) }}" class="img-fluid" alt="Preview"
                                style="z-index: 1; position: relative;">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</td>


                                        <td>
                                            <span class="badge badge-{{ $question->is_active ? 'success' : 'danger' }}">
                                                {{ $question->is_active ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $question->is_free ? 'success' : 'warning' }}">
                                                {{ $question->is_free ? 'مجاني' : 'مدفوع' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <a class="modal-effect btn btn-sm btn-info ml-2"
                                                    data-effect="effect-scale" data-id="{{ $question->id }}"
                                                    data-points="{{ $question->points }}"
                                                    data-question="{{ $question->question }}"
                                                    data-answer="{{ $question->answer }}"
                                                    data-link_question="{{ $question->link_question }}"
                                                    data-link_answer="{{ $question->link_answer }}"
                                                    data-link_type="{{ $question->link_type }}"
                                                    data-is_active="{{ $question->is_active }}"
                                                    data-is_free="{{ $question->is_free }}" data-toggle="modal"
                                                    href="#exampleModal2" title="تعديل">
                                                    <i class="las la-pen"></i>
                                                </a>

                                                <a class="modal-effect btn btn-sm btn-danger" data-effect="effect-scale"
                                                    data-id="{{ $question->id }}"
                                                    data-question="{{ $question->question }}" data-toggle="modal"
                                                    href="#modaldemo9" title="حذف">
                                                    <i class="las la-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach


                            </tbody>
                        </table>
                        @if ($questions instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            {{ $questions->links() }}
                        @endif

                    </div>
                </div>
            </div>
        </div>

        <!-- Add Modal -->
        <div class="modal" id="modaldemo8">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">إضافة سؤال</h6>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('question_horror.store') }}" method="post"
                            enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label>النقاط</label>
                                <input type="number" class="form-control" name="points" required>
                            </div>

                            <div class="form-group">
                                <label>السؤال</label>
                                <textarea class="form-control" name="question" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>الإجابة</label>
                                <textarea class="form-control" name="answer" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>ملف السؤال</label>
                                <input type="file" class="form-control" name="link_question">
                            </div>

                            <div class="form-group">
                                <label>ملف الإجابة</label>
                                <input type="file" class="form-control" name="link_answer">
                            </div>
                            <div class="form-group">
                                <label>نوع الرابط</label>
                                <select class="form-control" name="link_type">
                                    <option value="text">نص</option>
                                    <option value="image">صورة</option>
                                    <option value="video">فيديو</option>
                                    <option value="voice">صوت</option>
                                </select>
                            </div>


                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="is_active" value="1" checked> نشط
                                </label>
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="is_free" value="1"> مجاني
                                </label>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">تأكيد</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">تعديل السؤال</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('question_horror.update', 0) }}" method="post"
                            enctype="multipart/form-data">
                            {{ method_field('PUT') }}
                            {{ csrf_field() }}
                            <input type="hidden" name="id" id="id" value="">

                            <div class="form-group">
                                <label>النقاط</label>
                                <input type="number" class="form-control" id="points" name="points" required>
                            </div>

                            <div class="form-group">
                                <label>السؤال</label>
                                <textarea class="form-control" id="question" name="question" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>الإجابة</label>
                                <textarea class="form-control" id="answer" name="answer" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>ملف السؤال</label>
                                <input type="file" class="form-control" name="link_question">
                            </div>

                            <div class="form-group">
                                <label>ملف الإجابة</label>
                                <input type="file" class="form-control" name="link_answer">
                            </div>
                            <div class="form-group">
                                <label>نوع الرابط</label>
                                <select class="form-control" id="link_type" name="link_type">
                                    <option value="text">نص</option>
                                    <option value="image">صورة</option>
                                    <option value="video">فيديو</option>
                                    <option value="voice">صوت</option>
                                </select>
                            </div>


                            <div class="form-group">
                                <label>
                                    <input type="checkbox" id="is_active" name="is_active" value="1"> نشط
                                </label>
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="checkbox" id="is_free" name="is_free" value="1"> مجاني
                                </label>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">تحديث</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal" id="modaldemo9">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">حذف السؤال</h6>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('question_horror.destroy', 0) }}" method="post">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <div class="modal-body">
                            <p>هل أنت متأكد من عملية الحذف؟</p>
                            <input type="hidden" name="id" id="id">
                            <textarea class="form-control" id="question" readonly></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

    <script>
        function handleFileSelect(input) {
            const file = input.files[0];
            if (file) {
                document.getElementById('filePreview').classList.remove('d-none');
                document.getElementById('fileName').textContent = file.name;
            }
        }

        function submitForm() {
            const form = document.getElementById('importForm');
            const progressBar = document.getElementById('uploadProgress');
            const progressBarInner = progressBar.querySelector('.progress-bar');

            progressBar.classList.remove('d-none');

            const formData = new FormData(form);

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
                    Swal.fire({
                        icon: 'success',
                        title: 'نجاح',
                        text: response.message,
                        confirmButtonText: 'حسناً'
                    }).then((result) => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'حدث خطأ أثناء رفع الملف';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: errorMessage,
                        confirmButtonText: 'حسناً'
                    });
                    cancelUpload();
                }
            });
        }

        function cancelUpload() {
            document.getElementById('importForm').reset();
            document.getElementById('filePreview').classList.add('d-none');
            document.getElementById('uploadProgress').classList.add('d-none');
        }
        // Edit Modal
        $('#exampleModal2').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            // Set values to form fields
            modal.find('.modal-body #id').val(button.data('id'));
            modal.find('.modal-body #points').val(button.data('points'));
            modal.find('.modal-body #question').val(button.data('question'));
            modal.find('.modal-body #answer').val(button.data('answer'));
            modal.find('.modal-body #link_question').val(button.data('link_question'));
            modal.find('.modal-body #link_answer').val(button.data('link_answer'));
            modal.find('.modal-body #link_type').val(button.data('link_type'));
            modal.find('.modal-body #category_id').val(button.data('category_id'));
            modal.find('.modal-body #is_active').prop('checked', button.data('is_active') == 1);
            modal.find('.modal-body #is_free').prop('checked', button.data('is_free') == 1);
        });
        // Delete Modal
        $('#modaldemo9').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var modal = $(this);

            modal.find('#id').val(button.data('id'));
            modal.find('#question').val(button.data('question'));
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
