#!/usr/bin/env php
<?php
/**
* Nextcloud - News
*
* SPDX-FileCopyrightText: Benjamin Brahmer <info@b-brahmer.de>
* SPDX-License-Identifier: AGPL-3.0-or-later
*/

if ($argc < 2) {
    echo "This script expects two parameters:\n";
    echo "./file_from_env.php ENV_VAR PATH_TO_FILE\n";
    exit(1);
}

# Read environment variable
$content = getenv($argv[1]);

if (!$content){
    echo "Variable was empty\n";
    exit(1);
}

file_put_contents($argv[2], $content);

echo "Done...\n";