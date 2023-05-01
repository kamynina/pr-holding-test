<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\AppleSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="apple-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'color') ?>

    <?= $form->field($model, 'birthdate') ?>

    <?= $form->field($model, 'fall_date') ?>

    <?= $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'percent_eaten') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
