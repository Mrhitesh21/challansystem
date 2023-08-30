<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bill</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Upload Bill</h1>
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
            <h2>Bill Upload Form</h2>
            <form action="upload_bill.php" method="POST" enctype="multipart/form-data">
                <label for="bill_name">Bill Name:</label>
                <input type="text" name="bill_name" id="bill_name" required>
                <br>

                <!-- Remove the date input field -->
                
                <label for="bill">Select Bill Photo:</label>
                <input type="file" name="bill" id="bill" accept="image/*" required>
                <br>

                <input type="submit" class="button" value="Upload Bill">
            </form>
        </section>
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $uploadDir = "uploads/";

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileInfo = $_FILES["bill"];

                if ($fileInfo["error"] == UPLOAD_ERR_OK) {
                    $fileName = $uploadDir . uniqid() . "_" . $fileInfo["name"];

                    if (move_uploaded_file($fileInfo["tmp_name"], $fileName)) {
                        $billName = $_POST["bill_name"];
                        
                        // Automatically detect the current date and time
                        $billDate = date("Y-m-d H:i:s");

                        $host = $_SERVER['HTTP_HOST'];

                        // Construct the absolute URL for the image link
                        $imageLink = "http://$host/a1hardware/view_uploaded_image.php?image=" . urlencode($fileName) . "&bill_name=" . urlencode($billName);

                        $host = "localhost";
                        $username = "root";
                        $password = "";
                        $database = "a1hardware";

                        $conn = new mysqli($host, $username, $password, $database);

                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        $sql = "INSERT INTO bills (bill_name, bill_date, file_path) VALUES (?, ?, ?)";

                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("sss", $billName, $billDate, $fileName);

                        if ($stmt->execute()) {
                            // Display a success message
                            echo "<p>File uploaded and data inserted into the database successfully.</p>";

                            // Create a link to view the uploaded image on a separate page
                            echo "<p>View Image: <a href='$imageLink' target='_blank'>Click Here</a></p>";

                            // Add a "Copy Link" button
                            echo "<button id='copyLinkButton' onclick='copyLink()'>Copy Link</button>";
                        } else {
                            echo "<p>Error inserting data into the database: " . $stmt->error . "</p>";
                        }

                        $stmt->close();
                        $conn->close();
                    } else {
                        echo "<p>Error uploading file.</p>";
                    }
                } else {
                    echo "<p>Error: " . $fileInfo["error"] . "</p>";
                }
            }
        ?>
        <script>
            function copyLink() {
                // Get the link to the view page
                var link = "<?php echo $imageLink; ?>";

                // Create a temporary input element to copy the link
                var tempInput = document.createElement("input");
                tempInput.value = link;
                document.body.appendChild(tempInput);

                // Select the link text
                tempInput.select();
                tempInput.setSelectionRange(0, 99999); // For mobile devices

                // Copy the link to the clipboard
                document.execCommand("copy");
                document.body.removeChild(tempInput);

                // Provide a user feedback (you can customize this)
                alert("Link copied to clipboard: " + link);
            }
        </script>
    </main>
    
</body>
</html>
