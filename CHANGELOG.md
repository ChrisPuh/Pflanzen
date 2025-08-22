# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- New features that are in development but not yet released

### Changed
- Changes in existing functionality

### Fixed
- Bug fixes

### Removed
- Removed features

## [0.1.0] - 2025-01-22 - Alpha Release

**🚧 ALPHA STATUS: Core features implemented, but not production-ready for end users yet.**

### Added
- 🌱 **Garden Management System**
  - Create and manage multiple gardens
  - Support for different garden types (Vegetable, Herb, Flower, etc.)
  - Garden archiving and restoration functionality
  - Garden statistics and overview

- 🏡 **Area Management**
  - Organize gardens into specific areas
  - Multiple area types (Planting Bed, Greenhouse, Water Feature, etc.)
  - Area-specific plant management
  - Coordinates and size tracking for areas

- 🌿 **Plant Tracking**
  - Comprehensive plant database
  - Plant categories and types
  - Add plants to specific garden areas
  - Track plant quantities per area

- 🔐 **Authentication System**
  - User registration and login
  - Email verification
  - Password reset functionality
  - Remember me functionality
  - Login throttling for security

- 🎨 **Modern UI/UX**
  - Responsive design for all screen sizes
  - Dark/Light mode with system preference detection
  - Clean, modern interface with Tailwind CSS
  - Consistent component library

- 🛠️ **Admin Panel**
  - Filament-powered admin interface
  - Manage plants, plant types, and categories
  - User management capabilities
  - Resource-based administration

- 🇩🇪 **Localization**
  - Full German language support
  - Localized date and time formats
  - German validation messages

- 📋 **Legal Compliance**
  - GDPR-compliant cookie banner with Livewire
  - Privacy policy and terms of service pages
  - External impressum integration
  - Cookie consent management

- 🧪 **Quality Assurance**
  - Comprehensive test suite with Pest
  - 100% type coverage with strict typing
  - Code quality tools (Pint, Larastan, Rector)
  - Feature and unit tests for all major functionality

### Technical Stack
- **Backend**: Laravel 12.24.0, PHP 8.4.11
- **Frontend**: Livewire 3.6.4, Alpine.js 3.14.9, Tailwind CSS 4.1.12
- **Admin**: Filament 4.0.1
- **Database**: MySQL with comprehensive migrations
- **Testing**: Pest 3.8.2 with extensive coverage
- **Code Quality**: Laravel Pint, Larastan, Rector

### Infrastructure
- Laravel Herd compatible for local development
- GitHub Actions ready (CI/CD pipelines can be added)
- Docker support ready for deployment
- Environment-based configuration

---

## Development Process

### Version Numbering
This project follows [Semantic Versioning](https://semver.org/):
- **MAJOR** version for incompatible API changes
- **MINOR** version for backwards-compatible functionality additions  
- **PATCH** version for backwards-compatible bug fixes

### Change Categories
- **Added** for new features
- **Changed** for changes in existing functionality
- **Deprecated** for soon-to-be removed features
- **Removed** for now removed features
- **Fixed** for any bug fixes
- **Security** for vulnerability fixes

### Contributing
When contributing, please:
1. Update this CHANGELOG with your changes
2. Follow the established format and categories
3. Add entries to the `[Unreleased]` section
4. Include issue/PR references where applicable

---

*For more details about specific changes, see the [commit history](https://github.com/gardenkeeper-app/webapp/commits) or [GitHub releases](https://github.com/gardenkeeper-app/webapp/releases).*