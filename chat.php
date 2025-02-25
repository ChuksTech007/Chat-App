<?php
session_start();
include "db.php"; // Include database connection

// Load the Composer autoloader
require_once "vendor/autoload.php"; // Load Composer dependencies  // Make sure this path is correct

use Dotenv\Dotenv;
// Initialize dotenv and load the variables from the .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Now you can access the OpenAI API key
$openai_api_key = $_ENV['OPENAI_API_KEY'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $userMessage = htmlspecialchars($_POST['message']);

    // Store user message in DB
    $stmt = $conn->prepare("INSERT INTO messages (sender, message) VALUES ('user', ?)");
    $stmt->bind_param("s", $userMessage);
    $stmt->execute();
    $stmt->close();

    // Call OpenAI API for response
    $response = openaiChat($userMessage);

    // Store AI response in DB
    $stmt = $conn->prepare("INSERT INTO messages (sender, message) VALUES ('bot', ?)");
    $stmt->bind_param("s", $response);
    $stmt->execute();
    $stmt->close();

    echo $response;
}

// Function to call OpenAI API
function openaiChat($message) {
    global $openai_api_key;
    
    $url = "https://api.openai.com/v1/chat/completions";
    $data = [
        "model" => "gpt-4",  // Ensure you are using the correct model
        "messages" => [["role" => "user", "content" => $message]],
        "temperature" => 0.7
    ];

    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer " . $openai_api_key
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    curl_close($ch);

    // Check for errors in the response
    if ($response === false) {
        return "Error: Unable to get a response from OpenAI API.";
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode != 200) {
        return "Error: Received non-200 response from OpenAI API: $httpCode";
    }
}

?>
