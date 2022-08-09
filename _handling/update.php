<?php

$toProcess = [
  'combined' => [
    'inputFile' => "static/csv/gc-it-projects-combined.csv",
    'outputFile' => "layouts/shortcodes/tabledata_combined.html",
    'outputJson' => "static/js/generated/tabledata_combined.js",
    'isCombined' => true,
    'originDate' => "",
  ],
  '2022' => [
    'inputFile' => "static/csv/gc-it-projects-2022.csv",
    'outputFile' => "layouts/shortcodes/tabledata_2022.html",
    'outputJson' => "static/js/generated/tabledata_2022.js",
    'isCombined' => false,
    'originDate' => "April 25, 2022",
  ],
  '2019' => [
    'inputFile' => "static/csv/gc-it-projects-2019.csv",
    'outputFile' => "layouts/shortcodes/tabledata_2019.html",
    'outputJson' => "static/js/generated/tabledata_2019.js",
    'isCombined' => false,
    'originDate' => "May 1, 2019",
  ],
  '2016' => [
    'inputFile' => "static/csv/gc-it-projects-2016.csv",
    'outputFile' => "layouts/shortcodes/tabledata_2016.html",
    'outputJson' => "static/js/generated/tabledata_2016.js",
    'isCombined' => false,
    'originDate' => "June 9, 2016",
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

function displayTotalBudget($input, $isCombined = false) {
  if(! trim($input)) {
    if($isCombined) {
      return '';
    }
    else {
      return '<em class="no-data-provided">(not&nbsp;provided)</em>';
    }
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

function displayEstimatedCompletionDate($estimatedCompletionDate, $rawProvidedDate = "No date provided", $isCombined = false) {
  if(trim($estimatedCompletionDate)) {
    return str_replace(' ', '&nbsp;',$estimatedCompletionDate);
  }
  elseif($isCombined == false) {
    return '<em class="no-data-provided">' . $rawProvidedDate . '</em>';
  }
  else {
    // For combined data, don't list "not provided" each time
    return '';
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

function cleanupDescriptions($description) {
  $description = str_replace(["\n", "\t", "\r"], " ", $description);
  return htmlentities($description);
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

      // deptAcronym	shortcode	uniqueId	department	latestProjectName	latestDescription	originalBudget	latestBudget	originalBudgetSource	latestBudgetSource	originalEstimatedCompletionDate	latestEstimatedCompletionDate	originalEstimatedCompletionDateSource	latestEstimatedCompletionDateSource	budgetDelta	budgetDeltaPercentage	datesDelta	numOfEntries	estimatedStatus	isOver10M	isOver100M

      // For now, exclude entries without a name
      if($item['latestProjectName']) {
        $htmlOutput .= '
        <tr id="' . $item['uniqueId'] . '">
          <td data-search="' . $item['deptAcronym'] . ' ' . strtolower($item['department']) . '">' . $item['department'] . '</td>
          <td data-search="' . $item['uniqueId'] . ' ' . htmlentities($item['latestProjectName']) . ' ' . cleanupDescriptions($item['latestDescription']) . '"><a href="#uid=' . $item['uniqueId'] . '">' . $item['latestProjectName'] . '</a></td>
          <td class="pdt-date" data-order="' . parseTotalBudget($item['originalBudget']) . '">' . displayTotalBudget($item['originalBudget'], 1) . '</td>
          <td class="pdt-date" data-order="' . parseTotalBudget($item['latestBudget']) . '">' . displayTotalBudget($item['latestBudget'], 1) . '</td>
          <td class="pdt-date" data-order="' . parseTotalBudget($item['budgetDelta']) . '">' . displayTotalBudget($item['budgetDelta'], 1) . '</td>
          <td class="" data-order="' . floatval($item['budgetDeltaPercentage']) . '">' . $item['budgetDeltaPercentage'] . '</td>
          <td data-order="' . parseEstimatedCompletionDate($item['originalEstimatedCompletionDate']) . '">' . displayEstimatedCompletionDate($item['originalEstimatedCompletionDate'], "No date provided", 1) . '</td>
          <td data-order="' . parseEstimatedCompletionDate($item['latestEstimatedCompletionDate']) . '">' . displayEstimatedCompletionDate($item['latestEstimatedCompletionDate'], "No date provided", 1) . '</td>
          <td>' . $item['datesDelta'] . '</td>
          <td>' . $item['estimatedStatus'] . '</td>
        </tr>
        ';
      }
      
    }
    else {
      // Individual year entries

      // deptAcronym	shortcode	uniqueId	department	projectName	description	totalBudget	estimatedCompletionDate	rawProvidedDate	yearsRemaining	originalDocumentOrder	source	asOfDate	isOver10M	isOver100M

      // Note that if the Hugo page structure changed, this would need to be updated:
      $pageUrl = '/' . $key . '/';

      $htmlOutput .= '
      <tr id="' . $item['uniqueId'] . '">
        <td data-search="' . $item['deptAcronym'] . ' ' . strtolower($item['department']) . '">' . $item['department'] . '</td>
        <td data-search="' . $item['uniqueId'] . ' ' . htmlentities($item['projectName']) . ' ' . cleanupDescriptions($item['description']) . '"><a href="' . $pageUrl . '#uid=' . $item['uniqueId'] . '">' . $item['projectName'] . '</a></td>
        <td class="pdt-date" data-order="' . parseTotalBudget($item['totalBudget']) . '">' . displayTotalBudget($item['totalBudget']) . '</td>
        <td data-order="' . parseEstimatedCompletionDate($item['estimatedCompletionDate']) . '">' . displayEstimatedCompletionDate($item['estimatedCompletionDate'], $item['rawProvidedDate']) . '</td>
        <td>' . $item['yearsRemaining'] . '</td>
      </tr>
      ';
    }
    
  }
  
  file_put_contents($pathToOutputFile, $htmlOutput);

  echo "Updated table HTML file - $pathToOutputFile\n";

  

  if(isset($params['outputJson'])) {

    $jsonFileHeader = "// JSON data
// This file is generated automatically

var app = app || {};
app.data = app.data || {};


app.data = ";

    $indexedArray = [];
    foreach($array as $item) {
      $indexedArray[$item['uniqueId']] = $item;
    }


    file_put_contents($params['outputJson'], $jsonFileHeader . json_encode($indexedArray, JSON_PRETTY_PRINT) . ";\n");
    echo "Updated table JSON file - " . $params['outputJson'] . "\n";
  }

}
