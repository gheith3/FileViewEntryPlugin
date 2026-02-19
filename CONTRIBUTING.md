# Contributing to FileViewEntryPlugin

Thank you for your interest in contributing to FileViewEntryPlugin! We welcome contributions from the community.

## Getting Started

1. Fork the repository on GitHub
2. Clone your fork locally
3. Create a new branch for your feature or bug fix
4. Make your changes
5. Run tests and code style checks
6. Submit a pull request

## Development Setup

```bash
# Clone your fork
git clone https://github.com/your-username/FileViewEntryPlugin.git
cd FileViewEntryPlugin

# Install dependencies
composer install

# Run tests
vendor/bin/pest

# Run tests with coverage
vendor/bin/pest --coverage

# Run code style checks
vendor/bin/pint --test

# Fix code style issues
vendor/bin/pint
```

## Code Style

This project follows the [Laravel](https://laravel.com/docs/11.x/pint) code style. Please ensure your code passes the style checks before submitting a pull request.

## Testing

- Write tests for new features
- Ensure all tests pass before submitting PR
- Aim for high test coverage

## Pull Request Process

1. Update the README.md with details of changes if applicable
2. Update the CHANGELOG.md with your changes
3. Ensure your code follows the project's code style
4. Verify all tests pass
5. Link any related issues in your PR description

## Reporting Issues

When reporting issues, please include:

- PHP version
- Laravel version
- Filament version
- Clear description of the problem
- Steps to reproduce
- Expected vs actual behavior

## Code of Conduct

This project adheres to a code of conduct. By participating, you are expected to uphold this code:

- Be respectful and inclusive
- Welcome newcomers
- Focus on constructive feedback
- Respect differing viewpoints

## License

By contributing, you agree that your contributions will be licensed under the MIT License.
