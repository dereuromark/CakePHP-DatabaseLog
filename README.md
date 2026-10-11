#  CakePHP DatabaseLog Plugin

[![CI](https://github.com/dereuromark/CakePHP-DatabaseLog/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/dereuromark/CakePHP-DatabaseLog/actions/workflows/ci.yml?query=branch%3Amaster)
[![Coverage Status](https://img.shields.io/codecov/c/github/dereuromark/CakePHP-DatabaseLog/master.svg)](https://codecov.io/github/dereuromark/CakePHP-DatabaseLog/branch/master)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat)](https://phpstan.org/)
[![Latest Stable Version](https://poser.pugx.org/dereuromark/CakePHP-DatabaseLog/v/stable.svg)](https://packagist.org/packages/dereuromark/CakePHP-DatabaseLog)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.2-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/dereuromark/CakePHP-DatabaseLog/license.svg)](LICENSE)
[![Total Downloads](https://poser.pugx.org/dereuromark/CakePHP-DatabaseLog/d/total.svg)](https://packagist.org/packages/dereuromark/CakePHP-DatabaseLog)
[![Coding Standards](https://img.shields.io/badge/cs-PSR--2--R-purple.svg?style=flat-square)](https://github.com/php-fig-rectified/fig-rectified-standards)

Store CakePHP application logs in a database and inspect them through an admin interface.

This branch supports CakePHP 5. Composer defines the supported runtime versions.

## Features

- SQLite by default, or an application database connection.
- Web and CLI metadata alongside messages and JSON context.
- Admin dashboard and log viewer with an application access callback.
- Commands for retention, duplicate merging, text exports, and monitoring.
- PHP error-log fallback when a database write fails.

## Documentation

Read the [documentation website](https://dereuromark.github.io/CakePHP-DatabaseLog/) for [installation](https://dereuromark.github.io/CakePHP-DatabaseLog/guide/installation) and an [easy first setup](https://dereuromark.github.io/CakePHP-DatabaseLog/guide/getting-started).
