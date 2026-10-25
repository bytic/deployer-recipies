# deployer-recipies

Deployer recipes for the [ByTIC framework](https://github.com/ByTIC).

## Requirements

- PHP 8.4+
- [Deployer](https://deployer.org/) 8.x

The next release requires PHP 8.4 or newer. PHP 8.1–8.3 are no longer in the
test matrix; use the previous package release if your deploy runner still uses
one of those versions.

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

The main recipe loads the Bytic command helpers, Cloudflare task, deploy
sub-recipes, and Supervisor integration. The standard pipeline is
`deploy:prepare` → `deploy:vendors` → `deploy:assets` → `deploy:optimize` →
`deploy:publish` → `deploy:migrations`. It also links storage after
`deploy:symlink`, unlocks after a failed deploy, publishes scheduler events on
success, and manages Supervisor around symlink/deploy events.

### `recipies/deploy/*` — Deployment tasks

- `assets.php` installs and builds frontend assets using Webpack Encore. Set
  `assets/package_manager` to override the package manager; it defaults to
  `webpack_encore/package_manager` when present, otherwise `npm`.
- `cleanup.php` removes `node_modules` from the current and previous releases,
  and removes the previous release's `vendor` and `bootstrap/cache` directories.
  Set `cleanup_use_sudo` to use `sudo` for removal.
- `config.php` defines release retention, shared files/directories, writable
  directories, and writable permissions.
- `migrations.php` runs `migrations:migrate`; `optimize.php` runs
  `config:cache`.
- `scheduler.php` publishes scheduler events after a successful deployment.
- `storage.php` links `storage/app/public` to `current/public/uploads`.
- `supervisor.php` stops services before uploading configuration and updates
  and restarts them after deploy success, failure, or rollback.

These paths intentionally retain the package's historical `recipies/` spelling.

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

`supervisor_source_dir` is read from the local checkout. Matching files are
merged in filename order and uploaded as the configured remote file. An empty
merged configuration removes that remote file.

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

The message and color values can be overridden with `slack_title`,
`slack_text`, `slack_success_text`, `slack_failure_text`, `slack_color`,
`slack_success_color`, and `slack_failure_color`. Notifications are skipped
when `slack_webhook` is unset.

### `common/node_modules.php` — Node dependency cleanup

Defines `deploy:cleanup:node` and `deploy:previous_release:cleanup_node`.
These tasks are wired into the cleanup recipe; `cleanup_use_sudo` controls
whether removal uses `sudo`.

---

### `contrib/bytic-console.php` — Bytic CLI helper

Provides the `bytic()` helper function and `bin/bytic` config key.

```php
require_once 'vendor/bytic/deployer-recipies/contrib/bytic-console.php';

// Create a task that runs a bytic command:
task('my:task', bytic('my:command', ['showOutput']));
```

Supported option flags in the `$options` array:
- `'failIfNoEnv'` — abort if `.env` is missing or empty
- `'skipIfNoEnv'` — skip silently if `.env` is missing or empty
- `'showOutput'`  — print command output to the console

The options array can also contain named CLI arguments, for example
`['environment' => 'production']`. Argument values are shell-quoted before
execution.

---

### `recipies/deploy/scheduler.php` — Scheduler integration

Publishes cron/scheduler events after a successful deploy.

```php
require 'vendor/bytic/deployer-recipies/recipies/deploy/scheduler.php';
```

**Tasks:** `bytic:scheduler:publish`

### `contrib/bytic-commands.php` — Bytic command tasks

Loaded by the main recipe. Defines `bytic:optimize` and previous-release
cleanup tasks for `vendor` and `bootstrap/cache`.

### `common/cloudflare.php` — Cloudflare task

The main recipe loads this task but does not attach it to a deploy hook.
Configure credentials and domain as shown above, then explicitly register
`after('deploy:success', 'deploy:cloudflare')` if cache purging is desired.
The task checks HTTP status and Cloudflare's JSON success response for both
zone lookup and purge; it fails rather than treating an unsuccessful purge
as success.

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
composer validate    # validate Composer metadata
vendor/bin/phpstan analyse --no-progress
```

## Inspiration

- <https://github.com/Setono/deployer-supervisor>
