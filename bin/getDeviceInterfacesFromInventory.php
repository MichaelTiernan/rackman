#!/usr/bin/php
<?php
// Script to populate the device_interfaces table for devices in the inventory DB.
// Time-stamp: "2009-02-22 17:31:46 jantman"
// $Id$
// $Source$
require_once('../config/config.php');

$inventoryDB = "inventory";

// connect to MySQL
$conn = mysql_connect() or die("Error connecting to MySQL.\n");

$invQuery = "SELECT Host_ID,Host_HostName FROM host;";
mysql_select_db($inventoryDB) or die("Error selecting MySQL database: ".$inventoryDB."\n");
$invResult = mysql_query($invQuery) or die("Error in query: ".$invQuery."\nError: ".mysql_error());

while($invRow = mysql_fetch_assoc($invResult))
{
    mysql_select_db($dbName) or die("Error selecting MySQL database: ".$dbName."\n"); // select RackMan DB for further queries
    $rackQuery = "SELECT device_id,device_name FROM devices WHERE device_name LIKE '".strtoupper($invRow['Host_HostName'])."';";
    $rackRes = mysql_query($rackQuery) or die("Error in query: ".$rackQuery."\nError: ".mysql_error());
    if(mysql_num_rows($rackRes) < 1 ){ fwrite(STDERR, "Host named '".$invRow['Host_HostName']."' NOT in RackMan. Skipping.\n"); continue;} // if we don't already have it, skip.
    $tempRow = mysql_fetch_assoc($rackRes);
    $dev_id = $tempRow['device_id'];
    $dev_name = $tempRow['device_name'];
    fwrite(STDERR, "Checking interfaces for host named '".$invRow['Host_HostName']."' id=".$dev_name."\n");

    // ADD INTERFACES
    mysql_select_db($inventoryDB) or die("Error selecting MySQL database: ".$inventoryDB."\n");
    $invQuery2 = "SELECT * FROM nic WHERE Host_ID=".$invRow['Host_ID'].";";
    $invResult2 = mysql_query($invQuery2) or die("Error in query: ".$invQuery2."\nError: ".mysql_error());
    mysql_select_db($dbName) or die("Error selecting MySQL database: ".$dbName."\n"); // select RackMan DB for further queries
    while($invRow2 = mysql_fetch_assoc($invResult2))
    {
	// TODO - handle types other than Ethernet
	// TODO - how to figure out iftype for device_interfaces table?
	if($invRow2['Nic_Encap'] == "Ethernet")
	{
	    $if_name = $invRow2['Nic_Identifier'];
	    $if_ip_addr = $invRow2['Nic_IPaddr'];
	    $if_mac_addr = parseMAC($invRow2['Nic_MACaddr']);

	    // do we already have this?
	    $query = "SELECT * FROM device_interfaces WHERE di_mac_address LIKE '".strtoupper($if_mac_addr)."';";
	    $result = mysql_query($query) or die("Error in query: ".$query."\nError: ".mysql_error());
	    // if we already have the MAC, skip it.
	    if(mysql_num_rows($result) > 0) { fwrite(STDOUT, "\tInterface '".$if_name."' already in RackMan DB.\n"); continue;}

	    // TODO - handle other speeds
	    if(trim($invRow2['Nic_HighestSpeed']) == "100baseT/Full"){ $if_speed = 100000000;}
	    elseif(trim($invRow2['Nic_HighestSpeed']) == "1000baseT/Full"){ $if_speed = 1000000000;}
	    else{$if_speed = 0;}

	    $rackQuery = "INSERT INTO device_interfaces SET di_device_id=".$dev_id.",di_name='".$if_name."',di_ip_address='".$if_ip_addr."',di_mac_address='".$if_mac_addr."',di_default_speed_bps=".$if_speed.";";
	    $rackRes = mysql_query($rackQuery) or die("Error in query: ".$rackQuery."\nError: ".mysql_error());
	    fwrite(STDOUT, "\tAdded interface '".$if_name."' to host ".$dev_name." (".$dev_id.").\n");
	}
    }
}

mysql_close($conn);

function parseMAC($s)
{
    $s = str_replace(":", "", $s);
    return $s;
}

?>