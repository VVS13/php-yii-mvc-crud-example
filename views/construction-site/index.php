<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\User $currentUser */
/** @var app\models\ConstructionSite[] $sites */

$this->title = 'Construction Sites';
?>

<div class="construction-site-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php if ($currentUser->isAdmin()): ?>
            <?= Html::a('+ Add New Site', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </div>

    <?php if (empty($sites)): ?>
        <div class="alert alert-info">
            <?php if ($currentUser->isWorker()): ?>
                No construction sites found. You will see sites here once tasks are assigned to you.
            <?php else: ?>
                No construction sites found.
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Location</th>
                                <th>Size (m²)</th>
                                <th>Manager</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sites as $site): ?>
                                <tr>
                                    <td><?= Html::encode($site->getLocationWithLevel()) ?></td>
                                    <td><?= Html::encode(number_format($site->location_size, 2)) ?></td>
                                    <td>
                                        <?php if ($site->manager): ?>
                                            <?= Html::encode($site->manager->getFullName()) ?>
                                        <?php else: ?>
                                            <span class="text-muted">Unassigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= Html::encode(Yii::$app->formatter->asDate($site->created_date_time)) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary view-site-btn" 
                                                data-url="<?= Url::to(['view', 'id' => $site->id]) ?>"
                                                title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <?php if ($currentUser->isAdmin()): ?>
                                            <?= Html::a('<i class="bi bi-pencil"></i>', ['update', 'id' => $site->id], [
                                                'class' => 'btn btn-sm btn-warning',
                                                'title' => 'Edit',
                                            ]) ?>
                                            <?= Html::a('<i class="bi bi-trash"></i>', ['delete', 'id' => $site->id], [
                                                'class' => 'btn btn-sm btn-danger',
                                                'title' => 'Delete',
                                                'data-method' => 'post',
                                                'data-confirm' => 'Are you sure you want to delete this site? All tasks will be deleted as well.',
                                            ]) ?>
                                        <?php endif; ?>
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

<!-- Modal for viewing site details -->
<div class="modal fade" id="siteViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="siteViewContent">
            <!-- Content will be loaded here via AJAX -->
        </div>
    </div>
</div>

<?php
$this->registerJs("
    $('.view-site-btn').on('click', function() {
        var url = $(this).data('url');
        $.get(url, function(data) {
            $('#siteViewContent').html(data);
            $('#siteViewModal').modal('show');
        });
    });
");
?>
