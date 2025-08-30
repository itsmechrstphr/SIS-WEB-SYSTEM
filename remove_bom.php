<?php
// Script to detect and remove BOM and non-printable characters from a PHP file

function removeBOM($filename) {
    $contents = file_get_contents($filename);
    // Detect BOM (UTF-8)
    if (substr($contents, 0, 3) === "\xEF\xBB\xBF") {
        echo "BOM detected and removed from $filename\n";
        $contents = substr($contents, 3);
        file_put_contents($filename, $contents);
    } else {
        echo "No BOM detected in $filename\n";
    }
}

function removeNonPrintable($filename) {
    $contents = file_get_contents($filename);
    // Remove non-printable characters except newlines, tabs, carriage returns
    $cleaned = preg_replace('/[^\PC\s]/u', '', $contents);
    if ($cleaned !== $contents) {
        echo "Non-printable characters removed from $filename\n";
        file_put_contents($filename, $cleaned);
    } else {
        echo "No non-printable characters found in $filename\n";
    }
}

$targetFile = 'student_dashboard.php';

removeBOM($targetFile);
removeNonPrintable($targetFile);

echo "Cleaning completed for $targetFile\n";
?>
