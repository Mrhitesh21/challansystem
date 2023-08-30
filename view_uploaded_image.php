<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bill with Signature</title>
    <style>
        /* Reset default styles */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        /* Center the content both vertically and horizontally */
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* Style for the card */
        .card {
            border-radius: 10px;
            margin-top: 25px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }

        /* Style for the image */
        img {
            max-width: 100%;
            max-height: 100%;
        }

        /* Style for the heading */
        h1 {
            font-size: 24px;
            margin: 26px 0;
        }

        /* Style for the submit button */
        .submit-button {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 24px;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.24.0/axios.min.js"></script>
</head>

<body>
    <div class="container">
        <?php
        if (isset($_GET['image']) && isset($_GET['bill_name'])) {
            $imagePath = $_GET['image'];
            $billName = $_GET['bill_name'];

            echo '<div class="card">';
            echo "<img src='$imagePath' alt='Uploaded Image'>";
            echo "<h1>Bill Name : $billName</h1>";
            echo '</div>';
        } else {
            echo "Image or bill name not found.";
        }
        ?>

        <!-- Signature Field -->
        <div>
            <label for="signature">Signature:</label>
            <canvas id="signatureCanvas" width="300" height="150" style="border: 1px solid #000;"></canvas>
        </div>
        <button onclick="clearSignature()">Clear Signature</button>
        <input type="hidden" name="signatureData" id="signatureData">

        <!-- Submit Button -->
        <button class="submit-button" onclick="mergeAndUploadToTelegram('<?php echo $billName; ?>')">Upload to Telegram</button>
    </div>

    <script>
        var canvas = document.getElementById("signatureCanvas");
        var ctx = canvas.getContext("2d");
        var drawing = false;

        canvas.addEventListener("mousedown", function(e) {
            drawing = true;
            ctx.beginPath();
            var x = e.clientX || e.touches[0].clientX;
            var y = e.clientY || e.touches[0].clientY;
            ctx.moveTo(x - canvas.getBoundingClientRect().left, y - canvas.getBoundingClientRect().top);
        });

        canvas.addEventListener("mousemove", function(e) {
            if (!drawing) return;
            var x = e.clientX || e.touches[0].clientX;
            var y = e.clientY || e.touches[0].clientY;
            ctx.lineTo(x - canvas.getBoundingClientRect().left, y - canvas.getBoundingClientRect().top);
            ctx.stroke();
        });

        canvas.addEventListener("mouseup", function() {
            drawing = false;
            updateSignatureData();
        });

        canvas.addEventListener("mouseout", function() {
            drawing = false;
            updateSignatureData();
        });

        canvas.addEventListener("touchstart", function(e) {
            drawing = true;
            ctx.beginPath();
            var x = e.touches[0].clientX;
            var y = e.touches[0].clientY;
            ctx.moveTo(x - canvas.getBoundingClientRect().left, y - canvas.getBoundingClientRect().top);
        });

        canvas.addEventListener("touchmove", function(e) {
            if (!drawing) return;
            var x = e.touches[0].clientX;
            var y = e.touches[0].clientY;
            ctx.lineTo(x - canvas.getBoundingClientRect().left, y - canvas.getBoundingClientRect().top);
            ctx.stroke();
        });

        canvas.addEventListener("touchend", function() {
            drawing = false;
            updateSignatureData();
        });

        function updateSignatureData() {
            var signatureDataURL = canvas.toDataURL();
            document.getElementById("signatureData").value = signatureDataURL;
        }

        function clearSignature() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            document.getElementById("signatureData").value = "";
        }

        function mergeAndUploadToTelegram(billName) {
            var imageUrl = '<?php echo $_GET['image']; ?>';
            var signatureDataUrl = document.getElementById("signatureData").value;

            // Create an image element for the photo
            var photoImage = new Image();
            photoImage.src = imageUrl;

            // Create an image element for the signature
            var signatureImage = new Image();
            signatureImage.src = signatureDataUrl;

            // Make sure both images are loaded before proceeding
            Promise.all([loadImage(photoImage), loadImage(signatureImage)]).then(() => {
                // Create a canvas to merge the photo and signature
                var mergedCanvas = document.createElement("canvas");
                mergedCanvas.width = photoImage.width;
                mergedCanvas.height = photoImage.height;
                var ctx = mergedCanvas.getContext("2d");

                // Draw the photo onto the canvas
                ctx.drawImage(photoImage, 0, 0);

                // Calculate the size and position for the signature
                var signatureWidth = photoImage.width / 3; // Adjust the size as needed
                var signatureHeight = (signatureImage.height / signatureImage.width) * signatureWidth;
                var signatureX = (photoImage.width - signatureWidth) / 2;
                var signatureY = photoImage.height - signatureHeight;

                // Draw the signature onto the canvas
                ctx.drawImage(signatureImage, signatureX, signatureY, signatureWidth, signatureHeight);

                // Convert the canvas to a data URL
                var mergedDataUrl = mergedCanvas.toDataURL();

                // Upload the merged image to Telegram using the Telegram Bot API
                var telegramBotToken = '6526140808:AAEil2OswcJC466xqeRrY79CmZoUjeRSDRA'; // Replace with your bot token
                var chatId = '5905486194'; // Replace with your chat ID

                // Create a FormData object to send the image as a file
                var formData = new FormData();
                formData.append('photo', dataURItoBlob(mergedDataUrl), 'merged_photo.jpg');

                // Send the image to Telegram
                axios.post(`https://api.telegram.org/bot${telegramBotToken}/sendPhoto?chat_id=${chatId}`, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                        params: {
                            caption: `Bill Name: ${billName}`, // Include the billName as a caption
                        },
                    })
                    .then(function(response) {
                        console.log('Photo uploaded to Telegram:', response.data);
                        alert('Photo uploaded to Telegram!');

                        window.close();
                    })
                    .catch(function(error) {
                        console.error('Error uploading photo to Telegram:', error);
                        alert('Error uploading photo to Telegram.');
                    });
            });
        }

        // Function to load an image and return a promise when it's loaded
        function loadImage(image) {
            return new Promise((resolve, reject) => {
                image.onload = resolve;
                image.onerror = reject;
            });
        }

        // Function to convert a Data URL to a Blob
        function dataURItoBlob(dataURI) {
            var byteString = atob(dataURI.split(',')[1]);
            var ab = new ArrayBuffer(byteString.length);
            var ia = new Uint8Array(ab);
            for (var i = 0; i < byteString.length; i++) {
                ia[i] = byteString.charCodeAt(i);
            }
            return new Blob([ab], {
                type: 'image/jpeg'
            });
        }
    </script>
</body>

</html>