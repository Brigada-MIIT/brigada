<div class="container">
    <p class="page-title">Изменение аватарки</p>
    <div class="form">
        <div class="col-12">
            <div class="in">
                <form method="POST" action="/api/profile/avatar" enctype="multipart/form-data">
                    <br><input type="file" name="avatar"><br><br>
                    <div class="col-12" style="margin-top: 5%;">
                        <div class="in">
                            <div class="btn-group d-flex flex-wrap">
                                <button id="submit" type="submit" class="submit mr-4 mb-2">Сохранить</button>
                                <button id="submit" class="submit mr-4 mb-2">Удалить аватар</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>