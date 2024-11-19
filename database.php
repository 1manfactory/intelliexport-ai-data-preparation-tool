<?php

// database.php

function getDatabaseConnection($dbConfig) {
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8mb4";
    return new PDO($dsn, $dbConfig['user'], $dbConfig['password']);
}

function getViewData($pdo, $viewName)
{
    $stmt = $pdo->query("SELECT * FROM $viewName");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
