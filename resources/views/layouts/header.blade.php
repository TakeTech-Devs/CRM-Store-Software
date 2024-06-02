<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

<!-- Sidebar Toggle (Topbar) -->
<button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
    <i class="fa fa-bars"></i>
</button>
{{-- <div class="row"> --}}
    <div class="col text-right">
        <button type="button" class="btn btn-primary" id="sessionValue" value="{{Session::get('storeId')}}">Sync</button>
    </div>
{{-- </div> --}}
<!-- <ul class="navbar-nav ml-auto">
    <li class="nav-item no-arrow">
        
            <button class="btn btn-danger text-light py-1 px-4" data-toggle="modal" data-target="#logoutModal">
                <i class="fas fa-sign-out-alt fa-sm fa-fw text-white-400"></i>
                Logout
        </button>
    </li>

</ul> -->

</nav>
<!-- End of Topbar -->

<!-- Include Bootstrap JS and Popper.js -->
<script>
    $(document).ready(function () {


        $(document).on('click', '#sessionValue', function () {
            let store_id = $(this).val();
            sync(store_id)
        });

    });

    function sync(store_id){
        ajaxGetData(`/sync-data/${store_id}`, (response)=>{
            console.log(response);
            alert("Data Synced")
        })
    }
</script>