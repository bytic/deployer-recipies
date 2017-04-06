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
    $output = run("cd {{release_path}} && git submodule foreach 'npm install ||:' ");
    writeln('<info>' . $output . '</info>');
});

/*** GRUNT INSTALL ***/
desc('grunt in all submodules');
task('bytic:gitsub:grunt', function () {
    $output = run("cd {{release_path}} git submodule foreach 'grunt ||:' ");
    writeln('<info>' . $output . '</info>');
});

/*** PHINX INSTALL ***/
//desc('grunt in all submodules');
//task('bytic:gitsub:npm-install', function () {
//    $output = run("cd {{release_path}} git submodule foreach 'grunt ||:' ");
//    writeln('<info>' . $output . '</info>');
//});
