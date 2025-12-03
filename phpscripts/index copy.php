<html>
    <head>
        <title>Project1</title>
        </head>
        <body>
            <h4>temperatura:<span id="temperature">0 °c </span></h4>
            <h4>humedad: <span id="humidity">0 </span></h4>
            <button id="foco" onclick="toggle()">OFF</button>
        <script>
            var socket = new WebSocket('ws://192.168.0.9:81');
            
           socket.onmessage = function(event){
            console.log(event.data);
            const data = event.data.split(":");

            const msg     = data[0] || "";
            const sensor  = data[1] || "";

            if(sensor == "led"){
                var button =document.getElementById("foco");
                button.innerHTML = msg == "1" ? "ON" : "OFF";
            }
            else if(sensor == "dht"){
                var parts = msg.split(",");
                var temperature = parts[0];
                var himidity = parts[1];
             
                document.getElementById("temperature").innerHTML = temperature + "°c";
                document.getElementById("humidity").innerHTML =himidity + " %";
            }
           } ;

           function toggle() {
                var button = document.getElementById("foco");
                var status = button.innerHTML ==="OFF" ? "1" : "0";
                socket.send(status + ":led:esp:localhost");
           }
            </script>
            </body>

            </html>
<?php
echo "hello";
?>

