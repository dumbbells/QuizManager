<?php

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

function swap($data)
{
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $print = false;
    $pids = explode(" ", $data);
    if ($pids[1] == "down") {
        $print = true;
        $sql = "SELECT MAX(pid) as 'pid' FROM problem WHERE pid < $pids[0] AND del < 1";
        $pids[1] = $conn->query($sql)->fetch_object()->pid;
    }
    else if ($pids[1] == "up") {
        $print = true;
        $sql = "SELECT MIN(pid) as 'pid' FROM problem WHERE pid > $pids[0] AND del < 1";
        $pids[1] = $conn->query($sql)->fetch_object()->pid;
    }
    $sql = "UPDATE
    problem AS problem1
    JOIN problem AS problem2 ON
           ( problem1.pid = $pids[0] AND problem2.pid = $pids[1])
        OR ( problem1.pid = $pids[1] AND problem2.pid = $pids[0])    
    SET
	problem1.content = problem2.content,
        problem2.content = problem1.content;";
    $conn->query($sql);
    if ($print) {
        loadProblems($conn);
    }
    else {
        $conn->close();
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
    $conn->close();
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
    if ($firstOnPage == 0 && isset($_REQUEST['page']))
    {
        $firstOnPage = ($_REQUEST['page'] - 1) * 20;
    }
    $sql = "SELECT * FROM problem  WHERE del = 0 ORDER BY pid DESC LIMIT " . $firstOnPage . ", 20";
    $result = $conn->query($sql);
    $sql = "SELECT COUNT(*) as 'count' FROM problem";
    $countResult = $conn->query($sql)->fetch_object();
    $count = $countResult->count;
    $conn->close();

    $pages = ceil($count / 20);

    renderProblems($pages, $firstOnPage, $result);
}
