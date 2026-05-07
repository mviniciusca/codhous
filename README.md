# Codhous - Professional Storefront for Concrete Services 🏗️

> **⚠️ WORK IN PROGRESS (WIP)**  
> This project is currently under active development. Some features might be subject to change or further refinement.

Codhous is a comprehensive management and digital presence ecosystem for small construction companies and concrete services. It unifies inventory control, detailed budgeting (with m² pricing), and professional website creation through a powerful integrated administrative panel. Built with the **TALL stack** (Tailwind, Alpine.js, Laravel, Livewire) and **Filament**, it offers an end-to-end solution for operational efficiency and online visibility.

## 🚀 Key Features

-   **Dynamic Page Builder**: Manage content, sections, and layouts directly from the administrative panel using Filament's Builder.
-   **Concrete Volume Calculator**: Integrated tool for customers to calculate the exact amount of concrete needed for their projects.
-   **Regional Maps Integration**: Built-in support for Google Maps embeds with global fallback configurations.
-   **Automated Budget Requests**: Streamlined workflow for customers to request quotes, saving directly to the database and notifying administrators.
-   **Localized Contact Systems**: Advanced contact forms with SMTP integration, real-time validation, and database persistence.
-   **Premium UI/UX**: Modern, responsive design with dark mode support, glassmorphism effects, and Lucide icon integration.

## 🛠️ Tech Stack

-   **Framework**: [Laravel 11](https://laravel.com)
-   **Admin Panel**: [Filament PHP v3](https://filamentphp.com)
-   **Frontend**: [Livewire v3](https://livewire.laravel.com), [Tailwind CSS](https://tailwindcss.com)
-   **Icons**: [Lucide Icons](https://lucide.dev)
-   **Database**: MySQL / PostgreSQL / SQLite

## 📦 Installation

1.  **Clone the repository**:
    ```bash
    git clone https://github.com/your-repo/codhous.git
    ```

2.  **Install dependencies**:
    ```bash
    composer install
    npm install && npm run build
    ```

3.  **Environment Setup**:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Database Configuration**:
    Configure your `.env` file and run:
    ```bash
    php artisan migrate --seed
    ```

5.  **Storage Link**:
    ```bash
    php artisan storage:link
    ```

6.  **Run the application**:
    ```bash
    php artisan serve
    ```

## 🔐 Administration

Access the administrative panel via `/admin`.  
The initial credentials are set during the seeding process (check `DatabaseSeeder.php` or `UserFactory.php`).

## 📄 License

This project is currently private. All rights reserved.
