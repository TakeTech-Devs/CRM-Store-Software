function ajaxPostData(url, data, onSuccess, onError, processData, contentType) {
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        processData: processData !== undefined ? processData : true,
        contentType: contentType !== undefined ? contentType : "application/x-www-form-urlencoded",
        success: function (response) {
            onSuccess(response);
        },
        error: function (response) {
            onError(response);
        },
    });
}
function ajaxPutData(url, data, onSuccess, onError, processData, contentType) {
    $.ajax({
        type: "PUT",
        url: url,
        data: data,
        processData: processData !== undefined ? processData : true,
        contentType: contentType !== undefined ? contentType : "application/x-www-form-urlencoded",
        success: function (response) {
            onSuccess(response);
        },
        error: function (response) {
            onError(response);
        },
    });
}


function ajaxGetData(url, onSuccess, onError) {
    $.ajax({
        type: "GET",
        url: url,
        success: function (response) {
            onSuccess(response);
        },
        error: function (response) {
            onError(response);
        },
    });
}

function pagination(page, res) {

    // let res = response?.data?.activeEmployeeDetails;
    let paginationHtml = '';
    if (res?.last_page != 1) {
        paginationHtml +=
            `<li class="page-item ${Number(res?.current_page) == 1 ? 'disabled' : ''}"> <a class="page-link" tabindex="-1" aria-disabled="true">Previous</a> </li>`;

        if (res.current_page > 2) {
            paginationHtml += `<li class="page-item"><a class="page-link">1</a></li>`;
            if (res.current_page > 3) {
                paginationHtml += `<li class="page-item disabled"><a class="page-link">...</a></li>`;
            }
        }
        

        for (let index = Math.max(1, res?.current_page - 1); index <= Math.min(res?.current_page + 1, res?.last_page); index++) {
            paginationHtml +=
                `<li class="page-item ${res?.current_page == index ? 'active' : ''}"><a class="page-link">${index}</a></li>`;
        }

        if (res?.current_page < res?.last_page - 1) {
            if (res?.current_page < res?.last_page - 2) {
                paginationHtml += `<li class="page-item disabled"><a class="page-link">...</a></li>`;
            }
            paginationHtml += `<li class="page-item"><a class="page-link">${res.last_page}</a></li>`;
        }
        paginationHtml +=
            `<li class="page-item ${Number(res?.current_page) == Number(res?.last_page) ? 'disabled' : ''}"> <a class="page-link">Next</a> </li>`;
    }

    $(".pagination").html(paginationHtml);

    $(".pagination").on("click", ".page-item:not(.disabled) .page-link:contains('Next')", function() {
        const nextPage = Math.min(res.current_page + 1, res.last_page);
        console.log('nextPage', nextPage)
        updatePagination(nextPage);
    });

    $(".pagination").on("click", ".page-item:not(.disabled) .page-link:contains('Previous')", function() {
        const prevPage = Math.max(res.current_page - 1, 1);
        updatePagination(prevPage);
    });

    $(".pagination").on("click",
        ".page-item:not(.disabled) .page-link:not(:contains('Previous')):not(:contains('Next'))",
        function() {
            const newPage = parseInt($(this).text());
            updatePagination(newPage);
        });

    function updatePagination(newPage) {

        let updatedHtml = '';
        if (newPage > 2) {
            updatedHtml += `<li class="page-item"><a class="page-link">1</a></li>`;
            if (newPage > 3) {
                updatedHtml += `<li class="page-item disabled"><a class="page-link">...</a></li>`;
            }
        }

        for (let index = Math.max(1, newPage - 1); index <= Math.min(newPage + 1, res.last_page); index++) {
            console.log(res.last_page, "newPage")
            updatedHtml +=
                `<li class="page-item ${res.current_page == index ? 'active' : ''}"><a class="page-link">${index}</a></li>`;
        }

        if (newPage < res.last_page - 1) {
            if (newPage < res.last_page - 2) {
                updatedHtml += `<li class="page-item disabled"><a class="page-link">...</a></li>`;
            }
            updatedHtml += `<li class="page-item"><a class="page-link">${res.last_page}</a></li>`;
        }

        $(".pagination li:not(:first-child):not(:last-child)").remove();
        $(".pagination li:first-child").after(updatedHtml);
        $(".pagination li:first-child").toggleClass("disabled", newPage === 1);
        $(".pagination li:last-child").toggleClass("disabled", newPage === res.last_page);
        res.current_page = newPage;
    }


}
