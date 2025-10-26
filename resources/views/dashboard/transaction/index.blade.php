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
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            border-radius: 15px !important;
            color: white !important;
            transition: transform 0.3s ease;
            border: none !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stats-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;
        }

        .stats-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
        }

        .stats-card.danger {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
        }

        .stats-card.info {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%) !important;
        }

        .stats-card .card-body {
            padding: 1.5rem !important;
        }

        .stats-card h6,
        .stats-card h2 {
            color: white !important;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-success {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 20px;
        }

        .transaction-row:hover {
            background-color: #f8f9fa !important;
        }

        .amount-cell {
            font-weight: bold;
            font-size: 14px;
            color: #28a745;
        }

        .user-info {
            display: flex;
            align-items: center;
            min-width: 150px;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-left: 10px;
            flex-shrink: 0;
        }

        .user-details {
            flex-grow: 1;
            min-width: 0;
        }

        .user-details .font-weight-bold {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .filter-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .analytics-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .customer-item {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .customer-item:last-child {
            border-bottom: none;
        }

        .growth-positive {
            color: #28a745;
        }

        .growth-negative {
            color: #dc3545;
        }

        .bulk-actions {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: none;
        }

        .transaction-row.selected {
            background-color: #e3f2fd !important;
        }

        .table th {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 12px 8px;
            vertical-align: middle;
        }

        .table td {
            font-size: 0.8rem;
            padding: 10px 8px;
            vertical-align: middle;
        }
    </style>
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">المالية /</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">المعاملات المالية والإحصائيات</span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <div class="pr-1 mb-3 mb-xl-0">
                <button type="button" class="btn btn-success btn-icon ml-2" onclick="location.reload()">
                    <i class="mdi mdi-refresh"></i>
                </button>
            </div>
            <div class="pr-1 mb-3 mb-xl-0">
                <button type="button" class="btn btn-info btn-icon ml-2" onclick="exportData()">
                    <i class="mdi mdi-download"></i>
                </button>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')
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

    <!-- Main Statistics Cards Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card overflow-hidden stats-card success">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="mt-2">
                            <h6>إجمالي المعاملات</h6>
                            <h2 class="mb-0">{{ number_format($stats['total_transactions']) }}</h2>
                        </div>
                        <div class="mr-auto">
                            <i class="las la-credit-card" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card overflow-hidden stats-card success">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="mt-2">
                            <h6>المعاملات الناجحة</h6>
                            <h2 class="mb-0">{{ number_format($stats['successful_transactions']) }}</h2>
                            <small>معدل النجاح: {{ number_format($stats['success_rate'], 1) }}%</small>
                        </div>
                        <div class="mr-auto">
                            <i class="las la-check-circle" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card overflow-hidden stats-card info">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="mt-2">
                            <h6>إجمالي الإيرادات</h6>
                            <h2 class="mb-0">{{ number_format($stats['total_revenue'], 3) }} د.ك</h2>
                            <small>متوسط المعاملة: {{ number_format($stats['avg_transaction_amount'], 3) }} د.ك</small>
                        </div>
                        <div class="mr-auto">
                            <i class="las la-dollar-sign" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card overflow-hidden stats-card warning">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="mt-2">
                            <h6>إيرادات الشهر</h6>
                            <h2 class="mb-0">{{ number_format($stats['this_month_revenue'], 3) }} د.ك</h2>
                            <small class="{{ $stats['monthly_growth'] >= 0 ? 'growth-positive' : 'growth-negative' }}">
                                {{ $stats['monthly_growth'] >= 0 ? '+' : '' }}{{ number_format($stats['monthly_growth'], 1) }}%
                                من الشهر الماضي
                            </small>
                        </div>
                        <div class="mr-auto">
                            <i class="las la-chart-line" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Analytics Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card overflow-hidden stats-card danger">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="mt-2">
                            <h6>إجمالي العملاء</h6>
                            <h2 class="mb-0">{{ number_format($repeatCustomerStats['total_customers']) }}</h2>
                            <small>عملاء نشطون</small>
                        </div>
                        <div class="mr-auto">
                            <i class="las la-users" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card overflow-hidden stats-card success">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="mt-2">
                            <h6>العملاء المكررون</h6>
                            <h2 class="mb-0">{{ number_format($repeatCustomerStats['repeat_customers']) }}</h2>
                            <small>{{ number_format($repeatCustomerStats['repeat_customer_rate'], 1) }}% من العملاء</small>
                        </div>
                        <div class="mr-auto">
                            <i class="las la-redo-alt" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card overflow-hidden stats-card info">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="mt-2">
                            <h6>إجمالي الألعاب المباعة</h6>
                            <h2 class="mb-0">{{ number_format($stats['total_games_sold']) }}</h2>
                            <small>متوسط: {{ number_format($stats['avg_games_per_transaction'], 1) }} لعبة/معاملة</small>
                        </div>
                        <div class="mr-auto">
                            <i class="las la-gamepad" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card overflow-hidden stats-card warning">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="mt-2">
                            <h6>قيمة العميل المتوسطة</h6>
                            <h2 class="mb-0">{{ number_format($customerLifetimeValue['avg_lifetime_value'], 3) }} د.ك
                            </h2>
                            <small>{{ number_format($customerLifetimeValue['avg_transactions_per_customer'], 1) }}
                                معاملة/عميل</small>
                        </div>
                        <div class="mr-auto">
                            <i class="las la-star" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="filter-card">
        <h5 class="mb-3"><i class="fas fa-filter"></i> فلاتر البحث المتقدمة</h5>
        <form action="{{ route('transaction.index') }}" method="GET" id="filterForm">
            <div class="row">
                <div class="col-md-3">
                    <label>اسم المستخدم</label>
                    <input type="text" name="user_name" class="form-control" value="{{ request('user_name') }}"
                        placeholder="البحث بالاسم">
                </div>
                <div class="col-md-3">
                    <label>رقم الهاتف</label>
                    <input type="text" name="user_phone" class="form-control" value="{{ request('user_phone') }}"
                        placeholder="البحث بالهاتف">
                </div>
                <div class="col-md-3">
                    <label>طريقة الدفع</label>
                    <select name="payment_type" class="form-control">
                        <option value="">جميع الطرق</option>
                        @foreach ($paymentTypes as $type)
                            <option value="{{ $type }}" {{ request('payment_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>حالة المعاملة</label>
                    <select name="status" class="form-control">
                        <option value="">جميع الحالات</option>
                        <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>نجحت</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشلت</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-2">
                    <label>المبلغ من</label>
                    <input type="number" name="amount_from" class="form-control" value="{{ request('amount_from') }}"
                        placeholder="0.000" step="0.001">
                </div>
                <div class="col-md-2">
                    <label>المبلغ إلى</label>
                    <input type="number" name="amount_to" class="form-control" value="{{ request('amount_to') }}"
                        placeholder="999.999" step="0.001">
                </div>
                <div class="col-md-2">
                    <label>من تاريخ</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label>إلى تاريخ</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label>عدد الألعاب</label>
                    <select name="games_count" class="form-control">
                        <option value="">الكل</option>
                        @foreach ($gamesCounts as $count)
                            <option value="{{ $count }}" {{ request('games_count') == $count ? 'selected' : '' }}>
                                {{ $count }} ألعاب
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>نوع العميل</label>
                    <select name="repeat_customers" class="form-control">
                        <option value="">جميع العملاء</option>
                        <option value="0" {{ request('repeat_customers') == '0' ? 'selected' : '' }}>عملاء جدد
                        </option>
                        <option value="1" {{ request('repeat_customers') == '1' ? 'selected' : '' }}>عملاء مكررون
                        </option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3">
                    <label>الترتيب حسب</label>
                    <select name="sort_by" class="form-control">
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>تاريخ
                            الإنشاء</option>
                        <option value="amount" {{ request('sort_by') == 'amount' ? 'selected' : '' }}>المبلغ</option>
                        <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>الحالة</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>اتجاه الترتيب</label>
                    <select name="sort_direction" class="form-control">
                        <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>تنازلي</option>
                        <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>تصاعدي</option>
                    </select>
                </div>
                <div class="col-md-7 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-search"></i> بحث
                    </button>
                    <a href="{{ route('transaction.index') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-undo"></i> إعادة تعيين
                    </a>
                    <button type="button" class="btn btn-success" onclick="exportData()">
                        <i class="fas fa-download"></i> تصدير البيانات
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Charts and Analytics Row -->
    <div class="row mb-4">
        <div class="col-xl-6">
            <div class="analytics-card">
                <h5 class="mb-3"><i class="fas fa-chart-line"></i> الإيرادات الشهرية</h5>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="analytics-card">
                <h5 class="mb-3"><i class="fas fa-chart-area"></i> الإيرادات اليومية (آخر 30 يوم)</h5>
                <div class="chart-container">
                    <canvas id="dailyRevenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Customers and Repeat Customer Analysis -->
    <div class="row mb-4">
        <div class="col-xl-6">
            <div class="analytics-card">
                <h5 class="mb-3"><i class="fas fa-crown"></i> أفضل العملاء (حسب الإنفاق)</h5>
                @foreach ($topCustomers as $customer)
                    <div class="customer-item">
                        <div class="user-info">
                            <div class="user-avatar">
                                {{ substr($customer->user->name ?? 'مجهول', 0, 1) }}
                            </div>
                            <div class="user-details">
                                <div class="font-weight-bold">{{ $customer->user->name ?? 'مجهول' }}</div>
                                <small class="text-muted">{{ $customer->transaction_count }} معاملة |
                                    {{ $customer->total_games }} لعبة</small>
                            </div>
                        </div>
                        <div class="text-right">
                            <strong class="text-success">{{ number_format($customer->total_spent, 3) }} د.ك</strong><br>
                            <small class="text-muted">متوسط: {{ number_format($customer->avg_spent, 3) }} د.ك</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-xl-6">
            <div class="analytics-card">
                <h5 class="mb-3"><i class="fas fa-redo-alt"></i> العملاء الأكثر تكراراً</h5>
                @foreach ($repeatCustomerStats['most_frequent_customers'] as $customer)
                    <div class="customer-item">
                        <div class="user-info">
                            <div class="user-avatar">
                                {{ substr($customer->user->name ?? 'مجهول', 0, 1) }}
                            </div>
                            <div class="user-details">
                                <div class="font-weight-bold">{{ $customer->user->name ?? 'مجهول' }}</div>
                                <small class="text-muted">آخر شراء:
                                    {{ \Carbon\Carbon::parse($customer->last_purchase)->diffForHumans() }}</small>
                            </div>
                        </div>
                        <div class="text-right">
                            <strong class="text-primary">{{ $customer->purchase_count }} مرة</strong><br>
                            <small class="text-success">{{ number_format($customer->total_spent, 3) }} د.ك</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Payment Methods Statistics -->
    <div class="row mb-4">
        <div class="col-xl-12">
            <div class="analytics-card">
                <h5 class="mb-3"><i class="fas fa-credit-card"></i> إحصائيات طرق الدفع</h5>
                <div class="row">
                    @foreach ($payment_methods as $method)
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <h4 class="text-primary">{{ $method->payment_type ?? 'غير محدد' }}</h4>
                                <p class="mb-1"><strong>{{ number_format($method->count) }}</strong> معاملة</p>
                                <p class="mb-0 text-success"><strong>{{ number_format($method->total_amount, 3) }}
                                        د.ك</strong></p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions" id="bulkActions">
        <div class="d-flex align-items-center">
            <span class="mr-3">تم تحديد <span id="selectedCount">0</span> معاملة</span>
            <button type="button" class="btn btn-danger btn-sm" id="bulkDelete">
                <i class="fas fa-trash"></i> حذف المحدد
            </button>
        </div>
    </div>

    <!-- Main Transactions Table -->
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-list"></i> جميع المعاملات المالية
                        <span class="badge badge-light">{{ $transactions->total() }}</span>
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
                                    <th width="60">#</th>
                                    <th width="120">رقم الدفع</th>
                                    <th width="80">المبلغ</th>
                                    <th width="80">الكوبون</th>
                                    <th width="80">نوع الكوبون</th>
                                    <th width="80">قيمته</th>
                                    <th width="180">المستخدم</th>
                                    <th width="100">طريقة الدفع</th>
                                    <th width="80">الحالة</th>
                                    <th width="60">الألعاب</th>
                                    <th width="120">تاريخ المعاملة</th>
                                    <th width="100">رقم الفاتورة</th>
                                    {{-- <th width="80">العمليات</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $index => $transaction)
                                    <tr class="transaction-row" data-id="{{ $transaction->id }}">
                                        <td>
                                            <input type="checkbox" class="form-check-input transaction-checkbox"
                                                value="{{ $transaction->id }}">
                                        </td>
                                        <td><strong>{{ $transactions->firstItem() + $index }}</strong></td>
                                        <td>
                                            <span
                                                class="text-primary">{{ Str::limit($transaction->payment_id ?? 'N/A', 15) }}</span>
                                        </td>
                                        <td class="amount-cell">
                                            {{ number_format($transaction->amount, 3) }} د.ك
                                        </td>
                                        <td class="coupon-cell">
                                            {{ $transaction->couponUsage->coupon->code ?? '-' }}
                                        </td>
                                        <td class="coupon-cell">
                                            {{ $transaction->couponUsage->coupon->type ?? '-' }}
                                        </td>
                                        <td class="coupon-cell">
                                            {{ $transaction->couponUsage->coupon->value ?? '-' }}
                                        </td>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    {{ substr($transaction->user->name ?? 'مجهول', 0, 1) }}
                                                </div>
                                                <div class="user-details">
                                                    <div class="font-weight-bold"
                                                        title="{{ $transaction->user->name ?? 'مجهول' }}">
                                                        {{ Str::limit($transaction->user->name ?? 'مجهول', 15) }}
                                                    </div>
                                                    <small
                                                        class="text-muted">{{ $transaction->user->phone ?? 'غير محدد' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-info">{{ $transaction->payment_type ?? 'غير محدد' }}</span>
                                        </td>
                                        <td>
                                            <span class="status-badge status-{{ $transaction->status }}">
                                                @if ($transaction->status == 'success')
                                                    نجحت
                                                @elseif($transaction->status == 'pending')
                                                    معلقة
                                                @else
                                                    فشلت
                                                @endif
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge badge-primary">{{ $transaction->num_of_games_he_pay ?? 1 }}</span>
                                        </td>
                                        <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ Str::limit($transaction->invoice_id ?? 'N/A', 10) }}</td>
                                        {{-- <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-info" title="عرض التفاصيل"
                                                    data-toggle="modal" data-target="#viewModal{{ $transaction->id }}">
                                                    <i class="las la-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" title="حذف" data-toggle="modal"
                                                    data-target="#deleteModal{{ $transaction->id }}">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </div>
                                        </td> --}}
                                    </tr>


                                    {{-- <div class="modal fade" id="viewModal{{ $transaction->id }}" tabindex="-1"
                                        role="dialog">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-info text-white">
                                                    <h5 class="modal-title">تفاصيل المعاملة #{{ $transaction->id }}</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6><i class="fas fa-credit-card"></i> معلومات الدفع</h6>
                                                            <table class="table table-sm">
                                                                <tr>
                                                                    <td>رقم الدفع:</td>
                                                                    <td>{{ $transaction->payment_id ?? 'N/A' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>رقم المعاملة:</td>
                                                                    <td>{{ $transaction->tran_id ?? 'N/A' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>رقم التتبع:</td>
                                                                    <td>{{ $transaction->track_id ?? 'N/A' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>رقم الفاتورة:</td>
                                                                    <td>{{ $transaction->invoice_id ?? 'N/A' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>طريقة الدفع:</td>
                                                                    <td>{{ $transaction->payment_type ?? 'N/A' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>الحالة:</td>
                                                                    <td>
                                                                        <span
                                                                            class="status-badge status-{{ $transaction->status }}">
                                                                            @if ($transaction->status == 'success')
                                                                                نجحت
                                                                            @elseif($transaction->status == 'pending')
                                                                                معلقة
                                                                            @else
                                                                                فشلت
                                                                            @endif
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6><i class="fas fa-user"></i> معلومات المستخدم</h6>
                                                            <table class="table table-sm">
                                                                <tr>
                                                                    <td>الاسم:</td>
                                                                    <td>{{ $transaction->user->name ?? 'مجهول' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>الهاتف:</td>
                                                                    <td>{{ $transaction->user->phone ?? 'غير محدد' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>البريد:</td>
                                                                    <td>{{ $transaction->user->email ?? 'غير محدد' }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>المبلغ:</td>
                                                                    <td class="amount-cell">
                                                                        {{ number_format($transaction->amount, 3) }} د.ك
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>عدد الألعاب:</td>
                                                                    <td>{{ $transaction->num_of_games_he_pay ?? 1 }}</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>تاريخ المعاملة:</td>
                                                                    <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">إغلاق</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                  
                                    <div class="modal fade" id="deleteModal{{ $transaction->id }}" tabindex="-1"
                                        role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">حذف المعاملة</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ route('transaction.destroy', $transaction->id) }}"
                                                    method="post">
                                                    @method('DELETE')
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p>هل أنت متأكد من حذف هذه المعاملة؟</p>
                                                        <div class="bg-light p-3 rounded">
                                                            <strong>المستخدم:</strong>
                                                            {{ $transaction->user->name ?? 'مجهول' }}<br>
                                                            <strong>المبلغ:</strong>
                                                            {{ number_format($transaction->amount, 3) }} د.ك<br>
                                                            <strong>التاريخ:</strong>
                                                            {{ $transaction->created_at->format('Y-m-d H:i') }}
                                                        </div>
                                                        <input type="hidden" name="id"
                                                            value="{{ $transaction->id }}">
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div> --}}
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">لا توجد معاملات مطابقة للبحث</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
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
                $('.transaction-checkbox').prop('checked', this.checked);
                updateBulkActions();
            });

            // Individual checkbox change
            $('.transaction-checkbox').change(function() {
                updateBulkActions();
            });

            // Update bulk actions visibility
            function updateBulkActions() {
                const checkedBoxes = $('.transaction-checkbox:checked');
                const count = checkedBoxes.length;

                if (count > 0) {
                    $('#bulkActions').show();
                    $('#selectedCount').text(count);

                    // Highlight selected rows
                    $('.transaction-row').removeClass('selected');
                    checkedBoxes.each(function() {
                        $(this).closest('.transaction-row').addClass('selected');
                    });
                } else {
                    $('#bulkActions').hide();
                    $('.transaction-row').removeClass('selected');
                }

                // Update "select all" checkbox state
                const totalBoxes = $('.transaction-checkbox').length;
                $('#selectAll').prop('indeterminate', count > 0 && count < totalBoxes);
                $('#selectAll').prop('checked', count === totalBoxes);
            }

            // Bulk delete functionality
            $('#bulkDelete').click(function() {
                const selectedIds = [];
                $('.transaction-checkbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    Swal.fire('تنبيه', 'يرجى تحديد معاملة واحدة على الأقل للحذف', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: `سيتم حذف ${selectedIds.length} معاملة. هذا الإجراء لا يمكن التراجع عنه!`,
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
                            url: '{{ route('transaction.bulk-delete') }}',
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
        });

        // Export data function
        function exportData() {
            window.open('{{ route('transaction.export') }}', '_blank');
        }

        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const monthlyData = @json($monthly_revenue) || [];

        const labels = monthlyData.length > 0 ? monthlyData.map(item => {
            const months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
                'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
            ];
            return months[item.month - 1] + ' ' + item.year;
        }) : ['لا توجد بيانات'];

        const data = monthlyData.length > 0 ? monthlyData.map(item => parseFloat(item.total)) : [0];

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.reverse(),
                datasets: [{
                    label: 'الإيرادات الشهرية (د.ك)',
                    data: data.reverse(),
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'تطور الإيرادات خلال الأشهر الماضية'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(3) + ' د.ك';
                            }
                        }
                    }
                }
            }
        });

        // Daily Revenue Chart
        const dailyCtx = document.getElementById('dailyRevenueChart').getContext('2d');
        const dailyData = @json($dailyRevenue) || [];

        const dailyLabels = dailyData.length > 0 ? dailyData.map(item => item.date) : ['لا توجد بيانات'];
        const dailyRevenues = dailyData.length > 0 ? dailyData.map(item => parseFloat(item.revenue)) : [0];

        new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'الإيرادات اليومية (د.ك)',
                    data: dailyRevenues,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'الإيرادات اليومية لآخر 30 يوم'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(3) + ' د.ك';
                            }
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45
                        }
                    }
                }
            }
        });

        // Auto refresh every 5 minutes
        setInterval(function() {
            location.reload();
        }, 300000);
    </script>
@endsection
