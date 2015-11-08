<?php
if(!$sql){
    exit;
}
$pagesWithSpecial = null;
$que = "SELECT * FROM settings WHERE parameter='pagesWithSpecials'";
$erg = mysqli_query($sql,$que);
while($row = mysqli_fetch_array($erg)){
    $pagesWithSpecial = $row["value"];
}
mysqli_free_result($erg);
if(strpos($pagesWithSpecial,";$id;")>-1){
    echo('<link rel="stylesheet" href="styleSpecialContent.min.css" />');
    $specialContent = "<div class='imgSliderHeader'><div class='imgSliderHeaderInner'><div class='imgSliderHeaderLoading'></div><img class='imgSliderHeaderImages' id='imgSliderHeaderImg0' src='web-images/1/sliderImage0.jpg' /><img class='imgSliderHeaderImages' id='imgSliderHeaderImg1' src='web-images/1/sliderImage1.jpg' /><img class='imgSliderHeaderImages' id='imgSliderHeaderImg2' src='web-images/1/sliderImage2.jpg' /><img class='imgSliderHeaderImages' id='imgSliderHeaderImg3' src='web-images/1/sliderImage3.jpg' /><div class='imgSliderHeaderNav'><div class='imgSliderHeaderNavPoint' id='imgSliderHeaderNav0' onclick='specialSliderShowPic(0)'></div><div class='imgSliderHeaderNavPoint' id='imgSliderHeaderNav1' onclick='specialSliderShowPic(1)'></div><div class='imgSliderHeaderNavPoint' id='imgSliderHeaderNav2' onclick='specialSliderShowPic(2)'></div><div class='imgSliderHeaderNavPoint' id='imgSliderHeaderNav3' onclick='specialSliderShowPic(3)'></div></div></div></div>";
}