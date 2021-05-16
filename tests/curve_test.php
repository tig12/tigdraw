<?php
/** 
    Test src/curve.php
    Displays curves of data/ertel-female-mars-1993.csv
    PECL module php-yaml must be installed.
    On Debian-based systems :
    sudo pecl install php-yaml
    usage:
        php bar_test.php > curve_test.svg
    
**/

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';

use tigdraw\curve;

$testfile = implode(DIRECTORY_SEPARATOR, [__DIR__, 'data', 'ertel-female-mars-1993.yml']);

$yml = yaml_parse(file_get_contents($testfile));
//echo "\n"; print_r($yml); echo "\n"; exit;

[$html_markup, $file_contents] = curve::svg($yml);

echo $html_markup;
