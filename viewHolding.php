<?php
// index.php
//
// main index page
// Time-stamp: "2009-02-18 14:11:06 jantman"
//
// Rack Management Project
// by Jason Antman <jason@jasonantman.com>
// $Id$
// $Source$
require_once('config/config.php');
mysql_connect() or die("Error connecting to MySQL.\n");
mysql_select_db($dbName) or die("Error selecting MySQL database: ".$dbName."\n");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Holding - RackMan</title>
<link rel="stylesheet" type="text/css" href="main.css" />
</head>

<body>
<?php
echo '<h1>Devices not in Racks</h1>'."\n";

$query = "SELECT d.*,dr.*,dc.odc_name,dt.odt_name,odot.odot_name,ds.ods_name,ds.ods_color,r.rack_identifier FROM devices AS d LEFT JOIN devices_rack AS dr ON d.device_id=dr.dr_device_id LEFT JOIN opt_device_classes AS dc ON dc.odc_id=d.device_class_id LEFT JOIN opt_device_types AS dt ON dt.odt_id=d.device_type_id LEFT JOIN opt_device_os_types AS odot ON odot.odot_id=d.device_os_type_id LEFT JOIN opt_device_statuses AS ds ON d.device_status_id=ds.ods_id LEFT JOIN racks AS r ON dr.dr_rack_id=r.rack_id WHERE dr.dr_rack_id IS NULL;";
$result = mysql_query($query) or die("Error in query: ".$query."\nError: ".mysql_error());

echo '<table class="rackView">'."\n";
echo '<tr><th>Device Name/ID</th><th>Class</th><th>Type</th><th>Mfr.</th><th>Model</th><th>Height (U)</th><th>Status</th></tr>'."\n";
while($device_row = mysql_fetch_assoc($result))
{
    echo '<tr>';
    echo '<td><a href="viewDevice.php?id='.$device_row['device_id'].'">'.$device_row['device_name'].' ('.$device_row['device_id'].')</a></td>'."\n";
    echo '<td>'.$device_row['odc_name'].'</td>'."\n";
    echo '<td>'.$device_row['odt_name'].'</td>'."\n";
    echo '<td>'.$device_row['device_mfr'].'</td>'."\n";
    echo '<td>'.$device_row['device_model'].'</td>'."\n";
    echo '<td>'.$device_row['device_height_U'].'</td>'."\n";
    echo '<td style="background-color: #'.$device_row['ods_color'].';">'.$device_row['ods_name'].'</td>'."\n";
    echo '</tr>'."\n"; 
}
echo '</table>'."\n";

require_once('footer.php');

?>
</body>

</html>
