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
		
		<script type=\"text/javascript\">
                 function unhide(divID) {
                 var item = document.getElementById(divID);
                 if (item) {
                       item.className=(item.className=='hidden')?'unhidden':'hidden';
                 }
                }
        </script>
    </head> 
    <body> ";


$userid = optional_param('userid', null, PARAM_INT);

if (is_null($userid)) {
    // extract student id
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
    
 

	/*extaction of all courses (id and names) that this user is rolled in */
    $courseListDisplay = report_course_manager::get_course_list($theuser->id,$DB);
	/*extaction of all domaines */
	$courseListDomaine = report_course_manager::extract_list_domaine( $courseListDisplay,$DB);
	/*mapping courses and domaines*/
	$courseListFinal = report_course_manager::mapping_final_course_domaine($courseListDisplay,$courseListDomaine);
	

	

	foreach($courseListDomaine as $key1=>$var1){
		echo "<div id=\"cadreDomaine\"><a href=\"javascript:unhide('__".$key1."');\"><font color=\"white\">$var1</font></a><br /></div>\n";
		echo "<div id=\"__".$key1."\" class=\"hidden\">";
		
		if(is_array($courseListFinal) and $courseListFinal!=null){
			$nbModule=0;
		foreach($courseListFinal as $var2){
			$key=$var2["id"];
			if($var2["domaine"]==$var1){
				$nbModule++;
				echo "<a href=\"modulestat.php?data=$key\"><div id=\"cadreModule\"><img src=\"graphix/staticon.png\" height=\"15\" width=\"15\"><font color=\"#FF6347\">".$var2["shortname"] ." : " .$var2["fullname"] ."</font></div></a>"; //affichage nom du module
				
	                                   }
	                                }
			if($nbModule==0){
				echo get_string('T_nocourse', 'report_teacherreport');/////
			}
	    }
		else{
			echo get_string('T_nocourse', 'report_teacherreport');/////
		}
	
	echo "</div>";
	}
	
	
echo $OUTPUT->footer();
echo "</body> 
</html>"	;
