<style>
    .sidebar-brand-icon img{
        width:175px;
    }
    ul.navbar-nav.toggled .sidebar-brand-icon img{
        width:90px;
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
                        <a class="collapse-item" href="{{url('admin/add-employee')}}">Datewise Sales Report</a>
                        <a class="collapse-item" href="{{url('admin/add-customer')}}">Sold Producr Report</a>
                        <a class="collapse-item" href="{{url('admin/add-doctor')}}">Stock Report</a>
                        <a class="collapse-item" href="{{url('admin/add-doctor')}}">Expiry Date Report</a>
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
                        <a class="collapse-item" href="{{url('admin/add-employee')}}">Store Details</a>
                        <a class="collapse-item" href="{{url('admin/add-customer')}}">Store Stock Transfer</a>
                        <a class="collapse-item" href="{{url('admin/add-customer')}}">Store Sync History</a>
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
                        <a class="collapse-item" href="{{url('admin/add-employee')}}">Backup</a>
                        <a class="collapse-item" href="{{url('admin/add-customer')}}">Restore</a>
                    </div>
                </div>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="{{url('admin/reports')}}">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Logout</span></a>
            </li>

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>



        </ul>
</div>
