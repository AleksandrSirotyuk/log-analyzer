<?php
    $this->title = 'Отчет №2';
    use yii\widgets\ActiveForm;
?>
<div class="container-fluid" align="center">
    <div class="col-md-6 col-md-offset-3">
        <h3>Отчет №2: количество пользователей, совершавших повторные покупки за определенный период времени</h3>
        <?php
        $form = ActiveForm::begin(['class' => 'form-horizontal', 'id' => 'report-form']);
        ?>
        <?= $form->field($report2Form, 'date_from'); ?>
        <?= $form->field($report2Form, 'date_to'); ?>
        <div>
            <button type="submit" class="btn btn-primary">Получить отчет №2</button>
        </div>
        <?php
        ActiveForm::end();
        ?>
        <?php if(isset($result)): ?>
            За данный период времени <?= ' '.$result; ?> человек(а) совершал(и) повторные покупки.
        <?php endif; ?>
    </div>
</div>