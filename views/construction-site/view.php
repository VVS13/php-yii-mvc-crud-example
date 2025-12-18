<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ConstructionSite $model */
/** @var app\models\ConstructionSiteTask[] $tasks */
/** @var app\models\User $currentUser */

$canEdit = $currentUser->isAdmin();
?>

<div class="modal-header">
    <h5 class="modal-title">Construction Site Details</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row mb-3">
        <div class="col-md-6">
            <p><strong>Location:</strong> <?= Html::encode($model->location) ?></p>
            <p><strong>Size:</strong> <?= Html::encode(number_format($model->location_size, 2)) ?> m²</p>
        </div>
        <div class="col-md-6">
            <p><strong>Access Level Required:</strong> <?= Html::encode($model->access_level_needed) ?></p>
            <p><strong>Manager:</strong> 
                <?php if ($model->manager): ?>
                    <?= Html::encode($model->manager->getFullName()) ?>
                <?php else: ?>
                    <span class="text-muted">Unassigned</span>
                <?php endif; ?>
            </p>
        </div>
    </div>

    <hr>

    <h6 class="mb-3">Tasks at this Site</h6>
    <?php if (empty($tasks)): ?>
        <p class="text-muted">No tasks found for this site.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Task Name</th>
                        <th>Worker</th>
                        <th>Dates</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?= Html::encode($task->name) ?></td>
                            <td>
                                <?php if ($task->assignedWorker): ?>
                                    <?= Html::encode($task->assignedWorker->getFullName()) ?>
                                <?php else: ?>
                                    <span class="text-muted">Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= Html::encode(Yii::$app->formatter->asDate($task->start_date)) ?> - 
                                <?= Html::encode(Yii::$app->formatter->asDate($task->end_date)) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php if ($canEdit): ?>
<div class="modal-footer">
    <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $model->id], [
        'class' => 'btn btn-danger',
        'data-method' => 'post',
        'data-confirm' => 'Are you sure you want to delete this site? All tasks will be deleted as well.',
    ]) ?>
</div>
<?php endif; ?>
