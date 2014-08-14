<?php

/**
 * @file
 * Import Dspace Batch Export Format Into Fedora/Islandora.
 */

require_once 'inc/DspaceItemBundle.php';
require_once 'inc/IslandoraImportBundleWriter.php';
require_once 'lib/smarty/distribution/libs/Smarty.class.php';

$path_to_parse = '';
$parent_pid = '';
