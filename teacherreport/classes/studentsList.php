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
 
 
 
defined('MOODLE_INTERNAL') || die();

/**
 * Provides a first level of data needed in the display
 */
 
class report_students_manager {
	
	
	
	public static function get_students_list($id_course,$DB,$listactivities) {
               
                        $listid=report_students_manager::extract_students_id_list_from_db($id_course,$DB);
						$listgrades=report_students_manager::extract_students_grades_list_from_db($DB,$listactivities);
						$final_result=report_students_manager::mapping_data($listid,$listgrades,$listactivities);

        return $final_result;
    }
	

	//all activities related to the course with general info
	private static function extract_students_id_list_from_db($id_course,$DB) {
                     /* tables used      mdl_user
										 mdl_role
										 mdl_role_assignments
										 mdl_enrol
										 mdl_user_enrolments*/
						
						$sql='SELECT DISTINCT u.id,u.firstname,u.lastname
							  FROM {user} u, {role} r, {role_assignments} ra, {enrol} e, {user_enrolments} ue
							  WHERE u.id=ra.userid AND
							        ra.roleid=r.id AND
									r.archetype="student" AND
									u.id=ue.userid AND
									ue.enrolid=e.id AND
									e.courseid=?';
						
						$result = $DB->get_records_sql($sql,array($id_course));
						
        return $result;
    }
	
	
	//all students grades related to each activity
	private static function extract_students_grades_list_from_db($DB,$list_act) {
                     /* tables used      mdl_grade_grades */
					 
					    $arr_items=array();
					    foreach ($list_act as $var){
								$arr_items[]=$var["id"];
						}

						$result = $DB->get_records_list('grade_grades', 'itemid', $arr_items, null, 'id,itemid,userid,finalgrade');
						
				return $result;
						
    }
	
	
	private static function mapping_data($list_id,$list_grades,$list_act){
						$array_aux=array();
						$ind=0;
						foreach($list_id as $var1){
								$aux1=array();
								$aux2=array();
								foreach($list_act as $var2){
										$aux_var=0;
										foreach($list_grades as $var3){
													if ($var1->id==$var3->userid and $var2["id"]==$var3->itemid){$aux_var=$var3->finalgrade;}
										} 
										$aux1[$var2["id"]]=$aux_var;
										
								} 

								$aux2["id"]=$var1->id;
								$aux2["firstname"]=$var1->firstname;
								$aux2["lastname"]=$var1->lastname;
								$aux2["grade"]=$aux1;
								$array_aux[$ind]=$aux2;
								$ind++;
						}

						return $array_aux;

	}

	
}
