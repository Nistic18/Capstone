# laravel 12 with Stisla Template

Welcome to the repository for laravel 12 integrated with Stisla Bootstrap 5 template!

## Overview
This repository contains a laravel 12 template that comes pre-configured with authentication from Breeze and utilizes the Stisla Bootstrap 5 template for the UI. It includes various features to help you quickly set up a robust web application.

## Features
- laravel 12
- Stisla Bootstrap 5 Template
- Authentication with Laravel Breeze
- User Profile Management
- Responsive Design
- Pre-configured Dashboard
- Example Pages (Register, Login, Dashboard, Blank Page, Edit User Profile)

## Installation Guide

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js
- npm

### Installation Steps
1. Clone the repository:
    ```bash
    git clone https://github.com/vickymaulana/laravel11-stisla.git
    ```

2. Navigate into the project directory:
    ```bash
    cd laravel11-stisla
    ```

3. Install PHP dependencies:
    ```bash
    composer install
    ```

4. Copy the `.env.example` file and rename it to `.env`:
    ```bash
    cp .env.example .env
    ```

5. Generate application key:
    ```bash
    php artisan key:generate
    ```

6. Run database migrations:
    ```bash
    php artisan migrate
    ```

7. Start the development server:
    ```bash
    php artisan serve
    ```

### Register a New User
To register a new user, follow these steps:

1. Visit the registration page at `http://localhost:8000/register`.
2. Fill in the required fields: Name, Email, Password, and Confirm Password.
3. Click the "Register" button to create a new account.

After registration, you will be redirected to the dashboard.

If you encounter any issues during registration, please check the browser console for errors or refer to the Laravel logs for more details.
