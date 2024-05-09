<link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
<style>
    .row {
        overflow-x: auto;
    }
</style>
<script>
  $(document).ready(function() {
        $('#fileTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Russian.json"
            },
            "processing": true,
            "serverSide": true,
            "ajax": function(data, callback, settings) {
                console.log(data);
                console.log(settings);
                $.ajax({
                    url: "/api/main/get_uploads",
                    method: "POST",
                    data: {
                        "limit": data.length,
                        "page": Math.ceil(data.start / data.length) + 1,
                        "search": data.search.value,
                        "order": data.order[0].column,
                        "dir": data.order[0].dir
                    },
                    success: function(response) {
                        let result = JSON.parse(response);
                        callback({
                            draw: data.draw,
                            recordsTotal: result.count,
                            recordsFiltered: result.filtred_count,
                            data: result.data
                        });
                    },
                    dataSrc: ''
                });
            },
            "columns": [
                { "data": 'id' },
                { "data": 'name' },
                { "data": 'created' },
                { "data": 'author' },
            ],
            "paging": true,
            "lengthMenu": [ 10, 25, 50 ], // Опции выбора количества строк на странице
            "pageLength": 10, // Количество строк на странице по умолчанию
            "order": [[ 0, "desc" ]]
        });
  });
</script>
<div class="container mt-4">
    <div class="col-12" style="display: flex;flex-direction: row;align-items: center;">
        <p class="page-title">Последние загрузки</p>
        <br class="d-sm-none">
        <?php if($_user['ban_upload'] == 0) echo '
            <button class="submit">Создать загрузку</button>'; ?>
    </div>
    <table id="fileTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Имя загрузки</th>
                <th scope="col">Дата загрузки</th>
                <th scope="col">Имя пользователя</th>
            </tr>
        </thead>
        <tbody>
            
        </tbody>
    </table>
</div>