# GrumPHP Gitlab Lint

Lint your Gitlab CI configuration in a GrumPHP pre-commit hook

## Installation

Supports Symfony 5.3 and above, with PHP 7.4 or higher.

Install with Composer:

```bash
composer require --dev headsnet/grumphp-gitlab-lint
```

## Usage

```yaml
# grumphp.yml
grumphp:
    tasks:
        gitlab_lint:
            api_token: <YOUR TOKEN>
            gitlab_file: .gitlab-ci.yml
    extensions:
        - Headsnet\GrumPHP\GitlabLint\GitlabLintLoader
```

#### API Token (required)

You must create an API token to authenticate with. The token must have `api` access.

#### Gitlab File (optional)

Optional parameter to specify an alternative file to lint. Default is `.gitlab-ci.yml` in the project root.

## Contributing

Contributions are welcome. Please submit pull requests with one fix/feature per
pull request.

Composer scripts are configured for your convenience:

```
> composer test       # Run test suite
> composer cs         # Run coding standards checks
> composer cs-fix     # Fix coding standards violations
> composer static     # Run static analysis with Phpstan
```

## Licence

This code is released under the MIT licence. Please see the LICENSE file for more information.


