<?php

$toProcess = [
  'combined' => [
    'inputFile' => "static/csv/gc-it-projects-combined.csv",
    'outputFile' => "layouts/shortcodes/tabledata_combined.html",
    'isCombined' => true,
    'originDate' => "",
  ],
  '2019' => [
    'inputFile' => "static/csv/2019-gc-it-projects.csv",
    'outputFile' => "layouts/shortcodes/tabledata_2019.html",
    'isCombined' => false,
    'originDate' => "May 1, 2019",
  ],
];

// Helper functions

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

function displayEstimatedCompletionDate($estimatedCompletionDate, $rawProvidedDate = "No date provided") {
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

// Main operation

foreach($toProcess as $key => $params) {

  $pathToCsvFile = $params['inputFile'];
  $pathToOutputFile = $params['outputFile'];
  $htmlOutput = "";
  $originDate = $params['originDate'];

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

  foreach($array as $item) {

    if($params['isCombined']) {
      // Process as a combined dataset

      // projectName	projectDescription2016	projectDescription2019	totalBudget2016	estimatedCompletionDate2016	totalBudget2019	estimatedCompletionDate2019	mostRecentBudget	peakBudget	hasComparisonBudgets	hasComparisonDates	budgetDelta	budgetDeltaPercentage	datesDeltaYear	estimatedStatus

      // For now, exclude entries without a name
      if($item['projectName']) {
        $htmlOutput .= '
        <tr id="' . $item['uniqueId'] . '" data-row-json="' . htmlentities(json_encode($item)) . '">
          <td data-search="' . $item['deptAcronym'] . ' ' . strtolower($item['department']) . '">' . $item['department'] . '</td>
          <td>' . $item['projectName'] . '</td>
          <td class="pdt-date" data-order="' . parseTotalBudget($item['totalBudget2016']) . '">' . displayTotalBudget($item['totalBudget2016']) . '</td>
          <td class="pdt-date" data-order="' . parseTotalBudget($item['totalBudget2019']) . '">' . displayTotalBudget($item['totalBudget2019']) . '</td>
          <td class="pdt-date" data-order="' . parseTotalBudget($item['budgetDelta']) . '">' . displayTotalBudget($item['budgetDelta']) . '</td>
          <td class="" data-order="' . floatval($item['budgetDeltaPercentage']) . '">' . $item['budgetDeltaPercentage'] . '</td>
          <td data-order="' . parseEstimatedCompletionDate($item['estimatedCompletionDate2016']) . '">' . displayEstimatedCompletionDate($item['estimatedCompletionDate2016']) . '</td>
          <td data-order="' . parseEstimatedCompletionDate($item['estimatedCompletionDate2019']) . '">' . displayEstimatedCompletionDate($item['estimatedCompletionDate2019']) . '</td>
          <td>' . $item['datesDeltaYear'] . '</td>
          <td>' . $item['estimatedStatus'] . '</td>
        </tr>
        ';
      }
      
    }
    else {
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
    
  }
  
  file_put_contents($pathToOutputFile, $htmlOutput);
  
  echo "Updated table HTML file - $pathToOutputFile\n";

}
