<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ConstructionSiteTask $model */
/** @var app\models\ConstructionSite[] $sites */
/** @var app\models\User[] $workers */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="construction-site-task-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'placeholder' => 'Enter task name']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'construction_site_id')->dropDownList(
                ArrayHelper::map($sites, 'id', function($site) {
                    return $site->location . ' (Level ' . $site->access_level_needed . ')';
                }),
                ['prompt' => 'Select Construction Site']
            ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'assigned_worker_id')->dropDownList(
                ArrayHelper::map($workers, 'id', function($worker) {
                    return $worker->getFullName() . ' (Level ' . $worker->access_level . ')';
                }),
                ['prompt' => 'Select Worker (Optional)']
            ) ?>
            <small class="form-text text-muted">Worker name, surname, and access level are shown. Warning will display if access level is insufficient.</small>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'start_date')->input('date') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'end_date')->input('date') ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'description')->textarea([
                'rows' => 4,
                'maxlength' => true,
                'placeholder' => 'Enter task description (optional)'
            ]) ?>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
