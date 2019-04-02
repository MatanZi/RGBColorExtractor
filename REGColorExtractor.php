<?php
/**
 * Created by PhpStorm.
 * User: Matan
 * Date: 02/04/2019
 * Time: 13:33
 */

/**
 * @param $imageFile is the given image file.
 * @param int $granularity is the random optical texture of processed photographic film
 * @return array|bool returns a sorted array of all color of the given image in a descended order, otherwise return false
 * the time complexity is: O(n^2)
 * Description: colorPalette separates each color of each pixel and finding its RGB color.
 * now the RGB color is the key of which we will use to mark its position in colorArray
 * afterwards colorPalette checks whether this RGB color exists in the
 * returned array (colorArray) and if so the value of the RGB color in the his position in colorArray
 * is incrementing by 1, otherwise set the amount to 1, the function does the same for each and any pixel and return an array with
 * the appearance amount of each color in the given image.
 */
function colorPalette($imageFile, $granularity = 5)
{
    $granularity = max(1, abs((int)$granularity));
    $colorArray = array();
    $imageSize = @getimagesize($imageFile); // for more information about "getimagesize" see: https://www.geeksforgeeks.org/php-imagecolorsforindex-function/
    if($imageSize === false)
    {
        user_error("Unable to get image size data");
        return false;
    }

    if ($imageSize[2]==1)
        $img = @imagecreatefromgif($imageFile); // for more information about "imagecreatefromgif" see: http://www.phptutorial.info/?imagecreatefromgif
    if ($imageSize[2]==2)
        $img = @imagecreatefromjpeg($imageFile);// for more information about "imagecreatefromjpeg" see: https://www.php.net/manual/de/function.imagecreatefromjpeg.php
    if ($imageSize[2]==3)
        $img = @imagecreatefrompng($imageFile);// for more information about "imagecreatefrompng" see: https://www.php.net/manual/de/function.imagecreatefrompng.php
    if(!$img)
    {
        user_error("Unable to open image file");
        return false;
    }
    //filling the array with the amount of appearance of each color of the image file
    for($x = 0; $x < $imageSize[0]; $x += $granularity)
    {
        for($y = 0; $y < $imageSize[1]; $y += $granularity)
        {
            $thisColor = imagecolorat($img, $x, $y);// for more information about "imagecolorat" see: https://www.php.net/manual/de/function.imagecolorat.php
            $rgb = imagecolorsforindex($img, $thisColor);// for more information about "imagecolorsforindex" see: https://www.php.net/manual/de/function.imagecolorsforindex.php
            $red = round(round(($rgb['red'] / 0x33)) * 0x33);
            $green = round(round(($rgb['green'] / 0x33)) * 0x33);
            $blue = round(round(($rgb['blue'] / 0x33)) * 0x33);
            $thisRGB = sprintf('%02X%02X%02X', $red, $green, $blue);
            if(array_key_exists($thisRGB, $colorArray))
            {
                $colorArray[$thisRGB]++;
            }
            else
            {
                $colorArray[$thisRGB] = 1;
            }
        }
    }
    arsort($colorArray);
    return $colorArray;
}

/**
 * Html2Rgb helps build the table which present the output of this task.
 */
function Html2Rgb($strColor)
{
    if ($strColor[0] == '#')
        $strColor = substr($strColor, 1);

    if (strlen($strColor) == 6)
        list($red, $green, $blue) = array($strColor[0].$strColor[1],
            $strColor[2].$strColor[3],
            $strColor[4].$strColor[5]);
    elseif (strlen($strColor) == 3)
        list($red, $green, $blue) = array($strColor[0].$strColor[0], $strColor[1].$strColor[1], $strColor[2].$strColor[2]);
    else
        return false;

    $red = hexdec($red); $green = hexdec($green); $blue = hexdec($blue);
    $arrRGB = '('.$red.','. $green.','. $blue.')';
// Return colors format liek R(255) G(255) B(255)
    return $arrRGB;
}

/**
 * @param $imageFile is the given image file.
 * @param $granularity is the random optical texture of processed photographic film
 * @return float is the number of pixels of the given image file.
 */
function getTotalPixel($imageFile, $granularity)
{
    $imageSize = @getimagesize($imageFile);
    $totalPixel = $imageSize[0]*$imageSize[1];
    $Pixels = round($totalPixel / $granularity);
    return $Pixels;
}

//place your image url in the the imageFile field
$imageFile = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQIS3cbBJ454s-7nGY4elCuILyO6yBGPRt7lT3d8qdLbVbgWIcmig';
$granularity = 4;
$palette = colorPalette($imageFile, $granularity);
$totalPixel = getTotalPixel($imageFile, $granularity);

//set how many color your would like to check.
$colorsToShow = 5;


//building the the table which present the output of this task.
echo '<img src="'.$imageFile.'"><br/>';
echo '<table border="1"><tr><td>Color</td><td>Color Hex</td><td>Color RGB</td><td>Count</td><td>Percentage</td></tr>';
for($h=0;$h<$colorsToShow;$h++) {
    $color = array_keys($palette);
    $hex = '#'.$color[$h];
    $colorPixel = $palette[$color[$h]];
    $percentage = ($colorPixel / $totalPixel) * 100;
    echo '<tr><td style="background-color:'.$hex.';width:2em;">&nbsp;</td><td>'.$hex.'</td><td>rgb'.Html2Rgb($hex).'</td><td>'.$palette[$color[$h]].'</td><td>'.number_format($percentage, 1).' %</td>';
}
echo '</table>';

?>
