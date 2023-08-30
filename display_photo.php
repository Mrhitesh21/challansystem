<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Downloaded Photo</title>
</head>
<body>
    <h1>Downloaded Photo</h1>
    <img id="downloadedPhoto" src="" alt="Downloaded Photo">
    <script>
        // Get the URL of the downloaded image from the query parameter
        var urlParams = new URLSearchParams(window.location.search);
        var imageUrl = urlParams.get('image');

        // Set the source of the <img> element to display the downloaded image
        var imgElement = document.getElementById('downloadedPhoto');
        imgElement.src = imageUrl;
    </script>
</body>
</html>
