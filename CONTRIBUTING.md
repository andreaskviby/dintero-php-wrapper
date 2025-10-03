# Contributing

Contributions are welcome and will be fully credited.

## Guidelines

### Pull Requests

- **PSR-12 Coding Standard** - Check the code style with `composer cs-check` and fix it with `composer cs-fix`.

- **Add tests!** - Your patch won't be accepted if it doesn't have tests.

- **Document any change in behaviour** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow [SemVer v2.0.0](http://semver.org/). Randomly breaking public APIs is not an option.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.

### Running Tests

```bash
composer test
```

### Code Style

```bash
composer cs-check
composer cs-fix
```

### Static Analysis

```bash
composer phpstan
```

**Happy coding**!