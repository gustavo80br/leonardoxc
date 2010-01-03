<?
//************************************************************************
// Leonardo XC Server, http://leonardo.thenet.gr
//
// Copyright (c) 2004-8 by Andreadakis Manolis
//
// This program is free software. You can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License.
//
// $Id: GUI_admin_paths.php,v 1.4 2010/01/02 22:54:56 manolis Exp $                                                                 
//
//************************************************************************

	echo "<hr><h3>ADMIN: Move files to new path locations</h3>";

	if (! L_auth::isAdmin($userID) ) {
		echo "<BR><BR>Not authorized<BR><BR>";
		return;
	}
		
	
	$query="SELECT * FROM $flightsTable order by userServerID,userID ";
	
	$res= $db->sql_query($query);
	if($res <= 0){
	 echo("<H3> Error in query! </H3>\n");
	 exit();
	}
	
	//echo "List of filenames<HR><pre>\r\n";

	global $CONF;
	// temporary change to new way even if we are still in version 1
	// in order to create the migration script	
	$CONF['paths']=$CONF['paths_versions'][2];
	
	$output1='';
	$output2='';
	$dirlist=array();
	
	while ($row = $db->sql_fetchrow($res) ) { 
		if (!$row['filename']) continue;	
		
		$userDir='';
		if ($row['userServerID']) {
			$userDir=$row['userServerID'].'_';
		}	
		$userDir.=$row['userID'];
			
		$year=substr($row['DATE'],0,4);
		$filename=$row['filename'];
		
		$d=0;
		$f=0;
		$files=array();
		$dirs=array();
			
		$files[$f][0]="$userDir/flights/$year/$filename";
		$files[$f][1]=str_replace("%PILOTID%","$userDir",str_replace("%YEAR%","$year",$CONF['paths']['igc']) )."/$filename";
						
		if (!$dirlist[$userDir][$year]) {
			$dirs[$d++]=dirname($files[$f][1]);
			$dirs[$d++]=str_replace("%PILOTID%","$userDir",str_replace("%YEAR%","$year",$CONF['paths']['photos']) );
			$dirs[$d++]=str_replace("%PILOTID%","$userDir",str_replace("%YEAR%","$year",$CONF['paths']['map']) );
			$dirs[$d++]=str_replace("%PILOTID%","$userDir",str_replace("%YEAR%","$year",$CONF['paths']['charts']) );
			$dirs[$d++]=str_replace("%PILOTID%","$userDir",str_replace("%YEAR%","$year",$CONF['paths']['kml']) );
			$dirs[$d++]=str_replace("%PILOTID%","$userDir",str_replace("%YEAR%","$year",$CONF['paths']['js']) );
			$dirs[$d++]=str_replace("%PILOTID%","$userDir",str_replace("%YEAR%","$year",$CONF['paths']['intermediate']) );

			$dirlist[$userDir][$year]++;
		}
		
		$f++;
		
		$files[$f][0]="$userDir/flights/$year/$filename.jpg";
		$files[$f++][1]=str_replace("%PILOTID%","$userDir",str_replace("%YEAR%","$year",$CONF['paths']['map']) )."/$filename.jpg";
	
		$files[$f][0]="$userDir/flights/$year/$filename.saned.igc";
		$files[$f++][1]=str_replace("%PILOTID%","$userDir",str_replace("%YEAR%","$year",$CONF['paths']['intermediate']) )."/$filename.saned.igc";
	
		$files[$f][0]="$userDir/flights/$year/$filename.saned.full.igc";
		$files[$f++][1]=str_replace("%PILOTID%","$userDir",str_replace("%YEAR%","$year",$CONF['paths']['intermediate']) )."/$filename.saned.full.igc";
	
		$files[$f][0]="$userDir/flights/$year/$filename.poly.txt";
		$files[$f++][1]=str_replace("%PILOTID%","$userDir",str_replace("%YEAR%","$year",$CONF['paths']['intermediate']) )."/$filename.poly.txt";
	
		$files[$f][0]="$userDir/flights/$year/$filename.1.txt";
		$files[$f++][1]=str_replace("%PILOTID%","$userDir",str_replace("%YEAR%","$year",$CONF['paths']['intermediate']) )."/$filename.1.txt";
		
		$files[$f][0]="$userDir/flights/$year/$filename.json.js";
		$files[$f++][1]=str_replace("%PILOTID%","$userDir",str_replace("%YEAR%","$year",$CONF['paths']['js']) )."/$filename.json.js";
		
			
		foreach ($dirs as $dir ){
			$output2.="mkdir -p $dir\r\n";
		}
		foreach ($files as $f=>$farray ){
			$output1.=$farray[0].";";	
			$output2.="cp -a \"flights/".$farray[0]."\" \"".$farray[1]."\"\r\n";	
		}
		$output1.="\r\n";	
		$output2.="\r\n";					
		$igcNum++;		

/*	

// photo filenames
$CONF['paths']['photos']='data/flights/photos/%YEAR%/%PILOTID%';

// *.png 16 files / flight
$CONF['paths']['charts']='data/flights/charts/%YEAR%/%PILOTID%';

// *.kmz
// *.man.kmz
// *.igc2kmz.[version].kmz
$CONF['paths']['kml']	='data/flights/kml/%YEAR%/%PILOTID%';

*/
		
		
	
	}		
	
	//echo $output;
	
	$query="SELECT * FROM $flightsTable,$photosTable 
		WHERE $photosTable.flightID=$flightsTable.ID
		order by userServerID,userID ";
	
	$res= $db->sql_query($query);
	if($res <= 0){
	 echo("<H3> Error in query! </H3>\n");
	 exit();
	}
	
	//echo "List of filenames<HR><pre>\r\n";

	global $CONF;	
	$output1='';
	$output2='';
	$dirlist=array();
	
	while ($row = $db->sql_fetchrow($res)) { 
		// if (!$row['filename']) continue;
		$userDir='';
		if ($row['userServerID']) {
			$userDir=$row['userServerID'].'_';
		}	
		$userDir.=$row['userID'];
			
		$year=substr($row['DATE'],0,4);
		$filename=$row['name'];
		
		$output1.=$row['path']."/$filename\r\n";	
		$output2.="cp -a \"flights/".$row['path']."/$filename\" \"".
				str_replace("%PILOTID%","$userDir",str_replace("%YEAR%","$year",$CONF['paths']['photos']) )."/$filename".
				"\"\r\n";
						
	}
		
	$filename='files_list.csv';
	$fp=fopen(dirname(__FILE__)."/$filename","w" );
	fwrite($fp,$output1);
	fclose($fp);
	
	$filename2='copy_files.sh';
	$fp2=fopen(dirname(__FILE__)."/$filename2","w" );
	fwrite($fp2,$output2);
	fclose($fp2);
	
	// echo "\r\n</pre><HR>";
	echo "Flights found : $igcNum<HR><BR>";
	
	echo "A file named $filename2 with the shell commands( and a txt file  $filaneme with the igc paths for reference only) has been created in leonardo/ directory.
		 Check that the file is deleted on the server afterwards<BR>
	 Execute this command on a shell: <BR>";
	
	
	echo "<hr>cd ".dirname(__FILE__)."; ./$filename2;<HR>";
	return;


?>