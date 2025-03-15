# Laravel 11 Authentication & Delivery System API

## Overview
This project is a Laravel 11 API-based authentication and delivery system that includes user registration, JWT authentication, push notifications with Firebase, and an admin panel for user management. The system ensures security, proper logging, and exception handling while following best practices.

## Features
- **Laravel Telescope** for logging all actions and errors.
- **JWT Authentication** for secure API access.
- **User Registration** with:
  - Mobile number verification via Twilio SMS Gateway.
  - Google Maps location.
  - Profile image with a thumbnail.
- **Login API** using mobile number and password.
- **Profile Management** API.
- **Delivery Representative Search**:
  - Find the nearest delivery representatives.
  - Alternative distance calculation (without Google Maps due to cost restrictions).
- **Push Notifications** (Firebase FCM, issue with obtaining `FIREBASE_DEVICE_TOKEN`).
- **Exception Handling** to prevent HTTP 500 errors.
- **Admin Panel**:
  - CRUD operations for users (types: 'user', 'delivery').
  - Send push notifications to all users with static messages.
- **Database**:
  - Migrations and models with proper relationships.
  - Seeders for dummy data.

## Installation

### Prerequisites
Ensure you have the following installed:
- PHP 8+
- Composer
- Laravel 11
- MySQL
- Twilio SMS Account
- Firebase Cloud Messaging (FCM) Account

### Steps
1. Clone the repository:
   ```sh
   git clone https://github.com/yourusername/aravel-11-JWT-Auth-SMS-Verification-Push-Notification-System.git
   cd your-repo
   ```
2. Install dependencies:
   ```sh
   composer install
   ```
3. Set up environment variables:
   ```sh
   cp .env.example .env
   ```
   - Update `.env` file with database, Twilio, and Firebase credentials.
4. Generate application key:
   ```sh
   php artisan key:generate
   ```
5. Run database migrations and seed data:
   ```sh
   php artisan migrate --seed
   ```
6. Install Laravel Telescope:
   ```sh
   php artisan telescope:install
   ```
7. Start the Laravel development server:
   ```sh
   php artisan serve
   ```

### Postman Collection
The Postman collection for API testing is included in the repository.
https://documenter.getpostman.com/view/43110145/2sAYkBs1fD

## Notes & Issues
- **Firebase Push Notifications:** Could not retrieve `FIREBASE_DEVICE_TOKEN`.
- **Google Maps Distance Calculation:** Due to pricing constraints, an alternative method was implemented.

## Logs & Debugging
- Laravel Telescope is enabled to log all actions and errors.
- Ensure `TELESCOPE_ENABLED=true` in `.env`.

## Contribution & Deployment
- Ensure all features are tested before submitting changes.
- Before deployment, verify `.env` settings and run:
  ```sh
  php artisan config:cache
  php artisan migrate --force
  ```

## License
This project is open-source and available under the [MIT License](LICENSE).
