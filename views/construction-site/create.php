<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ConstructionSite $model */
/** @var app\models\User[] $managers */

$this->title = 'Create Construction Site';
?>
<div class="construction-site-create">

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
