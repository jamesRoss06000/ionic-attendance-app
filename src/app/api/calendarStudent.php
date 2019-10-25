<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, Content-Type");

require_once("connection2.php");

$rest_json = file_get_contents("php://input");
$_POST = json_decode($rest_json, true);

if (isset($_POST["id"])) {

    $id = $_POST['id'];

    $getDetails = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $getDetails->execute([':id' => $id]);
    $userDetails = $getDetails->fetchAll();
    $name = $userDetails[0]['nom'];
    $classe = $userDetails[0]['classe'];

    $stmtC = $conn->prepare("SELECT * FROM classes WHERE `classe` = :classe");
    $stmtC->execute([':classe' => $classe]);
        $classOutput = array();
        $classOutput = $stmtC->fetchAll();
        $startDate = $classOutput[0]['debut_cours'];
        $endDate = $classOutput[0]['fin_cours'];

    $stmt = $conn->prepare("SELECT * FROM planning WHERE `classe` = :classe OR `nom` = :name AND [Date] BETWEEN ':startDate' AND ':endDate 23:59:59'");
    $stmt->execute([':name' => $name, ':classe' => $classe, ':startDate' => $startDate, ':endDate' => $endDate]);

    if ($stmt->rowCount() > 0) {
        $output = array();
        $output = $stmt->fetchAll();
        echo json_encode($output);
    } else {
        $errors = "No data found for this date";
        echo json_encode($errors);
    }
    // $conn->close();
}