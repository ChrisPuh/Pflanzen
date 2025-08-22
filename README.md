# 🌱 GardenKeeper

A modern garden management application built with Laravel, helping you track and manage your plants, garden areas, and cultivation activities.

## ✨ Features

- 🏡 **Garden Management** - Organize your garden into different areas
- 🌿 **Plant Tracking** - Keep track of all your plants and their care schedules
- 📊 **Care Logging** - Record watering, fertilizing, and other maintenance activities
- 📱 **Responsive Design** - Works seamlessly on desktop, tablet, and mobile
- 🌙 **Dark/Light Mode** - Automatic theme switching based on system preference
- 🔐 **Authentication** - Secure user registration and login system
- 🇩🇪 **German Language** - Fully localized in German
- 📋 **GDPR Compliant** - Cookie consent and privacy policy included

## 🚀 Tech Stack

- **PHP** 8.4.11
- **Laravel** 12.24.0
- **Livewire** 3.6.4 - For dynamic components
- **Filament** 4.0.1 - Admin panel
- **Alpine.js** 3.14.9 - JavaScript framework
- **Tailwind CSS** 4.1.12 - Utility-first CSS framework
- **MySQL** - Database
- **Pest** 3.8.2 - Testing framework

## 📋 Requirements

- PHP 8.4+
- Composer
- Node.js & npm
- MySQL 8.0+
- Laravel Herd (recommended for local development)

## 🛠️ Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/gardenkeeper-app/webapp.git
   cd webapp
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure your database**
   Edit `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=gardenkeeper
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   # or with Laravel Herd: https://gardenkeeper.test
   ```

## 🔧 Development

### Running Tests
```bash
# Run all tests
php artisan test

# Run with coverage
composer test
```

### Code Quality
```bash
# Format code with Laravel Pint
vendor/bin/pint

# Static analysis with Larastan
vendor/bin/phpstan analyse

# Refactoring with Rector
vendor/bin/rector --dry-run
```

### Asset Development
```bash
# Watch for changes
npm run dev

# Build for production
npm run build
```

## 📁 Project Structure

```
├── app/
│   ├── Http/Controllers/     # Application controllers
│   ├── Livewire/            # Livewire components
│   ├── Models/              # Eloquent models
│   └── ...
├── resources/
│   ├── views/               # Blade templates
│   │   ├── components/      # Reusable components
│   │   ├── layouts/         # Layout templates
│   │   └── ...
│   ├── css/                 # Stylesheets
│   └── js/                  # JavaScript files
├── tests/                   # Test files
└── ...
```

## 🌐 Environment Variables

Key environment variables you might want to configure:

```env
APP_NAME=GardenKeeper
APP_URL=https://gardenkeeper.test

# Legal Information
IMPRESSUM_URL=https://gardenkeeper-app.github.io/Impressum/

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-server
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Code Style

This project follows Laravel conventions and uses:
- **Laravel Pint** for code formatting
- **Pest** for testing
- **Strict typing** in PHP files

## 🧪 Testing

The application includes comprehensive tests:
- **Feature tests** for HTTP endpoints
- **Unit tests** for individual components
- **Livewire tests** for component interactions

Run tests with:
```bash
php artisan test --parallel
```

## 📝 Legal

- **Privacy Policy**: Available at `/privacy`
- **Terms of Service**: Available at `/terms`
- **Impressum**: [External link](https://gardenkeeper-app.github.io/Impressum/)

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Chris Ganzert**
- Email: gardenkeeper.app@gmail.com
- GitHub: [@gardenkeeper-app](https://github.com/gardenkeeper-app)

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com/)
- UI components powered by [Tailwind CSS](https://tailwindcss.com/)
- Interactive components with [Livewire](https://laravel-livewire.com/)
- Admin interface with [Filament](https://filamentphp.com/)

---

**Made with ❤️ for garden enthusiasts** 🌱