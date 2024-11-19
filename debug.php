<?php

// debug.php

$GLOBALS['debug'] = false;

function debug($message) {
    if ($GLOBALS['debug']) {
        echo "[DEBUG] " . $message . "\n";
    }
}


function dd(...$vars) {
    $html = false;
    
    // Überprüfen, ob das letzte Argument ein Boolean für HTML-Ausgabe ist
    if (is_bool(end($vars))) {
        $html = array_pop($vars);
    }

    if ($html) {
        echo "<pre style='background-color: #f4f4f4; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: monospace;'>";
    } else {
        echo "\n--- Debug Output ---\n\n";
    }

    foreach ($vars as $var) {
        $type = gettype($var);
        $value = $type === 'array' || $type === 'object' ? print_r($var, true) : var_export($var, true);

        if ($html) {
            echo "<div style='margin-bottom: 10px;'>";
            echo "<strong>Type:</strong> <span style='color: blue;'>$type</span><br>";
            echo "<strong>Value:</strong> <span style='color: green;'>$value</span>";
            echo "</div>";
        } else {
            echo "Type: $type\n";
            echo "Value: $value\n\n";
        }
    }

    if ($html) {
        echo "</pre>";
    } else {
        echo "--- End Debug Output ---\n";
    }

    die();
}
