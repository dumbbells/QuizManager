<?php

include_once "Tag.php";
include_once "functions.php";

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

function removeTag($data) {
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM problem WHERE pid = ".$data['problem'].";";
    $result = $conn->query($sql);
    $problem = $result->fetch_object();
    $keywords = $problem->keywords;
    if (strpos($data['tagId'].", ") !== false) {
        $keywords = str_replace($keywords, $data['tagId'].", ", "");
    }
    elseif (strpos(' ,'.$data['tagId']) !== false) {
        $keywords = str_replace($keywords, ", ".$data['tagId'], "");
    }
    else {
        $keywords = "";
    }
    $conn->query("UPDATE problem SET keywords = '".$keywords."' WHERE pid = ".$data['problem'].";");
    
    $conn->close();
    echo "success";
}

function AddNewTag($data) {
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT id FROM keywords WHERE keyword = '" . $data["newTag"] . "';";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $id = $result->fetch_object()->id;
        $sql = "SELECT * FROM problem WHERE pid = '" . $data["problem"] . "' AND find_in_set(" . $id . ", cast(keywords as char)) > 0;";
        if ($conn->query($sql)->num_rows > 0){ 
            $conn->close();
            echo "-1";
            return;
        }
        $sql = "UPDATE keywords SET count = count + 1 WHERE id = " . $id . " ;";
        $conn->query($sql);
        
        $sql = "SELECT * FROM problem WHERE pid = " . $data["problem"] . ";";
        $result = $conn->query($sql);
        $keywords = $result->fetch_object()->keywords;
        if ($keywords == null ) {
            $sql = "UPDATE problem SET keywords ='" . $id . "' WHERE pid = '" . $data["problem"] . "';";
            $conn->query($sql);
        }
        else {
            $sql = "UPDATE problem SET keywords ='".$keywords.",".$id."' WHERE pid = '".$data['problem']."';";
            $conn->query($sql);
        }
        
        $conn->close();
        loadTags($id);
    }
    else {
        $sql = "INSERT INTO keywords (keyword) VALUES ('" . $data["newTag"] . "');";
        $conn->query($sql);
        $sql = "SELECT keywords FROM problem WHERE pid = '" . $data["problem"] . "';";
        $result = $conn->query($sql);
        if ($result->num_rows == 0) {
            $sql = "INSERT INTO problem (keywords) VALUES ('" . $data['newTag'] . "') WHERE pid = '" . $data["problem"] . "';";
            $conn->query($sql);
        }
        else {
            $keywords = $result->keywords;
            $sql = "UPDATE problem SET keywords ='".$keywords.",".$id."' WHERE pid = '".$data['problem']."';";
            $conn->query($sql);
        }
        $sql = "SELECT id FROM keywords WHERE keyword = '" . $data["newTag"] . "';";
        $result = $conn->query($sql);

        $conn->close();
        loadTags($result->fetch_object()->id);
    }

}

function loadTags($idList) {
    if ($idList == null){
        return;
    }
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT * FROM keywords WHERE keywords.id IN (" . $idList . ");";   
    $result = $conn->query($sql);
    if ($result){
        while ($row = $result->fetch_assoc()){
            $tag = new Tag($row['keyword'], $row['count'], $row['id']);
            $tag->printTag();
        }
    }
    $conn->close();
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
        $conn->close();
        loadProblems();
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
    loadProblems();
    $conn->close();
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

    loadProblems();
    $conn->close();
}

function loadProblems($data = null)
{
    $conn = new mysqli(SERVERNAME, USERNAME, PASSWORD, DBNAME);

    $firstOnPage = isset($_GET['page']) ? 20 * ($_GET['page'] - 1) : 0;
    if ($firstOnPage == 0 && isset($_REQUEST['page']))
    {
        $firstOnPage = ($_REQUEST['page'] - 1) * 20;
    }
        $id = [];
    if ($data == null) {
        $sql = "SELECT * FROM problem  WHERE del = 0 ORDER BY pid DESC LIMIT " . $firstOnPage . ", 20";
    }
    else {
        $keywords = explode(",", $data);
        foreach ($keywords as $keyword) {   
            $keyword = trim($keyword);
            $sql = "SELECT id FROM keywords WHERE keyword = '$keyword'";
            $id[] = $conn->query($sql)->fetch_object()->id;
        }
        $sql = "SELECT * FROM problem WHERE del = 0 AND (";
        $count = 0;
        foreach ($id as $single) {
            $sql .= ($count > 0 ? "OR " : "") . "keywords LIKE '%$single%' ";
            $count++;
        }
        
        $sql .= ") ORDER BY (";
        $count = 0;
        foreach ($id as $single) {
            $sql .= ($count > 0 ? " + ": "") . "(keywords LIKE '%$single%')";
            $count++;
        }   
        $sql .= ") DESC LIMIT " . $firstOnPage . ", 20;";
    }
    $result = $conn->query($sql);
    if ($data == null) {
        $sql1 = "SELECT COUNT(*) as 'count' FROM problem WHERE del = 0";
    }
    else {
        $sql1 = "SELECT COUNT(*) as 'count' FROM problem WHERE del = 0 AND (";
                $count = 0;
        foreach ($id as $single) {
            $sql1 .= ($count > 0 ? "OR " : "") . "keywords LIKE '%$single%' ";
            $count++;
        }
        $sql1 .= ")";
    }
    $countResult = $conn->query($sql1)->fetch_object();
    $count = $countResult->count;
    $conn->close();

    $pages = ceil($count / 20);

    renderProblems($pages, $firstOnPage, $result);
}
