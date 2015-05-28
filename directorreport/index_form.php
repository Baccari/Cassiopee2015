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

 
 
 require_once('../../config.php');
 //require_once("$CFG->libdir/formslib.php");
 require_once("$CFG->libdir/formslib.php");
 
 
 
 
 
 
 class lastaccess_form extends moodleform {
 
 public function definition() {
 global $DB;
 global $CFG;
 $mform =& $this->_form;
 //$mform2 =& $this->_form;
 //get the course passed to the form
 $options = array();
 $options[0] = get_string('Tous les cours');
 $options += $this->_customdata['courses']; //Ne jamais oublier le underscore 
 
 //$options2 = array();
 //$options2[0] = get_string('Tous les domaines');
 //$options2 += $this->_customdata['domains'];
 
 //$options2=array('Tous les domaines','Informatique','Autres');
 //Domain//////
 //$mform->addElement('select', 'domain', get_string('domain'),$options2,'align="center"');
 //$mform->setType('domain', PARAM_ALPHANUMEXT);
 //////////////
 $mform->addElement('select', 'course', get_string('course'),$options,'align="center"');
 $mform->setType('course', PARAM_ALPHANUMEXT);
 $mform->addElement('date_selector', 'lastaccesseddate', get_string('from'),'align="center"');
 $mform->setType('lastaccesseddate', PARAM_INT);
 $mform->addElement('date_selector', 'currentdate', get_string('to'),'align="center"');
 $mform->setType('currentdate', PARAM_INT);
 $mform->addElement('submit', 'save', get_string('display','report_lastaccess'),'align="right"');
 }
 
 public function validation($data,$files){
 $errors = parent::validation($data,$files);
 //added to check whether the course option selected is valid
 if($data['course']=='0')
 {
 $errors['course']=get_string('error_invalidcourse','report_lastaccess');
 }
 //added to compare last access date and current date
 if($data['lastaccesseddate']>$date['currentdate'])
 {
 $errors['lastaccesseddate']=get_string('error_invaliddate','report_lastaccess');
 }
 //added to compare current date with the system date
 if($data['currentdate']>time(date("d-m-Y")))
 {
 $errors['currentdate']=get_string('error_invalidcurrentdate','report_lastaccess');
 }
 //added to check the last accessed date is not equal to null/zero
 if($data['lastaccesseddate']=='0')
 {
 $errors['lastaccesseddate']=get_string('error_nolastaccessdate','report_lastaccess');
 }
 
       
 }
 
 
 }
