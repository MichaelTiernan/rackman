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

require_once('../config/config.php');
require_once('../inc/funcs.php.inc');
rackman_mysql_connect() or die("Error connecting to MySQL.\n");
mysql_select_db($dbName) or die("Error selecting MySQL database: ".$dbName."\n");

$query = "INSERT INTO devices_rack SET dr_device_id=".((int)$_POST['device_id']).",dr_rack_id=".((int)$_POST['rackID']).",dr_top_U_num=".((int)$_POST['top_U_num']).",dr_pending_status=1,dr_rack_side=".((int)$_POST['deviceSide']).";";
$result = mysql_query($query) or die("Error in query: ".$query."\nError: ".mysql_error());
if($result)
{
    header("Location: ../viewRack.php?rack_id=".$_POST['rackID']);
}


?>