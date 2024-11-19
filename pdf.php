<?php

// pdf.php

function calculateColumnWidths($data, $headers, $pdf) {
    $columnWidths = [];

    // Berücksichtigen Sie die Breite der Kopfzeilen
    foreach ($headers as $header) {
        $headerWidth = $pdf->GetStringWidth($header);
        $columnWidths[$header] = $headerWidth + 10;
    }

    // Bestimmen Sie die maximale Breite jeder Spalte basierend auf den Daten
    foreach ($data as $row) {
        foreach ($headers as $header) {
            // Berechnen Sie die Breite des Zellinhalts
            $cellContent = mb_convert_encoding($row[$header] ?? '', 'UTF-8', 'ISO-8859-1');
            $dataWidth = $pdf->GetStringWidth($cellContent);
            #if ($header=="bookingDate") print $dataWidth.PHP_EOL;
            if ($dataWidth > $columnWidths[$header]) {
                $columnWidths[$header] = $dataWidth + 10;
            }
        }
    }

    // Berechnen Sie die Gesamtbreite und passen Sie sie an
    $totalWidth = array_sum($columnWidths);

    if ($totalWidth > (841 - 20)) { // Berücksichtigt Margen
        // Normalisieren Sie die Breiten, um in den verfügbaren Platz zu passen
        foreach ($columnWidths as &$width) {
            $width = ($width / $totalWidth) * (841 - 20); // Anpassen an den verfügbaren Platz
        }
    }

    return $columnWidths;
}

function generatePDF($data, $viewName) {
    global $config;
    $companyName = $config['company']['name'] ?? 'Default Company Name';
    
    // Setzen Sie das Seitenformat auf DIN A1 (594 x 841 mm)
    $pdf = new TCPDF('L', 'mm', array(841, 594), true, 'UTF-8', false);
    
    $pdf->SetCreator("IntelliExport");
    $pdf->SetAuthor(mb_convert_encoding($companyName, 'UTF-8', 'ISO-8859-1'));
    $pdf->SetTitle(mb_convert_encoding("$viewName Report", 'UTF-8', 'ISO-8859-1'));
    $pdf->SetSubject(mb_convert_encoding("Data from $viewName", 'UTF-8', 'ISO-8859-1'));
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->SetFillColor(200, 220, 255); // Setzen Sie die Füllfarbe (z.B. hellblau)

    // Definieren Sie die Header für die Spalten
    if (empty($data)) {
        return; // Keine Daten vorhanden
    }
    
    // Die Header sind die Schlüssel des ersten Datensatzes
    $headers = array_keys($data[0]);

    // Berechnen Sie die Spaltenbreiten
    $columnWidths = calculateColumnWidths($data, $headers, $pdf);

    // Fügen Sie eine Seite hinzu und zeichnen Sie den Header
    $pdf->AddPage();
    
    // Draw primary header
    foreach ($headers as $header) {
        $pdf->Cell($columnWidths[$header], 10, mb_convert_encoding($header, 'UTF-8', 'ISO-8859-1'), 1, 0, 'C', true);
    }
    $pdf->Ln();

    // Draw data
    foreach ($data as $row) {
        foreach ($headers as $header) {
            $cellContent = mb_convert_encoding($row[$header] ?? '', 'UTF-8', 'ISO-8859-1');
            // Überprüfen Sie den Inhalt auf Zahlen und zulässige Zeichen
            if (preg_match('/^[0-9.,\/\-]+$/', trim($cellContent))) {
                $pdf->Cell($columnWidths[$header], 10, $cellContent, 1, 0, 'L');
            } else {
                // Kürzen Sie den Text und fügen Sie "..." hinzu, wenn nötig
                if ($pdf->GetStringWidth($cellContent) > ($columnWidths[$header] - 5)) { // -5 für Puffer
                    while (mb_strlen($cellContent) > 0 && 
                        $pdf->GetStringWidth($cellContent . '...') > ($columnWidths[$header] - 5)) {
                        $cellContent = mb_substr($cellContent, 0, -1); // Kürzen
                    }
                    if (mb_strlen($cellContent) > 0) {
                        $cellContent .= '...'; // Fügen Sie "..." hinzu
                    }
                }
                // Zeichnen Sie die Zelle mit dem angepassten Inhalt
                $pdf->Cell($columnWidths[$header], 10, $cellContent, 1, 0, 'L');
            }
        }
        // Neue Zeile nach jeder Datenreihe
        $pdf->Ln();
        
        // Überprüfen Sie den Platz und fügen Sie bei Bedarf eine neue Seite hinzu, inklusive Header
        if ($pdf->GetY() > 570) { // Platz für den Footer lassen
            $pdf->AddPage();
            foreach ($headers as &$header2) {
                $pdf->Cell($columnWidths[$header2], 10, mb_convert_encoding($header2, 'UTF-8', 'ISO-8859-1'), 1, 0, 'C', true);
            }
            $pdf->Ln();
        }
    }

    return $pdf->Output('', 'S');
}