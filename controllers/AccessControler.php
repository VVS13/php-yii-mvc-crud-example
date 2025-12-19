<?php

namespace app\controllers;

use Yii;
use app\models\User;
use app\models\ConstructionSite;
use app\models\ConstructionSiteTask;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * Info for testing:
 * POST http://localhost:8080/access/check
 * Body (x-www-form-urlencoded):
 * user_id: 1
 * company_id: 1
 * construction_site_id: 1
 */
/**
 * Result access logs location:
 * @runtime/logs/access_log.json
 */

class AccessController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['check' => ['POST']],
            ],
        ];
    }

    /**
     * POST /access/check
     * Validates construction site access
     */
    public function actionCheck()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $userId = $request->post('user_id');
        $companyId = $request->post('company_id');
        $siteId = $request->post('construction_site_id');
        
        // Validate input
        if (empty($userId) || empty($companyId) || empty($siteId)) {
            $result = ['access_allowed' => false, 'reason' => 'missing_required_fields'];
            $this->logAccess($userId, $siteId, 'denied', 'missing_required_fields');
            return $result;
        }

        // Get user
        $user = User::findOne(['id' => $userId, 'company_id' => $companyId]);
        if (!$user) {
            $result = ['access_allowed' => false, 'reason' => 'user_not_found'];
            $this->logAccess($userId, $siteId, 'denied', 'user_not_found');
            return $result;
        }

        // Check if enabled
        if (!$user->enabled) {
            $result = ['access_allowed' => false, 'reason' => 'user_disabled'];
            $this->logAccess($userId, $siteId, 'denied', 'user_disabled');
            return $result;
        }

        // Get site
        $site = ConstructionSite::findOne(['id' => $siteId, 'company_id' => $companyId]);
        if (!$site) {
            $result = ['access_allowed' => false, 'reason' => 'site_not_found'];
            $this->logAccess($userId, $siteId, 'denied', 'site_not_found');
            return $result;
        }

        // Check access by role
        $result = $this->checkAccessByRole($user, $site);
        $this->logAccess($userId, $siteId, $result['access_allowed'] ? 'allowed' : 'denied', $result['reason']);

        return $result;
    }

    protected function checkAccessByRole($user, $site)
    {
        // Admin: always allowed
        if ($user->isAdmin()) {
            return ['access_allowed' => true, 'reason' => null];
        }

        // Manager: assigned site + sufficient level
        if ($user->isManager()) {
            if ($site->manager_id !== $user->id) {
                return ['access_allowed' => false, 'reason' => 'site_not_assigned'];
            }
            if ($user->access_level < $site->access_level_needed) {
                return ['access_allowed' => false, 'reason' => 'insufficient_access_level'];
            }
            return ['access_allowed' => true, 'reason' => null];
        }

        // Worker: active task + sufficient level
        if ($user->isWorker()) {
            $today = date('Y-m-d');
            $activeTask = ConstructionSiteTask::find()
                ->where([
                    'construction_site_id' => $site->id,
                    'assigned_worker_id' => $user->id,
                ])
                ->andWhere(['<=', 'start_date', $today])
                ->andWhere(['>=', 'end_date', $today])
                ->exists();

            if (!$activeTask) {
                return ['access_allowed' => false, 'reason' => 'no_active_task'];
            }
            if ($user->access_level < $site->access_level_needed) {
                return ['access_allowed' => false, 'reason' => 'insufficient_access_level'];
            }
            return ['access_allowed' => true, 'reason' => null];
        }

        return ['access_allowed' => false, 'reason' => 'invalid_user_role'];
    }

    protected function logAccess($userId, $siteId, $result, $reason)
    {
        $logFile = Yii::getAlias('@runtime/logs/access_log.json');
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $logEntry = [
            'user_id' => $userId,
            'construction_site_id' => $siteId,
            'access_date_time' => date('Y-m-d H:i:s'),
            'result' => $result,
            'reason' => $reason
        ];

        file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
    }
}