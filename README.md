# Phone Inventory Management System (PIMS)

A web-based inventory management system designed specifically for phone stores and retailers. This system helps track phone inventory, manage categories, and monitor stock transactions.

## Features

- **Phone Management**
  - Add, edit, and delete phone entries
  - Track phone details (brand, model, SKU, price)
  - Manage phone specifications and descriptions
  - Monitor stock levels

- **Category Management**
  - Create and manage phone categories
  - Organize phones by categories
  - View phones in each category

- **Inventory Transactions**
  - Track stock in/out movements
  - Record transaction history
  - Monitor stock levels
  - Initial stock setup

- **Modern UI/UX**
  - Clean and responsive design
  - User-friendly interface
  - Bootstrap-based styling
  - Mobile-friendly layout

## Technologies Used

- PHP
- MySQL
- Bootstrap 5
- HTML/CSS
- JavaScript
- Font Awesome Icons

## Requirements

- PHP 7.0 or higher
- MySQL 5.6 or higher
- Web server (Apache/Nginx)
- XAMPP/WAMP/MAMP (for local development)

## Installation

1. Clone the repository to your web server directory:
   ```bash
   git clone https://github.com/chanalvarez/PIMS.git
   ```

2. Create a MySQL database named 'phone_inventory'

3. Import the database structure:
   - Navigate to your project in the browser
   - Run the setup script: `http://localhost/PIMS/setup_database.php`

4. Configure the database connection:
   - Edit `config/database.php` with your database credentials
   - Default credentials are:
     - Host: localhost
     - Username: root
     - Password: (empty)
     - Database: phone_inventory

5. Access the system through your web browser:
   ```
   http://localhost/PIMS
   ```

## Usage

1. **Adding a New Phone**
   - Click on "Phones" in the navigation
   - Click "Add New Phone"
   - Fill in the phone details
   - Save the entry

2. **Managing Categories**
   - Navigate to "Categories"
   - Add, edit, or delete categories
   - View phones in each category

3. **Tracking Transactions**
   - Go to "Transactions"
   - View stock movement history
   - Monitor inventory changes

## Contributing

Feel free to fork this project and submit pull requests. You can also open issues for bugs or feature requests.

## License

This project is open-source and available under the [MIT License](LICENSE).

## Author

Christian Ray M. Alvarez

## Acknowledgments

- Bootstrap Team for the amazing CSS framework
- Font Awesome for the beautiful icons
- PHP and MySQL communities 