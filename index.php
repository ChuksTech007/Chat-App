<?php
session_start();
include "db.php"; // Include DB connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Chat App</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <div class="chat-container">
        <div class="chat-header">
            <h2>PHP Chat App</h2>
        </div>
        <div class="chat-window" id="chat-window">
            <?php
            $result = $conn->query("SELECT sender, message FROM messages ORDER BY timestamp ASC");
            while ($row = $result->fetch_assoc()) {
                echo "<div class='message {$row['sender']}'>{$row['message']}</div>";
            }
            ?>
        </div>
        <div class="chat-input">
            <input type="text" id="message-input" placeholder="Type a message...">
            <button id="send-button">Send</button>
            <div id="loading" style="display:none;">Loading...</div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            $("#send-button").click(function(){
                sendMessage();
            });

            $("#message-input").keypress(function(event) {
                if (event.which == 13) { // Enter key press
                    sendMessage();
                }
            });

            function sendMessage() {
                var message = $("#message-input").val().trim();
                if (message === "") return;

                $("#chat-window").append("<div class='message user'>" + message + "</div>");
                $("#chat-window").scrollTop($("#chat-window")[0].scrollHeight);
                $("#loading").show();

                $.post("chat.php", {message: message}, function(data){
                    $("#chat-window").append("<div class='message bot'>" + data + "</div>");
                    $("#message-input").val("");
                    $("#chat-window").scrollTop($("#chat-window")[0].scrollHeight);
                    $("#loading").hide();
                });
            }
        });
    </script>

</body>
</html>
