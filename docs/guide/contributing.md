# Contributing

Run PHP checks from the repository root after installing Composer dependencies:

```sh
composer test
composer cs-check
composer stan-setup
composer stan
```

Build the documentation from `docs`:

```sh
npm ci
npm run docs:build
```

Use `npm run docs:dev` for a local preview. Check logging and query changes against the databases in the [CI workflow](https://github.com/dereuromark/CakePHP-DatabaseLog/blob/master/.github/workflows/ci.yml). Update examples whenever a configuration key or command changes.
