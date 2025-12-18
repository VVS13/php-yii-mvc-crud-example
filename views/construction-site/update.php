<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ConstructionSite $model */
/** @var app\models\User[] $managers */

$this->title = 'Update Construction Site: ' . $model->location;
?>
<div class="construction-site-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card">
        <div class="card-body">
            <?= $this->render('_form', [
                'model' => $model,
                'managers' => $managers,
            ]) ?>
        </div>
    </div>

</div>
