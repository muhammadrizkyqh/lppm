<?php

// Simple check script without Laravel bootstrap complexity
try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=lppm_stai_ali', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== DATABASE STATUS CHECK ===\n";

    // Check students count
    $stmt = $pdo->query("SELECT COUNT(*) FROM students");
    $studentsCount = $stmt->fetchColumn();
    echo "1. Total students: {$studentsCount}\n";

    // Check imports count
    $stmt = $pdo->query("SELECT COUNT(*) FROM imports");
    $importsCount = $stmt->fetchColumn();
    echo "2. Total imports: {$importsCount}\n";

    // Check last import
    if ($importsCount > 0) {
        $stmt = $pdo->query("SELECT * FROM imports ORDER BY created_at DESC LIMIT 1");
        $lastImport = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "3. Last import:\n";
        echo "   - File: {$lastImport['file_name']}\n";
        echo "   - Total rows: {$lastImport['total_rows']}\n";
        echo "   - Successful rows: {$lastImport['successful_rows']}\n";
        echo "   - Status: " . ($lastImport['completed_at'] ? 'Completed' : 'Processing') . "\n";
    }

    // Check failed imports
    $stmt = $pdo->query("SELECT COUNT(*) FROM failed_import_rows");
    $failedCount = $stmt->fetchColumn();
    echo "4. Failed import rows: {$failedCount}\n";

    if ($failedCount > 0) {
        $stmt = $pdo->query("SELECT validation_error, data FROM failed_import_rows LIMIT 3");
        $failedRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "5. Failed import details:\n";
        foreach ($failedRows as $row) {
            echo "   - Error: {$row['validation_error']}\n";
            echo "   - Data: " . substr($row['data'], 0, 100) . "...\n";
        }
    }

    // Check sample students
    if ($studentsCount > 0) {
        $stmt = $pdo->query("SELECT name, nim, email FROM students ORDER BY created_at DESC LIMIT 5");
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "6. Latest students:\n";
        foreach ($students as $student) {
            echo "   - {$student['name']} ({$student['nim']}) - {$student['email']}\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
