<? 
/************************************************************************/
/* Leonardo: Gliding XC Server					                        */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2004-5 by Andreadakis Manolis                          */
/* http://sourceforge.net/projects/leonardoserver                       */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
//************************************************************************/
 
  if ( !auth::isAdmin($userID) ) { echo "go away"; return; }
  
	$compareField='hash';

	$query="SELECT *  FROM $remotePilotsTable ORDER BY remoteServerID ASC";
	 // echo "#count query#$query<BR>";
	$res= $db->sql_query($query);
	if($res <= 0){   
	 echo("<H3> Error in count items query! $query0</H3>\n");
	 exit();
	}	
	
//-----------------------------------------------------------------------------------------------------------
	
	$legend="Pilot Mapping Tables";

	echo  "<div class='tableTitle shadowBox'>
	<div class='titleDiv'>$legend</div>
	<div class='pagesDiv' style='white-space:nowrap'>$legendRight</div>
	</div>" ;
	
	echo "<pre>";
	echo "<table>";
	echo "<tr><td>#</td><td>Srv</td><td>UserID</td><td>Name</td><td>Srv</td><td>UserID</td><td>Name</td></tr>\n";
	$i=1;
	while (	$row = $db->sql_fetchrow($res) ) {
		$pilotID1=$row['serverID'].'_'.$row['userID'];
		fillPilotInfo($pilotID1,$row['serverID'],$row['userID']);			
		
		$pilotID2=$row['remoteServerID'].'_'.$row['remoteUserID'];
		fillPilotInfo($pilotID2,$row['remoteServerID'],$row['remoteUserID']);
		
		echo "<tr><td>$i</td><td>".$row['serverID']."</td>
			<td>".$row['userID']."</td>
			
			<td><a href='".CONF_MODULE_ARG."&op=list_flights&year=0&month=0&pilotID=$pilotID1'>".$pilotNames[$pilotID1]['lname']." ".$pilotNames[$pilotID1]['fname']." [ ".$pilotNames[$pilotID1]['country']." ] CIVLID: ".$pilotNames[$pilotID1]['CIVL_ID']."</td>
			<td>".$row['remoteServerID']."</td>		
			<td>".$row['remoteUserID']."</td>
			<td><a href='".CONF_MODULE_ARG."&op=list_flights&year=0&month=0&pilotID=$pilotID2'>".$pilotNames[$pilotID2]['lname']." ".$pilotNames[$pilotID2]['fname']." [ ".$pilotNames[$pilotID2]['country']." ] CIVLID: ".$pilotNames[$pilotID2]['CIVL_ID']."</td>
</tr>
		\n";
		$i++;
	}
	echo "</table><BR><BR>";
	echo "</pre>";
return;

function fillPilotInfo($pilotID,$userServerID,$userID) {
	global $pilotNames,$CONF_use_utf;
	
	if ( ! $pilotNames[$pilotID]){
		$pilotInfo=getPilotInfo($userID,$userServerID );
		if (!$CONF_use_utf ) {
			$NewEncoding = new ConvertCharset;
			$lName=$NewEncoding->Convert($pilotInfo[0],$langEncodings[$nativeLanguage], "utf-8", $Entities);
			$fName=$NewEncoding->Convert($pilotInfo[1],$langEncodings[$nativeLanguage], "utf-8", $Entities);
		} else {
			$lName=$pilotInfo[0];
			$fName=$pilotInfo[1];
		}
		$pilotNames[$pilotID]['lname']=$lName;
		$pilotNames[$pilotID]['fname']=$fName;
		$pilotNames[$pilotID]['country']=$pilotInfo[2];
		$pilotNames[$pilotID]['sex']=$pilotInfo[3];
		$pilotNames[$pilotID]['birthdate']=$pilotInfo[4];
		$pilotNames[$pilotID]['CIVL_ID']=$pilotInfo[5];
	} 
	
}
function printHeaderTakeoffs($width,$sortOrder,$fieldName,$fieldDesc,$query_str) {
  global $moduleRelPath;
  global $Theme;

  if ($width==0) $widthStr="";
  else  $widthStr="width='".$width."'";

  if ($fieldName=="intName") $alignClass="alLeft";
  else $alignClass="";

  if ($sortOrder==$fieldName) { 
   echo "<td $widthStr  class='SortHeader activeSortHeader $alignClass'>
			<a href='".CONF_MODULE_ARG."&op=admin_logs&sortOrder=$fieldName$query_str'>$fieldDesc<img src='$moduleRelPath/img/icon_arrow_down.png' border=0  width=10 height=10></div>
		</td>";
  } else {  
	   echo "<td $widthStr  class='SortHeader $alignClass'><a href='".CONF_MODULE_ARG."&op=admin_logs&sortOrder=$fieldName$query_str'>$fieldDesc</td>";
   } 
}

  
   $headerSelectedBgColor="#F2BC66";

  ?>
  <table class='simpleTable' width="100%" border=0 cellpadding="2" cellspacing="0">
  <tr>
  	<td width="25" class='SortHeader'>#</td>
 	<?
		printHeaderTakeoffs(100,$sortOrder,"actionTime","DATE",$query_str) ;
		printHeaderTakeoffs(0,$sortOrder,"ServerItemID","Server",$query_str) ;
		printHeaderTakeoffs(80,$sortOrder,"userID","userID",$query_str) ;

		printHeaderTakeoffs(100,$sortOrder,"ItemType","Type",$query_str) ;
		printHeaderTakeoffs(100,$sortOrder,"ItemID","ID",$query_str) ;
		printHeaderTakeoffs(100,$sortOrder,"ActionID","Action",$query_str) ;
		echo '<td width="100" class="SortHeader">Details</td>';
		printHeaderTakeoffs(100,$sortOrder,"Result","Result",$query_str) ;
		echo '<td width="100" class="SortHeader">ACTIONS</td>';
		
	?>
	
	</tr>
<?
   	$currCountry="";
   	$i=1;
	while ($row = $db->sql_fetchrow($res)) {  
		if ( auth::isAdmin($row['userID'])  ) $admStr="*ADMIN*";
		else $admStr="";

		if ($row['ServerItemID']==0) $serverStr="Local";
		else $serverStr=$row['ServerItemID'];
		
		$i++;
		echo "<TR class='$sortRowClass'>";	
	   	echo "<TD>".($i-1+$startNum)."</TD>";
		
		echo "<td>".date("d/m/y H:i:s",$row['actionTime'])."</td>\n";
		echo "<td>".$serverStr."</td>\n";
		echo "<td>".$row['userID']."$admStr<br>(".$row['effectiveUserID'].")</td>\n";
		echo "<td>".Logger::getItemDescription($row['ItemType'])."</td>\n";
		echo "<td>".$row['ItemID']."</td>\n";
		echo "<td>".Logger::getActionDescription($row['ActionID'])."</td>\n";
		echo "<td>";

		echo "<div id='sh_details$i'><STRONG><a href='javascript:toggleVisibility(\"details$i\");'>Show details</a></STRONG></div>";
			echo "<div id='details$i' style='display:none'><pre>".$row['ActionXML']."</pre></div>";
		echo "</td>\n";
		echo "<td>".$row['Result']."</td>\n";
		
		echo "<td>";
		if ($row['ItemType']==4) { // waypoint
				echo "<a href='".CONF_MODULE_ARG."&op=show_waypoint&waypointIDview=".$row['ItemID']."'>Display</a>";
		}
		
		echo "</td>\n";

		
		echo "</TR>";
   }     
   echo "</table>";
   $db->sql_freeresult($res);

?>