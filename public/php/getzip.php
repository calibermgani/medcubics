<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$conn = mysql_connect($dbhost, $dbuser, $dbpass);
if(! $conn )
{
  die('Could not connect: ' . mysql_error());
}
$zip=$_GET["zip"];
$sql = 'SELECT zip_code, city, state FROM zipcodes WHERE zip_code = ' . $zip;

mysql_select_db('med');
$retval = mysql_query( $sql, $conn );
if(! $retval )
{
  die('Could not get data: ' . mysql_error());
}

while($row = mysql_fetch_array($retval, MYSQL_ASSOC))
{
		 $ziparray = array($row['zip_code']=>array($row['city'],$row['state']));
} 


if (strlen($zip) == 5 && $zip != $ziparray) {
    $city=$ziparray[$zip][0];
    $state=$ziparray[$zip][1];
}
 

mysql_close($conn);
?>



<div class="span8">
<input type="text" name="payaddresscity" value="<?php echo $city ?>" readonly>
   </div>
                       
 <div class="span4" style="width:28.3%;">
<input type="text" name="payaddressstate" value="<?php echo $state ?>" readonly>
</div>

