

# Article and Comment Management System

This project is a CRUD application for managing articles and comments. Users can register, login, create articles, write comments with like/dislike functionality, and Admin Can receive notifications via SMS and email when new articles are created.

## Features

- CRUD operations for articles and comments
- Like and dislike functionality for comments
- User registration, login, and logout
- Notification system via SMS and Email for new articles
- Caching mechanism with the option to enable/disable for articles
- Feature tests for all functionalities
- Swagger documentation for APIs

## Setup

1. Clone the repository
2. run docker compose `docker compose up -d --build`
3.run command to migrate `php artisan migrate`
4.Set up the environment variables
5.Run feature test  `php artisan test`


## Usage
1. Register a user account
2. Create, read, update, and delete articles
3. Add comments with like or dislike
4. Enjoy the notification system for new articles For admin
5. Explore the caching feature for articles

## Testing

To run feature tests, use the command `php artisan test`

## API Documentation

The API documentation is available in the Doc Folder

