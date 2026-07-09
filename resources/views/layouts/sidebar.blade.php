<style>
    .sidebar-brand-icon img {
        width: 175px;
    }

    ul.navbar-nav.toggled .sidebar-brand-icon img {
        width: 90px;
    }
</style>

<div style="position: sticky-left; align:left; display:block; height:100%;">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion fixed-left scroll-y" id="accordionSidebar">

        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/">
            <div class="sidebar-brand-icon px-3 py-1 bg-light">
                <img src="{{asset('assets/img/logo.png')}}" alt="">
                <!-- BRAND ICON HERE -->
            </div>
        </a>

        <hr class="sidebar-divider my-0">

        <li class="nav-item active">
            <a class="nav-link" href="{{url('store/dashboard')}}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{url('store/customer/create/billing')}}">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Create Customer Billing</span></a>
        </li>


        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                aria-expanded="true" aria-controls="collapseTwo">
                <i class="fas fa-boxes"></i>
                <span>Billing</span>
            </a>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{url('store/customer/billing')}}">Customer Billing</a>
                    <a class="collapse-item" href="{{url('store/staff/billing')}}">Staff Billing</a>
                </div>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUserPages"
                aria-expanded="true" aria-controls="collapseUserPages">
                <i class="fas fa-users fa-folder"></i>
                <span>Report</span>
            </a>
            <div id="collapseUserPages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{url('store/doctor/report')}}">Doctor Report</a>
                    <a class="collapse-item" href="{{url('store/cumulative/report')}}">Cumulative Sales Report</a>
                    <a class="collapse-item" href="{{url('store/expiry/report')}}">Expired Medicine Report</a>
                    <a class="collapse-item" href="{{url('store/gst/report')}}">GST Report</a>
                    <a class="collapse-item" href="{{url('store/stock/report')}}">Stock Report</a>
                </div>
            </div>
        </li>




        <!-- <li class="nav-item">
                <a class="nav-link" href="{{url('admin/backup')}}">
                    <i class="fas fa-recycle fa-folder"></i>
                    <span>Backup</span></a>
            </li> -->


        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMyStore"
                aria-expanded="true" aria-controls="collapseMyStore">
                <i class="fas fa-home fa-folder"></i>
                <span>My Store</span>
            </a>
            <div id="collapseMyStore" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{url('store/details')}}">Store Details</a>
                    <a class="collapse-item" href="{{url('store/stock/transfer')}}">Store Stock Transfer</a>
                    <a class="collapse-item" href="{{url('store/sync/history')}}">Store Sync History</a>
                    <a class="collapse-item" href="{{url('store/analytics')}}">Store Analytics</a>
                </div>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBackup"
                aria-expanded="true" aria-controls="collapseBackup">
                <i class="fas fa-recycle fa-folder"></i>
                <span>Backup</span>
            </a>
            <div id="collapseBackup" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{url('store/backup')}}">Backup</a>
                    {{-- <a class="collapse-item" href="{{url('admin/add-customer')}}">Restore</a> --}}
                </div>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{url('/logout')}}">
                <i class="fas fa-fw fa-table"></i>
                <span>Logout</span></a>
        </li>

        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>


    </ul>
</div>

<div id="font-size-controls" style="position:fixed; bottom:12px; left:0; width:15%; text-align:center; z-index:9999;">
    <button onclick="adjustFontSize(1)" class="btn btn-sm btn-light font-weight-bold mx-1" title="Increase font size">A+</button>
    <button onclick="adjustFontSize(-1)" class="btn btn-sm btn-light font-weight-bold mx-1" title="Decrease font size">A-</button>
</div>

<script>
    (function () {
        const MIN = 10, MAX = 20, STEP = 1, KEY = 'globalFontSize';
        const saved = parseInt(localStorage.getItem(KEY));
        if (saved) document.documentElement.style.fontSize = saved + 'px';

        window.adjustFontSize = function (delta) {
            const current = parseInt(getComputedStyle(document.documentElement).fontSize) || 14;
            const next = Math.min(MAX, Math.max(MIN, current + (delta * STEP)));
            document.documentElement.style.fontSize = next + 'px';
            localStorage.setItem(KEY, next);
        };
    })();
</script>