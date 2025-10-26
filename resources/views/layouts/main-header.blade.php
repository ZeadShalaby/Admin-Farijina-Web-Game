<!-- main-header opened -->
<div class="main-header sticky side-header nav nav-item">
    <div class="container-fluid">
        <div class="main-header-left ">
            <div class="responsive-logo">
                <a href="{{ url('/' . ($page = 'index')) }}"><img src="{{ URL::asset('assets/img/brand/logo.png') }}"
                        class="logo-1" alt="logo"></a>
                <a href="{{ url('/' . ($page = 'index')) }}"><img
                        src="{{ URL::asset('assets/img/brand/logo-white.png') }}" class="dark-logo-1" alt="logo"></a>
                <a href="{{ url('/' . ($page = 'index')) }}"><img src="{{ URL::asset('assets/img/brand/favicon.png') }}"
                        class="logo-2" alt="logo"></a>
                <a href="{{ url('/' . ($page = 'index')) }}"><img src="{{ URL::asset('assets/img/brand/favicon.png') }}"
                        class="dark-logo-2" alt="logo"></a>
            </div>
            <div class="app-sidebar__toggle" data-toggle="sidebar">
                <a class="open-toggle" href="#"><i class="header-icon fe fe-align-left"></i></a>
                <a class="close-toggle" href="#"><i class="header-icons fe fe-x"></i></a>
            </div>
            <div class="main-header-center mr-3 d-sm-none d-md-none d-lg-block">
                <input class="form-control" placeholder="Search for anything..." type="search"> <button
                    class="btn"><i class="fas fa-search d-none d-md-block"></i></button>
            </div>
        </div>
        <div class="main-header-right">
            <ul class="nav">
                <li class="">
                    <div class="dropdown  nav-itemd-none d-md-flex">
                        <a href="#" class="d-flex  nav-item nav-link pl-0 country-flag1" data-toggle="dropdown"
                            aria-expanded="false">
                            <span class="avatar country-Flag mr-0 align-self-center bg-transparent"><i
                                    class="flag-icon flag-icon-eg flag-icon-squared"></i></span>
                            <div class="my-auto">
                                <strong class="mr-2 ml-2 my-auto">جمهورية مصر العربيه</strong>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-left dropdown-menu-arrow" x-placement="bottom-end">


                        </div>
                    </div>
                </li>
            </ul>
            <div class="nav nav-item  navbar-nav-right ml-auto">
                <div class="nav-link" id="bs-example-navbar-collapse-1">
                    <form class="navbar-form" role="search">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search">
                            <span class="input-group-btn">
                                <button type="reset" class="btn btn-default">
                                    <i class="fas fa-times"></i>
                                </button>
                                <button type="submit" class="btn btn-default nav-link resp-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="feather feather-search">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                </button>
                            </span>
                        </div>
                    </form>
                </div>



                <div class="dropdown nav-item main-header-message ">

                    <a class="new nav-link" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-mail">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                            </path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        @if ($countNotifications)
                            <span class=" pulse-danger">



                            </span>
                        @endif



                    </a>
                    <div class="dropdown-menu">
                        <div class="menu-header-content bg-primary text-right">
                            <div class="d-flex">
                                <h6 class="dropdown-title mb-1 tx-15 text-white font-weight-semibold">رسائل</h6>
                                <a class="badge badge-pill badge-warning mr-auto my-auto float-left"
                                    href="{{ route('notification.markAllAsRead') }}">
                                    <span>ضع علامة بأنه تم القراءة</span></a>
                            </div>
                            <p class="dropdown-title-text subtext mb-0 text-white op-6 pb-0 tx-12 ">لديك
                                {{ $countNotifications }} غير مقروءة
                                رسائل</p>
                        </div>
                        <div class="main-message-list chat-scroll">

                            @foreach ($notifications as $item)
                                <a href="#" class="p-3 d-flex border-bottom">
                                    <div class="drop-img cover-image"
                                        data-image-src="{{ URL::asset('assets/img/faces/5.jpg') }}">
                                        <span class="avatar-status bg-teal"></span>
                                    </div>
                                    <div class="wd-90p">
                                        <div class="d-flex">
                                            <h5 class="mb-1 name">{{ $item['data']['title'] }}</h5>
                                        </div>
                                        <p class="mb-0 desc">{{ $item['data']['message'] }}</p>
                                        <p class="time mb-0 text-left float-right mr-2 mt-2">
                                            {{ \Carbon\Carbon::parse($item['created_at'])->format('M d h:i A') }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach

                        </div>
                        <div class="text-center dropdown-footer">
                            <a href="">VIEW ALL</a>
                        </div>
                    </div>
                </div>



                {{-- <div class="main-img-user profile-user">
								<img alt="" src="{{URL::asset('BLOSSOM.png')}}"><a class="fas fa-camera profile-edit" href="JavaScript:void(0);"></a>
							</div> --}}


                <div class="nav-item full-screen fullscreen-button">
                    <a class="new nav-link full-screen-link" href="#"><svg xmlns="http://www.w3.org/2000/svg"
                            class="header-icon-svgs" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-maximize">
                            <path
                                d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3">
                            </path>
                        </svg></a>
                </div>
                <div class="dropdown main-profile-menu nav nav-item nav-link">
                    <a class="profile-user d-flex" href=""><img alt=""
                            src="{{ URL::asset('logo.png') }}"></a>
                    <div class="dropdown-menu">
                        <div class="main-header-profile bg-primary p-3">
                            <div class="d-flex wd-100p">
                                <div class="main-img-user"><img alt=""
                                        src="{{ URL::asset('logo.png') }}" class=""></div>
                                <div class="mr-3 my-auto">
                                    <h6>{{ Auth::User()->name }}</h6><span>{{ Auth::User()->email }}</span>
                                </div>
                            </div>
                        </div>
                        {{-- <a class="dropdown-item" href=""><i class="bx bx-user-circle"></i>الملف الشخصي</a>
									<a class="dropdown-item" href=""><i class="bx bx-cog"></i> تعديل الملف الشخصي</a>
									<a class="dropdown-item" href=""><i class="bx bx-envelope"></i>الرسائل</a>
									<a class="dropdown-item" href=""><i class="bx bx-slider-alt"></i> اعدادات الحساب</a> --}}
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i
                                class="bx bx-log-out"></i>تسجيل خروج</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                            style="display: none;">
                            @csrf

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- /main-header -->
