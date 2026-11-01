# Contributing to NexPOS

Thank you for considering contributing to NexPOS! We welcome contributions from developers worldwide to make this the best open-source Point of Sale & Appointment Booking platform.

## Development Setup

1. Fork and clone the repository:
   ```bash
   git clone https://github.com/YOUR_USERNAME/laravel-pos-booking.git
   cd laravel-pos-booking
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Setup environment & database:
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   ```

4. Run tests:
   ```bash
   php artisan test
   ```

5. Format code with Laravel Pint:
   ```bash
   ./vendor/bin/pint
   ```

## Pull Request Guidelines

- Create a feature branch: `git checkout -b feature/your-feature-name`
- Ensure all tests pass before submitting your PR.
- Add new tests for new features or bug fixes.
- Follow PSR-12 coding style by running Pint before committing.
