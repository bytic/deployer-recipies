# deployer-recipies

Deployer recipes for the [ByTIC framework](https://github.com/ByTIC).

## Requirements

- PHP 8.1+
- [Deployer](https://deployer.org/) 8.x

## Installation

```bash
composer require bytic/deployer-recipies
```

## Usage

In your `deploy.php`, include the main recipe:

```php
<?php

namespace Deployer;

require 'recipe/common.php';
require 'vendor/bytic/deployer-recipies/recipies/bytic.php';

set('application', 'my-app');
set('repository', 'git@github.com:my-org/my-app.git');

host('production')
    ->set('hostname', 'example.com')
    ->set('remote_user', 'deploy')
    ->set('deploy_path', '/var/www/my-app');
```

## Available Recipes

### `recipies/bytic.php` — Main deploy recipe

Composes all sub-recipes into a full deploy pipeline:

```
deploy:prepare → deploy:vendors → deploy:assets → deploy:optimize → deploy:publish → deploy:migrations
```

**Configuration**

| Key                        | Default                  | Description                              |
|----------------------------|--------------------------|------------------------------------------|
| `keep_releases`            | `3`                      | Number of releases to keep on the server |
| `shared_files`             | `['.env']`               | Files shared between releases            |
| `shared_dirs`              | `['storage/app', 'storage/logs']` | Dirs shared between releases  |
| `writable_dirs`            | see `config.php`         | Dirs that must be writable               |
| `writable_chmod_mode`      | `'0775'`                 | chmod mode for writable dirs             |

---

### `common/cloudflare.php` — Cloudflare cache purge

Purges the entire Cloudflare cache for a domain after deploy.

```php
require 'vendor/bytic/deployer-recipies/common/cloudflare.php';

set('cloudflare', [
    'api_token' => getenv('CF_API_TOKEN'), // preferred
    // or: 'email' + 'api_key'
    // or: 'service_key'
    'domain' => 'example.com',
]);

after('deploy:success', 'deploy:cloudflare');
```

**Tasks:** `deploy:cloudflare`

---

### `common/supervisor.php` — Supervisor integration

Merges local config files and uploads them to the remote server, then restarts services.

```php
require 'vendor/bytic/deployer-recipies/common/supervisor.php';

set('supervisor_source_dir', 'etc/supervisor');       // local dir with .conf files
set('supervisor_remote_dir', '/etc/supervisor/conf.d'); // remote target dir
set('supervisor_config_filename', 'my-app-prod.conf');
set('supervisor_excluded_files', ['dev-worker.conf']); // optional exclusions
set('supervisor_sudo', true);                          // use sudo for supervisorctl
```

**Tasks:** `supervisor:stop`, `supervisor:upload`, `supervisor:start`

---

### `common/slack.php` — Slack notifications

Sends Block Kit messages to a Slack webhook.

```php
require 'vendor/bytic/deployer-recipies/common/slack.php';

set('slack_webhook', getenv('SLACK_WEBHOOK'));

before('deploy', 'slack:notify');
after('deploy:success', 'slack:notify:success');
after('deploy:failed', 'slack:notify:failure');
```

**Tasks:** `slack:notify`, `slack:notify:success`, `slack:notify:failure`

---

### `contrib/bytic-console.php` — Bytic CLI helper

Provides the `bytic()` helper function and `bin/bytic` config key.

```php
require_once 'vendor/bytic/deployer-recipies/contrib/bytic-console.php';

// Create a task that runs a bytic command:
task('my:task', bytic('my:command', ['showOutput']));
```

Supported option flags in the `$options` array:
- `'failIfNoEnv'` — abort if `.env` is missing
- `'skipIfNoEnv'` — skip silently if `.env` is missing
- `'showOutput'`  — print command output to the console

---

### `recipies/deploy/scheduler.php` — Scheduler integration

Publishes cron/scheduler events after a successful deploy.

```php
require 'vendor/bytic/deployer-recipies/recipies/deploy/scheduler.php';
```

**Tasks:** `bytic:scheduler:publish`

---

## Directory Structure

```
common/      Shared utilities (Cloudflare, Supervisor, Slack)
contrib/     Bytic-specific integrations (console runner, task helpers)
recipies/    Deploy orchestration (main pipeline + sub-recipes)
tests/       PHPUnit tests and fixtures
```

## Development

```bash
composer install
composer test        # run PHPUnit
composer cs-check    # check PSR-12 code style
composer cs-fix      # fix code style automatically
```

## Inspiration

- <https://github.com/Setono/deployer-supervisor>
