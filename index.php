<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>

</head>
<body>
    <div class="container">
        <h1>Job Listings on timesjobs.com</h1>
        <form method="post" action="scrape_handler.php">
            <button type="submit" class="btn btn-primary" name="scrapeButton">Scrape</button>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Company Name</th>
                    <th>Skills Required</th>
                    <th>More Information</th>
                </tr>
            </thead>
            <tbody>
                <?php
            
                include('connect.php');

                $sql = "SELECT * FROM jobs";
                
                $result = mysqli_query($con, $sql);

                 // Initialize an array to store skills and their counts
                 $skillCounts = [];

                // Check if there are any records
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>{$row['companyName']}</td>";
                        echo "<td>{$row['skillsRequired']}</td>";
                        echo "<td><a href='{$row['moreInformation']}' target='_blank'>More Info</a></td>";
                        echo "</tr>";
                    // Split skills by comma and trim whitespace
                    $skills = array_map('trim', explode(',', $row['skillsRequired']));

                    // Count the occurrence of each skill
                    foreach ($skills as $skill) {
                        if (!empty($skill)) {
                            if (isset($skillCounts[$skill])) {
                                $skillCounts[$skill]++;
                            } else {
                                $skillCounts[$skill] = 1;
                            }
                        }
                    }
                }
                } else {
                    echo "<tr><td colspan='3'>No records found</td></tr>";
                }

                // Close the database connection
                mysqli_close($con);
                ?>
            </tbody>
        </table>

        <h2>Commonly Required Skills</h2>
<ul>
    <?php
    // Sort skills by count in descending order
    arsort($skillCounts);

    // Create arrays to store skill names and their counts for the chart
    $chartSkillNames = [];
    $chartSkillCounts = [];

    // Display the top 5 most commonly required skills
    $i = 0;
    foreach ($skillCounts as $skill => $count) {
        if ($i >= 5) {
            break;
        }
        echo "<li>$skill (Required by $count companies)</li>";

        // Collect skill data for the chart
        $chartSkillNames[] = $skill;
        $chartSkillCounts[] = $count;

        $i++;
    }
    ?>
</ul>

<h2>Least Required Skills</h2>
<ul>
    <?php
    // Display the bottom 5 least required skills
    $i = 0;
    foreach ($skillCounts as $skill => $count) {
        if ($i >= count($skillCounts) - 5) {
            echo "<li>$skill (Required by $count companies)</li>";
        }
        $i++;
    }
    ?>
</ul>

    </div>

    <canvas id="skillsChart" width="300" height="100"></canvas>


    <!-- Include Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Define an array of different colors
        var skillColors = [
            'rgba(75, 192, 192, 0.6)',
            'rgba(255, 99, 132, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(153, 102, 255, 0.6)',
            // Add more colors as needed
        ];

        var ctx = document.getElementById('skillsChart').getContext('2d');
        var skillsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($chartSkillNames); ?>,
                datasets: [{
                    label: 'Number of Companies',
                    data: <?php echo json_encode($chartSkillCounts); ?>,
                    backgroundColor: skillColors, // Use the array of colors
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    </script>



</body>
</html>

<?php
// Include your database connection code
include('connect.php');

// CSV file path
$csv_file = '_cms_scrape4.csv';

// Open and read the CSV file
if (($handle = fopen($csv_file, 'r')) !== false) {
    // Read and skip the header row (if the CSV file has headers)
    fgetcsv($handle);

    while (($data = fgetcsv($handle, 1000, ',')) !== false) {
        $companyName = mysqli_real_escape_string($con, $data[0]);
        $skillsRequired = mysqli_real_escape_string($con, $data[1]);
        $moreInformation = mysqli_real_escape_string($con, $data[2]);

        // Create SQL query to insert data into the database
        $sql = "INSERT INTO jobs (companyName, skillsRequired, moreInformation) 
        VALUES ('$companyName', '$skillsRequired', '$moreInformation')";

        if (mysqli_query($con, $sql)) {
            echo "Good";
        } else {
            echo "Error inserting record: " . mysqli_error($con) . "<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error opening CSV file.";
}

// Close the database connection
mysqli_close($con);

// Use JavaScript to refresh the page after the code execution
echo '<script>
    if (!window.location.search.includes("refreshed=true")) {
        window.location.href = "index.php?refreshed=true";
    }
</script>';
?>




