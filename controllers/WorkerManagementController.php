<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class WorkerManagementController extends Controller
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
                    'toggle-status' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all users based on role
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        
        // Build query based on role
        $query = User::find()->where(['company_id' => $user->company_id]);
        
        if ($user->isManager()) {
            // Managers see only Workers
            $query->andWhere(['role' => User::ROLE_WORKER]);
        } elseif ($user->isWorker()) {
            // Workers see nothing (handled in view)
            $query->andWhere(['id' => -1]); // Return empty result
        }
        // Admins see everyone (no additional filter)
        
        $users = $query->orderBy(['role' => SORT_DESC, 'name' => SORT_ASC])->all();

        return $this->render('index', [
            'currentUser' => $user,
            'users' => $users,
        ]);
    }

    /**
     * Displays a single User model
     */
    public function actionView($id)
    {
        $currentUser = Yii::$app->user->identity;
        $model = $this->findModel($id);
        
        // Check access permissions
        if (!$this->canViewUser($model)) {
            throw new NotFoundHttpException('You do not have permission to view this user.');
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
     * Creates a new User model
     */
    public function actionCreate()
    {
        $user = Yii::$app->user->identity;
        
        // Only admins can create users
        if (!$user->isAdmin()) {
            throw new NotFoundHttpException('Access denied.');
        }

        $model = new User();
        $model->company_id = $user->company_id;
        $model->enabled = true;

        if ($model->load(Yii::$app->request->post())) {
            // Prevent creating Admin users via UI
            if ($model->role === User::ROLE_ADMIN) {
                Yii::$app->session->setFlash('error', 'Cannot create Admin users through UI. Use database migration.');
                return $this->render('create', ['model' => $model]);
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'User created successfully.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model
     */
    public function actionUpdate($id)
    {
        $currentUser = Yii::$app->user->identity;
        $model = $this->findModel($id);

        // Check if current user can edit this user
        if (!$this->canEditUser($model)) {
            throw new NotFoundHttpException('You do not have permission to edit this user.');
        }

        if ($model->load(Yii::$app->request->post())) {
            // Prevent changing role to Admin
            if ($model->role === User::ROLE_ADMIN) {
                Yii::$app->session->setFlash('error', 'Cannot change role to Admin through UI.');
                return $this->render('update', ['model' => $model]);
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'User updated successfully.');
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Toggle user enabled/disabled status
     */
    public function actionToggleStatus($id)
    {
        $currentUser = Yii::$app->user->identity;
        $model = $this->findModel($id);

        // Check if current user can edit this user
        if (!$this->canEditUser($model)) {
            throw new NotFoundHttpException('Access denied.');
        }

        $model->enabled = !$model->enabled;
        if ($model->save(false)) {
            $status = $model->enabled ? 'enabled' : 'disabled';
            Yii::$app->session->setFlash('success', "User {$status} successfully.");
        }

        return $this->redirect(['index']);
    }

    /**
     * Check if current user can view another user
     */
    protected function canViewUser($user)
{
    $currentUser = Yii::$app->user->identity;

    // Can't view users from different company
    if ($user->company_id !== $currentUser->company_id) {
        return false;
    }

    // Workers can only view themselves
    if ($currentUser->isWorker()) {
        return $user->id === $currentUser->id;
    }

    // Managers can view Workers
    if ($currentUser->isManager()) {
        return $user->isWorker();
    }

    // Admins can view everyone
    return true;
}

    /**
     * Check if current user can edit another user
     */
    protected function canEditUser($user)
{
    $currentUser = Yii::$app->user->identity;

    // Only admins can edit
    if (!$currentUser->isAdmin()) {
        return false;
    }

    // Can't edit users from different company
    if ($user->company_id !== $currentUser->company_id) {
        return false;
    }

    // Can't edit other Admins
    if ($user->isAdmin()) {
        return false;
    }

    return true;
}

    /**
     * Finds the User model based on its primary key value
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested user does not exist.');
    }
}
