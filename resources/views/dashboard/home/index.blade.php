@extends('layouts.master')
@section('css')
    <!--  Owl-carousel css-->
    <link href="{{ URL::asset('assets/plugins/owl-carousel/owl.carousel.css') }}" rel="stylesheet" />
    <!-- Maps css -->
    <link href="{{ URL::asset('assets/plugins/jqvmap/jqvmap.min.css') }}" rel="stylesheet">
    <!-- Chart.js -->
    <link href="{{ URL::asset('assets/plugins/chart.js/Chart.bundle.min.css') }}" rel="stylesheet">
    <style>
        .stats-card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
        }
        .growth-positive {
            color: #28a745;
        }
        .growth-negative {
            color: #dc3545;
        }
        .chart-container {
            position: relative;
            height: 400px;
            margin-bottom: 30px;
        }
        .top-list {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
        }
        .top-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .top-list-item:last-child {
            border-bottom: none;
        }
        .percentage-bar {
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 5px;
        }
        .percentage-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.3s ease;
        }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">لوحة التحكم الرئيسية</h2>
                <p class="mg-b-0">نظرة شاملة على إحصائيات النظام</p>
            </div>
        </div>
        <div class="main-dashboard-header-right">
            <div class="d-flex">
                <div class="justify-content-center">
                    <div class="chartjs-size-monitor">
                        <div class="chartjs-size-monitor-expand">
                            <div class=""></div>
                        </div>
                        <div class="chartjs-size-monitor-shrink">
                            <div class=""></div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="me-3">آخر تحديث: {{ now()->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /breadcrumb -->
@endsection
@section('content')
    <!-- Main Statistics Cards -->
    <div class="row row-sm">
        <!-- Users Card -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card stats-card overflow-hidden sales-card bg-primary-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">إجمالي المستخدمين</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ number_format($totalUsers) }}</h4>
                                <p class="mb-0 tx-12 text-white op-7">
                                    نشط: {{ number_format($activeUsers) }} | 
                                    مميز: {{ number_format($premiumUsers) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <span id="compositeline" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
            </div>
        </div>

        <!-- Games Card -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card stats-card overflow-hidden sales-card bg-success-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">إجمالي الألعاب</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ number_format($totalGames) }}</h4>
                                <p class="mb-0 tx-12 text-white op-7">
                                    مكتملة: {{ number_format($completedGames) }} | 
                                    مجانية: {{ number_format($freeGames) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <span id="compositeline2" class="pt-1">3,2,4,6,12,14,8,7,14,16,12,7,8,4,3,2,2,5,6,7</span>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card stats-card overflow-hidden sales-card bg-warning-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">إجمالي الإيرادات</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ number_format($totalRevenue, 2) }} د.ك</h4>
                                <p class="mb-0 tx-12 text-white op-7">
                                    هذا الشهر: {{ number_format($monthlyRevenue, 2) }} د.ك
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <span id="compositeline3" class="pt-1">5,10,5,20,22,12,15,18,20,15,8,12,22,5,10,12,22,15,16,10</span>
            </div>
        </div>

        <!-- Questions Card -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="card stats-card overflow-hidden sales-card bg-danger-gradient">
                <div class="pl-3 pt-3 pr-3 pb-2">
                    <div class="">
                        <h6 class="mb-3 tx-12 text-white">إجمالي الأسئلة</h6>
                    </div>
                    <div class="pb-0 mt-0">
                        <div class="d-flex">
                            <div class="">
                                <h4 class="tx-20 font-weight-bold mb-1 text-white">{{ number_format($totalQuestions) }}</h4>
                                <p class="mb-0 tx-12 text-white op-7">
                                    نشطة: {{ number_format($activeQuestions) }} | 
                                    مشاهدات: {{ number_format($totalQuestionViews) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <span id="compositeline4" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
            </div>
        </div>
    </div>

    <!-- Secondary Statistics -->
    <div class="row row-sm">
        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="counter-status">
                        <h5 class="mb-2">{{ number_format($totalCategories) }}</h5>
                        <p class="text-muted mb-0">الفئات</p>
                        <small class="text-success">{{ number_format($activeCategories) }} نشطة</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="counter-status">
                        <h5 class="mb-2">{{ number_format($totalTransactions) }}</h5>
                        <p class="text-muted mb-0">المعاملات</p>
                        <small class="text-success">{{ number_format($successfulTransactions) }} ناجحة</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="counter-status">
                        <h5 class="mb-2">{{ number_format($totalCoupons) }}</h5>
                        <p class="text-muted mb-0">الكوبونات</p>
                        <small class="text-success">{{ number_format($activeCoupons) }} نشطة</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="counter-status">
                        <h5 class="mb-2">{{ number_format($totalInquiries) }}</h5>
                        <p class="text-muted mb-0">الاستفسارات</p>
                        <small class="text-muted">تواصل العملاء</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="counter-status">
                        <h5 class="mb-2">{{ number_format($conversionRate, 1) }}%</h5>
                        <p class="text-muted mb-0">معدل التحويل</p>
                        <small class="text-info">مجاني إلى مميز</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 col-sm-12">
            <div class="card stats-card">
                <div class="card-body text-center">
                    <div class="counter-status">
                        <h5 class="mb-2">{{ number_format($gameCompletionRate, 1) }}%</h5>
                        <p class="text-muted mb-0">إكمال الألعاب</p>
                        <small class="text-info">معدل الإنجاز</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Growth Metrics -->
    <div class="row row-sm">
        <div class="col-xl-6 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">معدلات النمو الشهرية</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="metric-card">
                                <h6>نمو المستخدمين</h6>
                                <h3 class="{{ $userGrowthRate >= 0 ? 'growth-positive' : 'growth-negative' }}">
                                    {{ $userGrowthRate >= 0 ? '+' : '' }}{{ number_format($userGrowthRate, 1) }}%
                                </h3>
                                <small>{{ number_format($newUsersThisMonth) }} مستخدم جديد هذا الشهر</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="metric-card">
                                <h6>نمو الإيرادات</h6>
                                <h3 class="{{ $revenueGrowthRate >= 0 ? 'growth-positive' : 'growth-negative' }}">
                                    {{ $revenueGrowthRate >= 0 ? '+' : '' }}{{ number_format($revenueGrowthRate, 1) }}%
                                </h3>
                                <small>{{ number_format($monthlyRevenue, 2) }} د.ك هذا الشهر</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">مؤشرات الأداء</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between">
                                <span>متوسط الأسئلة لكل فئة</span>
                                <strong>{{ $averageQuestionsPerCategory }}</strong>
                            </div>
                            <div class="percentage-bar">
                                <div class="percentage-fill" style="width: {{ min(($averageQuestionsPerCategory / 50) * 100, 100) }}%"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between">
                                <span>متوسط الألعاب لكل مستخدم</span>
                                <strong>{{ $averageGamesPerUser }}</strong>
                            </div>
                            <div class="percentage-bar">
                                <div class="percentage-fill" style="width: {{ min(($averageGamesPerUser / 10) * 100, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between">
                                <span>متوسط مبلغ المعاملة</span>
                                <strong>{{ number_format($averageTransactionAmount, 2) }} د.ك</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between">
                                <span>متوسط مشاهدات الأسئلة</span>
                                <strong>{{ number_format($averageQuestionViews, 1) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row row-sm">
        <div class="col-xl-8 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">الإحصائيات الشهرية (آخر 12 شهر)</h4>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">توزيع أنواع الأسئلة</h4>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="questionTypesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Demographics -->
    <div class="row row-sm">
        <div class="col-xl-6 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">تركيبة المستخدمين</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="userGenderChart"></canvas>
                        </div>
                        <div class="col-md-6">
                            <canvas id="userTypeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">حالة المعاملات</h4>
                </div>
                <div class="card-body">
                    <canvas id="transactionStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Lists -->
    <div class="row row-sm">
        <div class="col-xl-4 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">أكثر اللاعبين نشاطاً</h4>
                </div>
                <div class="card-body">
                    <div class="top-list">
                        @foreach($topPlayers as $index => $player)
                            <div class="top-list-item">
                                <div>
                                    <strong>{{ $player->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $player->email }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge badge-primary">{{ $player->games_count }} لعبة</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">الأسئلة الأكثر مشاهدة</h4>
                </div>
                <div class="card-body">
                    <div class="top-list">
                        @foreach($topQuestions as $index => $question)
                            <div class="top-list-item">
                                <div>
                                    <strong>{{ Str::limit($question->question, 50) }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $question->type }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge badge-success">{{ number_format($question->views) }} مشاهدة</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">الفئات الأكثر شعبية</h4>
                </div>
                <div class="card-body">
                    <div class="top-list">
                        @foreach($topCategories as $index => $category)
                            <div class="top-list-item">
                                <div>
                                    <strong>{{ $category->title }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $category->questions_count }} سؤال</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge badge-info">{{ number_format($category->views) }} مشاهدة</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Statistics Chart -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">الإحصائيات اليومية للشهر الحالي</h4>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="dailyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Statistics Button -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>إدارة الإحصائيات</h5>
                            <p class="text-muted mb-0">حذف البيانات التجريبية والإحصائيات المؤقتة</p>
                        </div>
                        <a href="{{ route('delete-statics') }}" class="btn btn-danger" onclick="return confirm('هل أنت متأكد من حذف الإحصائيات؟')">
                            <i class="fas fa-trash"></i> حذف الإحصائيات
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <!--Internal  Chart.bundle js -->
    <script src="{{ URL::asset('assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>
    <!--Internal Apexchart js-->
    <script src="{{ URL::asset('assets/js/apexcharts.js') }}"></script>
    <!--Internal  index js -->
    <script src="{{ URL::asset('assets/js/index.js') }}"></script>
    <script src="{{ URL::asset('assets/js/jquery.vmap.sampledata.js') }}"></script>

    <script>
        // Monthly Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($monthlyData, 'month')) !!},
                datasets: [{
                    label: 'المستخدمين',
                    data: {!! json_encode(array_column($monthlyData, 'users')) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: 'الألعاب',
                    data: {!! json_encode(array_column($monthlyData, 'games')) !!},
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.1
                }, {
                    label: 'الإيرادات',
                    data: {!! json_encode(array_column($monthlyData, 'revenue')) !!},
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'الإحصائيات الشهرية'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Question Types Chart
        const questionTypesCtx = document.getElementById('questionTypesChart').getContext('2d');
        const questionTypesChart = new Chart(questionTypesCtx, {
            type: 'doughnut',
            data: {
                labels: ['يمعات', 'رعب', 'فقرات', 'حظ'],
                datasets: [{
                    data: [{{ $yamaatQuestions }}, {{ $horrorQuestions }}, {{ $vertebraeQuestions }}, {{ $luckQuestions }}],
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // User Gender Chart
        const userGenderCtx = document.getElementById('userGenderChart').getContext('2d');
        const userGenderChart = new Chart(userGenderCtx, {
            type: 'pie',
            data: {
                labels: ['ذكور', 'إناث'],
                datasets: [{
                    data: [{{ $maleUsers }}, {{ $femaleUsers }}],
                    backgroundColor: ['#36A2EB', '#FF6384']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'توزيع الجنس'
                    }
                }
            }
        });

        // User Type Chart
        const userTypeCtx = document.getElementById('userTypeChart').getContext('2d');
        const userTypeChart = new Chart(userTypeCtx, {
            type: 'pie',
            data: {
                labels: ['مجاني', 'مميز'],
                datasets: [{
                    data: [{{ $freeUsers }}, {{ $premiumUsers }}],
                    backgroundColor: ['#FFCE56', '#4BC0C0']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'نوع المستخدم'
                    }
                }
            }
        });

        // Transaction Status Chart
        const transactionStatusCtx = document.getElementById('transactionStatusChart').getContext('2d');
        const transactionStatusChart = new Chart(transactionStatusCtx, {
            type: 'bar',
            data: {
                labels: ['ناجحة', 'معلقة', 'فاشلة'],
                datasets: [{
                    label: 'عدد المعاملات',
                    data: [{{ $successfulTransactions }}, {{ $pendingTransactions }}, {{ $failedTransactions }}],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'حالة المعاملات'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Daily Chart
        const dailyCtx = document.getElementById('dailyChart').getContext('2d');
        const dailyChart = new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($dailyStats, 'day')) !!},
                datasets: [{
                    label: 'مستخدمين جدد',
                    data: {!! json_encode(array_column($dailyStats, 'users')) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: 'ألعاب جديدة',
                    data: {!! json_encode(array_column($dailyStats, 'games')) !!},
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.1
                }, {
                    label: 'الإيرادات اليومية',
                    data: {!! json_encode(array_column($dailyStats, 'revenue')) !!},
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'الإحصائيات اليومية'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
