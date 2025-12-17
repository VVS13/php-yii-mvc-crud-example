<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%construction_sites}}`.
 */
class m251217_163002_create_construction_sites_table extends Migration
{
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE construction_sites (
                id INT IDENTITY(1,1) NOT NULL,
                company_id INT NOT NULL,
                location NVARCHAR(500) NOT NULL,
                location_size DECIMAL(18,2) NOT NULL DEFAULT 0,
                access_level_needed TINYINT NOT NULL DEFAULT 10,
                manager_id INT NULL,
                created_date_time DATETIME2(0) NOT NULL DEFAULT GETUTCDATE(),
                
                CONSTRAINT PK_construction_sites PRIMARY KEY CLUSTERED (id),
                CONSTRAINT CHK_construction_sites_access_level CHECK (access_level_needed BETWEEN 1 AND 10),
                CONSTRAINT FK_construction_sites_manager FOREIGN KEY (manager_id) REFERENCES users(id)
            );
            
            CREATE INDEX IX_construction_sites_company_id ON construction_sites(company_id);
            CREATE INDEX IX_construction_sites_manager_id ON construction_sites(manager_id);
        ");
    }

    public function safeDown()
    {
        $this->dropTable('construction_sites');
    }
}
