<?php

/// This file is part of Moodle - http://moodle.org/
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
class report_activities_manager {
	
	
	
	public static function get_activities_list($id_course,$DB) {
               
                        $listid=report_activities_manager::extract_items_id_list_from_db($id_course,$DB);
						$listinfos=report_activities_manager::extract_info_grades_list_from_db($listid,$DB);
						$listidmoy=report_activities_manager::mapping_id_moy($listid,$listinfos);
						$listecart=report_activities_manager::extract_ecart_from_db($listidmoy,$DB);
						$final_result=report_activities_manager::mapping_data($listid,$listinfos,$listecart);

        return $final_result;
    }
	

	//all activities related to the course with general info
	private static function extract_items_id_list_from_db($id_course,$DB) {
                     /* tables used      mdl_grade_items */
						
						$result = $DB->get_records_list('grade_items', 'courseid', array($id_course), null, 'id,itemname');
        return $result;
    }
	
	
	//all students grades related to each activity
	private static function extract_info_grades_list_from_db($listiditems,$DB) {
                     /* tables used      mdl_grade_grades */
                        $aux_array=array();
						$ind=0;
					    $sql='SELECT itemid,avg(finalgrade) AS avgg
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
	
	
	
	
	private static function mapping_id_moy($list1,$list2){
		
		$aux_array=array();
		$ind=0;
		foreach($list1 as $var1){
			$aux=array();
			$id=$var1->id;
			$name=$var1->itemname;
			if($name!=null){
			foreach($list2 as $var2){
				if($id == $var2->itemid){
					$aux["id"]=$id;
					$aux["moyenne"]=$var2->avgg;
				}
			}
			$aux_array[$ind]=$aux;
			$ind++;
			}
		}
		
		return $aux_array;	
	}
	
	
	
    private static function extract_ecart_from_db($listiditems,$DB) {
                     /* tables used      mdl_grade_grades */
                        $ecart_array=array();
					    $sql='SELECT userid,finalgrade
						FROM {grade_grades}
						WHERE itemid=?';
					 
					 
					 foreach($listiditems as $var){
						        $aux=0;
								$ind=0;
								$tab_aux=array();
								$var_aux=$var["id"];
								
								$result = $DB->get_records_sql($sql, array($var_aux));
								if($result!=null){
											foreach($result as $var1){
												if($var1->finalgrade!=null){
												$aux=$aux+pow($var1->finalgrade-$var["moyenne"],2);
												$ind++;
												}
											}
									$ecart_array[$var_aux]=sqrt($aux/$ind);

								}
								else{
									$ecart_array[$var_aux]=0;
								}
					 }
					 
					 return $ecart_array;

    }
	
	
	
	
	private static function mapping_data($list_id,$list_infos,$list_ecart){
		
	             $array_aux=array();
				 $ind=0;
				 foreach($list_id as $var1){
					            $moy=0;
								$ecart=0;
								$aux=array();
								$xx=$var1->itemname;
								if($xx!=null){
						       foreach($list_infos as $var2){
								   if($var1->id==$var2->itemid){$moy=$var2->avgg;}	   
							   } 	
                               foreach($list_ecart as $key=>$var3){
								   if($var1->id==$key){$ecart=$var3;}	   
							   }	

							   $aux["id"]=$var1->id;
							   $aux["name"]=$xx;
							   $aux["moyenne"]=$moy;
							   $aux["ecarttype"]=$ecart;
							   $array_aux[$ind]=$aux;
							   $ind++;
								}
					 }
	
	        return $array_aux;
	}
	
}
