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

desc('Notify Slack about deployment start');
task('slack:notify', static function (): void {
    if (!get('slack_webhook', false)) {
        return;
    }

    Httpie::post(get('slack_webhook'))
        ->body([
            'blocks' => [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => '*' . get('slack_title') . '*' . "\n" . get('slack_text'),
                    ],
                ],
            ],
            'attachments' => [
                ['color' => get('slack_color'), 'fallback' => get('slack_text')],
            ],
        ])
        ->send();
})
    ->once()
    ->shallow()
    ->setPrivate();

desc('Notify Slack about successful deployment');
task('slack:notify:success', static function (): void {
    if (!get('slack_webhook', false)) {
        return;
    }

    Httpie::post(get('slack_webhook'))
        ->body([
            'blocks' => [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => '*' . get('slack_title') . '*' . "\n" . get('slack_success_text'),
                    ],
                ],
            ],
            'attachments' => [
                ['color' => get('slack_success_color'), 'fallback' => get('slack_success_text')],
            ],
        ])
        ->send();
})
    ->once()
    ->shallow()
    ->setPrivate();

desc('Notify Slack about failed deployment');
task('slack:notify:failure', static function (): void {
    if (!get('slack_webhook', false)) {
        return;
    }

    Httpie::post(get('slack_webhook'))
        ->body([
            'blocks' => [
                [
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => '*' . get('slack_title') . '*' . "\n" . get('slack_failure_text'),
                    ],
                ],
            ],
            'attachments' => [
                ['color' => get('slack_failure_color'), 'fallback' => get('slack_failure_text')],
            ],
        ])
        ->send();
})
    ->once()
    ->shallow()
    ->setPrivate();
