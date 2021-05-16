<?php
/******************************************************************************
    Displays 1 to n curves with their legends.
    
    @license    GPL
    @history    2021-05-09 03:27:12+01:00, Thierry Graff : Creation from bar.php
********************************************************************************/
namespace tigdraw;

class curve {
    
    /** 
        Computes the svg markup of a 1 to n distributions.
        
        Layout : the image is composed of legends, gaps and a bar area (containing only the bars).
        Bar area height is imposed (parameter $barH) ; bar width is computed.
        Image total height and width ($w and $h) are computed (= bar size + lengends and gaps).
        @return See {@link observe\parts\draw\svg::result()} documentation 
        @param  $data               The data to represent.
                                    Associative or regular array.
        @param  $stats              Associative array containing statistical informations about the distribution.
                                    Possible keys:
                                        - mean
                                        - top-key
                                        - top-key-index
        // image, general
        @param  $svg_separate       Save in a separate .svg file ?
        @param  $img_src            Useful only if $svg_separate = true
                                    In generated client page : <img src="$img_src">
        @param  $img_alt            Useful only if $svg_separate = true
                                    In generated client page : <img alt="$img_alt">
        @param  $drawAreaH          in px - height of the area where curves are drawn.
        @param  $hGap               in px - horizontal (left and right) gap of the image.
        @param  $vGap               in px - vertical (left and right) gap of the image.
        @param  $background         Background color of the image.
        // title
        @param  $title              Title to display on the image.
        @param  $titleH             in px - height of the title (= font size).
        @param  $titleBottomGap     in px - gap between the title and bar area.
                                    Set to 0 if title = empty string.
        // bar
        @param  $barW               in px - width of each vertical bar.
        @param  $barGap             in px - space between 2 vertical bars.
        @param  $barColor           Color of the vertical bars.
        @param  $barHover           If true, a tooltip with (key, value) is displayed on mouse hover
        // x and y axis
        @param  $xAxis              draw x axis ?
        @param  $xAxisStyle         Style to draw the line of x axis
        @param  $yAxis              boolean - draw y axis ?
        @param  $yAxisStyle         Style to draw the line of y axis
        // x legends
        @param  $xlegends           Text to write below the x axis.
                                    TODO explain syntax
        @param  $xlegendsH          in px - height of x legends (= font size)
        @param  $xlegendsTopGap     in px - gap between x axis and x legends
                                    Set to 0 if no x legends.
        // y legends
        @param  $ylegends           Text to write left of the y axis.
                                    TODO explain syntax
        @param  $ylegendsW          in px - width of y legends.
        @param  $ylegendsH          in px - height of y legends (= font size)
        @param  $ylegendsRightGap   in px - gap between y legends and y axis.
        @param  $ylegendsRound      Nb of decimal to include in the displayed values.
                                    (meaningful for mean, whidh is generally not integer)
        // other
        @param  $meanLine           Only if $ylegends contain 'mean'
                                    Draw horizontal line for mean ?
        @param  $meanLineStyle      Style for mean bar
        
        @return Array containing 2 elements.
                If $svg_separate = true,
                    - $res[0] = img tag to link to the svg image.
                    - $res[1] = markup of the svg to store in a .svg file
                If $svg_separate = false,
                    - $res[0] = markup of the <svg>.
                    - $res[1] = null
    **/
    public static function svg(array $params = []){
echo "\n<pre>"; print_r($params); echo "</pre>\n"; exit;
        $svg = '';
        // characteristics of data
        $dataKeys = array_keys($data);
// TODO min  max not always useful ?
        [$min, $max] = [min($data), max($data)];
        $maxMin = $max - $min;
        $N = count($data);
        //
        if($title == ''){
            $titleH = 0;
            $titleBottomGap = 0;
        }
        if(empty($xlegends)){
            $xlegendsH = 0;
            $xlegendsTopGap = 0;
        }
        if(empty($ylegends)){
            $ylegendsW = 0;
            $ylegendsRightGap = 0;
        }
        //
        // general variables for drawing
        // 
        // $xBegin, $xEnd, $yBegin, $yEnd = coordinates of top-left and bottom-right of the bar area
        $xBegin = $hGap + $ylegendsW + $ylegendsRightGap;
        $yBegin = $vGap + $titleH + $titleBottomGap;
        $drawAreaW = $N * $barW + ($N-1) * $barGap;
        // $drawAreaH given in parameter
        $xEnd = $xBegin + $drawAreaW;
        $yEnd = $yBegin + $drawAreaH;
        //
        $deltaY = $yEnd - $yBegin;
        // $h, $w = size of the image
        $w = $xEnd + $hGap;
        $h = $yEnd + $xlegendsTopGap + $xlegendsH + $vGap;
        //
        $barDelta = $barW + $barGap; 
        //
        //
        //
        $style = <<<SVG
<style type="text/css"><![CDATA[
.bl { /* bar line */
    stroke:$barColor;
    stroke-width:$barW;
}
.title{
    text-anchor:left;
    font-weight:bold;
    font-size:{$titleH}px;
}
.xAxis{{$xAxisStyle}}
.yAxis{{$yAxisStyle}}
.xLegends{
    text-anchor:middle;
    font-size:{$xlegendsH}px;
}
.yLegends{
    text-anchor:end;
    dominant-baseline:middle;
    font-size:{$ylegendsH}px;
}
.meanLine{
    stroke:black;
    stroke-dasharray:5,20;
}
]]></style>

SVG;
        $svg .= svg::header(
            separate: $svg_separate,
            width: $w,
            height: $h,
        );
        $svg .= $style;
        $svg .= "<rect width=\"100%\" height=\"100%\" fill=\"$background\" />\n"; // hack for bg color 
        //
        // title
        //
        [$x, $y] = [$hGap, $vGap + $titleH];
        $svg .= "<text x=\"$x\" y=\"$y\" class=\"title\">$title</text>\n";
        //
        // axis
        //
        if($xAxis){
            [$x1, $y1] = [$xBegin, $yEnd];
            [$x2, $y2] = [$xEnd, $yEnd];
            $svg .= "<line x1=\"$x1\" y1=\"$y1\" x2=\"$x2\" y2=\"$y2\" class=\"xAxis\" />\n";
        }
        if($yAxis){
            [$x1, $y1] = [$xBegin, $yBegin];
            [$x2, $y2] = [$xBegin, $yEnd];
            $svg .= "<line x1=\"$x1\" y1=\"$y1\" x2=\"$x2\" y2=\"$y2\" class=\"yAxis\" />\n";
        }
        //
        // bars
        //
        $i = 0;
        foreach($data as $key => $val){
            $x1 = $xBegin + ($i)*$barGap + $i*$barW;
            $y1 = $yEnd;
            $x2 = $x1;
            $y = round(($val-$min) * $deltaY / $maxMin, 1);
            $y2 = $yEnd - $y;
            if($barHover === true){
                $svg .= "<g><title>$key: $val</title>";
            }
            $svg .= "<line x1=\"$x1\" y1=\"$y1\" x2=\"$x2\" y2=\"$y2\" class=\"bl\" />";
            if($barHover === true){
                $svg .= '</g>';
            }
            $svg .= "\n";
            $i++;
        }
        //
        // x legend
        //
        if(!empty($xlegends)){
            $y = $yEnd + $xlegendsTopGap + $xlegendsH;
            if(in_array('min', $xlegends)){
                $x = $xBegin;
                $text = $dataKeys[0];
                $svg .= "<text x=\"$x\" y=\"$y\" class=\"xLegends\">$text</text>\n";
            }
            if(in_array('max', $xlegends)){
                $x = $xBegin + $drawAreaW;
                $text = $dataKeys[count($dataKeys)-1];
                $svg .= "<text x=\"$x\" y=\"$y\" class=\"xLegends\">$text</text>\n";
            }
            if(in_array('top', $xlegends)){
                $x = $xBegin + ($stats['top-key-index']-1)*$barGap + $stats['top-key-index']*$barW;
                $svg .= "<text x=\"$x\" y=\"$y\" class=\"xLegends\">{$stats['top-key']}</text>\n";
/* 
                [$top, $place] = self::compute_top($data);
                $x = $xBegin + ($place-1)*$barGap + $place*$barW;
                $text = $top;
                $svg .= "<text x=\"$x\" y=\"$y\" style=\"$xlegendsStyle\">$text</text>\n";
*/
            }
        }
        //
        // y legend
        //
        if(!empty($ylegends)){
            $x = $vGap + $ylegendsW;
            if(!empty($ylegends)){
                if(in_array('min', $ylegends)){
                    $y = $yEnd;
                    $svg .= "<text x=\"$x\" y=\"$y\" class=\"yLegends\">$min</text>\n";
                }
                if(in_array('max', $ylegends)){
                    $y = $yBegin;
                    $svg .= "<text x=\"$x\" y=\"$y\" class=\"yLegends\">$max</text>\n";
                }
                if(in_array('mean', $ylegends)){
                    $yMean = round($yBegin + $deltaY*($max-$stats['mean'])/$maxMin);
                    $y = $yMean;
                    $text = round($stats['mean'], $ylegendsRound);
                    $svg .= "<text x=\"$x\" y=\"$y\" class=\"yLegends\">$text</text>\n";
                }
            }
        }
        //
        // other
        //
        if($meanLine){
            $y1 = $y2 = $yMean;
            $x1 = $xBegin;
            $x2 = $xEnd;
            $svg .= "<g fill=\"none\"><path class=\"meanLine\" d=\"M$x1 $y1 H$x2 $y2 Z\" /></g>";
        }
        //
        return svg::result(
            svg:            $svg,
            svg_separate:   $svg_separate,
            img_src:        $img_src,
            img_alt:        $img_alt,
        );
    }
    
} // end class
