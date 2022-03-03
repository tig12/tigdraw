<?php
/** 
    Test src/bar.php
    Displays a bar chart of data/month.csv
    Used to test xlegends = ['all']
    
    usage:
        php bar_test_month.php > bar_test_month.svg
**/
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';

use tigdraw\bar;
use tigdraw\svg;

$testfile = implode(DIRECTORY_SEPARATOR, [__DIR__, 'data', 'month.csv']);
$csv = file($testfile, FILE_IGNORE_NEW_LINES);
$data = [];
foreach($csv as $line){
    $tmp = explode(';', $line);
    $data[$tmp[0]] = $tmp[1];
}

[$html_markup, $file_contents] = bar::svg(
    data:           $data,
    title:          'Test Bar chart - month',
    svg_separate:   false,
    barW:           20,
    xlegends:       ['all'],
    ylegends:       ['min', 'max'],
    ylegendsRound:  1,
);

echo $html_markup;
