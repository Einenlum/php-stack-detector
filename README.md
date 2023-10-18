# PHP Stack Detector

This library allows to easily detect the PHP stack (Wordpress, Laravel, Symfony…) and the version used, when parsing a directory or ar Github remote repository.

Supported Stacks for now:

- Wordpress
- Laravel
- Symfony
- Statamic
- Craft CMS

## Install

```
composer require einenlum/php-stack-detector
```

## Usage

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Einenlum\PhpStackDetector\Detector;
use Einenlum\PhpStackDetector\StackType;

$detector = Detector::createForFilesystem();
$stack = $detector->getStack('/path/to/a/symfony/directory');

$stack->type === StackType::SYMFONY;
$stack->version; // 5.4

$stack = $detector->getStack('/path/to/an/unknown/symfony/version/directory');
$stack->type === StackType::SYMFONY;
$stack->version; // null

$stack = $detector->getStack('/path/to/an/unknown/stack/directory');
$stack; // null

$detector = Detector::createForGithub();
$stack = $detector->getStack('symfony/demo');

$stack->type === StackType::SYMFONY;
$stack->version; // 6.3.0

// You can also pass an already authenticated Github Client
$client = new \Github\Client();
$client->authenticate('some_access_token', null, \Github\AuthMethod::ACCESS_TOKEN);
$detector = Detector::createForGithub($client);

$stack = $detector->getStack('einenlum/private-repo');

$stack->type === StackType::SYMFONY;
$stack->version; // 6.3.0
```

You can also use the CLI to test it.

```
php bin/detect-local.php ~/Prog/php/my_project/
Detected stack: laravel
Version: 10.19.0

php bin/detect-github.php 'symfony/demo'
Detected stack: symfony
Version: 6.3.0
```

It is advised to use an access token for github parsing, to either access private repositories or avoid reaching Github API limit.

```
GITHUB_ACCESS_TOKEN=my_token php bin/detect-github.php 'einenlum/private-repo'
Detected stack: laravel
Version: 10.19.0
```

## Tests

```
composer run test
```

## Contribute

Each stack has its own Detector implementing a [StackDetectorInterface](src/StackDetectorInterface.php).
If the stack uses composer you can use the [PackageVersionProvider](src/Composer/PackageVersionProvider.php) class.
This will use a [ComposerConfigProvider](src/Composer/ComposerConfigProvider.php) to get the lock or the json config.

All of them use an Adapter, that is for now either [FilesystemAdapter](src/DirectoryCrawler/FilesystemAdapter.php) or [GithubAdapter](src/DirectoryCrawler/GithubAdapter.php)

You can add your own StackDetector and then add it to the `create` method of the [Detector](src/Detector.php).

Any Pull Request welcome!
