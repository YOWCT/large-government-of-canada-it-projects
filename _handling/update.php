<?php

$pathToCsvFile = "static/csv/2019-gc-it-projects.csv";
$pathToOutputFile = "layouts/shortcodes/tabledata.html";
$htmlOutput = "";
$originDate = "May 1, 2019";

if(! file_exists($pathToCsvFile)) {
  exit("Could not find specified CSV file in \$pathToCsvFile.\n");
}

// Thanks to
// https://stackoverflow.com/a/4801943/756641
$array = $fields = array(); $i = 0;
$handle = @fopen($pathToCsvFile, "r");
if ($handle) {
    while (($row = fgetcsv($handle, 4096)) !== false) {
        if (empty($fields)) {
            $fields = $row;
            continue;
        }
        foreach ($row as $k=>$value) {
            $array[$i][$fields[$k]] = $value;
        }
        $i++;
    }
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
}

function parseTotalBudget($input) {
  if(! trim($input)) {
    return 0;
  }
  $input = str_replace(['$', ',',' '], '', $input);
  return intval($input);
}

function displayTotalBudget($input) {
  if(! trim($input)) {
    return '<em class="no-data-provided">(not&nbsp;provided)</em>';
  } 
  else {
    return $input;
  }
}

function parseEstimatedCompletionDate($estimatedCompletionDate) {
  if(trim($estimatedCompletionDate)) {
    return date("Y-m-d", strtotime($estimatedCompletionDate));
  }
  else {
    return "0000-00-00";
  }
}

function displayEstimatedCompletionDate($estimatedCompletionDate, $rawProvidedDate) {
  if(trim($estimatedCompletionDate)) {
    return str_replace(' ', '&nbsp;',$estimatedCompletionDate);
  }
  else {
    return '<em class="no-data-provided">' . $rawProvidedDate . '</em>';
  }
}

function calculateYearsRemaining($estimatedCompletionDate, $originDate) {
  // $difference = strtotime($estimatedCompletionDate) - strtotime($originDate);

  // Thanks to
  // https://stackoverflow.com/a/5387225/756641
  $d1 = new DateTime($estimatedCompletionDate);
  $d2 = new DateTime($originDate);
  
  $diff = $d2->diff($d1);
  
  $years = round($diff->y + $diff->m / 12, 1);

  // if($d1 < $d2) {
  //   $years = -$years;
  // }

  // $years += 1;

  if ($years < 0) {
    return 0;
  }
  else {
    return $years;
  }
  

}

function parseYearsRemaining($estimatedCompletionDate) {
  if(! $estimatedCompletionDate) {
    return 1000;
  }
  else {
    return 0;
  }
}

// var_dump($array);

foreach($array as $item) {
  $htmlOutput .= '
<tr id="' . $item['uniqueId'] . '">
  <td data-search="' . $item['deptAcronym'] . ' ' . strtolower($item['department']) . '">' . $item['department'] . '</td>
  <td>' . nl2br($item['description']) . '</td>
  <td class="pdt-date" data-order="' . parseTotalBudget($item['totalBudget']) . '">' . displayTotalBudget($item['totalBudget']) . '</td>
  <td data-order="' . parseEstimatedCompletionDate($item['estimatedCompletionDate']) . '">' . displayEstimatedCompletionDate($item['estimatedCompletionDate'], $item['rawProvidedDate']) . '</td>
  <td data-order="' . calculateYearsRemaining($item['estimatedCompletionDate'], $originDate) . '">' . calculateYearsRemaining($item['estimatedCompletionDate'], $originDate) . '</td>
</tr>
';
}

file_put_contents($pathToOutputFile, $htmlOutput);

echo "Updated table HTML file - $pathToOutputFile\n";
