<?php
/** 
    Test src/bar.php
    Displays a bar chart of data/day.csv
    
    usage:
        php bar_test.php > bar_test.svg
**/
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'autoload.php';

use tigdraw\bar;
use tigdraw\svg;

$testfile = implode(DIRECTORY_SEPARATOR, [__DIR__, 'data', 'day.csv']);
$csv = file($testfile, FILE_IGNORE_NEW_LINES);
$data = [];
foreach($csv as $line){
    $tmp = explode(';', $line);
    $data[$tmp[0]] = $tmp[1];
}

[$html_markup, $file_contents] = bar::svg(
    data:           $data,
    title:          "Test Bar chart",
    svg_separate:   false,
    barW:           2,
    xlegends:       ['min', 'max'],
    ylegends:       ['min', 'max'],
    ylegendsRound:  1,
);

echo $html_markup;
