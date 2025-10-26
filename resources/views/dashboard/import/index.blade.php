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
                                        <th>ملف السؤال</th>
                                        <th>رفع ملف السؤال</th>
                                        <th>ملف الإجابة</th>
                                        <th>رفع ملف الإجابة</th>
                                        <th>التصنيف</th>
                                        <th>الإسهم</th>
                                        <th>الملاحظات</th>
                                        <th>حفظ التعديلات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($questionsData as $index => $data)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <form action="{{ route('import.updateRowData') }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="row_index" value="{{ $index }}">

                                                <!-- النقاط -->
                                                <td><input type="number" name="points" class="form-control"
                                                        value="{{ $data['points'] }}"></td>

                                                <!-- السؤال -->
                                                <td>
                                                    <textarea name="question" class="form-control" rows="2">{{ $data['question'] }}</textarea>
                                                </td>

                                                <!-- الإجابة -->
                                                <td>
                                                    <textarea name="answer" class="form-control" rows="2">{{ $data['answer'] }}</textarea>
                                                </td>

                                                <!-- معاينة ملف السؤال -->
                                                <td>
                                                    <div id="qPreviewContainer{{ $index }}"
                                                        style="width:50px; height:50px; border-radius:50%; border:2px solid #28a745; display:flex; align-items:center; justify-content:center; overflow:hidden; cursor:pointer;">
                                                        <i class="fas fa-plus"></i>
                                                    </div>
                                                </td>

                                                <!-- رفع ملف السؤال -->
                                                <td>
                                                    <input type="file" name="question_file"
                                                        class="form-control-file mt-1"
                                                        onchange="previewFile(this,'qPreviewContainer{{ $index }}')">
                                                </td>

                                                <!-- معاينة ملف الإجابة -->
                                                <td>
                                                    <div id="aPreviewContainer{{ $index }}"
                                                        style="width:50px; height:50px; border-radius:50%; border:2px solid #28a745; display:flex; align-items:center; justify-content:center; overflow:hidden; cursor:pointer;">
                                                        <i class="fas fa-plus"></i>
                                                    </div>
                                                </td>

                                                <!-- رفع ملف الإجابة -->
                                                <td>
                                                    <input type="file" name="answer_file"
                                                        class="form-control-file mt-1"
                                                        onchange="previewFile(this,'aPreviewContainer{{ $index }}')">
                                                </td>

                                                <!-- التصنيف -->
                                                <td>{{ $data['categ'] ?? '—' }}</td>

                                                <!-- الإسهم -->
                                                <td>
                                                    <textarea name="direction" class="form-control" rows="2">{{ $data['direction'] }}</textarea>
                                                </td>

                                                <!-- الملاحظات -->
                                                <td>
                                                    <textarea name="notes" class="form-control" rows="2">{{ $data['notes'] }}</textarea>
                                                </td>

                                                <!-- حفظ التعديلات -->
                                                <td><button type="submit" class="btn btn-success btn-sm">حفظ</button>
                                                </td>
                                            </form>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>


                            <!-- زر حفظ الكل -->
                            <div class="mt-3">
                                <button class="btn btn-primary" onclick="saveAllRows()">حفظ الكل</button>
                            </div>

                            <div class="modal fade" id="dynamicModal" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content position-relative">
                                        <!-- زر التحميل فوق المحتوى -->
                                        <a href="#" id="dynamicDownloadBtn" download
                                            class="btn btn-light btn-sm position-absolute"
                                            style="top:10px; left:10px; z-index:10; border-radius:50%;">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <div class="modal-body text-center" id="dynamicModalBody">
                                            <!-- سيتم تحديث المحتوى ديناميكياً -->
                                        </div>
                                    </div>
                                </div>
                            </div>


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

    <script>
        function previewFile(input, containerId) {
            const file = input.files[0];
            if (!file) return;

            const container = document.getElementById(containerId);
            const url = URL.createObjectURL(file);
            const ext = file.name.split('.').pop().toLowerCase();

            let iconHtml = '';
            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                iconHtml = `<img src="${url}" style="width:100%; height:100%; object-fit:cover;">`;
            } else if (['mp4', 'webm', 'ogg'].includes(ext)) {
                iconHtml = `<i class="fas fa-video"></i>`;
            } else if (['mp3', 'wav', 'ogg'].includes(ext)) {
                iconHtml = `<i class="fas fa-microphone"></i>`;
            } else {
                iconHtml = `<i class="fas fa-file"></i>`;
            }

            container.innerHTML =
                `<div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center;" onclick="openModal('${url}', '${file.name}')">${iconHtml}</div>`;
        }

        function openModal(url, filename) {
            const modalBody = document.getElementById('dynamicModalBody');
            const downloadBtn = document.getElementById('dynamicDownloadBtn');

            const ext = filename.split('.').pop().toLowerCase();
            let content = '';

            if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                content = `<img src="${url}" class="img-fluid">`;
            } else if (['mp4', 'webm', 'ogg'].includes(ext)) {
                content = `<video controls class="img-fluid"><source src="${url}"></video>`;
            } else if (['mp3', 'wav', 'ogg'].includes(ext)) {
                content = `<audio controls class="w-100"><source src="${url}"></audio>`;
            } else {
                content = `<p>ملف غير مدعوم للمعاينة</p>`;
            }

            modalBody.innerHTML = content;
            downloadBtn.href = url;
            downloadBtn.download = filename;

            $('#dynamicModal').modal('show');
        }





        // حفظ صف واحد
        function saveSingleRow(index) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.enctype = 'multipart/form-data';
            form.action = '{{ route('import.updateRowData') }}';
            form.innerHTML = '@csrf';

            const rowIndex = document.createElement('input');
            rowIndex.type = 'hidden';
            rowIndex.name = 'row_index';
            rowIndex.value = index;
            form.appendChild(rowIndex);

            ['points', 'question', 'answer', 'question_file', 'answer_file', 'direction', 'notes'].forEach(name => {
                const el = document.querySelector(`[name="${name}[${index}]"]`);
                if (el) {
                    if (el.type === 'file' && el.files.length > 0) {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(el.files[0]);
                        const input = document.createElement('input');
                        input.type = 'file';
                        input.name = name;
                        input.files = dataTransfer.files;
                        form.appendChild(input);
                    } else {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = name;
                        input.value = el.value;
                        form.appendChild(input);
                    }
                }
            });

            document.body.appendChild(form);
            form.submit();
        }

        // حفظ كل الصفوف
      function saveAllRows() {
    const form = document.createElement('form');
    form.method = 'POST';
    form.enctype = 'multipart/form-data';
    form.action = '{{ route("import.updateAllRows") }}';
    form.innerHTML = '@csrf';

    const rows = document.querySelectorAll('#previewTable tbody tr');

    rows.forEach((row, i) => {
        // حقول النصوص
        ['points','question','answer','direction','notes'].forEach(name => {
            const el = row.querySelector(`[name="${name}"]`);
            if(el){
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `${name}[${i}]`;
                input.value = el.value;
                form.appendChild(input);
            }
        });

        // الملفات
        ['question_file','answer_file'].forEach(name => {
            const fileInput = row.querySelector(`[name="${name}"]`);
            if(fileInput && fileInput.files.length > 0){
                const dataTransfer = new DataTransfer();
                for(let f=0; f<fileInput.files.length; f++){
                    dataTransfer.items.add(fileInput.files[f]);
                }
                const input = document.createElement('input');
                input.type = 'file';
                input.name = `${name}[${i}]`;
                input.files = dataTransfer.files;
                form.appendChild(input);
            }
        });
    });

    document.body.appendChild(form);
    form.submit();

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
@endsection
