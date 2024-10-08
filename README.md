
# Laravel API with WeatherAPI Integration and Queue System

This is a Laravel project that includes a basic API for user management, posts, weather data integration using WeatherAPI, and a queued job to send a welcome email when a user registers. The application is set up with **Laravel Sail** for easy development with Docker.

## Prerequisites

- Docker (for Laravel Sail)
- Laravel Sail (development environment)
- Composer (for installing PHP dependencies)
- WeatherAPI account for weather data

## Setup Instructions

### Step 1: Clone the Repository

Clone the repository to your local machine:

```bash
git clone https://github.com/your-repo.git
cd your-repo
```

### Step 2: Install Dependencies

Run the following command to install all dependencies:

```bash
composer install
```

### Step 3: Configure Environment Variables

Copy the example `.env` file and configure it with your API keys and other environment variables:

```bash
cp .env.example .env
```

### Step 4: Start the Development Server

Start Laravel Sail, which is a Docker environment for running Laravel projects:

```bash
./vendor/bin/sail up
```

This will start the necessary services, including the web server, database and Mailpit.

### Step 5: Migrate the Database

Run the following command to create the necessary database tables:

```bash
./vendor/bin/sail artisan migrate
```

### Step 6: Set Up WeatherAPI

Make sure to sign up for an account at [WeatherAPI](https://www.weatherapi.com/) and retrieve your API key. Add this API key to your `.env` file:

```env
WEATHER_API_KEY={API KEY}
WEATHER_API_URL=http://api.weatherapi.com/v1/current.json
```

### Step 7: Set Up and Run the Queue Worker and Scheduler

To enable background job processing, such as sending a welcome email when a user registers, you need to set up the queue worker:

1. Ensure database is running (it should be started by Sail).
2. Run the queue worker with the following command:

```bash
./vendor/bin/sail artisan queue:work
```

This command will listen for jobs and process them in the background.

To run the scheduler for fetching the weather data periodically (every 1 hour):
```bash
./vendor/bin/sail artisan schedule:work
```
which can be set up with Supervisor in production environtment.

### Step 8: Run the Artisan Command to Manually Dispatch Welcome Email

You can manually trigger the welcome email job using an artisan command. Run the following command to dispatch the job:

```bash
./vendor/bin/sail artisan email:send-welcome {userId}
```
then 
```bash
./vendor/bin/sail artisan queue:work
```
to actually trigger it.

Replace `{userId}` with the actual ID of the user for whom you want to send the email.

### Step 9: View Sent Emails with Mailpit

By default, **Mailpit** (a local email testing tool) is installed and running on `localhost:8025` in Laravel Sail. To view emails sent by the application:

1. Go to [http://localhost:8025](http://localhost:8025) in your browser.
2. You will be able to see the emails, including the welcome email, that have been sent by the application.

### Step 10: Access the API Documentation

Swagger API documentation is available at `/api/documentation`. After running the server, visit:

```
http://localhost/api/documentation
```

This will show the OpenAPI documentation for all the available endpoints.

## Available Endpoints

1. **User Management**
    - `POST /api/register`: Register a new user
    - `POST /api/login`: Login a user
    - `POST /api/logout`: Logout a user
    - `GET /api/users/{id}`: Get a specific user (Requires authentication)

2. **Posts**
    - `GET /api/posts`: List all posts (Requires authentication)
    - `GET /api/posts/{id}`: Get a specific post (Requires authentication)
    - `POST /api/posts`: Create a new post (Requires authentication)
    - `PATCH /api/posts/{id}`: Update a post (Requires authentication)
    - `DELETE /api/posts/{id}`: Delete a post (Requires authentication)

3. **Weather**
    - `GET /api/weather`: Get current weather data for Perth, Australia

### Step 11: Testing

You can run the tests using Laravel's testing tools. To run the tests:

```bash
./vendor/bin/sail artisan test
```

This will run all unit and feature tests, including those for API endpoints and job dispatching.

## Additional Commands

- To run database migrations:

  ```bash
  ./vendor/bin/sail artisan migrate
  ```

- To create a migration for the queue jobs db table 
(in case running only ```artisan migrate``` does not work):

  ```bash
  ./vendor/bin/sail artisan queue:table
  ```
  then
  ```bash
  ./vendor/bin/sail artisan migrate
  ```

- To clear cache:

  ```bash
  ./vendor/bin/sail artisan cache:clear
  ```

- To stop the Sail services:

  ```bash
  ./vendor/bin/sail down
  ```

## Conclusion

This setup provides a full Laravel development environment with Docker, along with queue workers for background jobs, Mailpit for email testing, and Swagger for API documentation. Wish me luck !