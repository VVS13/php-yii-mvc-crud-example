<?php

use yii\db\Migration;

class m251217_165000_seed_initial_admin extends Migration
{
    public function safeUp()
    {
        $password = Yii::$app->security->generatePasswordHash('Admin123!');
        
        $this->execute("
            INSERT INTO users (
                company_id, 
                name, 
                surname, 
                birthdate, 
                access_level, 
                role, 
                login, 
                password,
                created_date_time,
                info_edited_date_time,
                enabled
            ) VALUES (
                1,
                'System',
                'Administrator',
                '2000-01-01',
                10,
                'Admin',
                'admin',
                '{$password}',
                GETUTCDATE(),
                GETUTCDATE(),
                1
            )
        ");
    }

    public function safeDown()
    {
        $this->execute("DELETE FROM users WHERE login = 'admin'");
    }
}
