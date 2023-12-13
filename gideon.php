<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel Data Analysis</title>

    <style>
    body {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0;
        background-color: #f0f0f0;
        flex-direction: column; /* Updated to column layout */
    }

    h1 {
        text-align: center;
        color: #333;
    }

    .result-container {
        margin: 20px;
    }

    table {
        width: 80%;
        border-collapse: collapse;
        margin: 20px auto;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    th, td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: center;
    }

    th {
        background-color: #4CAF50;
        color: white;
    }
</style>

</head>
<body>

<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// File paths
$rainfallDataFile = getcwd() . "/rainfall.xls";
$evaporationDataFile = getcwd() . "/evaporation.xls";

// Analyze rainfall data
$rainfallResults = analyzeExcelData($rainfallDataFile, "Rainfall");

// Analyze evaporation data
$evaporationResults = analyzeExcelData($evaporationDataFile, "Evaporation");

// Display results for rainfall
displayResults("Rainfall", $rainfallResults);

// Display results for evaporation
displayResults("Evaporation", $evaporationResults);

// Function to analyze Excel data
function analyzeExcelData($filePath, $dataType) {
    // Load the Excel file
    $spreadsheet = IOFactory::load($filePath);

    // Assuming data is in the first sheet (index 0)
    $sheet = $spreadsheet->getSheet(0);

    // Get the highest column letter (assuming the header is in the first row)
    $highestColumn = $sheet->getHighestColumn();

    // Convert the column letter to the corresponding index
    $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

    // Initialize an associative array to store results for each column
    $results = [];

    // Loop through each column
    for ($col = 1; $col <= $highestColumnIndex; $col++) {
        // Get all data from the column
        $columnData = [];
        foreach ($sheet->getRowIterator() as $row) {
            $cellValue = $sheet->getCellByColumnAndRow($col, $row->getRowIndex())->getValue();
            $columnData[] = $cellValue;
        }

        // Calculate mean, median, mode, and range for the column
        $mean = array_sum($columnData) / count($columnData);
        $median = calculateMedian($columnData);
        $mode = calculateMode($columnData);
        $range = calculateRange($columnData);

        // Store results in the associative array
        $results[$col] = [
            'Mean' => $mean,
            'Median' => $median,
            'Mode' => $mode,
            'Range' => $range,
        ];
    }

    // Return results as an associative array
    return $results;
}

// Function to display analysis results in a table
function displayResults($dataType, $results) {
    echo "<h1>$dataType Data Analysis Results</h1>";
    echo "<table border='1'>";
    echo "<tr><th>Column</th><th>Mean</th><th>Median</th><th>Mode</th><th>Range</th></tr>";

    foreach ($results as $col => $data) {
        echo "<tr>";
        echo "<td>Column $col</td>";
        echo "<td>{$data['Mean']}</td>";
        echo "<td>{$data['Median']}</td>";
        echo "<td>{$data['Mode']}</td>";
        echo "<td>{$data['Range']}</td>";
        echo "</tr>";
    }

    echo "</table>";
}

// Function to calculate median
function calculateMedian($data) {
    sort($data);
    $count = count($data);
    $mid = floor(($count - 1) / 2);

    if ($count % 2 == 0) {
        // Even number of elements
        $median = ($data[$mid] + $data[$mid + 1]) / 2;
    } else {
        // Odd number of elements
        $median = $data[$mid];
    }

    return $median;
}

// Function to calculate mode
function calculateMode($data) {
    // Filter out non-string and non-integer values
    $filteredData = array_filter($data, function ($value) {
        return is_string($value) || is_int($value);
    });

    // Count occurrences of each value
    $counts = array_count_values($filteredData);

    // Find the mode
    $maxCount = max($counts);
    $mode = array_search($maxCount, $counts);

    return $mode;
}

// Function to calculate range
function calculateRange($data) {
    // Convert the values to numeric
    $numericData = array_map('floatval', $data);

    // Calculate the range
    $range = max($numericData) - min($numericData);
    return $range;
}

?>
<div class="result-container">
    <?php
    // Display results for rainfall
    displayResults("Rainfall", $rainfallResults);
    ?>
</div>

<div class="result-container">
    <?php
    // Display results for evaporation
    displayResults("Evaporation", $evaporationResults);
    ?>
</div>
</body>
</html>
