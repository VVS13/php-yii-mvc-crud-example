<?php

namespace app\controllers;

use Yii;
use app\models\ConstructionSite;
use app\models\ConstructionSiteTask;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ConstructionSiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists construction sites based on role
     */
    public function actionIndex()
    {
        /** @var app\models\User $currentUser */
        $currentUser = Yii::$app->user->identity;
        
        $query = ConstructionSite::find()->where(['construction_sites.company_id' => $currentUser->company_id]);
        
        if ($currentUser->isManager()) {
            // Managers see only sites assigned to them
            $query->andWhere(['manager_id' => $currentUser->id]);
        } elseif ($currentUser->isWorker()) {
            // Workers see sites where they have tasks
            $query->joinWith('tasks')
                ->andWhere(['construction_site_tasks.assigned_worker_id' => $currentUser->id])
                ->distinct();
        }
        // Admins see all sites (no additional filter)
        
        $sites = $query->orderBy(['created_date_time' => SORT_DESC])->all();

        return $this->render('index', [
            'currentUser' => $currentUser,
            'sites' => $sites,
        ]);
    }

    /**
     * Displays a single ConstructionSite with its tasks
     */
    public function actionView($id)
    {
        /** @var app\models\User $currentUser */
        $currentUser = Yii::$app->user->identity;
        $model = $this->findModel($id);
        
        if (!$this->canViewSite($model)) {
            throw new NotFoundHttpException('You do not have permission to view this site.');
        }

        // Get tasks for this site based on role
        $tasksQuery = ConstructionSiteTask::find()
            ->where(['construction_site_id' => $model->id]);
        
        if ($currentUser->isWorker()) {
            // Workers see only their own tasks
            $tasksQuery->andWhere(['assigned_worker_id' => $currentUser->id]);
        }
        
        $tasks = $tasksQuery->orderBy(['start_date' => SORT_ASC])->all();

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'model' => $model,
                'tasks' => $tasks,
                'currentUser' => $currentUser,
            ]);
        }

        return $this->render('view', [
            'model' => $model,
            'tasks' => $tasks,
            'currentUser' => $currentUser,
        ]);
    }

    /**
     * Creates a new ConstructionSite
     */
    public function actionCreate()
    {
        /** @var app\models\User $currentUser */
        $currentUser = Yii::$app->user->identity;
        
        // Only admins can create sites
        if (!$currentUser->isAdmin()) {
            throw new NotFoundHttpException('Access denied.');
        }

        $model = new ConstructionSite();
        $model->company_id = $currentUser->company_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Construction site created successfully.');
            return $this->redirect(['index']);
        }

        // Get enabled managers for dropdown
        $managers = User::find()
            ->where([
                'company_id' => $currentUser->company_id,
                'role' => User::ROLE_MANAGER,
                'enabled' => true,
            ])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return $this->render('create', [
            'model' => $model,
            'managers' => $managers,
        ]);
    }

    /**
     * Updates an existing ConstructionSite
     */
    public function actionUpdate($id)
    {
        /** @var app\models\User $currentUser */
        $currentUser = Yii::$app->user->identity;
        
        // Only admins can update sites
        if (!$currentUser->isAdmin()) {
            throw new NotFoundHttpException('Access denied.');
        }

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Construction site updated successfully.');
            return $this->redirect(['index']);
        }

        // Get enabled managers for dropdown
        $managers = User::find()
            ->where([
                'company_id' => $currentUser->company_id,
                'role' => User::ROLE_MANAGER,
                'enabled' => true,
            ])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return $this->render('update', [
            'model' => $model,
            'managers' => $managers,
        ]);
    }

    /**
     * Deletes an existing ConstructionSite
     */
    public function actionDelete($id)
    {
        /** @var app\models\User $currentUser */
        $currentUser = Yii::$app->user->identity;
        
        // Only admins can delete sites
        if (!$currentUser->isAdmin()) {
            throw new NotFoundHttpException('Access denied.');
        }

        $model = $this->findModel($id);
        
        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Construction site and all its tasks deleted successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to delete construction site.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Check if current user can view a site
     */
    protected function canViewSite($site)
    {
        /** @var app\models\User $currentUser */
        $currentUser = Yii::$app->user->identity;

        // Can't view sites from different company
        if ($site->company_id !== $currentUser->company_id) {
            return false;
        }

        // Admins can view all sites
        if ($currentUser->isAdmin()) {
            return true;
        }

        // Managers can view only assigned sites
        if ($currentUser->isManager()) {
            return $site->manager_id === $currentUser->id;
        }

        // Workers can view sites where they have tasks
        if ($currentUser->isWorker()) {
            return ConstructionSiteTask::find()
                ->where([
                    'construction_site_id' => $site->id,
                    'assigned_worker_id' => $currentUser->id,
                ])
                ->exists();
        }

        return false;
    }

    /**
     * Finds the ConstructionSite model based on its primary key value
     */
    protected function findModel($id)
    {
        if (($model = ConstructionSite::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested construction site does not exist.');
    }
}
