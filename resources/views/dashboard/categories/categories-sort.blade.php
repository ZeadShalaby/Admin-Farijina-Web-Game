@extends('layouts.master')
@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">

<style>
    .cursor-grab {
        cursor: grab;
        font-size: 18px;
        color: #0d6efd;
        transition: color 0.3s;
    }
    .cursor-grab:hover { color: #dc3545; }

    .sortable-chosen { background-color: #ffffff; transform: scale(1.02); }
    .sortable-ghost { opacity: 0.6; }

    .category-card {
        border: 2px solid #eee;
        background: #fff;
        position: relative;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-radius: 0;
        overflow: hidden;
        clip-path: polygon(0 5%, 5% 0, 95% 0, 100% 5%, 100% 95%, 95% 100%, 5% 100%, 0 95%);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .category-card:hover { transform: scale(1.03); box-shadow:0 6px 12px rgba(0,0,0,0.2); }

    .category-grid {
        max-height: 70vh;
        overflow-y: auto;
    }
</style>
@endsection

@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="my-auto">
        <div class="d-flex">
            <h4 class="content-title mb-0 my-auto">العمليات /</h4>
            <span class="text-muted mt-1 tx-13 mr-2 mb-0">ترتيب الفئات</span>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid p-4">

    <!-- Tabs -->
    <div class="mb-3 d-flex gap-3">
        <button class="btn btn-outline-primary active" id="normalTab">الفئات العادية</button>
        <button class="btn btn-outline-primary" id="premiumTab">الفئات الدائمة</button>
    </div>

    <!-- زر الحفظ -->
    <div class="d-flex justify-content-end mb-3">
        <button id="saveOrderBtn" class="btn btn-primary">💾 حفظ الترتيب</button>
    </div>

 <!-- Grid الفئات العادية -->
<div id="normalGrid" class="row g-3 category-grid" style="direction: ltr;">
    @foreach ($categoriesnormal as $cat)
    <div class="col-md-2 category-item" data-id="{{ $cat->id }}">
        <div class="card category-card shadow-sm">
            <img src="{{ $cat->image ? url($cat->image) : url('images/default.jpg') }}"
                class="card-img-top rounded-top" style="height:150px; object-fit:cover;">
            <div class="card-body d-flex justify-content-between align-items-center">
                <span class="cursor-grab">⠿</span>
                <span class="fw-bold">{{ $cat->title }}</span>
                <span class="badge bg-secondary">#{{ $loop->iteration }}</span>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Grid الفئات الدائمة -->
<div id="premiumGrid" class="row g-3 category-grid d-none" style="direction: ltr;">
    @foreach ($categoriespremium as $cat)
    <div class="col-md-2 category-item" data-id="{{ $cat->id }}">
        <div class="card category-card shadow-sm">
            <img src="{{ $cat->image ? url($cat->image) : url('images/default.jpg') }}"
                class="card-img-top rounded-top" style="height:150px; object-fit:cover;">
            <div class="card-body d-flex justify-content-between align-items-center">
                <span class="cursor-grab">⠿</span>
                <span class="fw-bold">{{ $cat->title }}</span>
                <span class="badge bg-secondary">#{{ $loop->iteration }}</span>
            </div>
        </div>
    </div>
    @endforeach
</div>


</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/plugins/Swap/Swap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const normalGrid = document.getElementById('normalGrid');
    const premiumGrid = document.getElementById('premiumGrid');
    const normalTab = document.getElementById('normalTab');
    const premiumTab = document.getElementById('premiumTab');
    const saveBtn = document.getElementById('saveOrderBtn');

    // التبديل بين التبويبات
    normalTab.addEventListener('click', () => {
        normalTab.classList.add('active'); premiumTab.classList.remove('active');
        normalGrid.classList.remove('d-none'); premiumGrid.classList.add('d-none');
    });
    premiumTab.addEventListener('click', () => {
        premiumTab.classList.add('active'); normalTab.classList.remove('active');
        premiumGrid.classList.remove('d-none'); normalGrid.classList.add('d-none');
    });

    // تحديث Badges
    function updateBadges() {
        Array.from(normalGrid.querySelectorAll('.category-item')).forEach((el, idx) => {
            el.querySelector('.badge').textContent = `#${idx+1}`;
        });
        Array.from(premiumGrid.querySelectorAll('.category-item')).forEach((el, idx) => {
            el.querySelector('.badge').textContent = `#${idx+1}`;
        });
    }

    // Sortable
    Sortable.create(normalGrid, {
        animation: 150,
        handle: '.cursor-grab',
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        swap: true,
        swapClass: 'highlight',
        group: 'categories',
        onEnd: updateBadges
    });

    Sortable.create(premiumGrid, {
        animation: 150,
        handle: '.cursor-grab',
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        swap: true,
        swapClass: 'highlight',
        group: 'categories',
        onEnd: updateBadges
    });

    // حفظ الترتيب
    saveBtn.addEventListener('click', function() {
        const normalItems = Array.from(normalGrid.querySelectorAll('.category-item'));
        const premiumItems = Array.from(premiumGrid.querySelectorAll('.category-item'));
        let order = [];

        normalItems.forEach((el, idx) => { order.push({ id: parseInt(el.dataset.id), position: idx+1 }); });
        premiumItems.forEach((el, idx) => { order.push({ id: parseInt(el.dataset.id), position: normalItems.length+idx+1 }); });

        axios.post('{{ route("categories.reorder") }}', { order }, {
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        }).then(res => {
            updateBadges();
            Swal.fire({ toast:true, position:'top-end', icon:'success', title:'تم حفظ الترتيب', showConfirmButton:false, timer:1500 });
        }).catch(err => {
            console.error(err);
            Swal.fire('خطأ!','حصل خطأ في حفظ الترتيب','error');
        });
    });

});
</script>
@endsection
