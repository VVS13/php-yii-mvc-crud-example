<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ConstructionSiteTask $model */
/** @var app\models\User $currentUser */

$canDelete = !$currentUser->isWorker() && 
           $model->company_id === $currentUser->company_id &&
           ($currentUser->isAdmin() || 
            ($currentUser->isManager() && $model->constructionSite && $model->constructionSite->manager_id === $currentUser->id));

$canEdit = !$model->isPast() && $canDelete;
?>

<div class="modal-header">
    <h5 class="modal-title">Task Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <?php if ($model->getWarningStatus()): ?>
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i>
            <?php
            $warningStatus = $model->getWarningStatus();
            if ($warningStatus === 'worker_not_assigned') {
                echo 'No worker assigned to this task.';
            } elseif ($warningStatus === 'worker_disabled') {
                echo 'Assigned worker is currently disabled in the system.';
            } elseif ($warningStatus === 'worker_access_level_issue' && $model->assignedWorker) {
                echo 'Worker access level (' . $model->assignedWorker->access_level . ') is below site requirement (' . $model->constructionSite->access_level_needed . '). Physical access may be denied.';
            }
            ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <p><strong>Task Name:</strong> <?= Html::encode($model->name) ?></p>
            <p><strong>Construction Site:</strong> <?= Html::encode($model->constructionSite->location) ?></p>
            <p><strong>Site Access Level Required:</strong> <?= Html::encode($model->constructionSite->access_level_needed) ?></p>
            <?php if ($model->constructionSite->manager): ?>
                <p><strong>Site Manager:</strong> <?= Html::encode($model->constructionSite->manager->getFullName()) ?></p>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <p><strong>Assigned Worker:</strong> 
                <?php if ($model->assignedWorker): ?>
                    <?= Html::encode($model->assignedWorker->getFullName()) ?>
                    (Level <?= Html::encode($model->assignedWorker->access_level) ?>)
                <?php else: ?>
                    <span class="text-muted">Unassigned</span>
                <?php endif; ?>
            </p>
            <p><strong>Start Date:</strong> <?= Html::encode(Yii::$app->formatter->asDate($model->start_date)) ?></p>
            <p><strong>End Date:</strong> <?= Html::encode(Yii::$app->formatter->asDate($model->end_date)) ?></p>
            <p><strong>Created:</strong> <?= Html::encode(Yii::$app->formatter->asDatetime($model->created_date_time)) ?></p>
        </div>
    </div>

    <?php if ($model->description): ?>
        <div class="row mt-3">
            <div class="col-12">
                <p><strong>Description:</strong></p>
                <p><?= Html::encode($model->description) ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php if ($canDelete || $canEdit): ?>
<div class="modal-footer">
    <?php if ($canEdit): ?>
        <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?php endif; ?>
    <?php if ($canDelete): ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data-method' => 'post',
            'data-confirm' => 'Are you sure you want to delete this task?',
        ]) ?>
    <?php endif; ?>
</div>
<?php endif; ?>
