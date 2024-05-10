<style>
    .profile-card {
      max-width: 400px;
    }
    .avatar {
      width: 100px;
      height: 100px;
      margin: 0 auto;
    }
</style>
<div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card profile-card">
          <div class="card-body text-center">
            <img src="avatar.jpg" class="avatar rounded-circle mb-3" alt="Avatar">
            <h5 class="card-title">Имя пользователя (ФИО)</h5>
            <p class="card-text">Роль пользователя</p>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-5">
      <div class="col-md-12">
        <h2>Загрузки пользователя</h2>
        <hr>
        <table id="uploadsTable" class="table table-striped table-bordered" style="width:100%">
          <thead>
            <tr>
              <th>ID</th>
              <th>Название</th>
              <th>Дата</th>
              <th>Статус</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Название файла 1</td>
              <td>01.01.2022</td>
              <td>Опубликован</td>
            </tr>
            <tr>
              <td>2</td>
              <td>Название файла 2</td>
              <td>02.01.2022</td>
              <td>Не опубликован</td>
            </tr>
            <!-- Добавьте больше строк, если нужно -->
          </tbody>
        </table>
      </div>
    </div>
  </div>