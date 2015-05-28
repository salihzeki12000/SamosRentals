<?php

// <editor-fold defaultstate="collapsed" desc="Requires">
require '../admin/configuration.php';
session_start();
// </editor-fold>

$con = db_connect();

if (strcmp($_POST["SaAuDeAction"], "edit") == 0) {
    $query = "UPDATE auction SET Name=?, Description=?, Bid_Price=?, Buy_Price=?, PeopleCount=?, End_Date=?, Images=?, Closed=? WHERE ID=?";
    $sql = $con->prepare($query);
    $sql->bind_param('ssiiisbii', $_POST["AuctionName"], $_POST["Description"], $_POST["Bid_Price"], $_POST["Buy_Price"],
        $_POST["PeopleCount"], $_POST["End_Date"], $_POST["Images"], $_POST["Closed"], $_POST["AuctionID"]);
} else if (strcmp($_POST["SaAuDeAction"], "new") == 0) {
    $query = "INSERT INTO auction(Name, Description, Bid_Price, Buy_Price, PeopleCount, End_Date, Images, Hotel) VALUES (?,?,?,?,?,?,?,?)";
    $sql = $con->prepare($query);
    $sql->bind_param('ssiiisbi', $_POST["AuctionName"], $_POST["Description"], $_POST["Bid_Price"], $_POST["Buy_Price"],
        $_POST["PeopleCount"], $_POST["End_Date"], $_POST["Images"], $_POST["CrAuHotelID"]);
}

if (!$sql->execute()) {
    echo '{"success":"no","message":"ελέξτε όλα τα στοιχεία ότι είναι σωστά!"}';
} else {
    $rand = $_POST["AuctionName"] . rand(000000, 999999);
    $mysqlDate = date('Y-m-d H:i:s', strtotime($_POST["End_Date"]));
    $query = "CREATE EVENT " . $rand . " ON SCHEDULE AT '" . $mysqlDate . "' DO UPDATE Auction SET Closed = 1 WHERE ID=LAST_INSERT_ID()";
    $sql1 = $con->prepare($query);
    $sql1->execute();
    //die('{"success":"no","message":"' . $mysqlDate . '"}');

    // <editor-fold defaultstate="collapsed" desc="Upload Image">
    $image_str = "";
    $j = 0; //Variable for indexing uploaded image
    $target_path = "../images/"; //Declaring Path for uploaded images

    if (isset($_FILES['fileToUpload']['name'])) {

        for ($i = 0; $i < count($_FILES['fileToUpload']['name']); $i++) {//loop to get individual element from the array
            $validextensions = array("jpeg", "jpg", "png");  //Extensions which are allowed
            $ext = explode('.', basename($_FILES['fileToUpload']['name']));//explode file name from dot(.)  !!!warning [$i]
            $file_extension = end($ext); //store extensions in the variable

            $target_path = $target_path . md5(uniqid()) . "." . $ext[count($ext) - 1];//set the target path with a new name of image
            $j = $j + 1;//increment the number of uploaded images according to the files in array

            if (($_FILES["fileToUpload"]["size"][$i] < 1000000) //Approx. 1000kb files can be uploaded.
                && in_array($file_extension, $validextensions)
            ) {
                if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $target_path)) {//if file moved to uploads folder
                    $image_str = $image_str . str_replace("../", "", $target_path) . ";";
                } else {//if file was not moved.
                    die('{"success":"no","message":" please try again to upload image!"}');
                }
            } else {//if file size and file type was incorrect.
                die('{"success":"no","message":" Invalid file Size or Type!"}');
            }
        }

        if (isset($_POST["AuctionID"])) {
            $query = "UPDATE auction SET Images=? WHERE ID=?";
            $sql = $con->prepare($query);
            $sql->bind_param('si', $image_str, $_POST["AuctionID"]);
            $sql->execute();
        } else {
            $query = "UPDATE auction SET Images=? WHERE ID=LAST_INSERT_ID()";
            $sql = $con->prepare($query);
            $sql->bind_param('s', $image_str);
            $sql->execute();
        }

    }
    // </editor-fold>

    echo '{"success":"yes"}';
}
