<?php
/** 
    Test src/curve.php
    Displays curves of data/ertel-female-mars-1993.csv
    PECL module php-yaml must be installed.
    On Debian-based systems :
    sudo pecl install php-yaml
    usage:
        php curve_test.php > curve_test.svg
    
**/

die('!!!! curve not operational !!!!!');

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';
    
use tigdraw\curve;

$testfile = implode(DIRECTORY_SEPARATOR, [__DIR__, 'data', 'ertel-female-mars-1993.yml']);

$yml = yaml_parse_file$testfile);
//echo "\n"; print_r($yml); echo "\n"; exit;

[$html_markup, $file_contents] = curve::svg($yml);

echo $html_markup;
