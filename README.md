![Github Actions](https://github.com/headsnet/grumphp-gitlab-lint/actions/workflows/ci.yml/badge.svg)

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
            api_token:   '%env(GITLAB_TOKEN)%'   # required
            gitlab_file: .gitlab-ci.yml          # optional
            gitlab_url:  gitlab.com              # optional
    extensions:
        - Headsnet\GrumPHP\GitlabLint\Loader
```

#### API Token (required)

You must create an API token to authenticate with. The token must have `api` access.

You can use `'%env(YOUR_ENV_VAR_NAME)%'` syntax to import an environment variable so you don't commit the token to your 
repository.

#### Gitlab File (optional)

Optional parameter to specify an alternative file to lint. Default is `.gitlab-ci.yml` in the project root.

#### Gitlab URL (optional)

A custom location for your on-premises Gitlab instance.


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


