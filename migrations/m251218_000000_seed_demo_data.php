<?php

use yii\db\Migration;

class m251218_000000_seed_demo_data extends Migration
{
    public function safeUp()
    {
        // Hash password
        $password = Yii::$app->security->generatePasswordHash('password');
        
        $this->execute("
            INSERT INTO users (company_id, name, surname, birthdate, access_level, role, login, password, created_date_time, info_edited_date_time, enabled)
            VALUES 
            (1, 'Michael', 'Anderson', '1982-05-15', 8, 'Manager', 'manager1', '{$password}', GETUTCDATE(), GETUTCDATE(), 1),
            (1, 'Sarah', 'Johnson', '1985-08-22', 6, 'Manager', 'manager2', '{$password}', GETUTCDATE(), GETUTCDATE(), 1),
            (1, 'David', 'Chen', '1980-11-30', 9, 'Manager', 'manager3', '{$password}', GETUTCDATE(), GETUTCDATE(), 1)
        ");
        
        $this->execute("
            INSERT INTO users (company_id, name, surname, birthdate, access_level, role, login, password, created_date_time, info_edited_date_time, enabled)
            VALUES 
            (1, 'John', 'Smith', '1990-03-10', 8, 'Worker', 'worker1', '{$password}', GETUTCDATE(), GETUTCDATE(), 1),
            (1, 'Emily', 'Davis', '1992-07-18', 5, 'Worker', 'worker2', '{$password}', GETUTCDATE(), GETUTCDATE(), 1),
            (1, 'Robert', 'Wilson', '1995-12-05', 3, 'Worker', 'worker3', '{$password}', GETUTCDATE(), GETUTCDATE(), 1)
        ");
        
        $this->execute("
            INSERT INTO construction_sites (company_id, location, location_size, access_level_needed, manager_id, created_date_time)
            VALUES 
            (1, 'Wall Street Financial District, New York', 5000.00, 7, 2, GETUTCDATE()),
            (1, 'Big Ben Clock Tower, London', 2500.00, 5, 3, GETUTCDATE()),
            (1, 'The Great Wall of China Restoration Site', 15000.00, 9, 4, GETUTCDATE())
        ");
        
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $lastWeek = date('Y-m-d', strtotime('-7 days'));
        $nextWeek = date('Y-m-d', strtotime('+7 days'));
        $nextMonth = date('Y-m-d', strtotime('+30 days'));
        
        $this->execute("
            INSERT INTO construction_site_tasks (company_id, name, construction_site_id, assigned_worker_id, start_date, end_date, description, created_date_time)
            VALUES 
            (1, 'Foundation Reinforcement', 1, 5, '{$yesterday}', '{$nextWeek}', 'Reinforce foundation structure with steel beams and concrete', GETUTCDATE()),
            (1, 'Facade Installation', 1, 6, '{$nextWeek}', '{$nextMonth}', 'Install modern glass facade on main building', GETUTCDATE()),
            (1, 'Site Preparation', 1, 7, '{$lastWeek}', '{$yesterday}', 'Clear and prepare construction site area', GETUTCDATE())
        ");
        
        $this->execute("
            INSERT INTO construction_site_tasks (company_id, name, construction_site_id, assigned_worker_id, start_date, end_date, description, created_date_time)
            VALUES 
            (1, 'Clock Mechanism Maintenance', 2, 6, '{$yesterday}', '{$nextWeek}', 'Service and repair historic clock mechanism', GETUTCDATE()),
            (1, 'Tower Exterior Cleaning', 2, NULL, '{$nextWeek}', '{$nextMonth}', 'Clean and restore exterior stonework', GETUTCDATE()),
            (1, 'Safety Scaffolding Setup', 2, 5, '{$lastWeek}', '{$yesterday}', 'Install safety scaffolding around tower', GETUTCDATE())
        ");
        
        $this->execute("
            INSERT INTO construction_site_tasks (company_id, name, construction_site_id, assigned_worker_id, start_date, end_date, description, created_date_time)
            VALUES 
            (1, 'Ancient Stone Restoration', 3, 5, '{$yesterday}', '{$nextWeek}', 'Restore damaged sections using traditional methods', GETUTCDATE()),
            (1, 'Watchtower Reconstruction', 3, 7, '{$nextWeek}', '{$nextMonth}', 'Rebuild collapsed watchtower section', GETUTCDATE()),
            (1, 'Archaeological Survey', 3, 6, '{$lastWeek}', '{$yesterday}', 'Survey and document historical features', GETUTCDATE())
        ");
    }

    public function safeDown()
    {
        // Delete in reverse order due to foreign keys
        $this->execute("DELETE FROM construction_site_tasks WHERE company_id = 1 AND id > (SELECT MAX(id) FROM (SELECT id FROM construction_site_tasks WHERE construction_site_id IN (1,2,3)) t)");
        $this->execute("DELETE FROM construction_sites WHERE company_id = 1 AND location IN ('Wall Street Financial District, New York', 'Big Ben Clock Tower, London', 'The Great Wall of China Restoration Site')");
        $this->execute("DELETE FROM users WHERE company_id = 1 AND login IN ('manager1', 'manager2', 'manager3', 'worker1', 'worker2', 'worker3')");
    }
}
