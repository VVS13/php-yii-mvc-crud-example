<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m251217_163001_create_users_table extends Migration
{
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE users (
                id INT IDENTITY(1,1) NOT NULL,
                company_id INT NOT NULL,
                name NVARCHAR(100) NOT NULL,
                surname NVARCHAR(100) NOT NULL,
                birthdate DATE NOT NULL,
                access_level TINYINT NOT NULL DEFAULT 1,
                role VARCHAR(20) NOT NULL,
                login VARCHAR(50) NOT NULL,
                password VARCHAR(60) NOT NULL,
                created_date_time DATETIME2(0) NOT NULL DEFAULT GETUTCDATE(),
                info_edited_date_time DATETIME2(0) NOT NULL DEFAULT GETUTCDATE(),
                enabled BIT NOT NULL DEFAULT 1,
                
                CONSTRAINT PK_users PRIMARY KEY CLUSTERED (id),
                CONSTRAINT UQ_users_login UNIQUE (login),
                CONSTRAINT CHK_users_access_level CHECK (access_level BETWEEN 1 AND 10),
                CONSTRAINT CHK_users_role CHECK (role IN ('Worker', 'Manager', 'Admin'))
            );
            
            CREATE INDEX IX_users_company_id ON users(company_id);
            CREATE INDEX IX_users_role_company ON users(role, company_id);
        ");
    }

    public function safeDown()
    {
        $this->dropTable('users');
    }
}
