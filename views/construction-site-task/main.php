<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            padding-top: 56px;
        }
        .sidebar {
            position: fixed;
            top: 56px;
            bottom: 0;
            left: 0;
            width: 200px;
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
            padding: 20px 0;
            overflow-y: auto;
            z-index: 100;
        }
        .sidebar .nav {
            flex-direction: column;
        }
        .sidebar .nav-item {
            width: 100%;
        }
        .sidebar .nav-link {
            color: #333;
            padding: 10px 20px;
            display: block;
            border-radius: 0;
        }
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
        }
        .main-content {
            margin-left: 200px;
            padding: 20px;
        }
        .logout-button {
            position: absolute;
            bottom: 20px;
            width: 160px;
            left: 20px;
        }
        /* Task warning styles */
        .task-warning {
            border-left: 4px solid #dc3545 !important;
        }
        .task-past {
            opacity: 0.5;
        }
        .warning-icon {
            color: #dc3545;
            cursor: help;
        }
    </style>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => 'Construction Management System',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-lg navbar-dark bg-dark fixed-top',
        ],
    ]);
    
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto'],
        'items' => [
            ['label' => 'User: ' . Yii::$app->user->identity->getFullName(), 'url' => '#', 'options' => ['class' => 'text-white']],
        ],
    ]);
    
    NavBar::end();
    ?>

    <!-- Sidebar -->
    <div class="sidebar">
        <?php
        echo Nav::widget([
            'options' => ['class' => 'nav flex-column'],
            'items' => [
                [
                    'label' => 'Worker Management',
                    'url' => ['/worker-management/index'],
                ],
                [
                    'label' => 'Construction Sites',
                    'url' => ['/construction-site/index'],
                ],
                [
                    'label' => 'Construction Tasks',
                    'url' => ['/construction-site-task/index'],
                ],
            ],
        ]);
        ?>
        
        <?= Html::beginForm(['/site/logout'], 'post', ['class' => 'logout-button']) ?>
            <?= Html::submitButton('Logout', ['class' => 'btn btn-danger w-100']) ?>
        <?= Html::endForm() ?>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?= $content ?>
    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
