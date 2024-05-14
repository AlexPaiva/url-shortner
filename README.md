# URL Shortner

This is a URL Shortner project using Symfony for the backend and Vue.js for the frontend.

## Prerequisites

- Node.js and npm
- PHP and Composer
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

5. Start the Symfony server:
    ```sh
    symfony serve
    ```

### Frontend

1. Navigate to the frontend directory:
    ```sh
    cd frontend
    ```

2. Copy the example environment file and edit the configuration:
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
npm run test
```
