# Contributing to Laravel Notification System

Thank you for considering contributing to the Laravel Notification System! Here's how you can help.

## Bug Reports

If you discover a bug, please create an issue on GitHub with:
- A clear title and description
- Steps to reproduce the issue
- Expected vs actual behavior
- PHP, Laravel, and package version

## Pull Requests

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Write tests for your changes
4. Ensure all tests pass (`composer test`)
5. Follow existing code style (PSR-12)
6. Commit with clear messages
7. Push and open a Pull Request

### Code Style

- Follow PSR-12 coding standards
- Add PHPDoc blocks to all public methods
- Use `readonly` DTOs where applicable
- Keep classes focused and single-responsibility

### Testing

Run the test suite before submitting:

```bash
composer test
```

Or directly:

```bash
vendor/bin/phpunit
```

## Security Vulnerabilities

If you discover a security vulnerability, please see [SECURITY.md](SECURITY.md) for responsible disclosure.

## License

By contributing, you agree that your contributions will be licensed under the [MIT License](LICENSE).
