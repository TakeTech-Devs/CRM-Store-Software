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
                <a class="nav-link" href="{{url('admin/dashboard')}}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>


            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-boxes"></i>
                    <span>Stock</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{url('admin/product')}}">Products</a>
                        <a class="collapse-item" href="{{url('admin/brand')}}">Brand</a>
                        <a class="collapse-item" href="{{url('admin/category')}}">Category</a>
                        <a class="collapse-item" href="{{url('admin/subcategory')}}">Sub Category</a>
                        <a class="collapse-item" href="{{url('admin/pack')}}">Pack</a>
                        <a class="collapse-item" href="{{url('admin/supplier')}}">Supplier</a>
                        <a class="collapse-item" href="{{url('admin/purchase-stock')}}">Purchase Entry Details</a>
                        <a class="collapse-item" href="{{url('admin/store/assign')}}">Store Assign</a>
                    </div>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUserPages"
                    aria-expanded="true" aria-controls="collapseUserPages">
                    <i class="fas fa-users fa-folder"></i>
                    <span>Users</span>
                </a>
                <div id="collapseUserPages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{url('admin/add-employee')}}">Add Employee</a>
                        <a class="collapse-item" href="{{url('admin/add-customer')}}">Add Customer</a>
                        <a class="collapse-item" href="{{url('admin/add-doctor')}}">Add Doctor</a>
                    </div>
                </div>
            </li>

            
            <li class="nav-item">
                <a class="nav-link" href="{{url('admin/store')}}">
                    <i class="fas fa-home fa-folder"></i>
                    <span>Store</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{url('admin/backup')}}">
                    <i class="fas fa-recycle fa-folder"></i>
                    <span>Backup</span></a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="{{url('admin/reports')}}">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Report</span></a>
            </li>

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>



        </ul>
</div>
