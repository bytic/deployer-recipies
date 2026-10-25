<?php

declare(strict_types=1);

/* (c) David Jordan / CyberDuck <david@cyber-duck.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer;

use Deployer\Utility\Httpie;

/*
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
    if (!is_array($config)) {
        throw new \UnexpectedValueException('Cloudflare configuration must be an array.');
    }

    // Build auth headers.
    $serviceKey = $config['service_key'] ?? null;
    $apiToken = $config['api_token'] ?? null;
    $email = $config['email'] ?? null;
    $apiKey = $config['api_key'] ?? null;

    if (is_string($serviceKey) && $serviceKey !== '') {
        $authHeaders = ['X-Auth-User-Service-Key' => $serviceKey];
    } elseif (is_string($apiToken) && $apiToken !== '') {
        $authHeaders = ['Authorization' => 'Bearer ' . $apiToken];
    } elseif (is_string($email) && $email !== '' && is_string($apiKey) && $apiKey !== '') {
        $authHeaders = [
            'X-Auth-Key'   => $apiKey,
            'X-Auth-Email' => $email,
        ];
    } else {
        throw new \RuntimeException('Cloudflare: provide a service_key, api_token, or email + api_key.');
    }

    $domain = $config['domain'] ?? null;
    if (!is_string($domain) || $domain === '') {
        throw new \RuntimeException('Cloudflare: set a domain.');
    }

    $baseUrl = 'https://api.cloudflare.com/client/v4/';

    // Resolve the zone ID for the given domain.
    $zonesRequest = Httpie::get($baseUrl . 'zones')->query(['name' => $domain]);
    foreach ($authHeaders as $header => $value) {
        $zonesRequest->header($header, $value);
    }
    $zonesResponse = $zonesRequest->send();

    if ($zonesResponse->status() < 200 || $zonesResponse->status() >= 300) {
        throw new \RuntimeException(
            'Cloudflare: zone lookup returned HTTP ' . $zonesResponse->status() . '.'
        );
    }

    $zones = $zonesResponse->json();
    if (!is_array($zones) || ($zones['success'] ?? false) !== true || !empty($zones['errors'])) {
        throw new \RuntimeException('Cloudflare: could not retrieve zone data for domain "' . $domain . '".');
    }

    $zone = $zones['result'][0] ?? null;
    $zoneId = is_array($zone) ? ($zone['id'] ?? null) : null;
    if (!is_string($zoneId) || $zoneId === '') {
        throw new \RuntimeException('Cloudflare: no zone was found for domain "' . $domain . '".');
    }

    // Purge all files in the zone.
    $purgeRequest = Httpie::post($baseUrl . 'zones/' . rawurlencode($zoneId) . '/purge_cache')
        ->jsonBody(['purge_everything' => true]);
    foreach ($authHeaders as $header => $value) {
        $purgeRequest->header($header, $value);
    }
    $purgeResponse = $purgeRequest->send();

    if ($purgeResponse->status() < 200 || $purgeResponse->status() >= 300) {
        throw new \RuntimeException(
            'Cloudflare: cache purge returned HTTP ' . $purgeResponse->status() . '.'
        );
    }

    $purge = $purgeResponse->json();
    if (!is_array($purge) || ($purge['success'] ?? false) !== true || !empty($purge['errors'])) {
        throw new \RuntimeException('Cloudflare: cache purge failed.');
    }
});
