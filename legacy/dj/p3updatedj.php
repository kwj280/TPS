<?php
session_start();
date_default_timezone_set($_SESSION['TimeZone']);
?>
<!DOCTYPE HTML>
<head>
<link rel="stylesheet" type="text/css" href="../../css/altstyle.css" />
<title>TPS Administration</title>
</head>
<html>
<body>
      <div class="topbar">
           Welcome, <?php echo(strtoupper($_SESSION['usr'])); ?>
           </div>

      <table border="0" align="center" width="1000">
      <tr>
           <td align="center" colspan="2"><img src="<?php print("../../".$_SESSION['logo']); ?>" alt="logo"/></td>
      </tr>
      <tr style="background-color:white;">
      <td colspan="2">
<?php

$con = mysqli_connect($_SESSION['DBHOST'],$_SESSION['usr'],$_SESSION['rpw'],$_SESSION['DBNAME']);
if (!$con){
	echo 'Uh oh!';
	die('Error connecting to SQL Server, could not connect due to: ' . mysqli_error() . ';  username=' . $_SESSION["username"]);
	}
else if($con){
        ?>

        <table align="left" border="0" height="100">
        <tr><td colspan="5">
        <h2>Edit DJ</h2>
        </td></tr>

        <tr><th width="250">
        Name
        </th><th width="200">
        On-Air Name
        </th><th width="200">
        Active
        </th><th width="250">
        Year Joined
        </th>
        </tr>
        <?php
             if(!mysqli_select_db($con, $_SESSION['DBNAME'])){header('Location: /');}
             if(isset($_POST['active'])){
               $ACTI = "1";
             }
             else{
               $ACTI = "0";
             }
             $sql = "select * from `dj` where Alias ='" . $_POST['Alias'] . "' ";
             $result = mysqli_query($con, $sql) or die(mysqli_error());
             if(mysqli_num_rows($result)=="0"){
               echo '<tr><td colspan="100%" style="background-color:yellow;">';
               echo 'No Results Found';
               echo '</tr></td>';
             }
             else{

               while($row=mysqli_fetch_array($result)) {
               //begin form
               echo '<form name="removedj" action="p4updatedj.php" method="post">';

               //begin row
               echo '<tr><td>';
               //column 1
                   echo "<input name=\"name\" type=\"text\" size=\"30\" value=\"" . $row['djname'] . "\">";
                   echo "<input name=\"namex\" hidden=\"true\" type=\"text\" value=\"" . $row['djname'] . "\">";

               echo '</td><td>';
               //column 2
                   //echo $row['callsign'];
                   echo "<input name=\"Alias\" type=\"text\" value=\"" . $row['Alias'] . "\">";
                   echo "<input name=\"aliasx\" hidden=\"true\" type=\"text\" value=\"" . $row['Alias'] . "\">";

               echo '</td><td>';
               //column 3
                   echo "<input name=\"active\" type=\"text\" size=\"15\" value=\"" . $row['active'] . "\">";
                   echo "<input name=\"activex\" hidden=\"true\" type=\"text\" value=\"" . $row['active'] . "\">";

               echo '</td><td>';
               //column 4
                   echo "<input name=\"year\" type=\"text\" size=\"35\" value=\"" .$row['years'] . "\">";
                   echo "<input name=\"yearx\" hidden=\"true\" type=\"text\" value=\"" .$row['years'] . "\">";

               echo "</td><td><input type=\"submit\" value=\"Update Row\" /></form>";
               echo'</td></tr>';
               //end row
               }
             }
        echo '</table>';

}
else{
	echo 'UNKNOWN ERROR - CONNECTION FAILED';
}

?>

<table align="center" width="1000">
		<tr><td colspan="5"><hr /></td></tr>
	</table>

		<table align="left">
		<tr><td>

        <?php
            echo "<td><form name=\"main\" action=\"/\" method=\"POST\">";
            echo "<input type=\"submit\" value=\"Return\">";
            echo "</form></td>";
			echo "<img src=\"../../images/mysqls.png\" alt=\"MySQL\" align=\"right\"></tr>";
        ?>

        </table>
</body>
</html>
