# Symfony 7.2 Project: Virtual Library Catalog

## Project Overview
Virtual book catalog that allows the user to:

1. Browse and search for available books.
2. Check their borrowing status (e.g., available, borrowed).

Additionally, the library staff requires an administration interface to manage books and their statuses.

This project is built using Symfony 7.2 and includes functionality for both public catalog access and administrative management.
The project is made in two parts:
- The templating part with the render twig
- And a rest api with jwt token authentication

you can test the api with postman or use the angular project https://github.com/SimFerrer/angular-bibliotheque which makes requests there



## Table of Contents
- [Features](#features)
- [Installation](#installation)
- [Templating Part](#templating-part)
- [API Part](#api-part)
- [Security](#security)

## Features

### Public Catalog
- Visitors can view a list of books.
- Search and filter functionality to quickly find specific books.
- Display of book details, including their borrowing status.

### Administration Pages
- Add, edit, or delete books.
- Manage authors and editors.
- Update the borrowing status of books.

### Api
It is also possible to communicate with a rest API
- Manage books, authors and editors


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

## Templating Part

### Controllers Overview

#### Admin Controllers
##### AuthorController
Manage authors in the system, including listing, viewing, creating, and editing authors.

##### EditorController
Manage editors, including adding, editing, and listing editors.

##### BookController
Admin interface for managing books:
- Add/edit book details.
- Manage book statuses.

---

#### Public Controllers
##### BookController
The public-facing catalog that allows visitors to browse and search books.

##### RegistrationController
Handles user registration for accessing personalized features.

##### SecurityController
Manages authentication and user login/logout functionality.

---


### Example Admin Workflow
1. Navigate to `/admin`.
2. Use the `AuthorController`, `EditorController`, or `BookController` to manage corresponding entities.
3. Add or edit entities via the provided forms. For example, to create a new author:
   - Visit `/admin/author/new`.
   - Fill out the form and submit.

---

## API Part

### Authentification
The API uses JWT (JSON Web Token) authentication. Upon a successful login, both an access token and a refresh token are generated.

- Login
 - URL : `/api/login`
 - Method : `POST`
 - Parameters: `email` and `password`
 - Response : `access_token` : Token to use for authenticating subsequent requests.

### Authors API

- List Authors

Retrieve a paginated list of authors with optional date filters.

```http
GET /api/author
```

**Parameters:**
- `page` (optional): Page number (default: 1)
- `start` (optional): Filter by start date (YYYY-MM-DD)
- `end` (optional): Filter by end date (YYYY-MM-DD)


- Get Author

Retrieve detailed information about a specific author.

```http
GET /api/author/{id}
```

**Parameters:**
- `id`: Author ID (required)

- Create Author

Create a new author (requires ROLE_AJOUT_DE_LIVRE role).

```http
POST /api/author/create
Content-Type: application/json
```

- Update Author

Update an existing author (requires ROLE_EDITION_DE_LIVRE role).

```http
PUT /api/author/edit
Content-Type: application/json
```


- Delete Author

Delete an author (requires ROLE_AJOUT_DE_LIVRE role).

```http
DELETE /api/author/{id}
```

**Parameters:**
- `id`: Author ID (required)

### Books API

- List Books

Retrieve a paginated list of books.

```http
GET /api/book
```

**Parameters:**
- `page` (optional): Page number (default: 1)


- Get Book

Retrieve detailed information about a specific book.

```http
GET /api/book/{id}
```

**Parameters:**
- `id`: Book ID (required)

- Create Book

Create a new book (requires ROLE_AJOUT_DE_LIVRE role).

```http
POST /api/book/create
Content-Type: application/json
```


- Update Book

Update an existing book (requires ROLE_EDITION_DE_LIVRE role).

```http
PUT /api/book/edit
Content-Type: application/json
```

- Delete Book

Delete a book (requires ROLE_AJOUT_DE_LIVRE role).

```http
DELETE /api/book/{id}
```

**Parameters:**
- `id`: Book ID (required)

### Editors API

- List Editors

Retrieve a list of all editors.

```http
GET /api/editor
```

- Get Editor

Retrieve information about a specific editor.

```http
GET /api/editor/{id}
```

**Parameters:**
- `id`: Editor ID (required)

- Create Editor

Create a new editor (requires ROLE_AJOUT_DE_LIVRE role).

```http
POST /api/editor/create
Content-Type: application/json
```


### Error Handling

The API uses standard HTTP status codes to indicate the success or failure of requests:

| Status Code | Description |
|------------|-------------|
| 200 | Success |
| 400 | Bad Request - Invalid parameters or validation errors |
| 401 | Unauthorized - Missing or expired token |
| 404 | Not Found - Resource not found |
| 500 | Internal Server Error |

## Security
- Access to the administration pages requires authentication.
- Role-based permissions restrict access to sensitive features (e.g., `ROLE_ADMIN`, `ROLE_EDITION_DE_LIVRE`).
- JWT Authentication: API access requires a valid JWT token.
- Role-based Permissions: Access to sensitive resources is restricted by roles (e.g., ROLE_ADMIN, ROLE_AJOUT_DE_LIVRE, ROLE_EDITION_DE_LIVRE).
- Public Access: Some endpoints (e.g., /api/login, /api/book) are publicly accessible. All other API routes require authentication.

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

