<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ConstructionSiteTask $model */
/** @var app\models\ConstructionSite[] $sites */
/** @var app\models\User[] $workers */

$this->title = 'Update Task: ' . $model->name;
?>
<div class="construction-site-task-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card">
        <div class="card-body">
            <?= $this->render('_form', [
                'model' => $model,
                'sites' => $sites,
                'workers' => $workers,
            ]) ?>
        </div>
    </div>

</div>
