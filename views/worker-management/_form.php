<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use app\models\User;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'birthdate')->input('date') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'access_level')->dropDownList(
                array_combine(range(1, 10), range(1, 10)),
                ['prompt' => 'Select Access Level']
            ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'role')->dropDownList([
                User::ROLE_WORKER => 'Worker',
                User::ROLE_MANAGER => 'Manager',
            ], ['prompt' => 'Select Role']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'login')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'password_plain')->passwordInput([
                'maxlength' => true,
                'placeholder' => $model->isNewRecord ? 'Enter password' : 'Leave empty to keep current password'
            ])->label('Password') ?>
            <?php if (!$model->isNewRecord): ?>
                <p class="form-text text-muted">Leave empty to keep current password</p>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'enabled')->checkbox() ?>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
