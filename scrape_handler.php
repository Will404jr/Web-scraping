<?php
// Execute the Python script
$output = shell_exec("python scrape.py");

// Check if the CSV file was created as an indicator of success
if (file_exists("_cms_scrape4.csv")) {
    echo "<script>alert('Scraping successful');</script>";
} else {
    echo "<script>alert('Scraping failed. Please check the Python script and try again.');</script>";
}

// Redirect back to the index.php page after displaying the alert
echo "<script>window.location.href = 'index.php';</script>";
?>
