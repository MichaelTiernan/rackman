<?php
//
// +----------------------------------------------------------------------+
// | RackMan      http://rackman.jasonantman.com                          |
// +----------------------------------------------------------------------+
// | Copyright (c) 2009 Jason Antman.                                     |
// |                                                                      |
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation; either version 3 of the License, or    |
// | (at your option) any later version.                                  |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to:                           |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+
// | ADDITIONAL TERMS (pursuant to GPL Section 7):                        |
// | 1) You may not remove any of the "Author" or "Copyright" attributions|
// |     from this file or any others distributed with this software.     |
// | 2) If modified, you must make substantial effort to differentiate    |
// |     your modified version from the original, while retaining all     |
// |     attribution to the original project and authors.                 |
// +----------------------------------------------------------------------+
// |Please use the above URL for bug reports and feature/support requests.|
// +----------------------------------------------------------------------+
// | Authors: Jason Antman <jason@jasonantman.com>                        |
// +----------------------------------------------------------------------+
// | $LastChangedRevision::                                             $ |
// | $HeadURL::                                                         $ |
// +----------------------------------------------------------------------+
require_once('config/config.php');
require_once('inc/funcs.php.inc');
rackman_mysql_connect() or die("Error connecting to MySQL.\n");
mysql_select_db($dbName) or die("Error selecting MySQL database: ".$dbName."\n");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title>Left Nav</title>
<script language="javascript" src="inc/TreeMenu.js"></script>
<link rel="stylesheet" type="text/css" href="leftnav.css" />

</head>
<body class='navbar'>

<h1>RackMan</h1>
<?php
require_once('version.php');
echo '<p class="version">version '.$rackman_version.'</p>';
?>


<!-- CREATE & OUTPUT MENU -->
<?php

require_once 'HTML/TreeMenu.php';

$menu  = new HTML_TreeMenu();
$top = new HTML_TreeNode(array('cssClass' => 'dhtmlMenu'));
$physical = new HTML_TreeNode(array('text' => 'By Location'));

$query = "SELECT loc_id,loc_name FROM locations ORDER BY loc_id ASC;";
$result = mysql_query($query) or die("Error in query: ".$query."\nError: ".mysql_error());
$locations = array();
$rooms = array();
$racks = array();
while($row = mysql_fetch_assoc($result))
{
    // start one location
    $loc_index = count($locations);
    $locMenu = new HTML_TreeNode(array('text' => $row['loc_name']));
    $query2 = "SELECT room_name,room_id FROM rooms WHERE room_location_id=".$row['loc_id'].";";
    $result2 = mysql_query($query2) or die("Error in query: ".$query2."\nError: ".mysql_error());
    while($row2 = mysql_fetch_assoc($result2))
    {
	// one room, iterate the racks in the room
	$room_index = count($rooms);
	$roomMenu = new HTML_TreeNode(array('text'=>$row2['room_name']));

	// get the racks for this room
	$query3 = "SELECT rack_id,rack_identifier FROM racks WHERE rack_room_id=".$row2['room_id'].";";
	$result3 = mysql_query($query3) or die("Error in query: ".$query3."\nError: ".mysql_error());
	while($row3 = mysql_fetch_assoc($result3))
	{
	    $rackItem = new HTML_TreeNode(array('text'=>$row3['rack_identifier'], 'link'=>'viewRack.php?rack_id='.$row3['rack_id'], 'linkTarget'=>'Main'));
	    $rack_index = count($racks);
	    $racks[$rack_index] = $rackItem;
	    $roomMenu->addItem($racks[$rack_index]);
	}
	$rooms[$room_index] = $roomMenu;
	$locMenu->addItem($rooms[$room_index]);
	// end one room
    }
    $locations[$loc_index] = $locMenu;
    $physical->addItem($locations[$loc_index]);
    // end one location
}

// Chose a generator. You can generate DHTML or a Listbox

$top->addItem($physical);

$top->addItem(new HTML_TreeNode(array('text' => 'Add Device', 'link' => 'admin/addDevice.php', 'linkTarget' => 'Main')));
$top->addItem(new HTML_TreeNode(array('text' => 'Holding Area', 'link' => 'viewHolding.php', 'linkTarget' => 'Main')));

// admin links
$adminMenu = new HTML_TreeNode(array('text' => 'Admin'));
$adminMenu->addItem(new HTML_TreeNode(array('text' => 'Locations', 'link' => 'admin/locAdmin.php', 'linkTarget' => 'Main')));
$adminMenu->addItem(new HTML_TreeNode(array('text' => 'Rooms', 'link' => 'admin/roomAdmin.php', 'linkTarget' => 'Main')));
$adminMenu->addItem(new HTML_TreeNode(array('text' => 'Racks', 'link' => 'admin/rackAdmin.php', 'linkTarget' => 'Main')));
$adminMenu->addItem(new HTML_TreeNode(array('text' => 'VLANs', 'link' => 'admin/vlanAdmin.php', 'linkTarget' => 'Main')));
$top->addItem($adminMenu);

// help menu
$helpMenu = new HTML_TreeNode(array('text' => 'Help'));
$helpMenu->addItem(new HTML_TreeNode(array('text' => 'Features', 'link' => 'help/features.php', 'linkTarget' => 'Main')));
$helpMenu->addItem(new HTML_TreeNode(array('text' => 'Overview', 'link' => 'help/overview.php', 'linkTarget' => 'Main')));
$helpMenu->addItem(new HTML_TreeNode(array('text' => 'Objects', 'link' => 'help/objects.php', 'linkTarget' => 'Main')));
$helpMenu->addItem(new HTML_TreeNode(array('text' => 'Workflow', 'link' => 'help/workflow.php', 'linkTarget' => 'Main')));
$helpMenu->addItem(new HTML_TreeNode(array('text' => 'Automation', 'link' => 'help/automation.php', 'linkTarget' => 'Main')));
$helpMenu->addItem(new HTML_TreeNode(array('text' => 'Extension', 'link' => 'help/extension.php', 'linkTarget' => 'Main')));
$top->addItem($helpMenu);

$tree = new HTML_TreeMenu_DHTML($top);

echo '<div class="dhtmlMenu">'."\n";
echo $tree->toHTML();
echo '</div> <!-- END dhtmlMenu DIV -->'."\n";

?> 
<!-- END OUTPUT MENU -->

</body>
</html>