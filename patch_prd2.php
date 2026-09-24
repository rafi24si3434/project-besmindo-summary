<?php
$file = "C:/xampp/htdocs/Project Besmindo Summary/PRD_SIMOR_BMS.md";
$text = file_get_contents($file);

$text = str_replace("### 6.7 Modul Master Data", "### 6.7 Pusat Master Data Terpadu", $text);

file_put_contents($file, $text);
echo "Patched master data!";
?>
