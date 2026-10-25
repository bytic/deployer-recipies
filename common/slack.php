<?php

declare(strict_types=1);

/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

use Deployer\Utility\Httpie;

// Title of project
set('slack_title', static function (): string {
    return get('application', 'Project');
});

// Deploy messages
set('slack_text', '_{{user}}_ deploying `{{branch}}` to *{{target}}*');
set('slack_success_text', 'Deploy to *{{target}}* successful');
set('slack_failure_text', 'Deploy to *{{target}}* failed');

// Block Kit colors
set('slack_color', '#4d91f7');
set('slack_success_color', '#00c100');
set('slack_failure_color', '#ff0909');

$notifySlack = static function (string $textKey, string $colorKey): void {
    $webhook = get('slack_webhook', false);
    if ($webhook === false || $webhook === null || $webhook === '') {
        return;
    }
    if (!is_string($webhook)) {
        throw new \UnexpectedValueException('Slack webhook must be a URL string.');
    }

    $title = get('slack_title');
    $text = get($textKey);
    $color = get($colorKey);
    if (!is_string($title) || !is_string($text) || !is_string($color)) {
        throw new \UnexpectedValueException('Slack title, message, and color settings must be strings.');
    }

    $response = Httpie::post($webhook)
        ->jsonBody([
            'blocks' => [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => '*' . $title . '*' . "\n" . $text,
                    ],
                ],
            ],
            'attachments' => [
                ['color' => $color, 'fallback' => $text],
            ],
        ])
        ->send();

    if ($response->status() < 200 || $response->status() >= 300 || trim($response->body()) !== 'ok') {
        throw new \RuntimeException(
            'Slack notification failed with HTTP ' . $response->status() . ' or an unexpected response body.'
        );
    }
};

desc('Notify Slack about deployment start');
task('slack:notify', static function () use ($notifySlack): void {
    $notifySlack('slack_text', 'slack_color');
})->once()->hidden();

desc('Notify Slack about failed deployment');
task('slack:notify:success', static function () use ($notifySlack): void {
    $notifySlack('slack_success_text', 'slack_success_color');
})->once()->hidden();

task('slack:notify:failure', static function () use ($notifySlack): void {
    $notifySlack('slack_failure_text', 'slack_failure_color');
})->once()->hidden();
