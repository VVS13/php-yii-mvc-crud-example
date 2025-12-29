<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\User $currentUser */
/** @var app\models\ConstructionSiteTask[] $tasks */

$this->title = 'Construction Tasks';
?>

<div class="construction-site-task-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php if (!$currentUser->isWorker()): ?>
            <?= Html::a('+ Add New Task', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </div>

    <?php if (empty($tasks)): ?>
        <div class="alert alert-info">
            <?php if ($currentUser->isWorker()): ?>
                No tasks assigned to you yet.
            <?php elseif ($currentUser->isManager()): ?>
                No tasks found on your assigned sites.
            <?php else: ?>
                No tasks found.
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Task Name</th>
                                <th>Site Location</th>
                                <th>Worker</th>
                                <th>Dates</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tasks as $task): ?>
                                <?php
                                $rowClass = [];
                                if ($task->getWarningStatus()) {
                                    $rowClass[] = 'task-warning';
                                }
                                if ($task->isPast()) {
                                    $rowClass[] = 'task-past';
                                }
                                $rowClassStr = !empty($rowClass) ? ' class="' . implode(' ', $rowClass) . '"' : '';
                                ?>
                                <tr<?= $rowClassStr ?>>
                                    <td>
                                        <?= Html::encode($task->name) ?>
                                        <?php if ($task->getWarningStatus()): ?>
                                            <i class="bi bi-exclamation-circle warning-icon" 
                                               title="<?= Html::encode(str_replace('_', ' ', $task->getWarningStatus())) ?>"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= Html::encode($task->constructionSite->getLocationWithLevel()) ?></td>
                                    <td>
                                        <?php if ($task->assignedWorker): ?>
                                            <?= Html::encode($task->assignedWorker->getFullName()) ?>
                                            (<?= Html::encode($task->assignedWorker->access_level) ?>)
                                        <?php else: ?>
                                            <span class="text-muted">Unassigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?= Html::encode(Yii::$app->formatter->asDate($task->start_date)) ?> - 
                                        <?= Html::encode(Yii::$app->formatter->asDate($task->end_date)) ?>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary view-task-btn" 
                                                data-url="<?= Url::to(['view', 'id' => $task->id]) ?>"
                                                title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal for viewing task details -->
<div class="modal fade" id="taskViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="taskViewContent">
            <!-- Content will be loaded here via AJAX -->
        </div>
    </div>
</div>

<?php
$this->registerJs("
    $('.view-task-btn').on('click', function() {
        var url = $(this).data('url');
        $.get(url, function(data) {
            $('#taskViewContent').html(data);
            $('#taskViewModal').modal('show');
        });
    });
");
?>