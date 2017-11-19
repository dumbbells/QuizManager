<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpassword = "";
$dbname = "mathprobdb";

$con = mysql_connect($dbhost, $dbuser, $dbpassword);

if (!$con) {
    die('Could not connect: ' . mysql_error());
}

mysql_select_db($dbname, $con);

$probIdArr = array();
$probContArr = array();

$query = "SELECT pid, content FROM problem ORDER BY pid DESC";

$result = mysql_query($query);

while ($row = mysql_fetch_assoc($result)) {
    $probIdArr[] = $row['pid'];
    $probContArr[] = $row['content'];
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>MathProbs</title>
        <script type="text/javascript">
            < script type = "text/javascript" >
                    window.MathJax = {
                        tex2jax: {
                            inlineMath: [["\\(", "\\)"]],
                            processEscapes: true
                        }
                    };
        </script>
        <script type="text/javascript"
                src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
        </script>
    </head>
    <body>
        <table>
            <tr>
                <th>Id</th>
                <th>Problem</th>
            </tr>
            <?php
            for ($i = 0; $i < count($probIdArr); $i++) {
                ?>
                <tr>
                    <td><?php print $probIdArr[$i]; ?></td>
                    <td><?php print $probContArr[$i]; ?></td>
                </tr>
                <?php
            }
            ?>
        </table>
    </body>
</html>
<?php
mysql_close($con);
?>