
# Roster System

The Roster System is a Laravel-based (version 10.10) application designed to manage and import crew member data. It provides a simple yet powerful interface for interacting with the crew's roster information, including listing all crew members and importing data from an HTML file.

## Installation

To install and run the Roster System, follow these steps:

1. Clone the repository:
   ```
   git clone https://github.com/asadigli/roster_system.git
   ```
2. Navigate to the project directory:
   ```
   cd Roster-System
   ```
3. Install dependencies:
   ```
   composer install
   ```
4. Create an environment file:
   ```
   cp .env.example .env
   ```
5. Generate an application key:
   ```
   php artisan key:generate
   ```
6. Run database migrations:
   ```
   php artisan migrate
   ```
7. Start the Laravel development server:
   ```
   php artisan serve
   ```
   
## API Endpoints

The Roster System provides the following API endpoints:

### GET /api/rawdatas

- **Description**: Retrieves flights within a specified date range and optionally filtered by crew name.
- **Header**: `Content-Type: application/json`
- **Query Parameters**:
  - `start_date` (date format): The start date for the filter range.
  - `end_date` (date format): The end date for the filter range.
  - `crew_name` (string, optional): The name of the crew to filter the list.

### POST /api/rawdatas

- **Description**: Imports data into the database from an HTML file.
- **Header**: `Content-Type: application/json`
- **Parameters**:
  - `file` (file format): The HTML file containing the data to be imported.
  - `table_id` (string): The identifier for the table where data will be imported.
  - `crew_fullname` (string): The full name of the crew member associated with the data.

## Documentation

The API documentation is available at `/api/documentation`, powered by Swagger. It provides a comprehensive guide to the API's endpoints, including request parameters and response structures.

## Testing

PHPUnit tests have been written to ensure the application's functionality is working as expected. To run the tests, execute the following command:

```
./vendor/bin/phpunit
```

