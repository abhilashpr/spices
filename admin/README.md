# Admin Panel - Framework Structure

## Directory Structure

```
admin/
├── config/           # Configuration files
│   ├── config.php   # Admin configuration
│   └── database.php # Database connection
├── core/            # Core framework classes
│   ├── Auth.php     # Authentication handler
│   ├── Controller.php # Base controller
│   ├── Model.php    # Base model
│   ├── Router.php   # Router (for future use)
│   └── View.php     # View renderer
├── controllers/     # Controllers
│   ├── AuthController.php
│   ├── CategoryController.php
│   └── DashboardController.php
├── models/          # Models
│   └── CategoryModel.php
├── views/           # Views
│   ├── layouts/     # Layout templates
│   ├── partials/    # Reusable partials
│   ├── auth/        # Auth views
│   ├── dashboard/   # Dashboard views
│   └── categories/  # Category views
├── includes/        # Includes
│   ├── bootstrap.php # Bootstrap file
│   └── helpers.php   # Helper functions
├── assets/          # Static assets
│   ├── css/
│   └── js/
├── index.php        # Main entry point
├── login.php        # Login page
└── logout.php       # Logout handler
```

## Setup

1. Run setup script: `php admin/setup_admin.php`
2. Login at: `/online-sp/admin/login.php`
   - Username: `admin@gmail.com`
   - Password: `admin123`

## Framework Features

- MVC Architecture
- Authentication system
- Base Model with CRUD operations
- View rendering with layouts
- Helper functions
- Flash messages
- CSRF protection (ready)

## Adding New Features

1. Create Model in `models/`
2. Create Controller in `controllers/`
3. Create Views in `views/`
4. Add route in `index.php`



