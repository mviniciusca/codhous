## Codhous Construction Management Software

## Overview
Management Software focused on small construction companies. Control product inventory, generate budget reports, set prices per square meter, design your website without even leaving the control panel, and much more.

Built with **Laravel** and **Filament**.

## Features
- Product Inventory Management
- Budget Reports Generation
- Price Calculation by Square Meter
- Website Builder
- User Authentication & Authorization
- Dynamic Dashboard with Analytics
- Customizable Settings


## Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/project-name.git
   cd project-name
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Configure environment:
   - Copy the `.env.example` file to `.env`
   - Set your database credentials
   - Generate app key:
   ```bash
   php artisan key:generate
   ```

4. Run migrations:
   ```bash
   php artisan migrate
   ```

5. Start development server:
   ```bash
   php artisan serve
   ```

## Usage
- Access the admin panel at: `http://localhost:8000/admin`
- Default credentials:
  - Email: `codhous@codhous.app`
  - Password: `password`

## Contributing
Feel free to open issues or submit pull requests.

## License
This project is licensed under the [MIT License](LICENSE).

