<?php if ($this->breadcrumbs): ?>
    <div class="container">
        <div class="breadcrumbs">
            <?= \app\widgets\Breadcrumbs::widget([
                'links' => $this->breadcrumbs,
            ]) ?>
        </div>
    </div>
<?php endif ?>


