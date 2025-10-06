# Project & Task Management System

A **Laravel-based REST API** for managing projects and tasks, featuring **role-based authentication** using **Laravel Sanctum**.

---

##  Features

- **Laravel Sanctum Authentication** – Secure API token-based authentication  
- **Role-Based Access Control** – Admin and Member roles with distinct permissions  
- **Project Management** – Create and manage projects with multiple users  
- **Task Management** – Assign tasks to users with status tracking  
- **RESTful API** – Clean and structured API endpoints  
- **Database Relationships** – Proper foreign key relationships and constraints  

---

##  System Architecture

### Entities

- **Users** – Admin or Member roles  
- **Projects** – Created by admins; can include multiple users  
- **Tasks** – Belong to projects; assigned to users with status and due dates  

### Database Tables

- **users** – Stores user information with role field  
- **projects** – Contains project details with owner relationship  
- **tasks** – Stores task information with project and user assignments  
- **project_user** – Pivot table for project-user many-to-many relationships  
- **personal_access_tokens** – Stores Laravel Sanctum API tokens  

---

##  Requirements

- PHP 8.1 or higher  
- Composer  
- MySQL 5.7 or higher  
- Laravel 11.x  

---

##  Installation & Setup

### Clone Repository

```bash
git clone <your-repo-url>
cd task-management
composer install
cp .env.example .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_management
DB_USERNAME=your_username
DB_PASSWORD=your_password

```

###Generate Application Key

```bash
php artisan key:generate
```

###Run Database Migrations

```bash
php artisan migrate
```
###Seed Database

```bash
php artisan db:seed
```

###Start Development Server

```bash
php artisan serve
```

A ready-to-use **Postman collection** is included in this repository for testing the API.  

📂 Location: [`task-management/postman/ProjectTaskManagement.json`](./postman/ProjectTaskManagement.json)


You can import this file into Postman to quickly start testing all available endpoints.


