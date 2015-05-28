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
 * Provides a second level of data needed in the display
 */
class report_activities_manager {
	
	
	//all activities related to the course with general info
	public static function extract_items_id_list_from_db($courseList,$DB) {
                     /* tables used      mdl_grade_items */
										 
						$arr=array();
						foreach($courseList as $key=>$value ){
							$arr[]=$key;
						}
						$result = $DB->get_records_list('grade_items', 'courseid', $arr, null, 'id,courseid,itemname,grademax');
        return $result;
    }
	
	
	//all students grades related to each activity
	public static function extract_info_grades_list_from_db($listiditems,$DB) {
                     /* tables used      mdl_grade_grades */
                        $aux_array=array();
						$ind=0;
					    $sql='SELECT itemid,max(finalgrade) AS maxG,min(finalgrade) AS minG,avg(finalgrade) AS avgG
						FROM {grade_grades}
						WHERE itemid=?';
					 
						foreach($listiditems as $var){
						$result = $DB->get_record_sql($sql, array($var->id));
						if($result!=null){
						$aux_array[$ind]=$result;
						$ind++;}
                        }

				return $aux_array;
						
    }
	
	
	public static function extract_user_grades_list_from_db($listiditems,$id_user,$DB) {
                     /* tables used      mdl_grade_grades */
					    $usr_array=array();
						$ind=0;
					    $sql='SELECT itemid,finalgrade
						FROM {grade_grades}
						WHERE itemid=? AND userid=?';
					 
						foreach($listiditems as $var){
						$result = $DB->get_record_sql($sql, array($var->id,$id_user));
						if($result!=null){
						$usr_array[$ind]=$result;
						$ind++;}
						}
						
				return $usr_array;
    }
	
	
	

	public static function extract_ecart_type_from_db($itemid,$DB,$moy) {
                     /* tables used      mdl_grade_grades */
                        $aux_array=array();
						$aux=0;
						$ind=0;
					    $sql='SELECT userid,finalgrade
						FROM {grade_grades}
						WHERE itemid=?';
					 
						$result = $DB->get_records_sql($sql, array($itemid));
						if($result!=null){
									foreach($result as $var){
										if($var->finalgrade!=null){
										$aux=$aux+pow($var->finalgrade-$moy,2);
										$ind++;
										}
									}
						return sqrt($aux/$ind);
						}

				return 0;
    }
	
	
	public static function mapping_data_activities($listid,$liststat,$listmarks,$DB){
	
	$arr_final=array();
	$ind=0;
	
	foreach($listid as $var1){
		$id=$var1->id;
		$max=0;
		$min=0;
		$moy=0;
		$ecart=0;
		$finalgrade=0;
		$aux=array();
		foreach($liststat as $var2){
			if($id==$var2->itemid){
				$max=$var2->maxg;
				$min=$var2->ming;
				$moy=$var2->avgg;
				$ecart=report_activities_manager::extract_ecart_type_from_db($id,$DB,$moy);
			}
		}
		
		foreach($listmarks as $var3){
			if($id==$var3->itemid){
				$finalgrade=$var3->finalgrade;
			}
		}
		
		if($var1->itemname != ""){
		$aux["id"]=$id;
		$aux["courseid"]=$var1->courseid;
		$aux["itemname"]=$var1->itemname;
		$aux["grademax"]=$var1->grademax;
		$aux["maxg"]=$max;
		$aux["ming"]=$min;
		$aux["avgg"]=$moy;
		$aux["ecart"]=$ecart;
		$aux["finalgrade"]=$finalgrade;
		
		$arr_final[$ind]=$aux;
		$ind++;
		}
	}
	
	return $arr_final;
}	
	
}
