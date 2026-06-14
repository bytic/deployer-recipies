<?php

declare(strict_types=1);

/* (c) David Jordan / CyberDuck <david@cyber-duck.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

use Deployer\Utility\Httpie;

/**
 * Cloudflare configuration array keys:
 *   - service_key (string)  — Cloudflare service key (X-Auth-User-Service-Key)
 *   - api_token   (string)  — API token (Authorization: ******
 *   - email       (string)  — Account e-mail (used with api_key)
 *   - api_key     (string)  — Global API key (used with email)
 *   - domain      (string)  — Domain whose cache should be purged (required)
 */
set('cloudflare', []);

desc('Clear Cloudflare cache');
task('deploy:cloudflare', static function (): void {
    $config = get('cloudflare', []);

    // Build auth headers.
    if (!empty($config['service_key'])) {
        $authHeaders = ['X-Auth-User-Service-Key' => $config['service_key']];
    } elseif (!empty($config['api_token'])) {
        $authHeaders = ['Authorization' => 'Bearer ' . $config['api_token']];
    } elseif (!empty($config['email']) && !empty($config['api_key'])) {
        $authHeaders = [
            'X-Auth-Key'   => $config['api_key'],
            'X-Auth-Email' => $config['email'],
        ];
    } else {
        throw new \RuntimeException('Cloudflare: provide a service_key, api_token, or email + api_key.');
    }

    if (empty($config['domain'])) {
        throw new \RuntimeException('Cloudflare: set a domain.');
    }

    $baseUrl = 'https://api.cloudflare.com/client/v4/';

    // Resolve the zone ID for the given domain.
    $zonesResponse = Httpie::get($baseUrl . 'zones')
        ->query(['name' => $config['domain']])
        ->addHeaders($authHeaders)
        ->send();

    $zones = json_decode($zonesResponse, true);

    if (empty($zones['success']) || !empty($zones['errors'])) {
        throw new \RuntimeException('Cloudflare: could not retrieve zone data for domain "' . $config['domain'] . '".');
    }

    $zoneId = current($zones['result'])['id'];

    // Purge all files in the zone.
    Httpie::post($baseUrl . "zones/$zoneId/purge_cache")
        ->addHeaders($authHeaders)
        ->body(['purge_everything' => true])
        ->send();
});
