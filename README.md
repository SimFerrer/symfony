# Symfony 7.2 Project: Virtual Library Catalog

## Project Overview
Virtual book catalog that allows the user to:

1. Browse and search for available books.
2. Check their borrowing status (e.g., available, borrowed).

Additionally, the library staff requires an administration interface to manage books and their statuses.

This project is built using Symfony 7.2 and includes functionality for both public catalog access and administrative management.

---

## Features

### Public Catalog
- Visitors can view a list of books.
- Search and filter functionality to quickly find specific books.
- Display of book details, including their borrowing status.

### Administration Pages
- Add, edit, or delete books.
- Manage authors and editors.
- Update the borrowing status of books.

---

## Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- A supported SQL database (e.g., MySQL, PostgreSQL, SQLite)
- Symfony CLI (optional but recommended)

### Setup Steps
1. Clone the repository:
   ```bash
   https://github.com/SimFerrer/symfony.git
   cd symfony
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Set up your `.env` or `.env.local` file:
   - Update the `DATABASE_URL` to match your database configuration.

4. Create the database:
   ```bash
   symfony console doctrine:database:create
   ```

5. Generate migrations based on your SQL server:
   ```bash
   symfony console make:migration
   symfony console doctrine:migrations:migrate
   ```

6. (Optional) Load sample data using fixtures:
   ```bash
   symfony console doctrine:fixtures:load
   ```

---

## Controllers Overview

### Admin Controllers
#### AuthorController
Manage authors in the system, including listing, viewing, creating, and editing authors.

#### EditorController
Manage editors, including adding, editing, and listing editors.

#### BookController
Admin interface for managing books:
- Add/edit book details.
- Manage book statuses.

---

### Public Controllers
#### BookController
The public-facing catalog that allows visitors to browse and search books.

#### RegistrationController
Handles user registration for accessing personalized features.

#### SecurityController
Manages authentication and user login/logout functionality.

---


## Example Admin Workflow
1. Navigate to `/admin`.
2. Use the `AuthorController`, `EditorController`, or `BookController` to manage corresponding entities.
3. Add or edit entities via the provided forms. For example, to create a new author:
   - Visit `/admin/author/new`.
   - Fill out the form and submit.

---

## Security
- Access to the administration pages requires authentication.
- Role-based permissions restrict access to sensitive features (e.g., `ROLE_ADMIN`, `ROLE_EDITION_DE_LIVRE`).

---

## Technologies Used
- Symfony 7.2
- Doctrine ORM
- Twig for templating
- Pagerfanta for pagination
- PHPUnit for testing

---

## Contributing
Contributions are welcome! Please fork the repository and submit a pull request with detailed changes.

---

## License
This project is licensed under the MIT License. See the `LICENSE` file for details.

