<link href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
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
                $.ajax({
                    url: "/api/main/get_uploads",
                    method: "POST",
                    data: {
                        "limit": data.length,
                        "page": Math.ceil(data.start / data.length) + 1
                    },
                    success: function(response) {
                        console.log(response);
                        callback({
                            draw: data.draw,
                            recordsTotal: response.length,
                            recordsFiltered: response.length,
                            data: response
                        });
                    }, 
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            },
            "columns": [
                {"data": "id"},
                {"data": "name"},
                {"data": "created"},
                {"data": "author"}
            ],
            "paging": true,
            "lengthMenu": [ 10, 25, 50 ], // Опции выбора количества строк на странице
            "pageLength": 10 // Количество строк на странице по умолчанию
        });
  });
</script>
<div class="container mt-4">
    <h2>Последние загрузки</h2>
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
            <tr>
                <td>1</td>
                <td>Примерный_файл.txt</td>
                <td>2024-05-09</td>
                <td>Пользователь123</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Другой_файл.pdf</td>
                <td>2024-05-08</td>
                <td>Гость007</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Другой_файл.pdf</td>
                <td>2024-05-08</td>
                <td>Гость007</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Другой_файл.pdf</td>
                <td>2024-05-08</td>
                <td>Гость007</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Другой_файл.pdf</td>
                <td>2024-05-08</td>
                <td>Гость007</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Другой_файл.pdf</td>
                <td>2024-05-08</td>
                <td>Гость007</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Другой_файл.pdf</td>
                <td>2024-05-08</td>
                <td>Гость007</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Другой_файл.pdf</td>
                <td>2024-05-08</td>
                <td>Гость007</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Другой_файл.pdf</td>
                <td>2024-05-08</td>
                <td>Гость007</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Другой_файл.pdf</td>
                <td>2024-05-08</td>
                <td>Гость007</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Другой_файл.pdf</td>
                <td>2024-05-08</td>
                <td>Гость007</td>
            </tr>
            <tr>
                <td>2</td>
                <td>ebaat.pdf</td>
                <td>2024-05-08</td>
                <td>Гость007</td>
            </tr>
        </tbody>
    </table>
</div>