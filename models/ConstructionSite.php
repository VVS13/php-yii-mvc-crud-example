<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * ConstructionSite model
 *
 * @property int $id
 * @property int $company_id
 * @property string $location
 * @property float $location_size
 * @property int $access_level_needed
 * @property int|null $manager_id
 * @property string $created_date_time
 *
 * @property User $manager
 * @property ConstructionSiteTask[] $tasks
 */
class ConstructionSite extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'construction_sites';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['location'], 'required'],
            [['company_id', 'access_level_needed', 'manager_id'], 'integer'],
            [['location_size'], 'number'],
            [['created_date_time'], 'safe'],
            [['location'], 'string', 'max' => 500],
            [['access_level_needed'], 'integer', 'min' => 1, 'max' => 10],
            [['location_size'], 'default', 'value' => 0],
            [['access_level_needed'], 'default', 'value' => 10],
            [['manager_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['manager_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company ID',
            'location' => 'Location',
            'location_size' => 'Location Size (m²)',
            'access_level_needed' => 'Access Level Required',
            'manager_id' => 'Manager',
            'created_date_time' => 'Created',
        ];
    }

    /**
     * Get manager assigned to this site
     */
    public function getManager()
    {
        return $this->hasOne(User::class, ['id' => 'manager_id']);
    }

    /**
     * Get all tasks for this site
     */
    public function getTasks()
    {
        return $this->hasMany(ConstructionSiteTask::class, ['construction_site_id' => 'id']);
    }

    /**
     * Get location with access level in brackets
     */
    public function getLocationWithLevel()
    {
        return $this->location . ' (' . $this->access_level_needed . ')';
    }

    /**
     * Before delete - tasks will cascade delete automatically via DB constraint
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            // Cascade delete is handled by database constraint
            return true;
        }
        return false;
    }
}
