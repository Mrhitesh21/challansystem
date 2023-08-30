<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "a1hardware";

    $conn = new mysqli($host, $username, $password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get data from the POST request
    $billName = $_POST['billName'];
    $mergedDataUrl = $_POST['mergedDataUrl'];

    // Prepare and execute the SQL query to insert data
    $sql = "INSERT INTO completed_challans (bill_name, photo) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $billName, $mergedDataUrl);

    if ($stmt->execute()) {
        echo "Data saved to the database successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
