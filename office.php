<?php

// office.php

function exportToXLSX($data, $outputFile) {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Write headers
    if (!empty($data)) {
        $headers = array_keys($data[0]);
        $sheet->fromArray($headers, NULL, 'A1');

        // Write data
        $sheet->fromArray($data, NULL, 'A2');
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save($outputFile);
}

function generateXLSX($data, $viewName) {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle($viewName);

    // Write headers and data
    if (!empty($data)) {
        $headers = array_keys($data[0]);
        $sheet->fromArray($headers, NULL, 'A1');
        $sheet->fromArray($data, NULL, 'A2');

        // Optional: Formatierung der Kopfzeile
        $headerRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers)) . '1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
    }

    return $spreadsheet;
}