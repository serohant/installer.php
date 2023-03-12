<?php 
//clear cache for getting real file size
clearstatcache();

// get all files in folder function
function getDirContents($dir, &$results = array()) {
    global $filename;
    $files = scandir($dir);
    if(count($files) <= 3){
        echo "[ERROR] Please put generator.php into some files";
        die();
    }
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            if($path != $dir.DIRECTORY_SEPARATOR.$filename){
                $results[] = $path;
            }
        } else if ($value != "." && $value != ".." && $value != ".git") {
            getDirContents($path, $results);
        }
    }
    return $results;
}

$path = __DIR__;
$filename = basename(__FILE__, ".php").".php";
getDirContents($path, $files);
for ($i=0; $i < count($files); $i++) { 
    $filen = explode($path, $files[$i])[1];
    $array[$i] = array($path,$filen, filesize($files[$i]), $files[$i]);
}
$installerfile = "";
$generatefiles = array();

$return = "";
if(isset($_GET['value'])){
    $return = "installer.php successfully generated.";
}

if($_POST){
    $selectedfiles = $_POST['files'];
    for ($i=0; $i < count($selectedfiles); $i++) {
        $filename = explode($path.DIRECTORY_SEPARATOR, $selectedfiles[$i])[1]; 
        $filepath = explode($path, $selectedfiles[$i])[1];
        $filecontent = base64_encode(file_get_contents($selectedfiles[$i]));
        $generatefiles[] = array(array(
            'file_path' => $filepath,
            'file_content'   => $filecontent
        ));
    }
    $installercontent = "<?php 
error_reporting(0);
\$decode = \" ".base64_encode(json_encode($generatefiles))."\";\r\n";
    $installercontentt = "JGRlY29kZWRfYXJyYXkgPSBqc29uX2RlY29kZShiYXNlNjRfZGVjb2RlKCRkZWNvZGUpLHRydWUpOwpwcmludF9yKCRkZWNvZGVkX2FycmF5KTsKJGxvZyA9ICRteWZpbGUgPSBmb3BlbihfX0RJUl9fLkRJUkVDVE9SWV9TRVBBUkFUT1IuImluc3RhbGxlcl9sb2cudHh0IiwgImEiKSBvciBkaWUoIlVuYWJsZSB0byBvcGVuIGZpbGUhIik7CmZ3cml0ZSgkbXlmaWxlLCAibG9nIGZpbGUgY3JlYXRlZFxyXG4iKTsKZmNsb3NlKCRteWZpbGUpOwplY2hvIGNvdW50KCRkZWNvZGVkX2FycmF5KTsKZm9yICgkaT0wOyAkaSA8PSBjb3VudCgkZGVjb2RlZF9hcnJheSktMTsgJGkrKykgeyAKCiAgICBpZighZmlsZV9leGlzdHMoX19ESVJfXy4kZGVjb2RlZF9hcnJheVskaV1bMF1bJ2ZpbGVfcGF0aCddKSl7CgogICAgICAgIGZvciAoJGs9MDsgJGsgPD0gY291bnQoJGRlY29kZWRfYXJyYXkpLTE7ICRrKyspIHsKICAgICAgICAgICAgJHJlYWxwYXRoID0gJGRlY29kZWRfYXJyYXlbJGtdWzBdWydmaWxlX3BhdGgnXTsKICAgICAgICAgICAgJHJlYWxjb250ZW50ID0gJGRlY29kZWRfYXJyYXlbJGtdWzBdWydmaWxlX2NvbnRlbnQnXTsKICAgICAgICAgICAgJGZvbGRlciA9ICIiOwogICAgICAgICAgICAkZm9sZGVycyA9IGV4cGxvZGUoRElSRUNUT1JZX1NFUEFSQVRPUiwkZGVjb2RlZF9hcnJheVska11bMF1bJ2ZpbGVfcGF0aCddKTsKICAgICAgICAgICAgZm9yICgkaT0xOyAkaSA8IGNvdW50KCRmb2xkZXJzKS0xIDsgJGkrKykgeyAKICAgICAgICAgICAgICAgICRmb2xkZXIgLj0gJGZvbGRlcnNbJGldLkRJUkVDVE9SWV9TRVBBUkFUT1I7CiAgICAgICAgICAgICAgICBpZiAoIWlzX2RpcigkZm9sZGVyKSkgbWtkaXIoJGZvbGRlciwgMDc3NywgdHJ1ZSk7CiAgICAgICAgICAgICAgICAkbG9nID0gJG15ZmlsZSA9IGZvcGVuKF9fRElSX18uRElSRUNUT1JZX1NFUEFSQVRPUi4iaW5zdGFsbGVyX2xvZy50eHQiLCAiYSIpIG9yIGRpZSgiVW5hYmxlIHRvIG9wZW4gZmlsZSEiKTsKICAgICAgICAgICAgICAgICAgICBmd3JpdGUoJG15ZmlsZSwgJGZvbGRlcnNbJGxdLiIgZm9sZGVyIGNyZWF0ZWRcclxuIik7CiAgICAgICAgICAgICAgICAgICAgZmNsb3NlKCRteWZpbGUpOwogICAgICAgICAgICB9ICAgCiAgICAgICAgICAgICRteWZpbGUgPSBmb3BlbihfX0RJUl9fLiRyZWFscGF0aCwgIngiKSBvciBkaWUoIlVuYWJsZSB0byBvcGVuIGZpbGUhIik7CiAgICAgICAgICAgICRjcmVhdGUgPSBmd3JpdGUoJG15ZmlsZSwgYmFzZTY0X2RlY29kZSgkcmVhbGNvbnRlbnQpKTsKICAgICAgICAgICAgaWYoJGNyZWF0ZSl7CiAgICAgICAgICAgICAgICAkbG9nID0gJG15ZmlsZSA9IGZvcGVuKF9fRElSX18uRElSRUNUT1JZX1NFUEFSQVRPUi4iaW5zdGFsbGVyX2xvZy50eHQiLCAiYSIpIG9yIGRpZSgiVW5hYmxlIHRvIG9wZW4gZmlsZSEiKTsKICAgICAgICAgICAgICAgIGZ3cml0ZSgkbXlmaWxlLCAkcmVhbHBhdGguIiBmaWxlIGNyZWF0ZWRcclxuIik7CiAgICAgICAgICAgICAgICBmY2xvc2UoJG15ZmlsZSk7CiAgICAgICAgICAgIH0KICAgICAgICAgICAgaWYoJGs9PWNvdW50KCRkZWNvZGVkX2FycmF5KS0xKXsKICAgICAgICAgICAgICAgICRteWZpbGUgPSBmb3BlbihfX0RJUl9fLkRJUkVDVE9SWV9TRVBBUkFUT1IuImluc3RhbGxlcl9sb2cudHh0IiwgImEiKSBvciBkaWUoIlVuYWJsZSB0byBvcGVuIGZpbGUhIik7CiAgICAgICAgICAgICAgICBmd3JpdGUoJG15ZmlsZSwgY291bnQoJGRlY29kZWRfYXJyYXkpLiIgZmlsZSBpcyBpbnN0YWxsZWRcclxuIik7CiAgICAgICAgICAgICAgICBmY2xvc2UoJG15ZmlsZSk7CiAgICAgICAgICAgIH0KICAgICAgICB9CiAgICB9Cn0=";
    $myfile = fopen("installer.php", "w") or die("Unable to open file!");
    fwrite($myfile, $installercontent.base64_decode($installercontentt));
    fclose($myfile);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installer.php Generator</title>
    <style>
        table, tr, th, td {
            border: 2px black solid
        }
    </style>
</head>
<body>
    <center>
    <h3>Auto Installer.php generator</h3>
        <form action="?value=" method="POST">
            <p>Select some files to be installed with installer</p>
            <table>
                <tr>
                    <th>File Number</th>
                    <th>File name</th>
                    <th>File size</th>
                    <th>Add/Remove</th>
                </tr>
                <?php 
                for ($i=0; $i < count($array) ; $i++) { 
                    echo "<tr>";
                    echo "<td> ".$array[$i][0]." </td>";
                    echo "<td> ".$array[$i][1]." </td>";
                    echo "<td> ".$array[$i][2]." KB </td>";
                    echo "<td> <input type='checkbox' name='files[]' value='".$array[$i][3]."'></td>";
                    echo "</tr>";
                }
                
                ?>
            </table>
            <br>
            <p><?= $return; ?></p>
            <input type="button" onclick="toggle()" value="Select All">
            <input type="submit" value="submit">
        </form>
</center>
</body>
<script language="JavaScript">
function toggle() {
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    for (var i = 0; i < checkboxes.length; i++) {
        if(checkboxes[i].checked == true){
            checkboxes[i].checked = false
        }else if(checkboxes[i].checked == false){
            checkboxes[i].checked = true
        }
    }
}
</script>
</html>