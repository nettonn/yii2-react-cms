<div class="field fileupload" data-url="<?= yii\helpers\Url::to(['/ajax/file-upload-images']) ?>">
    <div class="row fileupload-list">
        <div class="col new-image item no-images" data-short-classes="col-md-2 col-3">
            <div class="inner">
                <img src="/media/img/1x1-00000000.png" alt="Плейсхолдер для изображения 1х1">
                <div class="placeholder">
                    <span class="add">+</span>
                    <span class="add only-new">Добавить фото</span>
                </div>
                <input type="file" name="new_file" class="fileupload-input" multiple>
                <input type="hidden" name="fileupload_token" value="<?= uniqid() ?>">
            </div>
        </div>
    </div>
    <script type="text/template" class="fileupload-tmp">
        <div class="col-md-2 col-3 image item">
            <img src="{thumburl}" alt="Загруженное изображение">
            <!--                                    <a href="#" class="delete fileupload-item-delete"></a>-->
            <input type="hidden" name="files[]" value="{nameExt}">
        </div>
    </script>
</div>
