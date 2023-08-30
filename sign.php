<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signature Capture</title>
</head>

<body>
    <canvas id="signatureCanvas" width="400" height="200" style="border: 1px solid #000;"></canvas>
    <button id="clearButton">Clear</button>
    <input type="hidden" id="signatureData" name="signatureData">

    <script>
        var canvas = document.getElementById("signatureCanvas");
        var ctx = canvas.getContext("2d");
        var isDrawing = false;
        var points = []; // Array to store points for smoothing

        canvas.addEventListener("mousedown", () => {
            isDrawing = true;
            // Capture initial coordinates
            points = [];
            addPoint(event.clientX - canvas.getBoundingClientRect().left, event.clientY - canvas.getBoundingClientRect().top);
        });

        canvas.addEventListener("mouseup", () => isDrawing = false);
        canvas.addEventListener("mousemove", draw);

        canvas.addEventListener("touchstart", (event) => {
            isDrawing = true;
            // Capture initial coordinates for touch
            points = [];
            addPoint(event.touches[0].clientX - canvas.getBoundingClientRect().left, event.touches[0].clientY - canvas.getBoundingClientRect().top);
        });

        canvas.addEventListener("touchend", () => isDrawing = false);
        canvas.addEventListener("touchmove", draw);

        function addPoint(x, y) {
            points.push({
                x,
                y
            });
        }

        function draw(event) {
            if (!isDrawing) return;

            ctx.lineWidth = 3; // Adjust the line width as needed
            ctx.strokeStyle = "#000";
            ctx.lineCap = "round";

            if (event.touches && event.touches[0]) {
                event = event.touches[0];
            }

            var x = event.clientX - canvas.getBoundingClientRect().left;
            var y = event.clientY - canvas.getBoundingClientRect().top;

            addPoint(x, y);

            if (points.length < 3) {
                // Need at least 3 points to create a smooth curve
                ctx.beginPath();
                ctx.moveTo(points[0].x, points[0].y);
                ctx.lineTo(points[0].x, points[0].y);
                ctx.stroke();
                return;
            }

            // Use a Bezier curve to interpolate between points
            ctx.beginPath();
            ctx.moveTo(points[0].x, points[0].y);

            for (var i = 1; i < points.length - 2; i++) {
                var xc = (points[i].x + points[i + 1].x) / 2;
                var yc = (points[i].y + points[i + 1].y) / 2;
                ctx.quadraticCurveTo(points[i].x, points[i].y, xc, yc);
            }

            // Connect the last two points with a straight line
            ctx.lineTo(points[i].x, points[i].y);
            ctx.stroke();

            // Remove the first point from the array if there are too many
            if (points.length > 100) {
                points.shift();
            }
        }

        document.getElementById("clearButton").addEventListener("click", clearSignature);

        function clearSignature() {
            ctx.clearRect(0, 0, canvas.width, canvas.height); // Clear the entire canvas
            document.getElementById("signatureData").value = "";
        }
    </script>
</body>

</html>