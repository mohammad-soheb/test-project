# Laravel Project

This Laravel project allows you to manage and display data through forms and REST APIs. Follow the instructions below to get started with setting up the environment and running the application.

## Prerequisites

- PHP >= 8.0
- Composer
- MySQL

## Installation and Setup

Follow the steps below to install and configure the application.

### 1. Clone the Repository

Clone the repository to your local machine and navigate into the project directory.

git clone https://github.com/your-username/your-repo-name.git

cd your-repo-name

## 2 .Install all the required dependencies using Composer.

composer install

## 3. Set Up Environment Variables

cp .env.example .env

Edit the .env file and update the following fields:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password


## 4. Generate Application Key

php artisan key:generate


##  5. Run Migrations and Seed the Database

php artisan migrate
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UserSeeder

# Make sure to run Roles seeder first for ensure relation between roles in users table

##  6. Serve the Application

php artisan serve 