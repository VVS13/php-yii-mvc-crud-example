<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * ConstructionSiteTask model
 *
 * @property int $id
 * @property int $company_id
 * @property string $name
 * @property int $construction_site_id
 * @property int|null $assigned_worker_id
 * @property string $start_date
 * @property string $end_date
 * @property string|null $description
 * @property string $created_date_time
 *
 * @property ConstructionSite $constructionSite
 * @property User $assignedWorker
 */
class ConstructionSiteTask extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'construction_site_tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'construction_site_id', 'start_date', 'end_date'], 'required'],
            [['company_id', 'construction_site_id', 'assigned_worker_id'], 'integer'],
            [['start_date', 'end_date', 'created_date_time'], 'safe'],
            [['description'], 'string', 'max' => 1000],
            [['name'], 'string', 'max' => 100],
            [['construction_site_id'], 'exist', 'skipOnError' => true, 'targetClass' => ConstructionSite::class, 'targetAttribute' => ['construction_site_id' => 'id']],
            [['assigned_worker_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['assigned_worker_id' => 'id']],
            ['end_date', 'validateEndDate'],
        ];
    }

    /**
     * Validate that end_date >= start_date
     */
    public function validateEndDate($attribute, $params)
    {
        if (!empty($this->start_date) && !empty($this->end_date)) {
            if (strtotime($this->end_date) < strtotime($this->start_date)) {
                $this->addError($attribute, 'End date must be greater than or equal to start date.');
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company ID',
            'name' => 'Task Name',
            'construction_site_id' => 'Construction Site',
            'assigned_worker_id' => 'Assigned Worker',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'description' => 'Description',
            'created_date_time' => 'Created',
        ];
    }

    /**
     * Get construction site
     */
    public function getConstructionSite()
    {
        return $this->hasOne(ConstructionSite::class, ['id' => 'construction_site_id']);
    }

    /**
     * Get assigned worker
     */
    public function getAssignedWorker()
    {
        return $this->hasOne(User::class, ['id' => 'assigned_worker_id']);
    }

    /**
     * Check if task is in the past (end_date < today)
     */
    public function isPast()
    {
        return strtotime($this->end_date) < strtotime(date('Y-m-d'));
    }

    /**
     * Check if task is current (today is between start and end dates)
     */
    public function isCurrent()
    {
        $today = strtotime(date('Y-m-d'));
        return strtotime($this->start_date) <= $today && strtotime($this->end_date) >= $today;
    }

    /**
     * Check if task is future (start_date > today)
     */
    public function isFuture()
    {
        return strtotime($this->start_date) > strtotime(date('Y-m-d'));
    }

    /**
     * Check if worker has access level issue
     */
    public function hasAccessLevelIssue()
    {
        if ($this->assignedWorker && $this->constructionSite) {
            return $this->assignedWorker->access_level < $this->constructionSite->access_level_needed;
        }
        return false;
    }

    /**
     * Check if worker is disabled
     */
    public function hasDisabledWorker()
    {
        return $this->assignedWorker && !$this->assignedWorker->enabled;
    }

    /**
     * Check if worker is unassigned
     */
    public function isUnassigned()
    {
        return empty($this->assigned_worker_id);
    }

    /**
     * Get warning status for task
     * Returns: null, 'worker_not_assigned', 'worker_disabled', 'worker_access_level_issue'
     */
    public function getWarningStatus()
    {
        if ($this->isUnassigned()) {
            return 'worker_not_assigned';
        }
        if ($this->hasDisabledWorker()) {
            return 'worker_disabled';
        }
        if ($this->hasAccessLevelIssue()) {
            return 'worker_access_level_issue';
        }
        return null;
    }

    /**
     * Get CSS class for row styling
     */
    public function getRowClass()
    {
        $classes = [];
        
        if ($this->getWarningStatus()) {
            $classes[] = 'task-warning';
        }
        
        if ($this->isPast()) {
            $classes[] = 'task-past';
        }
        
        return implode(' ', $classes);
    }
}
