<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\User;

/** @var yii\web\View $this */
/** @var app\models\User $currentUser */
/** @var app\models\User[] $users */

$this->title = 'Worker Management';
?>

<div class="worker-management-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Profile -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">MY PROFILE</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> <?= Html::encode($currentUser->getFullName()) ?></p>
                    <p><strong>Birthdate:</strong> <?= Html::encode($currentUser->birthdate) ?></p>
                    <p><strong>Role:</strong> <?= Html::encode($currentUser->role) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Access Level:</strong> <?= Html::encode($currentUser->access_level) ?></p>
                    <p><strong>Login:</strong> <?= Html::encode($currentUser->login) ?></p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-<?= $currentUser->enabled ? 'success' : 'secondary' ?>">
                            <?= $currentUser->enabled ? 'Enabled' : 'Disabled' ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php if (!$currentUser->isWorker()): ?>
        <!-- User List -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <?php if ($currentUser->isAdmin()): ?>
                        ALL COMPANY USERS
                    <?php else: ?>
                        ALL WORKERS
                    <?php endif; ?>
                </h5>
                <?php if ($currentUser->isAdmin()): ?>
                    <?= Html::a('+ Add New User', ['create'], ['class' => 'btn btn-success']) ?>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($users)): ?>
                    <p class="text-muted">No users found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Level</th>
                                    <th>Status</th>
                                    <th>View</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= Html::encode($user->getFullName()) ?></td>
                                        <td><?= Html::encode($user->role) ?></td>
                                        <td><?= Html::encode($user->access_level) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $user->enabled ? 'success' : 'secondary' ?>">
                                                <?= $user->enabled ? 'Enabled' : 'Disabled' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary view-user-btn" 
                                                    data-url="<?= Url::to(['view', 'id' => $user->id]) ?>">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal for viewing user details -->
<div class="modal fade" id="userViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="userViewContent">
            <!-- Content will be loaded here via AJAX -->
        </div>
    </div>
</div>

<?php
$this->registerJs("
    $('.view-user-btn').on('click', function() {
        var url = $(this).data('url');
        $.get(url, function(data) {
            $('#userViewContent').html(data);
            $('#userViewModal').modal('show');
        });
    });
");
?>
