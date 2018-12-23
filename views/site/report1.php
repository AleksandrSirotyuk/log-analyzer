<?php
    $this->title = 'Отчет №1';
    use yii\widgets\ActiveForm;
?>
<div class="container-fluid" align="center">
    <div class="col-md-6 col-md-offset-3">
        <h3>Отчет №1: количество брошенных корзин за определенный период времени</h3>
        <?php
        $form = ActiveForm::begin(['class' => 'form-horizontal', 'id' => 'report-form']);
        ?>
        <?= $form->field($report1Form, 'date_from'); ?>
        <?= $form->field($report1Form, 'date_to'); ?>
        <div>
            <button type="submit" class="btn btn-primary">Получить отчет №1</button>
        </div>
        <?php
        ActiveForm::end();
        ?>
        <?php if(isset($result1)): ?>
            За данный период времени было брошено<?= ' '.$result1; ?> корзин(ы).
        <?php endif; ?>
    </div>
</div>
