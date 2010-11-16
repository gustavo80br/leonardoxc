<?
//************************************************************************
// Leonardo XC Server, http://www.leonardoxc.net
//
// Copyright (c) 2004-2010 by Andreadakis Manolis
//
// This program is free software. You can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License.
//
// $Id: EXT_comments_functions.php,v 1.5 2010/11/16 14:58:14 manolis Exp $                                                                 
//
//************************************************************************

 	require_once dirname(__FILE__)."/EXT_config_pre.php";
	require_once "config.php";
 	require_once "EXT_config.php";

	require_once "CL_flightData.php";
	require_once "CL_comments.php";
	require_once "FN_functions.php";	
	require_once "FN_UTM.php";
	require_once "FN_waypoint.php";	
	require_once "FN_output.php";
	require_once "FN_pilot.php";
	require_once "FN_flight.php";
	//require_once dirname(__FILE__)."/templates/".$PREFS->themeName."/theme.php";
	setDEBUGfromGET();

	
	// if ( !L_auth::isAdmin($userID) ) { echo "go away"; return; }

	$op=makeSane($_POST['op']);	

	if ($op=='add'){	
		$commentData['flightID']=makeSane($_POST['flightID']);
		$commentData['parentID']=makeSane($_POST['parentID'])+0;
		$commentData['guestName']=makeSane($_POST['guestName']);
		$commentData['guestEmail']=makeSane($_POST['guestEmail']);
		$commentData['guestPass']=makeSane($_POST['guestPass']);
		$commentData['text']=$_POST['commentText'];
		$commentData['userID']=makeSane($_POST['userID']);
		$commentData['userServerID']=makeSane($_POST['userServerID']);
		$commentData['languageCode']=makeSane($_POST['languageCode']);
		
		$newCommentDepth=makeSane($_POST['depth'])+0;
		
		$flightComments=new flightComments($flightID);
		$newCommentID=$flightComments->addComment(
				array(
					'parentID'=>$commentData['parentID'],
					'userID'=>$commentData['userID'],
					'userServerID'=>$commentData['userServerID'],
					'guestName'=>$commentData['guestName'],
					'guestPass'=>$commentData['guestPass'],
					'guestEmail'=>$commentData['guestEmail'],
					'text'=>$commentData['text'],
					'languageCode'=>$commentData['languageCode']
					)
		);			
			
		$str='';
		$now=gmdate("Y-m-d H:i:s");
		$commentData['dateUpdated']=$now;
		// $commentData['dateAdded']=
		$commentData['commentID']=$newCommentID;
		$commentID=$newCommentID;		
		$commentDepth=$newCommentDepth;
			
		include dirname(__FILE__).'/INC_comment_row.php';
		echo $str;
									 
		//echo " newCommentID=$newCommentID, flightID=$flightID 
		//		parentID=$parentID, guestName=$guestName, userID=$userID,
		//		<hr> $commentText <BR>";
		//echo "OK";
	} else if ($op=='edit'){	
		$flightID=makeSane($_POST['flightID']);
		$commentID=makeSane($_POST['commentID'])+0;
		$commentText=$_POST['commentText'];
		
		$flightComments=new flightComments($flightID);
		$result=$flightComments->changeComment(
				array(
					'commentID'=>$commentID,
					'text'=>$commentText,
					)
		);			
		echo "Result: $result";						 
	} else if ($op=='delete'){	
		$flightID=makeSane($_POST['flightID']);
		$commentID=makeSane($_POST['commentID'])+0;
		$parentID=makeSane($_POST['parentID'])+0;
		
		if (!$flightID|| !$commentID) {
			echo "0:Bad paramters";
			return;		
		}
		$flightComments=new flightComments($flightID);
		$result=$flightComments->deleteComment($commentID,$parentID);
		echo "Result: $result";
	}

?>