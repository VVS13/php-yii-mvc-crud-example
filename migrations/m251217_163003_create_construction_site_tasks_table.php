<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%construction_site_tasks}}`.
 */
class m251217_163003_create_construction_site_tasks_table extends Migration
{
    public function safeUp()
    {
        $this->execute("
            CREATE TABLE construction_site_tasks (
                id INT IDENTITY(1,1) NOT NULL,
                company_id INT NOT NULL,
                name NVARCHAR(100) NOT NULL,
                construction_site_id INT NOT NULL,
                assigned_worker_id INT NULL,
                start_date DATE NOT NULL,
                end_date DATE NOT NULL,
                description NVARCHAR(1000) NULL,
                created_date_time DATETIME2(0) NOT NULL DEFAULT GETUTCDATE(),
                
                CONSTRAINT PK_construction_site_tasks PRIMARY KEY CLUSTERED (id),
                CONSTRAINT CHK_construction_site_tasks_dates CHECK (end_date >= start_date),
                CONSTRAINT FK_construction_site_tasks_site FOREIGN KEY (construction_site_id) 
                    REFERENCES construction_sites(id) ON DELETE CASCADE,
                CONSTRAINT FK_construction_site_tasks_worker FOREIGN KEY (assigned_worker_id) 
                    REFERENCES users(id)
            );
            
            CREATE INDEX IX_construction_site_tasks_site_id ON construction_site_tasks(construction_site_id);
            CREATE INDEX IX_construction_site_tasks_worker_id ON construction_site_tasks(assigned_worker_id);
            CREATE INDEX IX_construction_site_tasks_company_id ON construction_site_tasks(company_id);
            CREATE INDEX IX_construction_site_tasks_dates ON construction_site_tasks(start_date, end_date);
        ");
    }

    public function safeDown()
    {
        $this->dropTable('construction_site_tasks');
    }
}
