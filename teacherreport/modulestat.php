<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Displays some overview statistics for the site
 *
 * @package     report_teacherreport
 * @school      Télécom SudParis France
 * @copyright   2015 BAKARI Houssem <baccarihoucem21@gmail.com>
				     ALIMI Marwa <>
					 BEN CHEIKH BRAHIM Amine <>
					 CHOUCHANE Rania <chouchene.rania2013@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/report/teacherreport/locallib.php');

global $PAGE;

echo "<!DOCTYPE html> 
<html> 
    <head> 
		<link rel=\"stylesheet\" href=\"design.css\"> 
    </head> 
    <body> ";


$userid = optional_param('userid', null, PARAM_INT);

if (is_null($userid)) {
    $userid=$USER->id;
} 
require_login();


    // check that the user has the right to access this page
    $theuser = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);  //does the id exists in the database
    $usercontext = CONTEXT_USER::instance($theuser->id);

	$PAGE->set_pagelayout('report');
	$PAGE->set_context(context_system::instance());
    $PAGE->set_url(new moodle_url('/report/teacherreport/index.php', array('userid' => $theuser->id)));
	$PAGE->set_title(get_string('pluginname', 'report_teacherreport'));
	$PAGE->set_heading(get_string('pluginname', 'report_teacherreport'));
    echo $OUTPUT->header();
    
  
  
	if (!isset($_GET["data"])) {
		echo get_string('T_noid', 'report_teacherreport');////
	}
	else {
		$idcourse = $_GET["data"];
		
		$activitiesListDisplay = report_activities_manager::get_activities_list($idcourse,$DB);
		$studentsListDisplay = report_students_manager::get_students_list($idcourse,$DB,$activitiesListDisplay);
		
		$stringOut="<table class=\"tg\"><tr><th class=\"tg-s6z2\">nom étudiant</th><th class=\"tg-s6z2\">niveau général</th>";
		
		foreach($activitiesListDisplay as $var){
			$stringOut=$stringOut."<th class=\"tg-s6z2\">  ".$var["name"]."  </th>";
		}
		$stringOut=$stringOut."</tr>";
		
		
		foreach($studentsListDisplay as $key1=>$var1){
			$stringOut=$stringOut."<tr><td>".$var1["firstname"]." ".$var1["lastname"]."</td>";
			$stringAux="";
			$ind=0;
			$nbSupMoy=0;
			foreach($var1["grade"] as $key2=>$var2){
				
				                     $Gavg=$activitiesListDisplay[$ind]["moyenne"];
									 $ecartType=$activitiesListDisplay[$ind]["ecarttype"];
									 $userGrade=$var2;
									 
									if($userGrade<$Gavg-$ecartType){
										$stringAux=$stringAux."<td class=\"coloring\"><img src=\"graphix/level0.png\" height=\"20\" width=\"20\"></td>";
									 }
									else if($userGrade>=$Gavg-$ecartType and $userGrade<$Gavg-($ecartType/3)){
										$stringAux=$stringAux."<td class=\"coloring\"><img src=\"graphix/level1.png\" height=\"20\" width=\"20\"></td>";
										$nbSupMoy+=0.25;
										}
									else if($userGrade>=$Gavg-($ecartType/3) and $userGrade<=$Gavg+($ecartType/3)){
										$stringAux=$stringAux."<td class=\"coloring\"><img src=\"graphix/level2.png\" height=\"20\" width=\"20\"></td>";
										$nbSupMoy+=0.5;
										}
									else if($userGrade>$Gavg+($ecartType/3) and $userGrade<=$Gavg+$ecartType){
										$stringAux=$stringAux."<td class=\"coloring\"><img src=\"graphix/level3.png\" height=\"20\" width=\"20\"></td>";
										$nbSupMoy+=0.75;
										}
									else {
										$stringAux=$stringAux."<td class=\"coloring\"><img src=\"graphix/level4.png\" height=\"20\" width=\"20\"></td>";
										$nbSupMoy+=1;
									}
									$ind++;
			}
			
			$count=$ind--;
			$perfor=0;
			$string_perfor="";
			
			if($count!=0){
			$perfor=$nbSupMoy/$count;
			
			if($perfor<0.2){$string_perfor=$string_perfor."<div>Faible</br>    <img src=\"graphix/level0.png\" height=\"20\" width=\"20\"></div>";}
			else if($perfor>=0.2 and ($perfor<0.4)){$string_perfor=$string_perfor."<div>Passable</br>   <img src=\"graphix/level1.png\" height=\"20\" width=\"20\"></div>";}
			else if($perfor>=0.4 and ($perfor<0.6)){$string_perfor=$string_perfor."<div>Assez Bien</br>   <img src=\"graphix/level2.png\" height=\"20\" width=\"20\"></div>";}
			else if($perfor>=0.6 and ($perfor<0.8)){$string_perfor=$string_perfor."<div>Bien</br>   <img src=\"graphix/level3.png\" height=\"20\" width=\"20\"></div>";}
			else{$string_perfor=$string_perfor."<div>Excellent</br>   <img src=\"graphix/level4.png\" height=\"20\" width=\"20\"></div>";}
			}
			
			
			
			
			$stringOut=$stringOut."<td class=\"tg-s6z2\"> ".$string_perfor." </td>".$stringAux;
			
			$stringOut=$stringOut."</tr>";
		}
		$stringOut=$stringOut."</table>";
		echo $stringOut;

	}

		


echo $OUTPUT->footer();
echo "</body> 
</html>"	;
