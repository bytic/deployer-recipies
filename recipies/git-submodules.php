<?php

/* (c) Gabriel Solomon <hello@gabrielsolomon.ro>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/*
 * This recipe supports ByTic Framework 0.9
 */

namespace Deployer;

/*** NPM INSTALL ***/
desc('npm in all submodules');
task('bytic:gitsub:npm-install', function () {
    runInSubmodules('npm install');
});

/*** GRUNT INSTALL ***/
desc('grunt in all submodules');
task('bytic:gitsub:grunt', function () {
    runInSubmodules('grunt');
});

/*** PHINX INSTALL ***/
//desc('grunt in all submodules');
//task('bytic:gitsub:npm-install', function () {
//    $output = run("cd {{release_path}} git submodule foreach 'grunt ||:' ");
//    writeln('<info>' . $output . '</info>');
//});

/*** Run command in each submodule **
 * @param $command
 * @param bool $output
 * @return Type\Result
 */
function runInSubmodules($command, $output = true)
{
    $result = run("cd {{release_path}} && git submodule foreach '{$command} ||:' ");
    if ($output) {
        writeln('<info>' . $result . '</info>');
    }
    return $result;
}
