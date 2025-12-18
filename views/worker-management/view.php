<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var app\models\User $currentUser */

$canEdit = $currentUser->isAdmin() && !$model->isAdmin() && $model->company_id === $currentUser->company_id;
?>

<div class="modal-header">
    <h5 class="modal-title">User Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <p><strong>Name:</strong> <?= Html::encode($model->getFullName()) ?></p>
            <p><strong>Birthdate:</strong> <?= Html::encode($model->birthdate) ?></p>
            <p><strong>Role:</strong> <?= Html::encode($model->role) ?></p>
            <p><strong>Access Level:</strong> <?= Html::encode($model->access_level) ?></p>
            <p><strong>Login:</strong> <?= Html::encode($model->login) ?></p>
            <p><strong>Status:</strong> 
                <span class="badge bg-<?= $model->enabled ? 'success' : 'secondary' ?>">
                    <?= $model->enabled ? 'Enabled' : 'Disabled' ?>
                </span>
            </p>
            <p><strong>Created:</strong> <?= Html::encode($model->created_date_time) ?></p>
            <p><strong>Last Updated:</strong> <?= Html::encode($model->info_edited_date_time) ?></p>
        </div>
    </div>
</div>
<?php if ($canEdit): ?>
<div class="modal-footer">
    <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?php if ($model->enabled): ?>
        <?= Html::a('Disable', ['toggle-status', 'id' => $model->id], [
            'class' => 'btn btn-warning',
            'data-method' => 'post',
            'data-confirm' => 'Are you sure you want to disable this user?'
        ]) ?>
    <?php else: ?>
        <?= Html::a('Enable', ['toggle-status', 'id' => $model->id], [
            'class' => 'btn btn-success',
            'data-method' => 'post',
            'data-confirm' => 'Are you sure you want to enable this user?'
        ]) ?>
    <?php endif; ?>
</div>
<?php endif; ?>