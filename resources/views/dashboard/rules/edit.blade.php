@extends('layouts.master')

@section('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">

<style>
:root{
    --bg:#f6f8fb;
    --card:#ffffff;
    --muted:#7b8596;
    --primary:#1f6feb;
    --light-gray:#eef2f6;
    --border:#e6eaf0;
    --radius:12px;
    --shadow: 0 6px 18px rgba(20,30,60,0.08);
}

body { background: linear-gradient(180deg,var(--bg),#f4f6fa 60%); direction: rtl; }

.page-wrap{ max-width:1600px; margin:28px auto; padding:12px; }
.card{ background:var(--card); border-radius:16px; padding:18px; box-shadow:var(--shadow); border:1px solid var(--border); }

.page-header{ display:flex; gap:12px; align-items:center; margin-bottom:16px; }
.breadcrumbs{ color:var(--muted); font-size:13px; }
.header-actions{ margin-left:auto; display:flex; gap:10px; align-items:center; }

.date-pill{ background:var(--card); border:1px solid var(--border); padding:6px 10px; border-radius:999px; color:var(--muted); font-size:13px; }

.btn{ border:0; padding:8px 12px; border-radius:10px; font-weight:600; cursor:pointer; font-size:14px; }
.btn-ghost{ background:transparent; color:#475569; border:1px solid var(--border); }
.btn-primary{ background:var(--primary); color:#fff; box-shadow: 0 6px 14px rgba(31,111,235,0.16); }

.top-row{ display:flex; gap:16px; align-items:flex-start; flex-wrap:wrap; }
.left-col{ flex:1; min-width:280px; }
.right-col{ width:320px; }

.section{ margin-top:14px; border-radius:12px; border:1px solid var(--light-gray); background:linear-gradient(180deg,#fff,#fbfdff); padding:14px; }
.section .section-header{ display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
.section-title{ font-weight:700; color:#1b2b3a; }
.section-sub{ display: none !important; }

label{ display:block; font-size:13px; margin-bottom:6px; color:#233044; }

input[type="text"], textarea, .select2-container--default .select2-selection--single {
    width:100%; padding:10px 12px; border-radius:10px; border:1px solid var(--border); outline:none; font-size:14px; background:transparent;
}
textarea{ min-height:76px; }

.permissions-wrapper{ margin-top:10px; display:flex; flex-direction:column; gap:12px; }
.perm-card{ border-radius:10px; border:1px solid var(--border); padding:10px; background:linear-gradient(90deg,#ffffff,#fcfdff); }
.perm-row{ display:flex; gap:10px; flex-wrap:wrap; align-items:center; }

.checkbox{ display:inline-flex; align-items:center; gap:6px; cursor:pointer; user-select:none; font-size:14px; color:#273447; }

.checkbox input:checked + .box svg{ opacity:1; transform:scale(1); }
.checkbox input:checked + .box{ background:#fff; border-color:var(--primary); }

.select-all{ display:flex; gap:8px; align-items:center; color:var(--muted); font-size:13px; }

.permissions-scroll { max-height:420px; overflow:auto; padding-right:6px; }
.permissions-scroll::-webkit-scrollbar{ width:10px; }
.permissions-scroll::-webkit-scrollbar-thumb{ background:var(--light-gray); border-radius:8px; border:2px solid transparent; background-clip:padding-box; }

.meta-card{ display:flex; gap:12px; align-items:center; justify-content:space-between; }
.avatar{ width:64px; height:64px; border-radius:12px; overflow:hidden; border:1px solid var(--border); background:#fff; display:inline-grid; place-items:center; }
.avatar img{ width:100%; height:100%; object-fit:cover; }

.footer-actions{ margin-top:16px; display:flex; gap:10px; justify-content:flex-end; }

@media (max-width:880px){
    .top-row{ flex-direction:column; }
    .right-col{ width:100%; }
}
</style>
@endsection

@section('page-header')
<div class="page-header">
    <div class="breadcrumbs">الأدوار &gt; إدارة الأدوار &gt; <strong>تعديل الدور</strong></div>
    <div class="header-actions">
        <div class="date-pill">{{ now()->format('F d, Y') }}</div>

    </div>
</div>
@endsection

@section('content')
<div class="page-wrap">
@if ($errors->any())
<div class="alert alert-danger mb-3">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if (session()->has('edit'))
<div class="alert alert-success">{{ session('edit') }}</div>
@endif

<div class="card" role="main" aria-labelledby="cardTitle">
    <div class="card-title" style="display:flex;justify-content:space-between;align-items:center; gap:12px;">
        <div>
            <h2 id="cardTitle">تعديل الدور</h2>
        </div>
    </div>

    <form id="roleForm" action="{{ route('rules.update', $role->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="top-row" style="margin-top:14px;">
            <div class="left-col">
                <div class="section" aria-labelledby="detailsTitle">
                    <div class="section-header">
                        <div>
                            <div class="section-title" id="detailsTitle">تفاصيل الدور</div>
                        </div>
                        <div class="mini-toggle">⌄</div>
                    </div>

                    <div class="form-row" style="display:flex; gap:12px;">
                        <div style="flex:1;">
                            <label for="name">اسم الدور</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $role->name) }}" required>
                        </div>

                        <div style="width:160px;">
                            <label for="guard_name">Guard</label>
                            <select id="guard_name" name="guard_name">
                                <option value="web" {{ old('guard_name', $role->guard_name) == 'web' ? 'selected' : '' }}>web</option>
                                <option value="api" {{ old('guard_name', $role->guard_name) == 'api' ? 'selected' : '' }}>api</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="section" style="margin-top:12px;">
                    <div class="section-header">
                        <div>
                            <div class="section-title">الصلاحيات</div>
                        </div>
                        <div class="mini-toggle">⌄</div>
                    </div>

                    <div class="permissions-scroll">
                        <div class="permissions-wrapper">
                            @php
                                $grouped = $permissions->groupBy(function($p){
                                    return $p->group ?? ($p->module ?? 'عام');
                                });
                            @endphp

                            @foreach($grouped as $groupName => $groupPermissions)
                            <div class="perm-card" data-group="{{ \Illuminate\Support\Str::slug($groupName) }}">
                                <div style="display:flex;justify-content:space-between;align-items:center; margin-bottom:8px;">
                                    <div>
                                        <div style="font-weight:700">{{ $groupName }}</div>
                                    </div>

                                    <div style="display:flex;align-items:center;gap:12px">
                                        <label class="select-all">
                                            <input type="checkbox" class="select-group" data-target="{{ \Illuminate\Support\Str::slug($groupName) }}" />
                                            <span>تحديد الكل</span>
                                        </label>
                                        <button type="button" class="btn btn-ghost" style="padding:6px 8px;border-radius:8px;" onclick="toggleGroup(this)">⌄</button>
                                    </div>
                                </div>

                                <div class="perm-row">
                                    @foreach($groupPermissions as $permission)
                                    <label class="checkbox" style="min-width:140px;">
                                        <input style="color: #1b2b3a" type="checkbox"
                                            name="permissions[]"
                                            value="{{ $permission->id }}"
                                            {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                      
                                        <span class="label">{{ $permission->name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="right-col">
                <div class="section">
                    <div class="section-header">
                        <div>
                            <div class="section-title">معلومات سريعة</div>
                        </div>
                    </div>

                    <div class="meta-card" style="margin-top:6px;">
                        <div style="display:flex;gap:12px;align-items:center">
                            <div>
                                <div class="avatar" id="roleAvatar">
                                    @if(!empty($role->avatar))
                                    <img src="{{ asset('storage/' . $role->avatar) }}" alt="avatar">
                                    @else
                                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='64' height='64'><rect width='100%' height='100%' fill='%23dfe7f7'/><text x='50%' y='55%' font-size='28' text-anchor='middle' fill='%23576b95' font-family='Arial' dy='.35em'>{{ strtoupper(substr($role->name,0,1)) }}</text></svg>" alt="avatar">
                                    @endif
                                </div>
                            </div>

                            <div style="min-width:0">
                                <div style="font-weight:700">{{ $role->name }}</div>
                                <div style="font-size:13px;color:var(--muted); margin-top:6px;">
                                    Guard: <strong>{{ $role->guard_name }}</strong>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top:12px; display:flex;flex-direction:column; gap:8px;">
                            <div style="font-size:13px;color:var(--muted)"><strong>عدد الصلاحيات:</strong> {{ $rolePermissions ? count($rolePermissions) : 0 }}</div>
                            <div style="font-size:13px;color:var(--muted)"><strong>مُنشأ في:</strong> {{ $role->created_at ? $role->created_at->format('Y-m-d') : '-' }}</div>
                        </div>
                    </div>

                    <div class="section" style="margin-top:12px;">
                        <div style="font-weight:700; margin-bottom:8px;">إجراءات سريعة</div>
                        <div style="display:flex; flex-direction:column; gap:8px;">
                            <a href="{{ route('rules.index') }}" class="btn btn-ghost" style="text-align:center">الرجوع للقائمة</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>

    <div class="footer-actions">
        <button class="btn btn-ghost" onclick="window.history.back()">إلغاء</button>
        <button form="roleForm" class="btn btn-primary">حفظ التغييرات</button>
    </div>
</div>
</div>
@endsection

@section('js')
<script>
function toggleGroup(btn){
    const card = btn.closest('.perm-card');
    const row = card.querySelector('.perm-row');
    if(!row) return;
    if(row.style.display === 'none'){ row.style.display = ''; btn.textContent = '⌄'; }
    else { row.style.display = 'none'; btn.textContent = '›'; }
}

document.querySelectorAll('.select-group').forEach(chk => {
    chk.addEventListener('change', e => {
        const target = chk.dataset.target;
        const card = document.querySelector(`.perm-card[data-group="${target}"]`);
        if(!card) return;
        const inputs = card.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
        inputs.forEach(i => i.checked = chk.checked);
    });
});

document.querySelectorAll('.perm-card').forEach(card => {
    const groupSlug = card.dataset.group;
    const groupCheckbox = card.querySelector(`.select-group[data-target="${groupSlug}"]`);
    if(!groupCheckbox) return;
    const inputs = card.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
    function updateGroupState(){
        const all = Array.from(inputs);
        const checked = all.filter(i => i.checked).length;
        groupCheckbox.checked = (checked > 0 && checked === all.length);
        groupCheckbox.indeterminate = (checked > 0 && checked < all.length);
    }
    inputs.forEach(i => i.addEventListener('change', updateGroupState));
    updateGroupState();
});

document.querySelectorAll('.checkbox').forEach(lbl => {
    const inp = lbl.querySelector('input[type="checkbox"]');
    if(!inp) return;

    lbl.addEventListener('click', (e) => {
        // لو الكليك على input نفسه، سيبها تتصرف طبيعي
        if(e.target === inp) return;

        // عكس حالة الـ checkbox
        inp.checked = !inp.checked;

        // تفعل event عشان باقي الكود يعرف التغيير
        inp.dispatchEvent(new Event('change', { bubbles: true }));
    });
});

</script>
@endsection
