<!-- Topbar -->
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <h5 class="fs-6 text-dark mr-3">
        Store Panel
    </h5>
    <div class="col text-right">
        <button type="button" class="btn btn-primary" id="sessionValue" value="{{Session::get('storeId')}}">Sync</button>
    </div>
</nav>
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