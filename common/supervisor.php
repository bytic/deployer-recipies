<?php

declare(strict_types=1);

namespace Deployer;

use Symfony\Component\Finder\Finder;

/**
 * The supervisor(ctl) binary
 */
set(
    'bin/supervisor',
    static function (): string {
        $sudo = get('supervisor_sudo') ? 'sudo ' : '';
        return $sudo . which('supervisorctl');
    }
);

set('supervisor_sudo', true);

/**
 * Local directory containing your supervisor config files.
 */
set('supervisor_source_dir', 'etc/supervisor');

/**
 * Remote directory where the merged config file will be uploaded.
 */
set('supervisor_remote_dir', '/etc/supervisor/conf.d');

/**
 * Name of the merged config file created on the server.
 */
set('supervisor_config_filename', '{{application}}-{{stage}}.conf');

/**
 * List of local config file names to exclude from the merged output.
 */
set('supervisor_excluded_files', []);

desc('Stop all services managed by Supervisor');
task(
    'supervisor:stop',
    static function (): void {
        run('{{bin/supervisor}} stop all');
    }
);

desc('Upload merged supervisor config to the server');
task(
    'supervisor:upload',
    static function (): void {
        $folder = get('supervisor_source_dir');
        if (!test('[ -d {{release_path}}/' . $folder . ' ]')) {
            return;
        }

        $finder = new Finder();
        $finder->files()->in($folder);

        $mergedConfigs = '';
        foreach ($finder as $file) {
            if (in_array($file->getFilename(), get('supervisor_excluded_files'), true)) {
                continue;
            }

            $mergedConfigs .= trim((string) file_get_contents($file->getRealPath())) . "\n\n";
        }

        $remoteFile = '{{supervisor_remote_dir}}/{{supervisor_config_filename}}';

        if ($mergedConfigs === '') {
            run("rm -rf $remoteFile");
            return;
        }

        // Write merged config to a local temp file and upload it safely,
        // avoiding any shell injection risks from the config file contents.
        $tmpFile = tempnam(sys_get_temp_dir(), 'supervisor_');
        try {
            file_put_contents($tmpFile, $mergedConfigs);
            upload($tmpFile, $remoteFile);
        } finally {
            @unlink($tmpFile);
        }
    }
);

desc('Start all services managed by Supervisor');
task(
    'supervisor:start',
    static function (): void {
        run('{{bin/supervisor}} update');
        run('{{bin/supervisor}} start all');
    }
);

before('supervisor:upload', 'supervisor:stop');
