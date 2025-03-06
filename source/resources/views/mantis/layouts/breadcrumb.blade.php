<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    @yield('breadcrumb-title')
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Kỳ Đài</a></li>
                    {{-- <li class="breadcrumb-item"><a href="javascript: void(0)">Dashboard</a></li> --}}
                    @yield('breadcrumb')
                </ul>
            </div>
        </div>
    </div>
</div>