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
 * @package     report_studentreport
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
class report_course_manager {

    
    
    public static function get_course_list($id_user,$DB) {
               
                        $listid=report_course_manager::extract_course_id_list_from_db($id_user,$DB);
						$listname=report_course_manager::extract_course_name_list_from_db($listid,$DB);
						$final_result=report_course_manager::mapping_course_name_id($listid,$listname,$DB);

        return $final_result;
    }
	
	
	private static function extract_course_id_list_from_db($id_user,$DB) {
                     /* tables used      mdl_user_enrolments
										 mdl_enrol  */
										 
						$sql='SELECT DISTINCT e.courseid
							  FROM {user_enrolments} AS ue,{enrol} AS e,{role} r, {role_assignments} ra
							  WHERE ue.userid=? AND ue.enrolid=e.id
							  AND ra.userid=ue.userid AND ra.roleid=r.id AND r.archetype="student"';
							  
						$result=$DB->get_records_sql($sql, array($id_user));

        return $result;
    }
	
	private static function extract_course_name_list_from_db($listid,$DB) {
                     /* tables used      mdl_course
										   */
						$arr=array();
						foreach($listid as $var){
							$arr[]=$var->courseid;
						}
						$result = $DB->get_records_list('course', 'id', $arr, null, 'id,fullname,shortname');
        return $result;
    }
	
	
	private static function mapping_course_name_id($listid,$listname,$DB) {
                     
                    $arr=array();
					foreach($listid as $var1){
						$aux=array();
						foreach($listname as $var2){
							if($var1->courseid=$var2->id){
								$aux["shortname"]=$var2->shortname;
								$aux["fullname"]=$var2->fullname;
								$arr[$var1->courseid]=$aux;}
						}
					}
					
		return $arr;
    }
	
	
	
	/*
	extract the list of domaines that verify the codification of "3lettres:4numbers" after deleting all the spaces
	*/
	public static function extract_list_domaine($listname,$DB){
		
		                $domaine=array();
						$ind=0;
						$arr=array();
										foreach($listname as $key=>$var){
											$arr[]=$key;
										}
										$result = $DB->get_records_list('course', 'id', $arr, null, 'id,shortname');
										
										
						if($result!=null){				
						foreach($result as $var){
							if($var->shortname!=null and $var->shortname!=""){
							$text=str_replace(' ','',$var->shortname);
							$rest=substr($text, -7);
							 if(strlen($rest)==7 and ctype_alpha(substr($rest, 0, 3))  and  ctype_digit(substr($rest,-4))){
								
								$exist=false;
								 for($i=0;$i<$ind;$i++){
								    if(substr($rest, 0, 3)==$domaine[$i]) {$exist=true;}
							     }
								 
								 if($exist==false){
								 $domaine[$ind]=substr($rest, 0, 3);
								 $ind++;
								 }
							 }					
							}
						}	
						}		

						$domaine[$ind]="autres";
						return $domaine;
		
	}
	
	
	
	public static function mapping_final_course_domaine($listcourse,$listdomaine){
		
		$arr_final=array();
		$ind=0;
		
		foreach($listcourse as $key=>$var1){
			$aux=array();
			$domaine="autres";
			foreach($listdomaine as $var2){
				if(strpos($var1["shortname"],$var2) !== FALSE){
					$domaine=$var2;
				}
			}
			
			$aux["id"]=$key;
			$aux["shortname"]=$var1["shortname"];
			$aux["fullname"]=$var1["fullname"];
			$aux["domaine"]=$domaine;
			$arr_final[$ind]=$aux;
			$ind++;
			
		}
		
		return $arr_final;
	}
	
}
