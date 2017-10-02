<?php

//del column
//0 is not deleted. Inrement by 1 for each deletion. Largest del is most recent
//put the undone deleted item back to front of list (highest pid in table)
include "functions.php";
define("SERVERNAME", "localhost");
define("USERNAME", "root");
define("PASSWORD", "");
define("DBNAME", "mathprobdb");

if (isset($_POST['newQuestionAdded'])) {
    $newQuestion = $_POST['content'];
    insertNewQuestion($newQuestion);
    header("location: index.php");
    exit();
}


if (isset($_REQUEST["func"])) {
    $function = $_REQUEST["func"];
    if (isset($_REQUEST["data"])) {
        $function($_REQUEST["data"]);
    }
    else {
        $function();
    }
}

function edit($data)
{
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "UPDATE problem SET content = '" . $data['data'] . "' WHERE pid = " . $data['id'];
    $conn->query($sql);
    
    echo $data['data'];
    $conn->close();
}

function insertNewQuestion($content)
{
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "INSERT INTO mathprobdb.problem (content) VALUES ('$content')";
    $conn->query($sql);
}

function swap($data)
{
    $pids = explode(" ", $data);
}

function undoDeletion()
{
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM mathprobdb.problem ORDER BY del DESC LIMIT 1";
    $result = $conn->query($sql)->fetch_object();
    if ($result->del > 0) {
        $pid = $result->pid;
        $sql = "UPDATE mathprobdb.problem SET del = 0 WHERE pid = $pid";
        $conn->query($sql);
    }
    else {
        echo "<script> alert('No problems to undelete');</script>";
    }
    loadProblems($conn);
}

function deleteProblem($id)
{
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT MAX( del ) as 'delIndex' FROM mathprobdb.problem";
    $max = $conn->query($sql)->fetch_object();
    $delIndex = $max->delIndex + 1;
    $sql = "UPDATE mathprobdb.problem SET del = $delIndex WHERE pid = $id";
    $conn->query($sql);

    loadProblems($conn);
}

function loadProblems($conn = null)
{
    if ($conn === null) {
        $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    }

    $firstOnPage = isset($_GET['page']) ? 20 * ($_GET['page'] - 1) : 0;
    $sql = "SELECT * FROM problem  WHERE del = 0 ORDER BY pid DESC LIMIT " . $firstOnPage . ", 20";
    $result = $conn->query($sql);
    $sql = "SELECT COUNT(*) as 'count' FROM problem";
    $countResult = $conn->query($sql)->fetch_object();
    $count = $countResult->count;
    $conn->close();

    $pages = ceil($count / 20);

    renderProblems($pages, $firstOnPage, $result);
}