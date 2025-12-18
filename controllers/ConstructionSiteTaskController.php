<?php

namespace app\controllers;

use Yii;
use app\models\ConstructionSiteTask;
use app\models\ConstructionSite;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ConstructionSiteTaskController extends Controller
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
 * Lists tasks based on role
 */
public function actionIndex()
{
    /** @var app\models\User $currentUser */
    $currentUser = Yii::$app->user->identity;
    
    $query = ConstructionSiteTask::find()
        ->joinWith(['constructionSite', 'assignedWorker'])
        ->where(['construction_site_tasks.company_id' => $currentUser->company_id]);
    
    if ($currentUser->isManager()) {
        // Managers see tasks only on sites assigned to them
        $query->andWhere(['construction_sites.manager_id' => $currentUser->id]);
    } elseif ($currentUser->isWorker()) {
        // Workers see only their own tasks
        $query->andWhere(['construction_site_tasks.assigned_worker_id' => $currentUser->id]);
    }
    // Admins see all tasks
    
    $tasks = $query->all();
    
    // Separate tasks into batches
    $currentTasks = [];
    $futureTasks = [];
    $pastTasks = [];

    foreach ($tasks as $task) {
        if ($task->isCurrent()) {
            $currentTasks[] = $task;
        } elseif ($task->isFuture()) {
            $futureTasks[] = $task;
        } else {
            $pastTasks[] = $task;
        }
    }

    // Sort each batch by start date
    usort($currentTasks, function($a, $b) { 
        return strcmp($a->start_date, $b->start_date); 
    });
    usort($futureTasks, function($a, $b) { 
        return strcmp($a->start_date, $b->start_date); 
    });
    usort($pastTasks, function($a, $b) { 
        return strcmp($a->start_date, $b->start_date); 
    });

    // Merge: current -> future -> past
    $tasks = array_merge($currentTasks, $futureTasks, $pastTasks);

    return $this->render('index', [
        'currentUser' => $currentUser,
        'tasks' => $tasks,
    ]);
}

    /**
     * Displays a single task
     */
    public function actionView($id)
    {
        $currentUser = Yii::$app->user->identity;
        $model = $this->findModel($id);
        
        if (!$this->canViewTask($model)) {
            throw new NotFoundHttpException('You do not have permission to view this task.');
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'model' => $model,
                'currentUser' => $currentUser,
            ]);
        }

        return $this->render('view', [
            'model' => $model,
            'currentUser' => $currentUser,
        ]);
    }

    /**
     * Creates a new task
     */
    public function actionCreate()
    {
        /** @var app\models\User $currentUser */
        $currentUser = Yii::$app->user->identity;
        
        // Only admins and managers can create tasks
        if ($currentUser->isWorker()) {
            throw new NotFoundHttpException('Access denied.');
        }

        $model = new ConstructionSiteTask();
        $model->company_id = $currentUser->company_id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Check for warnings
            $warning = $model->getWarningStatus();
            if ($warning) {
                $messages = [
                    'worker_not_assigned' => 'Task created successfully. Warning: No worker assigned to this task.',
                    'worker_disabled' => 'Task created successfully. Warning: Assigned worker is disabled.',
                    'worker_access_level_issue' => 'Task created successfully. Warning: Worker access level (' . $model->assignedWorker->access_level . ') is below site requirement (' . $model->constructionSite->access_level_needed . '). Physical access may be denied.',
                ];
                Yii::$app->session->setFlash('warning', $messages[$warning]);
            } else {
                Yii::$app->session->setFlash('success', 'Task created successfully.');
            }
            return $this->redirect(['index']);
        }

        // Get available sites based on role
        $sitesQuery = ConstructionSite::find()
            ->where(['company_id' => $currentUser->company_id]);
        
        if ($currentUser->isManager()) {
            $sitesQuery->andWhere(['manager_id' => $currentUser->id]);
        }
        
        $sites = $sitesQuery->orderBy(['location' => SORT_ASC])->all();
        
        // Get enabled workers
        $workers = User::find()
            ->where([
                'company_id' => $currentUser->company_id,
                'role' => User::ROLE_WORKER,
                'enabled' => true,
            ])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return $this->render('create', [
            'model' => $model,
            'sites' => $sites,
            'workers' => $workers,
        ]);
    }

    /**
     * Updates an existing task
     */
    public function actionUpdate($id)
    {
        /** @var app\models\User $currentUser */
        $currentUser = Yii::$app->user->identity;
        $model = $this->findModel($id);

        if (!$this->canEditTask($model)) {
            throw new NotFoundHttpException('You do not have permission to edit this task.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Check for warnings
            $warning = $model->getWarningStatus();
            if ($warning) {
                $messages = [
                    'worker_not_assigned' => 'Task updated successfully. Warning: No worker assigned to this task.',
                    'worker_disabled' => 'Task updated successfully. Warning: Assigned worker is disabled.',
                    'worker_access_level_issue' => 'Task updated successfully. Warning: Worker access level (' . $model->assignedWorker->access_level . ') is below site requirement (' . $model->constructionSite->access_level_needed . '). Physical access may be denied.',
                ];
                Yii::$app->session->setFlash('warning', $messages[$warning]);
            } else {
                Yii::$app->session->setFlash('success', 'Task updated successfully.');
            }
            return $this->redirect(['index']);
        }

        // Get available sites based on role
        $sitesQuery = ConstructionSite::find()
            ->where(['company_id' => $currentUser->company_id]);
        
        if ($currentUser->isManager()) {
            $sitesQuery->andWhere(['manager_id' => $currentUser->id]);
        }
        
        $sites = $sitesQuery->orderBy(['location' => SORT_ASC])->all();
        
        // Get enabled workers
        $workers = User::find()
            ->where([
                'company_id' => $currentUser->company_id,
                'role' => User::ROLE_WORKER,
                'enabled' => true,
            ])
            ->orderBy(['name' => SORT_ASC])
            ->all();

        return $this->render('update', [
            'model' => $model,
            'sites' => $sites,
            'workers' => $workers,
        ]);
    }

    /**
     * Deletes a task
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (!$this->canEditTask($model)) {
            throw new NotFoundHttpException('Access denied.');
        }

        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Task deleted successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to delete task.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Check if current user can view a task
     */
    protected function canViewTask($task)
    {
        /** @var app\models\User $currentUser */
        $currentUser = Yii::$app->user->identity;

        // Can't view tasks from different company
        if ($task->company_id !== $currentUser->company_id) {
            return false;
        }

        // Admins can view all tasks
        if ($currentUser->isAdmin()) {
            return true;
        }

        // Managers can view tasks on their assigned sites
        if ($currentUser->isManager()) {
            return $task->constructionSite && 
                   $task->constructionSite->manager_id === $currentUser->id;
        }

        // Workers can view only their own tasks
        if ($currentUser->isWorker()) {
            return $task->assigned_worker_id === $currentUser->id;
        }

        return false;
    }

    /**
     * Check if current user can edit a task
     */
    protected function canEditTask($task)
    {
        /** @var app\models\User $currentUser */
        $currentUser = Yii::$app->user->identity;

        // Workers can't edit
        if ($currentUser->isWorker()) {
            return false;
        }

        // Can't edit tasks from different company
        if ($task->company_id !== $currentUser->company_id) {
            return false;
        }

        // Admins can edit all tasks
        if ($currentUser->isAdmin()) {
            return true;
        }

        // Managers can edit tasks only on Ftheir assigned sites
        if ($currentUser->isManager()) {
            return $task->constructionSite && 
                   $task->constructionSite->manager_id === $currentUser->id;
        }

        return false;
    }

    /**
     * Finds the ConstructionSiteTask model based on its primary key value
     */
    protected function findModel($id)
    {
        if (($model = ConstructionSiteTask::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested task does not exist.');
    }
}
