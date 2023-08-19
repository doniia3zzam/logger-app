# Logger Viewer Project

This is a simple logger viewer project that allows you to view file content and navigate through its pages.

## Features

- View file content with pagination.
- Navigate between pages: first, last, next, previous.
- Display line numbers and content for each page.

## Getting Started

Follow these instructions to set up and run the Logger Viewer project locally.

### Prerequisites

- PHP 7.4 or higher
- Composer
- Laravel Framework (installed globally or via Composer)
- Web server (e.g., Apache)

### Installation

1. Clone the repository to your local machine:
2. Navigate to the project directory:
3. Install PHP dependencies using Composer: `composer install`

### Configuration

1. Copy the `.env.example` and rename it to `.env`:

### Running the Application

1. Generate the application key: `php artisan key:generate`
2. Start the development server: `php artisan serve`
3. Access the application in your web browser at `http://localhost:8000`.
4. On the login page, use the following credentials:
- Username: admin
- Password: admin

5. After successfully logging in, you will be redirected to the viewer page.

### Usage

1. Open the application in your web browser.

2. Enter the path to the log file you want to view. For example: D:\xampp_new\htdocs\logger-app\storage\logs\laravel.log

3. Click the "View" button to load and display the content.

4. Use the pagination controls to navigate between pages.
