## Installation Guide

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js
- npm

### Installation Steps
1. Clone the repository:
    ```bash
    git clone https://github.com/Nistic18/Capstone
    ```

2. Navigate into the project directory:
    ```bash
    cd ../../xampp/htdocs/Capstone
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

6. Run database migrations and seed:
    ```bash
    php artisan migrate
    ```
    ```bash
    php artisan db:seed
    ```
7. Run image link:
    ```bash
    php artisan storage:link
    ```
8. Start the development server:
    ```bash
    php artisan serve
    ```
9. For Gemini API
    ```bash
    composer require guzzlehttp/guzzle
    ```
    
10. Edit .env folder for Gemini API Key
    ```bash
    GOOGLE_GEMINI_API_KEY="Your API KEY"
    GOOGLE_GEMINI_API_URL=https://generativelanguage.googleapis.com/v1beta
    GEMINI_RATE_LIMIT=15 # Increased from 5 to 15 requests per minute per user
    ```

### Register a New User
To register a new user, follow these steps:

1. Visit the registration page at `http://localhost:8000/register`.
2. Fill in the required fields: Name, Email, Password, and Confirm Password.
3. Click the "Register" button to create a new account.

After registration, you will be redirected to the dashboard.

If you encounter any issues during registration, please check the browser console for errors or refer to the Laravel logs for more details.
