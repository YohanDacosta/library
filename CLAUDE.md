# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A Swiss library management system built with Symfony 7.4, used for kindergartens in Baselland. The application manages books, students, tutors, schools, courses, and book loans.

## Common Commands

```bash
# Start development server
symfony server:start

# Start Docker services (MariaDB, Nginx, PHP)
docker-compose up -d

# Run all tests
./bin/phpunit

# Run a specific test file
./bin/phpunit tests/Integration/BookIntegrationTest.php

# Run a specific test method
./bin/phpunit --filter testCreateBook

# Database migrations
php bin/console doctrine:migrations:migrate

# Clear cache
php bin/console cache:clear

# Compile Tailwind CSS (runs in watch mode)
php bin/console tailwind:build --watch
```

## Architecture

### Domain Model
The core domain consists of:
- **Book**: Has unique code (auto-generated), status enum (available/loaned/reserved/repaired/lost), many-to-many with BookCategory
- **Student**: Belongs to one Course and one School, can have multiple Loans
- **Loan**: Links Book, Student, and Tutor with status enum (active/returned/overdue)
- **Tutor**: Supervises loans

All entities use UUID v4 as primary keys (generated in constructor).

### Layers
- **Controllers**: `src/Controller/AppController.php` (public-facing) and `src/Controller/Admin/` (EasyAdmin CRUD controllers)
- **Services**: Business logic in `src/Services/` (e.g., BookService handles book retrieval with pagination)
- **Repositories**: Doctrine repositories with Pagerfanta for pagination

### Frontend
- Uses Symfony AssetMapper (no webpack/encore)
- Tailwind CSS via symfonycasts/tailwind-bundle
- Stimulus controllers in `assets/controllers/`

### Admin Panel
EasyAdmin at `/admin` route - CRUD controllers extend `AbstractDashboardController` and individual entity controllers (BookCrudController, StudentCrudController, etc.)

### Tests
- **Integration tests** (`tests/Integration/`): Test entity persistence with real database
- **Application tests** (`tests/Application/`): HTTP tests using WebTestCase
- Test environment uses separate database with `_test` suffix (see `.env.test`)

## Key Patterns

- Entities auto-set `created_at` and generate UUID in constructor
- Book codes are auto-generated using timestamp + random number
- Pagination uses Pagerfanta with Doctrine ORM adapter
- Book status and Loan status use PHP enums (`src/Enums/`)

## Claude Code Scope (IMPORTANT)

Claude Code is used **ONLY as a Frontend assistant**.

### Allowed Responsibilities
Claude Code MAY:
- Write and modify **Twig templates** (HTML structure and semantics)
- Apply **TailwindCSS** classes for layout, styling, and responsive design
- Create or update **Stimulus controllers** for small UI interactions
- Improve accessibility (ARIA attributes, labels, keyboard navigation)
- Refactor frontend code for readability and maintainability
- Assume that all required data is already provided by the controller

### Forbidden Responsibilities
Claude Code MUST NOT:
- Create or modify Symfony controllers
- Define or change routes
- Modify entities, services, repositories, or business logic
- Touch Doctrine, database logic, or migrations
- Introduce new backend dependencies
- Make architectural decisions affecting the backend

### Assumptions
- This is a **server-side rendered Symfony application**
- Backend logic and data preparation are handled by the developer
- Frontend logic should remain lightweight
- JavaScript should only be used when Twig is not sufficient

### Frontend Principles
- Prefer Twig logic over JavaScript when possible
- Use Stimulus only for interaction, not business rules
- Keep Tailwind usage clean and readable
- Avoid over-engineering UI solutions
