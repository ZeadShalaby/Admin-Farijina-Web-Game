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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .stats-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stats-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stats-card.danger {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .filter-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .table-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }

        .badge-custom {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .btn-action {
            margin: 1px;
            border-radius: 15px;
            padding: 4px 8px;
            font-size: 0.8rem;
        }

        .chart-container {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .bulk-actions {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: none;
        }

        .complaint-row.selected {
            background-color: #e3f2fd !important;
        }

        .table th {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 12px 8px;
            vertical-align: middle;
            border-top: none;
        }

        .table td {
            font-size: 0.8rem;
            padding: 10px 8px;
            vertical-align: middle;
        }

        .text-truncate-custom {
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .user-details {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.8rem;
            margin: 0;
            line-height: 1.2;
        }

        .user-contact {
            font-size: 0.7rem;
            color: #6c757d;
            margin: 0;
            line-height: 1.2;
        }

        .question-info {
            max-width: 200px;
        }

        .question-text {
            font-size: 0.8rem;
            line-height: 1.3;
            margin-bottom: 4px;
        }

        .question-meta {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }
    </style>
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">إدارة النظام /</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">الشكاوى والاستفسارات</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')
    <!-- Alert Messages -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
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

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stats-card">
                <div class="stats-number">{{ $statistics['total'] }}</div>
                <div class="stats-label">
                    <i class="fas fa-comments"></i> إجمالي الشكاوى
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stats-card success">
                <div class="stats-number">{{ $statistics['this_month'] }}</div>
                <div class="stats-label">
                    <i class="fas fa-calendar-month"></i> شكاوى هذا الشهر
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stats-card warning">
                <div class="stats-number">{{ $statistics['today'] }}</div>
                <div class="stats-label">
                    <i class="fas fa-calendar-day"></i> شكاوى اليوم
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stats-card info">
                <div class="stats-number">{{ $statistics['with_users'] }}</div>
                <div class="stats-label">
                    <i class="fas fa-users"></i> مع معلومات المستخدم
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Statistics -->
    <div class="row mb-4">
        <div class="col-xl-6">
            <div class="chart-container">
                <h5 class="mb-3"><i class="fas fa-chart-bar"></i> الموضوعات الأكثر شيوعاً</h5>
                @if ($statistics['common_subjects']->count() > 0)
                    @foreach ($statistics['common_subjects'] as $subject)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $subject->subject }}</span>
                            <span class="badge badge-primary">{{ $subject->count }}</span>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">لا توجد بيانات</p>
                @endif
            </div>
        </div>
        <div class="col-xl-6">
            <div class="chart-container">
                <h5 class="mb-3"><i class="fas fa-question-circle"></i> الشكاوى حسب نوع السؤال</h5>
                @if ($statistics['by_question_type']->count() > 0)
                    @foreach ($statistics['by_question_type'] as $type)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $type->type }}</span>
                            <span class="badge badge-info">{{ $type->count }}</span>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">لا توجد بيانات</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <h5 class="mb-3"><i class="fas fa-filter"></i> فلاتر البحث</h5>
        <form action="{{ route('contactus') }}" method="GET" id="filterForm">
            <div class="row">
                <div class="col-md-3">
                    <label>اسم المرسل</label>
                    <input type="text" name="name" class="form-control" value="{{ request('name') }}"
                        placeholder="البحث بالاسم">
                </div>
                <div class="col-md-3">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" value="{{ request('email') }}"
                        placeholder="البحث بالبريد">
                </div>
                <div class="col-md-3">
                    <label>رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control" value="{{ request('phone') }}"
                        placeholder="البحث بالهاتف">
                </div>
                <div class="col-md-3">
                    <label>الموضوع</label>
                    <input type="text" name="subject" class="form-control" value="{{ request('subject') }}"
                        placeholder="البحث بالموضوع">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3">
                    <label>نوع السؤال</label>
                    <select name="question_type" class="form-control">
                        <option value="">جميع الأنواع</option>
                        @foreach ($questionTypes as $type)
                            <option value="{{ $type }}"
                                {{ request('question_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>الفئة</label>
                    <select name="category_id" class="form-control">
                        <option value="">جميع الفئات</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>نقاط السؤال</label>
                    <select name="question_points" class="form-control">
                        <option value="">جميع النقاط</option>
                        @foreach ($questionPoints as $points)
                            <option value="{{ $points }}"
                                {{ request('question_points') == $points ? 'selected' : '' }}>
                                {{ $points }} نقطة
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>مع معلومات المستخدم</label>
                    <select name="has_user" class="form-control">
                        <option value="">الكل</option>
                        <option value="1" {{ request('has_user') == '1' ? 'selected' : '' }}>نعم</option>
                        <option value="0" {{ request('has_user') == '0' ? 'selected' : '' }}>لا</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3">
                    <label>مع سؤال</label>
                    <select name="has_question" class="form-control">
                        <option value="">الكل</option>
                        <option value="1" {{ request('has_question') == '1' ? 'selected' : '' }}>نعم</option>
                        <option value="0" {{ request('has_question') == '0' ? 'selected' : '' }}>لا</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>من تاريخ</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label>إلى تاريخ</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label>الترتيب</label>
                    <select name="sort_by" class="form-control">
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>تاريخ
                            الإنشاء</option>
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>الاسم</option>
                        <option value="subject" {{ request('sort_by') == 'subject' ? 'selected' : '' }}>الموضوع</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> بحث
                    </button>
                    <a href="{{ route('contactus') }}" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> إعادة تعيين
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions" id="bulkActions">
        <div class="d-flex align-items-center">
            <span class="mr-3">تم تحديد <span id="selectedCount">0</span> عنصر</span>
            <button type="button" class="btn btn-danger btn-sm" id="bulkDelete">
                <i class="fas fa-trash"></i> حذف المحدد
            </button>
        </div>
    </div>

    <!-- Complaints Table -->
    <div class="table-card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> قائمة الشكاوى والاستفسارات
                <span class="badge badge-light">{{ $complaints->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th width="50">#</th>
                            <th width="180">معلومات المرسل</th>
                            <th width="150">المستخدم المرتبط</th>
                            <th width="100">الموضوع</th>
                            <th width="200">الرسالة</th>
                            <th width="220">السؤال المرتبط</th>
                            <th width="80">التاريخ</th>
                            <th width="120">العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($complaints as $complaint)
                            <tr class="complaint-row" data-id="{{ $complaint->id }}">
                                <td>
                                    <input type="checkbox" class="form-check-input complaint-checkbox"
                                        value="{{ $complaint->id }}">
                                </td>
                                <td><strong>{{ $complaint->id }}</strong></td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-details">
                                            <p class="user-name">{{ $complaint->name }}</p>
                                            @if ($complaint->email)
                                                <p class="user-contact">
                                                    <i class="fas fa-envelope"></i>
                                                    {{ Str::limit($complaint->email, 20) }}
                                                </p>
                                            @endif
                                            @if ($complaint->phone)
                                                <p class="user-contact">
                                                    <i class="fas fa-phone"></i> {{ $complaint->phone }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $userInfo = $complaint->userInfo;
                                    @endphp
                                    @if ($userInfo)
                                        <div class="user-info">
                                            @if ($userInfo->image)
                                                <img src="{{ $userInfo->image }}" alt="User" class="user-avatar">
                                            @else
                                                <div
                                                    class="user-avatar bg-primary d-flex align-items-center justify-content-center text-white">
                                                    {{ substr($userInfo->name, 0, 1) }}
                                                </div>
                                            @endif
                                            <div class="user-details">
                                                <p class="user-name">{{ Str::limit($userInfo->name, 15) }}</p>
                                                <p class="user-contact">{{ $userInfo->username }}</p>
                                                <span
                                                    class="badge badge-{{ $userInfo->status ? 'success' : 'danger' }} badge-custom">
                                                    {{ $userInfo->status ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted small">
                                            <i class="fas fa-user-slash"></i><br>غير مرتبط
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span
                                        class="badge badge-info badge-custom">{{ Str::limit($complaint->subject, 15) }}</span>
                                </td>
                                <td>
                                    <div class="text-truncate-custom" title="{{ $complaint->message }}">
                                        {{ Str::limit($complaint->message, 80) }}
                                    </div>
                                </td>
                                <td>
                                    @if ($complaint->question)
                                        <div class="question-info">
                                            <div class="question-text text-truncate-custom"
                                                title="{{ $complaint->question->question }}">
                                                <strong>{{ Str::limit($complaint->question->question, 60) }}</strong>
                                            </div>
                                            <div class="question-meta">
                                                <span
                                                    class="badge badge-secondary badge-custom">{{ $complaint->question->type }}</span>
                                                <span
                                                    class="badge badge-warning badge-custom">{{ $complaint->question->points }}ن</span>
                                                @if ($complaint->question->category)
                                                    <span
                                                        class="badge badge-primary badge-custom">{{ Str::limit($complaint->question->category->title, 10) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-warning small text-center">
                                            <i class="fas fa-exclamation-triangle"></i><br>
                                            @if ($complaint->question_id)
                                                رقم: {{ $complaint->question_id }}<br>
                                                <small>(محذوف)</small>
                                            @else
                                                لا يوجد سؤال
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <small>{{ $complaint->created_at->format('m-d') }}</small><br>
                                    <small class="text-muted">{{ $complaint->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group-vertical" role="group">
                                        <button type="button" class="btn btn-info btn-action" data-toggle="modal"
                                            data-target="#viewModal{{ $complaint->id }}" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if ($complaint->question)
                                            <button type="button" class="btn btn-warning btn-action" data-toggle="modal"
                                                data-target="#editQuestionModal" data-id="{{ $complaint->question->id }}"
                                                data-points="{{ $complaint->question->points }}"
                                                data-question="{{ $complaint->question->question }}"
                                                data-answer="{{ $complaint->question->answer }}"
                                                data-link_type="{{ $complaint->question->link_type }}"
                                                data-category_id="{{ $complaint->question->category_id }}"
                                                data-is_active="{{ $complaint->question->is_active }}"
                                                data-is_free="{{ $complaint->question->is_free }}" title="تعديل السؤال">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-danger btn-action" data-toggle="modal"
                                            data-target="#deleteModal{{ $complaint->id }}" title="حذف الشكوى">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- View Modal -->
                            <div class="modal fade" id="viewModal{{ $complaint->id }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title">تفاصيل الشكوى #{{ $complaint->id }}</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6><i class="fas fa-user"></i> معلومات المرسل</h6>
                                                    <p><strong>الاسم:</strong> {{ $complaint->name }}</p>
                                                    <p><strong>البريد:</strong> {{ $complaint->email ?? 'غير محدد' }}</p>
                                                    <p><strong>الهاتف:</strong> {{ $complaint->phone ?? 'غير محدد' }}</p>
                                                    <p><strong>الموضوع:</strong> {{ $complaint->subject }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    @if ($complaint->userInfo)
                                                        <h6><i class="fas fa-user-circle"></i> معلومات المستخدم المرتبط
                                                        </h6>
                                                        <p><strong>الاسم:</strong> {{ $complaint->userInfo->name }}</p>
                                                        <p><strong>اسم المستخدم:</strong>
                                                            {{ $complaint->userInfo->username }}</p>
                                                        <p><strong>النوع:</strong> {{ $complaint->userInfo->type }}</p>
                                                        <p><strong>الحالة:</strong>
                                                            <span
                                                                class="badge badge-{{ $complaint->userInfo->status ? 'success' : 'danger' }}">
                                                                {{ $complaint->userInfo->status ? 'نشط' : 'غير نشط' }}
                                                            </span>
                                                        </p>
                                                    @else
                                                        <h6><i class="fas fa-user-slash"></i> لا يوجد مستخدم مرتبط</h6>
                                                    @endif
                                                </div>
                                            </div>
                                            <hr>
                                            <h6><i class="fas fa-message"></i> الرسالة</h6>
                                            <p class="bg-light p-3 rounded">{{ $complaint->message }}</p>

                                            @if ($complaint->question)
                                                <hr>
                                                <h6><i class="fas fa-question-circle"></i> السؤال المرتبط</h6>
                                                <div class="bg-light p-3 rounded">
                                                    <p><strong>السؤال:</strong> {{ $complaint->question->question }}</p>
                                                    <p><strong>الإجابة:</strong> {{ $complaint->question->answer }}</p>
                                                    <p><strong>النوع:</strong> {{ $complaint->question->type }}</p>
                                                    <p><strong>النقاط:</strong> {{ $complaint->question->points }}</p>
                                                    @if ($complaint->question->category)
                                                        <p><strong>الفئة:</strong>
                                                            {{ $complaint->question->category->title }}</p>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">إغلاق</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $complaint->id }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">حذف الشكوى</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <form action="{{ route('contactus.destroy') }}" method="post">
                                            @method('DELETE')
                                            @csrf
                                            <div class="modal-body">
                                                <p>هل أنت متأكد من حذف هذه الشكوى؟</p>
                                                <div class="bg-light p-3 rounded">
                                                    <strong>المرسل:</strong> {{ $complaint->name }}<br>
                                                    <strong>الموضوع:</strong> {{ $complaint->subject }}
                                                </div>
                                                <input type="hidden" name="id" value="{{ $complaint->id }}">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">إلغاء</button>
                                                <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">لا توجد شكاوى مطابقة للبحث</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $complaints->links() }}
        </div>
    </div>

    <!-- Edit Question Modal -->
    <div class="modal fade" id="editQuestionModal" tabindex="-1" role="dialog"
        aria-labelledby="editQuestionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="editQuestionModalLabel">تعديل السؤال</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('questions.update', 0) }}" method="post" enctype="multipart/form-data">
                        {{ method_field('PUT') }}
                        {{ csrf_field() }}
                        <input type="hidden" name="id" id="edit_question_id" value="">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>النقاط</label>
                                    <input type="number" class="form-control" id="edit_points" name="points" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>نوع الرابط</label>
                                    <select class="form-control" id="edit_link_type" name="link_type">
                                        <option value="text">نص</option>
                                        <option value="image">صورة</option>
                                        <option value="video">فيديو</option>
                                        <option value="voice">صوت</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>السؤال</label>
                            <textarea class="form-control" id="edit_question" name="question" rows="3" required></textarea>
                        </div>

                        <div class="form-group">
                            <label>الإجابة</label>
                            <textarea class="form-control" id="edit_answer" name="answer" rows="3" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>ملف السؤال</label>
                                    <input type="file" class="form-control" name="link_question">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>ملف الإجابة</label>
                                    <input type="file" class="form-control" name="link_answer">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>الفئة</label>
                            <select class="form-control" id="edit_category_id" name="category_id" required>
                                <option value="">اختر الفئة</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check"> <label class="form-check-label"
                                            for="edit_is_active">نشط</label>

                                        <input type="checkbox" class="form-check-input" id="edit_is_active"
                                            name="is_active" value="1">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-check"> <label class="form-check-label"
                                            for="edit_is_free">مجاني</label>

                                        <input type="checkbox" class="form-check-input" id="edit_is_free" name="is_free"
                                            value="1">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">تحديث السؤال</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Most Problematic Questions -->
    @if ($statistics['problematic_questions']->count() > 0)
        <div class="chart-container mt-4">
            <h5 class="mb-3"><i class="fas fa-exclamation-triangle text-warning"></i> الأسئلة الأكثر إثارة للشكاوى</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>السؤال</th>
                            <th>النوع</th>
                            <th>عدد الشكاوى</th>
                            <th>العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($statistics['problematic_questions'] as $question)
                            <tr>
                                <td>{{ Str::limit($question->question, 80) }}</td>
                                <td><span class="badge badge-secondary">{{ $question->type }}</span></td>
                                <td><span class="badge badge-danger">{{ $question->complaint_count }}</span></td>
                                <td>
                                    @if ($question->type == 'horror')
                                        <a href="{{ route('question_horror.index', ['question_id' => $question->id]) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                    @else
                                        <a href="{{ route('questions.index', ['question_id' => $question->id]) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
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
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            // Select All functionality
            $('#selectAll').change(function() {
                $('.complaint-checkbox').prop('checked', this.checked);
                updateBulkActions();
            });

            // Individual checkbox change
            $('.complaint-checkbox').change(function() {
                updateBulkActions();
            });

            // Update bulk actions visibility
            function updateBulkActions() {
                const checkedBoxes = $('.complaint-checkbox:checked');
                const count = checkedBoxes.length;

                if (count > 0) {
                    $('#bulkActions').show();
                    $('#selectedCount').text(count);

                    // Highlight selected rows
                    $('.complaint-row').removeClass('selected');
                    checkedBoxes.each(function() {
                        $(this).closest('.complaint-row').addClass('selected');
                    });
                } else {
                    $('#bulkActions').hide();
                    $('.complaint-row').removeClass('selected');
                }

                // Update "select all" checkbox state
                const totalBoxes = $('.complaint-checkbox').length;
                $('#selectAll').prop('indeterminate', count > 0 && count < totalBoxes);
                $('#selectAll').prop('checked', count === totalBoxes);
            }

            // Bulk delete functionality
            $('#bulkDelete').click(function() {
                const selectedIds = [];
                $('.complaint-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    Swal.fire('تنبيه', 'يرجى تحديد عنصر واحد على الأقل للحذف', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: `سيتم حذف ${selectedIds.length} شكوى. هذا الإجراء لا يمكن التراجع عنه!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذف!',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send AJAX request for bulk delete
                        $.ajax({
                            url: '{{ route('contactus.bulk-delete') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ids: selectedIds
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire('تم!', response.message, 'success').then(
                                    () => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire('خطأ!', response.message, 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('خطأ!', 'حدث خطأ أثناء الحذف', 'error');
                            }
                        });
                    }
                });
            });

            // Edit Question Modal
            $('#editQuestionModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);

                // Set the form values
                modal.find('#edit_question_id').val(button.data('id'));
                modal.find('#edit_points').val(button.data('points'));
                modal.find('#edit_question').val(button.data('question'));
                modal.find('#edit_answer').val(button.data('answer'));
                modal.find('#edit_link_type').val(button.data('link_type'));
                modal.find('#edit_category_id').val(button.data('category_id'));
                modal.find('#edit_is_active').prop('checked', button.data('is_active') == 1);
                modal.find('#edit_is_free').prop('checked', button.data('is_free') == 1);
            });
        });
    </script>
@endsection
