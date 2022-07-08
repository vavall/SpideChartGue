<?php

require_once('QuickChart.php');

function generateChart(){  

  $URLparameters = [$_GET["X1"],$_GET["X2"],$_GET["X3"],$_GET["X4"],$_GET["X5"],$_GET["X6"],$_GET["X7"]];
  if(isset($_GET["X8"])){
    $file = 'lang/' . $_GET["X8"] . '.json';

    if(!file_exists($file)){
      $file = 'lang/en.json';
    }
  } else {
    $file = 'lang/en.json';
  }

  $jsonData =  file_get_contents($file);
  $languagesStrings = json_decode($jsonData, true);

  $labels = ["tachespigmentaires", "hydratation", "pores", "graindepeau", "fermete", "rides"];
  $priority = [4,5,1,3,0,2];

  $labelText = [
    [$languagesStrings['criteria'][0], $URLparameters[0]." / 100"],
    [$languagesStrings['criteria'][1], $URLparameters[1]." / 100"],
    [$languagesStrings['criteria'][2], $URLparameters[2]." / 100"],
    [$languagesStrings['criteria'][3], $URLparameters[3]." / 100"],
    [$languagesStrings['criteria'][4], $URLparameters[4]." / 100"],
    [$languagesStrings['criteria'][5], $URLparameters[5]." / 100"]
  ];

  $preIndex = array_search(array_pop($URLparameters), $labels);

  $order = array();
  $minIndexes = array_keys($URLparameters, min($URLparameters));
  foreach($minIndexes as $index){
    $order[$index] = array_search($index, $priority);
  }
  $minIndex = array_search(min($order), $order);

  $order = array();
  $maxIndexes = array_keys($URLparameters, max($URLparameters));
  foreach($maxIndexes as $index){
    $order[$index] = array_search($index, $priority);
  }
  $maxIndex = array_search(min($order), $order);

  $labelFontColor = [];
  $pointBackgroundColor = [];
 
  $count_params = count($URLparameters);

  for ($i = 0; $i < $count_params; $i++) {
    if ($i == $minIndex || $i == $maxIndex || $i == $preIndex) {
      if (($maxIndex == $preIndex && $i == $preIndex) || ($minIndex == $preIndex && $i == $preIndex)) {
        //Force
        if ($i == $maxIndex) {
          array_push($labelFontColor, '"rgba(26, 35, 78, 1)"');
          array_push($pointBackgroundColor, '"rgba(94, 68, 37, 255)"');
        } else { //Priorité
          array_push($labelFontColor, '"rgba(134, 143, 185, 1)"');
          array_push($pointBackgroundColor, '"rgba(163, 133, 87, 255)"');

        }
        if($i == 3){
          array_unshift($labelText[$i],'','','');
        }
      }
      else {
        //Priorité
        if ($i == $minIndex) {
          array_push($labelFontColor, '"rgba(134, 143, 185, 1)"');
          array_push($pointBackgroundColor, '"rgba(163, 133, 87, 255)"');
        }
        //Force
        else if ($i == $maxIndex) {
          array_push($labelFontColor, '"rgba(26, 35, 78, 1)"');
          array_push($pointBackgroundColor, '"rgba(94, 68, 37, 255)"');
        }
        //Preoccupation
        else {
          array_push($labelFontColor, '"rgba(68, 79, 132, 1)"');
          array_push($pointBackgroundColor, '"rgba(151, 134, 113, 255)"');
        }

        if($i == 3){
          array_unshift($labelText[$i],'','');
        }

      }
    }
    else {
      array_push($labelFontColor,'"rgba(102, 102, 102, 1)"');
      array_push($pointBackgroundColor,'"rgba(0, 0, 0, 1)"');
    }
  }

  while (count($labelText[0]) < 5){
    array_unshift($labelText[0],'');
  }
  while (count($labelText[3]) < 5){
    array_push($labelText[3],'');
  }






  // $labelFontColor = $param[0];
  // $labelText = $param[1];
  // $pointBackgroundColor = $param[2];

  $chart = new QuickChart(    
    array(
    'width' => 400,
    'height' => 300,
    'backgroundColor' => "white",
    'format' => "png"
    )
  );
      
  $chart->setConfig(
    '{                
      type: "radar",          
      data: {            
        labels:'.json_encode($labelText).',            
        datasets: [              
          {                
            backgroundColor: "rgba(240,241,242,0.5)",                
            borderColor: "rgba(0, 0, 0, 0.15)",                
            pointBackgroundColor: ['.implode(",",$pointBackgroundColor).'],                
            data: ['.$_GET["X1"].','.$_GET["X2"].','.$_GET["X3"].','.$_GET["X4"].','.$_GET["X5"].','.$_GET["X6"].'],
            pointRadius: 1.5            
          }          
          ]          
        },          
        options: {            
          maintainAspectRatio: true,            
          spanGaps: false,            
          title: {              
            display: false,
            position: "top",
            text: "Vos résultats",
            fontSize: 18,
            fontFamily: "Gotham",
            fontColor: "rgba(0, 0, 0, 1)"          
          },
            legend: {
              display: false
            },
            scale: {
              angleLines: {
                display: false
              },
              ticks: {
                suggestedMin: 0,
                suggestedMax: 100,
                display: false,
                stepSize: 20
              },
              pointLabels: {
                display: true,
                fontSize: 7,
                fontColor:['.implode(",",$labelFontColor).'],
                backdropPadding: "20",
                padding: "40",
                fontStyle: "bold"
              },
              gridLines: {
                color: "rgba(234, 234, 255, 1)",
                zeroLineColor: "red"
              }
            },
            elements: {
              line: {
                backgroundColor: "rgba(0, 0, 0, 0.5)",
                borderWidth: 1
              }
            }
          }        
        }'
  );
    

  $binary = $chart->toBinary();

  $image = imagecreatefromstring($binary);
  
  $blueLight = imagecolorallocate($image, 134, 143, 185);
  $blueMedium = imagecolorallocate($image, 68, 79, 132);
  $blueDark = imagecolorallocate($image, 26, 35, 78);
  $white = imagecolorallocate($image, 255, 255, 255);
  
  function imagefillroundedrect($im,$x,$y,$cx,$cy,$rad,$col){
      imagefilledrectangle($im,$x,$y+$rad,$cx,$cy-$rad,$col);
      imagefilledrectangle($im,$x+$rad,$y,$cx-$rad,$cy,$col);
  
      $dia = $rad*2;
  
      imagefilledellipse($im, $x+$rad, $y+$rad, $rad*2, $dia, $col);
      imagefilledellipse($im, $x+$rad, $cy-$rad, $rad*2, $dia, $col);
      imagefilledellipse($im, $cx-$rad, $cy-$rad, $rad*2, $dia, $col);
      imagefilledellipse($im, $cx-$rad, $y+$rad, $rad*2, $dia, $col);
  }
  

  $indicator = array(
    "graindepeau" => array(
      'bubble' => array(400,511,400,536,10),
      'text' => array(11,0,400,531)
    ),
    "tachespigmentaires" => array(
      'bubble' => array(400,20,400,45,10),
      'text' => array(11,0,400,40)
    ),
    "hydratation" => array(
      'bubble' => array(581,130,581,155,10),
      'text' => array(11,0,581,150)
    ),
    "pores" => array(
      'bubble' => array(581,375,581,400,10),
      'text' => array(11,0,581,395)
    ),
    "fermete" => array(
      'bubble' => array(210,375,210,400,10),
      'text' => array(11,0,210,395)
    ),
    "rides" => array(
      'bubble' => array(210,130,210,155,10),
      'text' => array(11,0,210,150)
    )
  );

  // $LevelParameters = [$_GET["X1"],$_GET["X2"],$_GET["X3"],$_GET["X4"],$_GET["X5"],$_GET["X6"]];
  // $labels = ["tachespigmentaires", "hydratation", "pores", "graindepeau", "fermete", "rides"];
  // $priority = [1,0,2,3,4,5];

  // $preIndex = array_search($_GET["X7"], $labels);

  // // $minIndex = array_search(min($LevelParameters), $LevelParameters);
  // $minIndexes = array_keys($LevelParameters, min($LevelParameters));
  // foreach($minIndexes as $index){
  //   $order[$index] = array_search($index, $priority);
  // }
  // $minIndex = array_search(min($order), $order);

  // // $maxIndex = array_search(max($LevelParameters), $LevelParameters);

  // $order = array();
  // $maxIndexes = array_keys($LevelParameters, max($LevelParameters));
  // foreach($maxIndexes as $index){
  //   $order[$index] = array_search($index, $priority);
  // }
  // $maxIndex = array_search(min($order), $order);


  if(isset($languagesStrings['indicator']['font'])){
    $font = './' . $languagesStrings['indicator']['font'];
  } else {
    $font = "./SimHei.ttf";
  }

  if($maxIndex != $minIndex){
    // MAX
    if($maxIndex == $preIndex){
      $strString = 'str&ppt';

      if(isset($languagesStrings['indicator'][$strString . 'Length'])){
        $strStringLength = $languagesStrings['indicator'][$strString . 'Length'];
      } else {
        $strStringLength = max(
          array_map(
            function($string){
              return strlen(remove_accents($string));
            },
            explode("\n",$languagesStrings['indicator'][$strString])
          )
        );
      }
      if($maxIndex == 3) {
        $down = 1;
        $up = 0;
      } else {
        $down = 0;
        $up = 1;
      }

    } else {
      $strString = 'str';
      if(isset($languagesStrings['indicator'][$strString . 'Length'])){
        $strStringLength = $languagesStrings['indicator'][$strString . 'Length'];
      } else {
        $strStringLength = strlen(remove_accents($languagesStrings['indicator'][$strString]));
      }
      $up = 0;
      $down = 0;
    }

    if($maxIndex == 1 || $maxIndex == 2){
      $left = 0;
      $right = ($strStringLength*8);
    } elseif($maxIndex == 0 || $maxIndex == 3){
      $left = ($strStringLength*4);
      $right = ($strStringLength*4);
    } else {
      $left = ($strStringLength*8);
      $right = 0;
    }

    imagefillroundedrect($image,
    $indicator[$labels[$maxIndex]]['bubble'][0] - $left,
    $indicator[$labels[$maxIndex]]['bubble'][1] - $up*15,
    $indicator[$labels[$maxIndex]]['bubble'][2] + $right,
    $indicator[$labels[$maxIndex]]['bubble'][3] + $down*15,
    $indicator[$labels[$maxIndex]]['bubble'][4],
    $blueDark
    );
    
    imagettftext($image,
    $indicator[$labels[$maxIndex]]['text'][0],
    $indicator[$labels[$maxIndex]]['text'][1],
    $indicator[$labels[$maxIndex]]['text'][2] - $left + 5,
    $indicator[$labels[$maxIndex]]['text'][3] - $up*15,
    $white,$font,$languagesStrings['indicator'][$strString]);

    // MIN
    if($minIndex == $preIndex){
      $prtString = 'prt&ppt';
      if(isset($languagesStrings['indicator'][$prtString . 'Length'])){
        $prtStringLength = $languagesStrings['indicator'][$prtString . 'Length'];
      } else {
        $prtStringLength = max(
          array_map(
            function($string){
              return strlen(remove_accents($string));
            },
            explode("\n",$languagesStrings['indicator'][$prtString])
          )
        );
      }
      if($minIndex == 3) {
        $down = 1;
        $up = 0;
      } else {
        $down = 0;
        $up = 1;
      }
    } else {
      $prtString = 'prt';
      if(isset($languagesStrings['indicator'][$prtString . 'Length'])){
        $prtStringLength = $languagesStrings['indicator'][$prtString . 'Length'];
      } else {
        $prtStringLength = strlen(remove_accents($languagesStrings['indicator'][$prtString]));
      }
      $up = 0;
      $down = 0;
    }

    if($minIndex == 1 || $minIndex == 2){
      $left = 0;
      $right = ($prtStringLength*8);
    } elseif($minIndex == 0 || $minIndex == 3){
      $left = ($prtStringLength*4);
      $right = ($prtStringLength*4);
    } else {
      $left = ($prtStringLength*8);
      $right = 0;
    }

    imagefillroundedrect($image,
    $indicator[$labels[$minIndex]]['bubble'][0] - $left,
    $indicator[$labels[$minIndex]]['bubble'][1] - $up*10,
    $indicator[$labels[$minIndex]]['bubble'][2] + $right,
    $indicator[$labels[$minIndex]]['bubble'][3] + $down*15,
    $indicator[$labels[$minIndex]]['bubble'][4],
    $blueLight
    );
    
    imagettftext($image,
    $indicator[$labels[$minIndex]]['text'][0],
    $indicator[$labels[$minIndex]]['text'][1],
    $indicator[$labels[$minIndex]]['text'][2] - $left + 8,
    $indicator[$labels[$minIndex]]['text'][3] - $up*15,
    $white,$font,$languagesStrings['indicator'][$prtString]);

    
  }

  if(($preIndex != $maxIndex && $preIndex != $minIndex) || ($preIndex == $maxIndex && $preIndex == $minIndex && $preIndex == 0)){
    // PRE
    $preString = 'ppt';

    if(isset($languagesStrings['indicator'][$preString . 'Length'])){
      $preStringLength = $languagesStrings['indicator'][$preString . 'Length'];
    } else {
      $preStringLength = strlen(remove_accents($languagesStrings['indicator'][$preString]));
    }

    if($preIndex == 1 || $preIndex == 2){
      $left = 0;
      $right = ($preStringLength*8);
    } elseif($preIndex == 0 || $preIndex == 3){
      $left = ($preStringLength*4);
      $right = ($preStringLength*4);
    } else {
      $left = ($preStringLength*8);
      $right = 0;
    }

    imagefillroundedrect($image,
    $indicator[$labels[$preIndex]]['bubble'][0] - $left,
    $indicator[$labels[$preIndex]]['bubble'][1],
    $indicator[$labels[$preIndex]]['bubble'][2] + $right,
    $indicator[$labels[$preIndex]]['bubble'][3],
    $indicator[$labels[$preIndex]]['bubble'][4],
    $blueMedium
    );
    
    imagettftext($image,
    $indicator[$labels[$preIndex]]['text'][0],
    $indicator[$labels[$preIndex]]['text'][1],
    $indicator[$labels[$preIndex]]['text'][2]- $left + 5,
    $indicator[$labels[$preIndex]]['text'][3],
    $white,$font,$languagesStrings['indicator']["ppt"]);
  }

 

  //$chart->defaults->scale->gridLines->color = "#FFFFFF"; 
  ob_clean();
  header('Content-type: image/png');
  imagepng($image);
  imagedestroy($image);
  ob_end_flush();
}

generateChart();

function remove_accents($string) {
  if ( !preg_match('/[\x80-\xff]/', $string) )
      return $string;

  $chars = array(
  // Decompositions for Latin-1 Supplement
  chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
  chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
  chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
  chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
  chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
  chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
  chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
  chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
  chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
  chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
  chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
  chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
  chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
  chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
  chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
  chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
  chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
  chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
  chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
  chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
  chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
  chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
  chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
  chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
  chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
  chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
  chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
  chr(195).chr(191) => 'y',
  // Decompositions for Latin Extended-A
  chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
  chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
  chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
  chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
  chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
  chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
  chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
  chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
  chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
  chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
  chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
  chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
  chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
  chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
  chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
  chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
  chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
  chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
  chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
  chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
  chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
  chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
  chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
  chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
  chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
  chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
  chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
  chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
  chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
  chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
  chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
  chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
  chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
  chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
  chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
  chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
  chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
  chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
  chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
  chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
  chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
  chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
  chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
  chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
  chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
  chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
  chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
  chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
  chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
  chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
  chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
  chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
  chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
  chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
  chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
  chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
  chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
  chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
  chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
  chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
  chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
  chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
  chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
  chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
  );

  $string = strtr($string, $chars);

  return $string;
}

?>