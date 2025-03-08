<?php

// main.php

// Check if the script was called directly
if (php_sapi_name() !== 'cli' || !isset($_SERVER['WRAPPER_SCRIPT'])) {
	die("Error: This script must be run through the wrapper.\n");
}

ini_set('display_errors', '1');
error_reporting(E_ALL);

// Use Composer's autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Load custom files
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/debug.php';
require_once __DIR__ . '/pdf.php';
require_once __DIR__ . '/office.php';

function loadConfig($configFile)
{
	if (!file_exists($configFile)) {
		die("Error: Config file '$configFile' not found.\n");
	}
	$config = parse_ini_file($configFile, true);
	if ($config === false) {
		$error = error_get_last();
		die("Error: Unable to parse config file '$configFile'. " . ($error['message'] ?? '') . "\n");
	}
	return $config;
}

function exportToCSV($data, $outputFile)
{
	$file = fopen($outputFile, 'w');

	// Write header
	fputcsv($file, array_keys($data[0]), "," ,'"', "\n");

	// Write data
	foreach ($data as $row) {
		fputcsv($file, $row, "," ,'"', "\n");
	}

	fclose($file);
}


function exportToTXT($data, $outputFile)
{
	$file = fopen($outputFile, 'w');

	// Write data
	foreach ($data as $row) {
		fputs($file, strip_tags(implode(" | ", $row)));
	}

	fclose($file);
}


function main($argv)
{
	$debug = false;
	if (($key = array_search('--debug', $argv)) !== false) {
		$debug = true;
		unset($argv[$key]);
		$argv = array_values($argv);
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		echo "Debug mode enabled\n";
	}
	$GLOBALS['debug'] = $debug;

	if (count($argv) < 3) {
		die("Usage: php main.php [config_file] <database:view1> [<database:view2>] ...\n");
	}

	$configFile = $argv[1];
	$dbViewPairs = array_slice($argv, 2);

	$config = loadConfig($configFile);

	// Setze das Speicherlimit
	if (isset($config['settings']['memory_limit'])) {
		ini_set('memory_limit', $config['settings']['memory_limit']);
	}

	$exportDir = 'export';
	if (!is_dir($exportDir)) {
		mkdir($exportDir, 0777, true);
	}

	foreach ($dbViewPairs as $dbViewPair) {
		list($database, $view) = explode(':', $dbViewPair);

		if (!isset($config["database_$database"])) {
			die("Error: Database configuration for '$database' not found in config file.\n");
		}

		$dbConfig = $config["database_$database"];

		debug("Connecting to database '$database'...");
		$pdo = getDatabaseConnection($dbConfig);

		debug("Fetching data from '$view'...");
		$data = getViewData($pdo, $view);
		#print_r($data);

		/*
		debug("Generating PDF for '$view'...");
        $pdfContent = generatePDF($data, $view, $config['company']['name']);
        $pdfOutputFile = $exportDir . "/{$database}_{$view}_" . date('Y-m-d_Hi') . '.pdf';
        file_put_contents($pdfOutputFile, $pdfContent);
        debug("PDF generated: $pdfOutputFile");
        */

		debug("Generating CSV for '$view'...");
		$csvOutputFile = $exportDir . "/{$database}_{$view}_" . date('Y-m-d_Hi') . '.csv';
		exportToCSV($data, $csvOutputFile);
		debug("CSV exported: $csvOutputFile");

		debug("Generating TXT for '$view'...");
		$csvOutputFile = $exportDir . "/{$database}_{$view}_" . date('Y-m-d_Hi') . '.txt';
		exportToTXT($data, $csvOutputFile);
		debug("TXT exported: $csvOutputFile");		

		// XLSX-Export
		debug("Generating XLSX for '$view'...");
		$xlsxOutputFile = $exportDir . '/data_export_' . date('Y-m-d_Hi') . '.xlsx';
		exportToXLSX($data, $xlsxOutputFile);
		debug("XLSX exported: $xlsxOutputFile");
	}
}

main($argv);
