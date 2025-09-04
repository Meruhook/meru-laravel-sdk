# Contributing

Contributions are **welcome** and will be fully **credited**.

We accept contributions via Pull Requests on [GitHub](https://github.com/meruhook/meruhook-sdk).

## Pull Requests

- **[PSR-12 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-12-coding-style-guide.md)** - The easiest way to apply the conventions is to install [Laravel Pint](https://laravel.com/docs/pint).

- **Add tests!** - Your patch won't be accepted if it doesn't have tests.

- **Document any change in behaviour** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow [SemVer v2.0.0](http://semver.org/). Randomly breaking public APIs is not an option.

- **Create feature branches** - Don't ask us to pull from your main branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.

## Development Setup

1. Clone the repository:
```bash
git clone https://github.com/meruhook/meruhook-sdk.git
cd meruhook-sdk
```

2. Install dependencies:
```bash
composer install
```

3. Run the test suite:
```bash
composer test
```

4. Run PHPStan analysis:
```bash
composer analyse
```

5. Format code with Laravel Pint:
```bash
composer format
```

## Running Tests

We use [Pest](https://pestphp.com/) for testing. Run the tests with:

```bash
composer test
```

For test coverage:
```bash
composer test-coverage
```

## Code Quality

This project uses several tools to maintain code quality:

- **Pest** - Testing framework
- **PHPStan** - Static analysis tool
- **Laravel Pint** - Code formatter

All of these are automatically run via GitHub Actions on pull requests.

## Security

If you discover any security related issues, please use the security contact information in the README instead of using the issue tracker.

**Happy coding**!