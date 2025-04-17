## Setup project
1. copy docker folder to .docker folder. -> cd .docker and in .env
2. set `COMPOSE_PROJECT_NAME` e.g `tms`
3. set `HTTP_PORT`, `MYSQL_PORT`, `MYSQL_DATABASE` and `MYSQL_ROOT_PASSWORD`
## set database connection in main .env 
1. set DB_HOST= with `db` parameter
2. RUN `docker compose build`
3. RUN `docker compose up`
After successfully setup you login to docker cli using this command
`docker exec -u docker_app_user -it COMPOSE_PROJECT_NAME_php_service bash` to run php artisan command

## Project Description
This project is a Translation Management API that allows users to create, update, search, and manage translations for different locales. The API provides features such as translation tagging, searching by tags, assigning tags, and exporting translations. The translations are stored in a structured JSON format to support multiple languages dynamically.

## Repository Pattern
The Repository Pattern is used in this project to decouple the business logic from the data access layer. This offers several benefits:

Separation of Concerns – The repository handles database operations, while services focus on business logic.
Easier Testing & Maintenance – Mocking repositories makes unit testing more effective.
Code Reusability – Database queries are centralized, reducing redundancy.
Improved Scalability – Switching databases or modifying query logic doesn’t affect the business logic layer.
In this project, the TranslationRepository manages all database interactions, while the TranslationService handles business logic and calls the repository when needed.

Key Benefits of the Repository Pattern
✅ 1. Separation of Concerns (Clean Code)

Keeps database logic separate from business logic.
The controller doesn’t directly interact with the database, making the code more structured.
✅ 2. Easier Maintenance & Scalability

If the database structure changes, you only need to update the repository instead of multiple parts of the code.
New data sources (e.g., switching from MySQL to MongoDB) can be integrated without changing the business logic.
✅ 3. Improves Testability

Makes unit testing easier by allowing you to mock the repository instead of testing with real database queries.
Avoids direct database interactions in tests, improving speed and reliability.

## Tests
1. Unit Tests
Unit tests are used to test individual components (like functions, repositories, or services) in isolation. They ensure that each method works correctly without external dependencies like databases or HTTP requests.

Why Use Unit Tests?
To verify that the business logic works correctly.
To catch errors early in individual methods.
To make debugging easier by testing small parts of the application separately.

2. Feature Tests
Feature tests simulate real HTTP requests to test the API's behavior as a whole. They check how different components work together, including the controller, service, and database.

✅ Why Use Feature Tests?
To validate API endpoints and responses.
To ensure that user interactions work as expected.
To test full workflows like creating, updating, or deleting translations.

## Swagger Documentation
Swagger is integrated into this project for API documentation and testing. It provides:

Interactive API Documentation – Developers can test API endpoints directly in the browser.
Clear API Contracts – Endpoints, request parameters, and responses are well-documented.
Standardization – Swagger follows OpenAPI standards, making it easier for other developers to understand and integrate the API.
The documentation includes endpoints for creating, searching, updating, assigning tags, exporting translations, and more.

`After project setup you can visit http://localhost/api/documentation for api documentation`

To reflect documentation changes in Swagger UI, run the following command:
`php artisan l5-swagger:generate`

## Run Queue worker
1. login into docker container
RUN `php artisan queue:work`
