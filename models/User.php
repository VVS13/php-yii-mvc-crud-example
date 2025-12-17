<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property int $id
 * @property int $company_id
 * @property string $name
 * @property string $surname
 * @property string $birthdate
 * @property int $access_level
 * @property string $role
 * @property string $login
 * @property string $password
 * @property string $created_date_time
 * @property string $info_edited_date_time
 * @property bool $enabled
 *
 * @property ConstructionSite[] $managedSites
 * @property ConstructionSiteTask[] $assignedTasks
 */
class User extends ActiveRecord implements IdentityInterface
{
    const ROLE_WORKER = 'Worker';
    const ROLE_MANAGER = 'Manager';
    const ROLE_ADMIN = 'Admin';

    public $password_plain; // For form input (not saved to DB)
    public $authKey; // Required by IdentityInterface but not used

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'surname', 'birthdate', 'role', 'login'], 'required'],
            [['company_id', 'access_level'], 'integer'],
            [['birthdate', 'created_date_time', 'info_edited_date_time'], 'safe'],
            [['enabled'], 'boolean'],
            [['name', 'surname'], 'string', 'max' => 100],
            [['role'], 'string', 'max' => 20],
            [['login'], 'string', 'max' => 50],
            [['password'], 'string', 'max' => 60],
            [['password_plain'], 'string', 'min' => 8],
            [['access_level'], 'integer', 'min' => 1, 'max' => 10],
            [['role'], 'in', 'range' => [self::ROLE_WORKER, self::ROLE_MANAGER, self::ROLE_ADMIN]],
            [['login'], 'unique'],
            [['access_level'], 'default', 'value' => 1],
            [['enabled'], 'default', 'value' => true],
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
            'name' => 'Name',
            'surname' => 'Surname',
            'birthdate' => 'Birthdate',
            'access_level' => 'Access Level',
            'role' => 'Role',
            'login' => 'Login',
            'password' => 'Password',
            'password_plain' => 'Password',
            'created_date_time' => 'Created',
            'info_edited_date_time' => 'Last Updated',
            'enabled' => 'Enabled',
        ];
    }

    /**
     * Before save - hash password if provided
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Hash password if password_plain is set
            if (!empty($this->password_plain)) {
                $this->password = Yii::$app->security->generatePasswordHash($this->password_plain);
            }

            // Set info_edited_date_time on update
            if (!$insert) {
                $this->info_edited_date_time = date('Y-m-d H:i:s');
            }

            return true;
        }
        return false;
    }

    /**
     * Validate password
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Get construction sites managed by this user (if Manager)
     */
    public function getManagedSites()
    {
        return $this->hasMany(ConstructionSite::class, ['manager_id' => 'id']);
    }

    /**
     * Get tasks assigned to this user (if Worker)
     */
    public function getAssignedTasks()
    {
        return $this->hasMany(ConstructionSiteTask::class, ['assigned_worker_id' => 'id']);
    }

    /**
     * Get full name
     */
    public function getFullName()
    {
        return $this->name . ' ' . $this->surname;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user is manager
     */
    public function isManager()
    {
        return $this->role === self::ROLE_MANAGER;
    }

    /**
     * Check if user is worker
     */
    public function isWorker()
    {
        return $this->role === self::ROLE_WORKER;
    }

    // IdentityInterface methods
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'enabled' => true]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null; // Not using access tokens
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Find user by login
     */
    public static function findByLogin($login)
    {
        return static::findOne(['login' => $login, 'enabled' => true]);
    }
}
