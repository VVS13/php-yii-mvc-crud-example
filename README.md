# Construction Management System

PHP web application for managing construction sites, workers, and tasks with role-based access control.

## Tech Stack

- **Framework:** Yii2
- **Database:** Microsoft SQL Server 2022
- **Frontend:** Bootstrap 5
- **Containerization:** Docker

## Features

- Role-based access control (Admin, Manager, Worker)
- Worker management with skill levels
- Construction site management
- Task assignment and tracking
- Task warning system (access level mismatches, disabled workers)
- AJAX modals for quick viewing

## Usage

### Admin Users
- Full access to all modules
- Create/edit/delete workers, sites, and tasks
- Assign managers to sites
- Assign workers to tasks

### Manager Users
- View assigned construction sites
- Create/edit/delete tasks on assigned sites
- View worker list (read-only)

### Worker Users
- View own profile
- View assigned tasks and construction sites

## Installation & Setup

### 1. Clone Repository
```bash
git clone https://github.com/VVS13/php-yii-mvc-crud-example.git
cd php-yii-mvc-crud-example
```

### 2. Start Docker Containers
```bash
docker compose up -d --build
```

First-time build takes 5-10 minutes.

### 3. Run Database Migrations
```bash
docker exec construction_php php yii migrate --interactive=0
```

This creates tables, seeds an admin user and demo db data(workers, managers, sites, tasks).

### 4. Access Application

Open browser: **http://localhost:8080**

**Default Credentials:**
- Login: `admin`
- Password: `Admin123!`

## Stopping the Application
```bash
docker compose down
```

Data persists in Docker volumes.

## Troubleshooting

### Port 8080 already in use
Edit `docker-compose.yml` and change the port:
```yaml
ports:
  - "8081:8080"
```

### View container logs
```bash
docker logs construction_php
docker logs construction_mssql
```

### Reset everything (WARNING: Deletes all data)
```bash
docker compose down -v
docker compose up -d --build
docker exec construction_php php yii migrate --interactive=0
```