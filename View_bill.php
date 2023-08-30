<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="View_bill.css">
    <title>View Uploaded Images</title>
    
</head>

<body>
    <header>
        <h1>View Uploaded Images</h1>
        <nav>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="upload_bill.php">Upload Bill</a></li>
                <li><a href="#">Manage Users</a></li>
                <li><a href="View_bill.php">View Bills</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Uploaded Images</h2>

            <!-- Add the attractive search bar -->
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search Bill...">
                <!-- <button id="searchButton">Search</button> -->
            </div>

            <?php
            // Database connection parameters
            $host = "localhost";
            $username = "root";
            $password = "";
            $database = "a1hardware";

            // Create a database connection
            $conn = new mysqli($host, $username, $password, $database);

            // Check for connection errors
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Check for search query
            $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

            // Fetch file paths and bill names from the database
            $sql = "SELECT file_path, bill_name FROM bills";

            // If a search query is provided, add a WHERE clause to filter the results
            if (!empty($searchQuery)) {
                $sql .= " WHERE bill_name LIKE '%$searchQuery%'";
            }

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo '<div class="image-grid">';
                $matchingBills = []; // Store matching bills

                while ($row = $result->fetch_assoc()) {
                    $filePath = $row["file_path"];
                    $billName = $row["bill_name"];
                    $matchingBills[] = '<div class="image-item"><img src="' . $filePath . '" alt="Uploaded Image"><div class="bill-name">' . $billName . '</div></div>';
                }

                // Print matching bills first
                foreach ($matchingBills as $bill) {
                    echo $bill;
                }

                echo '</div>';
            } else {
                echo "<p>No images found.</p>";
            }

            // Close the database connection
            $conn->close();
            ?>

        </section>
    </main>

    <footer>
        <p>&copy; 2023 Your Company Name</p>
    </footer>

    <script>
        // JavaScript code to handle the smooth scrolling animation
        document.getElementById("searchInput").addEventListener("input", function() {
            var searchQuery = this.value.toLowerCase();
            var billNames = document.querySelectorAll(".bill-name");
            var imageGrid = document.querySelector(".image-grid");

            // If the search query is empty, reset to show all bills
            if (searchQuery === "") {
                location.reload(); // Reload the page to show all bills
                return;
            }

            // Loop through bill names and find the matching ones
            var matchingBills = [];
            for (var i = 0; i < billNames.length; i++) {
                var billName = billNames[i].textContent.toLowerCase();

                if (billName.includes(searchQuery)) {
                    matchingBills.push(billNames[i].parentNode.outerHTML);
                }
            }

            // Clear the current content of the image grid
            imageGrid.innerHTML = "";

            // Print matching bills first
            for (var j = 0; j < matchingBills.length; j++) {
                imageGrid.innerHTML += matchingBills[j];
            }
        });
    </script>
</body>

</html>