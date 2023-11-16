<?php
// Execute the Python script
$output = shell_exec("python scrape.py");

if (file_exists("_cms_scrape4.csv")) {
    echo "<script>alert('Scraping successful');</script>";
} else {
    echo "<script>alert('Scraping failed. Please check the Python script and try again.');</script>";
}

echo "<script>window.location.href = 'index.php';</script>";
?>
