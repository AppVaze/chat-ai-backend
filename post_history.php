<?php
// Include config file
include 'config.php';

// Set headers for CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the JSON data from the request body
    $json = file_get_contents('php://input');
    $requestData = json_decode($json, true);
    
    // Check if JSON decoding was successful
    if ($requestData !== null && isset($requestData['history'], $requestData['uuid'])) {
        $historyArray = $requestData['history'];
        $uuid = $requestData['uuid'];
        
        $response = array();

        // Iterate through each history item
        foreach ($historyArray as $history) {
            $title =  $history['title'];
            $createdAt =  $history['createdAt'];
            $userQuery = mysqli_query($conn, "SELECT * FROM `user` WHERE `uuid` = '$uuid'");
            if ($userQuery) {
                // Check if UUID exists in the user table
                if (mysqli_num_rows($userQuery) > 0) {
                    // Insert data into the history table
                    $sql = "INSERT INTO `history` (`title`, `createdAt`, `uuid`) VALUES ('$title', '$createdAt', '$uuid')";
                    $query = mysqli_query($conn, $sql);
                    if ($query) {
                        $response[] = array("success" => true, "message" => "Data added successfully");
                    } else {
                        $response[] = array("success" => false, "message" => "An error occurred while inserting data into the history table: " . mysqli_error($conn));
                    }
                } else {
                    // UUID not found in the user table
                    $response[] = array("success" => false, "message" => "UUID not found in the user table, saving it first");
                    
                    // Insert UUID into the user table
                    $userInsertQuery = mysqli_query($conn, "INSERT INTO `user` (`uuid`) VALUES ('$uuid')");
                    if ($userInsertQuery) {
                        // Insert data into the history table
                        $sql = "INSERT INTO `history` (`title`, `createdAt`, `uuid`) VALUES ('$title', '$createdAt', '$uuid')";
                        $query = mysqli_query($conn, $sql);
                        if ($query) {
                            $response[] = array("success" => true, "message" => "Data added successfully");
                        } else {
                            $response[] = array("success" => false, "message" => "An error occurred while inserting data into the history table: " . mysqli_error($conn));
                        }
                    } else {
                        $response[] = array("success" => false, "message" => "Failed to save UUID in the user table: " . mysqli_error($conn));
                    }
                }
            } else {
                $response[] = array("success" => false, "message" => "Error executing the SQL query: " . mysqli_error($conn));
            }
        }
        echo json_encode($response);
    } else {
        echo json_encode(array("success" => false, "message" => "Invalid JSON data format"));
    }
} else {
    // Invalid request method
    echo json_encode(array("success" => false, "message" => "Invalid request method"));
}
?>
