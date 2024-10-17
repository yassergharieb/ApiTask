# Laravel API Project README

## Overview

This project demonstrates the creation of a comprehensive API system using Laravel 10. The system includes user authentication via Laravel Sanctum, tag and post management, scheduled background jobs, and an advanced caching mechanism for performance optimization. The database uses SQLite, making it lightweight for development purposes.

## Features

1. **User Authentication with Sanctum**:
   - **Register** new users with name, phone number, and password.
   - **Login** users and return an access token.
   - Users are assigned a random 6-digit verification code upon registration.
   - Only **verified** users can log in.
   
2. **Tag Management**:
   - Authenticated users can manage tags (CRUD operations).
   - Tag names must be unique.

3. **Post Management**:
   - Authenticated users can manage their own posts (CRUD operations).
   - Posts can be **soft deleted** and restored.
   - Posts have a **many-to-many** relationship with tags.
   - Pinned posts are displayed first.
   
4. **Scheduled Jobs**:
   - A job that **force-deletes** soft-deleted posts older than 30 days.
   - A job that makes an HTTP request to `https://randomuser.me/api/` every six hours and logs the results.

5. **Statistics**:
   - A `/stats` API endpoint returns:
     - Total number of users.
     - Total number of posts.
     - Number of users with zero posts.
   - The results are cached and updated with changes to users or posts.

---

## Installation and Setup

### 1. Create a New Laravel Project
```bash
composer create-project --prefer-dist laravel/laravel ApiTask
```

### 2. Configure SQLite Database
Open the `.env` file and set the database connection to SQLite:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/path_to_your_database/database.sqlite
```
Create the `database.sqlite` file in the `database` folder:
```bash
touch database/database.sqlite
```

### 3. Install Sanctum
```bash
composer require laravel/sanctum
```
Publish the Sanctum configuration:
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```
Add Sanctum's middleware to `api` middleware group in `app/Http/Kernel.php`:
```php
'api' => [
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    'throttle:api',
    \Illuminate\Routing\Middleware\SubstituteBindings::class,
],
```

### 4. Create the Authentication System

#### Register Route (`/register`)
- Fields: `name`, `phone_number`, `password`
- A 6-digit verification code is generated for every user and logged.
- Return user data and access token after successful registration.

#### Login Route (`/login`)
- Users can only log in after verifying their account with the code sent.
- Return user data and access token upon successful login.

#### Verification Route
- Endpoint to verify the code sent to the user.

### 5. Create Tags API Resource
- Authenticated users can **view**, **store**, **update**, and **delete** tags.
- The `name` field is **required** and must be **unique**.

### 6. Create Posts API Resource
- Authenticated users can:
  - View their own posts.
  - Create new posts with:
    - `title` (required, max 255 characters)
    - `body` (required)
    - `cover_image` (required on creation, optional on update)
    - `pinned` (boolean)
    - One or more tags (many-to-many relationship)
  - Update and delete their posts (soft delete).
  - View their deleted posts and restore them.
  - Pinned posts appear first in the user's post list.

### 7. Scheduled Jobs

#### Daily Job to Delete Soft-Deleted Posts
- A job runs daily to force-delete posts that were soft-deleted for more than 30 days.

#### Job to Log Random User API Data
- A job runs every 6 hours, makes an HTTP request to `https://randomuser.me/api/`, and logs the result.

### 8. `/stats` API Endpoint
- Returns:
  - Total number of users.
  - Total number of posts.
  - Number of users with 0 posts.
- The data is cached and automatically updated when changes occur to users or posts.

------------------------------------

## Scheduled Jobs

- **Daily Job**: Deletes soft-deleted posts older than 30 days.
- **Every 6 Hours Job**: Fetches data from `https://randomuser.me/api/` and logs the response.

---


### Installation and Setup

#### 1. Clone the Laravel Project from GitHub

Run the following command to clone the repository:

```bash
git clone https://github.com/yassergharieb/ApiTask.git
```

Navigate into the project directory:

```bash
cd ApiTask
```

#### 2. Install the Dependencies

After cloning the project, install all the necessary dependencies by running:

```bash
composer install
```

#### 3. Set Up the Environment

1. Copy the `.env.example` file to create a new `.env` file:

   ```bash
   cp .env.example .env
   ```

2. Open the `.env` file and update the database configuration to use **SQLite**. For example:

   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=/path_to_your_database/database.sqlite
   ```

3. Create the SQLite database file:

   ```bash
   touch database/database.sqlite
   ```

#### 4. Run the Database Migrations

Run the migrations to set up the database structure:

```bash
php artisan migrate
```

#### 5. Start the Development Server

Now you can start the Laravel development server:

```bash
php artisan serve
```

Your Laravel API project should now be up and running!

#### 6. Run Queue Worker (for jobs)

Make sure to start the Laravel queue worker to process the scheduled jobs:

```bash
php artisan queue:work
```

#### 8. Schedule the Jobs

To run scheduled jobs automatically, add the following to your server's crontab (or equivalent):

```bash
* * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1
```
