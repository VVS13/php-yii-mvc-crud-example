<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ConstructionSite $model */
/** @var app\models\User[] $managers */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="construction-site-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'location')->textInput(['maxlength' => true, 'placeholder' => 'Enter site location/address']) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'location_size')->input('number', [
                'step' => '0.1',
                'min' => '0',
                'placeholder' => 'Enter size in m²'
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'access_level_needed')->dropDownList(
                array_combine(range(1, 10), range(1, 10)),
                ['prompt' => 'Select Access Level Required']
            ) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'manager_id')->dropDownList(
                ArrayHelper::map($managers, 'id', function($manager) {
                    return $manager->getFullName() . ' (Level ' . $manager->access_level . ')';
                }),
                ['prompt' => 'Select Manager (Optional)']
            ) ?>
        </div>
    </div>

    <div class="form-group mt-3">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
