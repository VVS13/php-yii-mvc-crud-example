<?php

/** @var yii\web\View $this */
/** @var app\models\LoginForm $model */

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Login';
?>

<div class="login-form">
    <div class="login-header">
        <h2>Construction Management System</h2>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'fieldConfig' => [
            'template' => "{label}\n{input}\n{error}",
            'labelOptions' => ['class' => 'form-label'],
            'inputOptions' => ['class' => 'form-control'],
            'errorOptions' => ['class' => 'invalid-feedback d-block'],
        ],
    ]); ?>

        <?= $form->field($model, 'login')->textInput(['autofocus' => true, 'placeholder' => 'Enter login']) ?>

        <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Enter password']) ?>

        <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "<div class=\"form-check\">{input} {label}</div>\n{error}",
            'labelOptions' => ['class' => 'form-check-label'],
            'inputOptions' => ['class' => 'form-check-input'],
        ]) ?>

        <div class="d-grid gap-2">

            <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>

    <?php ActiveForm::end(); ?>

    <div style="color:#999;">
        <br>Admin: <strong>admin / Admin123!</strong><br>
        <br>Managers: <strong>manager1/2/3 - password</strong><br>
        <br>Workers: <strong>worker1/2/3 - password</strong><br>
    </div>
</div>