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

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@simonwep/pickr@1.8.0/dist/themes/nano.min.css" rel="stylesheet">
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr@1.8.0/dist/pickr.min.js"></script>
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">المنتجات /</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">
                    الألوان</span>
            </div>
        </div>

    </div>
    <!-- breadcrumb -->
@endsection
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
@section('content')
    <div class="row">

        {{-- Flash Messages for success/error --}}
        @if (session('success'))
            <div class="col-12">
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="col-12">
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        <!-- 1) Import Questions Package Form -->
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h3>استيراد الأسئلة من الملف</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('import.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="zip_file">اختر ملف الأسئلة (ZIP)</label>
                            <input type="file" name="zip_file" id="zip_file" class="form-control" accept=".zip"
                                required>
                        </div>
                        <div class="col-lg" id="type">
                            <p class="mg-b-10">نوع الاسئله</p>
                            <select class="form-control" id="type" name="type">
                                <option value="yamaat">(الليمعات)</option>
                                <option value="horror">(الرعب)</option>
                            </select>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-primary">رفع الملف</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- 2) Preview & Actions: Only shown if there is import data in session -->
        @if (session('import_temp_path') && session('questions_data'))
            @php
                $questionsData = session('questions_data');
            @endphp
            {{--  --}}
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h3>إجراءات الاستيراد</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-inline-block">
                            <!-- Confirm Import -->
                            <form action="{{ route('import.confirm') }}" method="POST" class="d-inline-block">
                                @csrf
                                <button type="submit" class="btn btn-success">تأكيد الاستيراد</button>
                            </form>

                            <!-- Cancel Import -->
                            <form action="{{ route('import.cancel') }}" method="POST" class="d-inline-block">
                                @csrf
                                <button type="submit" class="btn btn-danger">إلغاء الاستيراد</button>
                            </form>

                            <!-- (Optional) Download Updated Excel -->
                            @if (Route::has('import.download'))
                                <a href="{{ route('import.download') }}" class="btn btn-info d-inline-block">
                                    تحميل ملف Excel المحدث
                                </a>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
            <!-- Preview Table -->
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h3>عرض بيانات الاستيراد</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">





                            <table class="table text-md-nowrap" id="previewTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>النقاط</th>
                                        <th>السؤال</th>
                                        <th>الإجابة</th>
                                        <th>رابط ملف السؤال</th>
                                        <th>تحميل ملف السؤال</th>
                                        <th>رابط ملف الإجابة</th>
                                        <th>تحميل ملف الإجابة</th>
                                        <th>التصنيف</th>
                                        <th>الإسهم</th>
                                        <th>الملاحظات</th>
                                        <th>حفظ التعديلات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($questionsData as $index => $data)
                                        <tr>
                                            <!-- Display the sort order -->
                                            <td>{{ $index + 1 }}</td>

                                            <!-- Form and other columns -->
                                            <form action="{{ route('import.updateRowData') }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <!-- Identify which row is being edited -->
                                                <input type="hidden" name="row_index" value="{{ $index }}">

                                                <!-- Editable Points - Now displayed first -->
                                                <td>
                                                    <input type="number" name="points" class="form-control"
                                                        value="{{ $data['points'] }}">
                                                </td>

                                                <!-- Editable Question -->
                                                <td>
                                                    <textarea name="question" class="form-control" rows="2">{{ $data['question'] }}</textarea>
                                                </td>
                                                <td>
                                                    <textarea name="answer" class="form-control" rows="2">{{ $data['answer'] }}</textarea>
                                                </td>

                                                <!-- Question File Link Column -->
                                                <td>
                                                    @if ($data['question_link'])
                                                        <!-- If there's already a file, show a link to preview it -->
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#qModal{{ $index }}">
                                                            {{ $data['question_link'] }}
                                                        </a>
                                                        <!-- Modal to preview the file -->
                                                        <div class="modal fade" id="qModal{{ $index }}"
                                                            tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered"
                                                                role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-body">
                                                                        @php
                                                                            $tempPath = session('import_temp_path');
                                                                            $folderName = basename($tempPath);
                                                                            $link = asset(
                                                                                '/temp/' .
                                                                                    $folderName .
                                                                                    '/files/' .
                                                                                    $data['question_link'],
                                                                            );
                                                                        @endphp
                                                                        <p>تجربة الصوره</p>
                                                                        <img src="{{ $link }}" class="img-fluid"
                                                                            alt="Preview">
                                                                        <br>
                                                                        {{-- <p>تجربة الفديو</p> --}}
                                                                        <video controls class="img-fluid">
                                                                            <source src="{{ $link }}">
                                                                            Your browser does not support the video tag.
                                                                        </video>
                                                                        {{-- <br> --}}
                                                                        {{-- <p>تجربة الصوت</p>
                                                                        <audio controls class="w-100">
                                                                            <source src="{{ $link }}">
                                                                            Your browser does not support the audio
                                                                            element.
                                                                        </audio> --}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">لا يوجد ملف</span>
                                                    @endif
                                                </td>

                                                <!-- Question File Upload Column -->
                                                <td>
                                                    <input type="file" name="question_file" class="form-control-file">
                                                    @if ($data['question_link'])
                                                        <small class="text-muted d-block">سيتم استبدال الملف الحالي</small>
                                                    @endif
                                                </td>

                                                <!-- Answer File Link Column -->
                                                <td>
                                                    @if ($data['answer_link'])
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#aModal{{ $index }}">
                                                            {{ $data['answer_link'] }}
                                                        </a>
                                                        <div class="modal fade" id="aModal{{ $index }}"
                                                            tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered"
                                                                role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-body">
                                                                        @php
                                                                            $tempPath = session('import_temp_path');
                                                                            $folderName = basename($tempPath);
                                                                            $link = asset(
                                                                                '/temp/' .
                                                                                    $folderName .
                                                                                    '/files/' .
                                                                                    $data['answer_link'],
                                                                            );
                                                                        @endphp
                                                                        <p>تجربة الصوره</p>
                                                                        <img src="{{ $link }}" class="img-fluid"
                                                                            alt="Preview">
                                                                        <br>
                                                                        {{-- <p>تجربة الفديو</p> --}}
                                                                        <video controls class="img-fluid">
                                                                            <source src="{{ $link }}">
                                                                            Your browser does not support the video tag.
                                                                        </video>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">لا يوجد ملف</span>
                                                    @endif
                                                </td>

                                                <!-- Answer File Upload Column -->
                                                <td>
                                                    <input type="file" name="answer_file" class="form-control-file">
                                                    @if ($data['answer_link'])
                                                        <small class="text-muted d-block">سيتم استبدال الملف الحالي</small>
                                                    @endif
                                                </td>
                                                <!-- Category (not editable, or you can make it editable if you wish) -->
                                                <td>{{ $data['categ'] ?? '—' }}</td>
                                               <td>
                                                    <textarea name="direction" class="form-control" rows="2">{{ $data['direction'] }}</textarea>
                                                </td>
                                               <td>
                                                    <textarea name="notes" class="form-control" rows="2">{{ $data['notes'] }}</textarea>
                                                </td>
                                                <!-- Save Button -->
                                                <td>
                                                    <button type="submit" class="btn btn-success btn-sm">حفظ</button>
                                                </td>
                                            </form>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Action Buttons -->
        @endif

        <!-- 3) (Optional) Existing Data Table or any other content you wish to display -->
        {{-- 
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h3>جدول آخر أو محتوى إضافي</h3>
                </div>
                <div class="card-body">
                    <!-- Example table or content -->
                </div>
            </div>
        </div>
    --}}

        <!-- 4) (Optional) Delete Modal or other modals for your existing data -->
        {{-- 
        <div class="modal" id="deleteModal">
            <!-- ... -->
        </div>
    --}}
    </div>
@endsection

@section('js')
    <script>
        // Example: If you want to use jQuery DataTables for the preview
        $(document).ready(function() {
            $('#previewTable').DataTable();
        });

        // Example: If you have a modal for deletion
        // $('#modaldemo9').on('show.bs.modal', function(event) {
        //     var button = $(event.relatedTarget);
        //     var id = button.data('id');
        //     var name = button.data('name');
        //     var modal = $(this);
        //     modal.find('.modal-body #id').val(id);
        //     modal.find('.modal-body #name').val(name);
        // });
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
