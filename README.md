# My Awesome URL Shortner

This project is a full-stack URL shortener application built with Symfony (backend) and Vue.js (frontend).

It features Backend Unit and Integration Testing, along with Frontend Unit, Integration and B2B Testing. Data is persisted in a MySQL Database.

## Prerequisites

- Node.js and npm
- PHP and Composer
- Symfony CLI
- MySQL

## Getting Started

### Backend

1. Navigate to the backend directory:
    ```sh
    cd backend
    ```

2. Copy the example environment file and edit the configuration:
    ```sh
    cp .env.example .env
    ```

3. Install the dependencies:
    ```sh
    composer install
    ```

4. Create the database and run migrations:
    ```sh
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    ```
5. Create the database and run migrations for the test environment:
    ```sh
    php bin/console doctrine:database:create --env=test --if-not-exists
    php bin/console doctrine:migrations:migrate --env=test
    ```

6. Start the Symfony server on a specified port (e.g., 43000):
    ```sh
    symfony serve --port=43000
    ```

### Frontend

1. Navigate to the frontend directory:
    ```sh
    cd frontend
    ```

2. Copy the example environment file and edit the configuration (Ensure it contains the correct API URL (matching the Symfony server URL):
    ```sh
    cp .env.example .env
    ```

3. Install the dependencies:
    ```sh
    npm install
    ```

4. Start the Vue.js development server:
    ```sh
    npm run serve
    ```

## Running Tests

### Backend

To run backend tests:
```sh
cd backend
php bin/phpunit
```

### Frontend

To run frontend tests:
```sh
cd frontend
npm run test:unit
npm run test:integration
npm run test:e2e
```

###Usage

Once both the Symfony backend and Vue.js frontend servers are running, open your browser and navigate to:
- http://localhost:8080

You can now use the My Awesome URL shortener application.

## Database Structure

### MySQL Database Table

The application uses a single table to store the shortened URLs. Here is the structure of the table:

- **id**: An auto-incrementing integer that serves as the primary key.
- **original_url**: A `LONGTEXT` field that stores the original URL.
- **short_code**: A `VARCHAR(255)` field that stores the generated short code.
- **created_at**: A `DATETIME` field that stores the creation timestamp.
- **updated_at**: A `DATETIME` field that stores the timestamp of the last update (optional).

The table is created and managed using Doctrine migrations. The migration script ensures the table is created if it doesn't exist and handles any necessary schema updates.

