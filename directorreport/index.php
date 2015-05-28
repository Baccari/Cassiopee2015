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
 * @package     report_directorreport
 * @school      Télécom SudParis France
 * @copyright   2015 BAKARI Houssem <baccarihoucem21@gmail.com>
				     ALIMI Marwa <>
					 BEN CHEIKH BRAHIM Amine <>
					 CHOUCHANE Rania <chouchene.rania2013@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* index.php */
require_once('../../config.php');
require($CFG->dirroot.'/report/lastaccess/index_form.php');
// Get the system context.
$systemcontext = context_system::instance();
$url = new moodle_url('/report/lastaccess/index.php');
// Check basic permission.
require_capability('report/lastaccess:view',$systemcontext);
// Get the language strings from language file.
             
$strgrade = get_string('grade','report_lastaccess');
$strcourse = get_string('course','report_lastaccess');
$strlastaccess = get_string('lastaccess','report_lastaccess');
$strname = get_string('name','report_lastaccess');
$strtitle = get_string('title','report_lastaccess');
// Set up page object.
$PAGE->set_url($url);
$PAGE->set_context($systemcontext);
$PAGE->set_title($strtitle);
$PAGE->set_pagelayout('report');
$PAGE->set_heading($strtitle);
// Get the courses.
$sql = "SELECT id, fullname
        FROM {course}
        WHERE visible = :visible
        AND id != :siteid
        ORDER BY fullname";
		
		

$courses = $DB->get_records_sql_menu($sql, array('visible' => 1, 'siteid' => SITEID));
/
// Load up the form.
$mform = new lastaccess_form('', array('courses' => $courses));

echo $OUTPUT->header();
echo $OUTPUT->heading($strtitle);

if($data = $mform->get_data()){
  // Process the data:
  $begindate = $data->lastaccesseddate;
  $enddate = $data->currentdate;
  
  try
{
$bdd = new PDO('mysql:host=localhost;dbname=moodle', 'root', '');
}
catch(Exception $e)
{
die('Erreur : '.$e->getMessage());
}


  
  ////////////Liste des étudiants non actifs
  
if($begindate>$enddate)
{
echo "Operation Impossible : la date de debut est superieure A la date de fin. Veuillez reessayer !!<br><br>";
}
 else 
  {
$courseid = $_POST[course];
if($courseid!=0)
{
//echo "La liste des Etudiants Non actifs dans ce cours est :<br>";
$actifs[0]=-1;$inscrits[0]=-1;$inactifs[0]=-1;
// Bloc affichage des actifs dans le cours
	$reponse3 = $bdd->query('SELECT DISTINCT(userid) FROM mdl_logstore_standard_log WHERE userid NOT IN (SELECT userid FROM mdl_role_assignments WHERE roleid!=5) AND userid NOT IN (SELECT DISTINCT(modifierid) FROM mdl_role_assignments) AND courseid=\''.$_POST[course].'\' AND timecreated>'.$begindate.' AND timecreated<'.$enddate.'');
		while ($donnees3 = $reponse3->fetch())
		{
		$actifs[]=$donnees3['userid'];
		}
		//echo "<Strong>La liste des actifs: </Strong><br>";
		foreach($actifs as $element)
		{
		//echo $element . '<br />'; 
		}
//////////////////////
// Bloc affichage des inscrits dans le cours
		$reponse = $bdd->query('SELECT id FROM mdl_enrol WHERE courseid=\''.$_POST[course].'\'');
		while ($donnees = $reponse->fetch())
		{
		$reponse2 = $bdd->query('SELECT DISTINCT(userid) FROM mdl_user_enrolments WHERE userid NOT IN (SELECT userid FROM mdl_role_assignments WHERE roleid!=5) AND enrolid=\''.$donnees['id'].'\'');
		while ($donnees2 = $reponse2->fetch())
		{
		$inscrits[]=$donnees2['userid'];
		//echo $donnees2['userid'];echo "<br>";
		}
		}
		
		//echo "<Strong>La liste des inscrits: </Strong><br>";
		foreach($inscrits as $element)
		{
		//echo $element . '<br />'; 
		}	
//////////////////////	
//echo "<Strong>La liste des inactifs: </Strong><br>";
$inactifs = array_diff($inscrits, $actifs);
foreach($inactifs as $element)
		{
		//echo $element . '<br />'; 
		}

		$table = new html_table();
		$table->head = array("Prenom", "Nom", "Mail");
		foreach($inactifs as $element)
		{
		$reponse3 = $bdd->query('SELECT firstname,lastname,email FROM mdl_user WHERE id=\''.$element.'\'');
			while ($donnees3 = $reponse3->fetch())
			{
				$table->data[] = array($donnees3['firstname'],$donnees3['lastname'], $donnees3['email']);
			}
		}
		
}
else
{
//echo "La liste des Etudiants Non actifs dans la formation est :<br>";
$actifs[0]=-1;$inscritsp[0]=-1;$inactifs[0]=-1;
// Bloc affichage des actifs dans la promotion
	$reponse3 = $bdd->query('SELECT DISTINCT(userid) FROM mdl_logstore_standard_log WHERE userid NOT IN (SELECT userid FROM mdl_role_assignments WHERE roleid!=5) AND userid NOT IN (SELECT DISTINCT(modifierid) FROM mdl_role_assignments) AND userid>0 AND timecreated>'.$begindate.' AND timecreated<'.$enddate.'');
		while ($donnees3 = $reponse3->fetch())
		{
		$actifs[]=$donnees3['userid'];
		}
		//echo "<Strong>La liste des actifs: </Strong><br>";
		foreach($actifs as $element)
		{
		//echo $element . '<br />'; 
		}
//////////////////////

// Bloc affichage des inscrits dans la promotion
$reponse = $bdd->query('SELECT id FROM mdl_enrol');
while ($donnees = $reponse->fetch())
{
$reponse2 = $bdd->query('SELECT DISTINCT(userid) FROM mdl_user_enrolments WHERE userid NOT IN (SELECT userid FROM mdl_role_assignments WHERE roleid!=5) AND enrolid=\''.$donnees['id'].'\'');
	while ($donnees2 = $reponse2->fetch())
	{
		$inscrits[]=$donnees2['userid'];
	}
}
$inscritspromo = array_unique($inscrits);
//echo "<Strong>La liste des inscrits: </Strong><br>";
foreach($inscritspromo as $element)
		{
		//echo $element . '<br />'; 
		}
		
//////////////////////	
//echo "<Strong>La liste des inactifs: </Strong><br>";
$inactifs = array_diff($inscrits, $actifs);
$inactifspromo = array_unique($inactifs);
foreach($inactifspromo as $element)
		{
		//echo $element . '<br />'; 
		}

		$table = new html_table();
		$table->head = array("Prenom", "Nom", "Mail");
		foreach($inactifspromo as $element)
		{
		$reponse3 = $bdd->query('SELECT firstname,lastname,email FROM mdl_user WHERE id=\''.$element.'\'');
			while ($donnees3 = $reponse3->fetch())
			{
				$table->data[] = array($donnees3['firstname'],$donnees3['lastname'], $donnees3['email']);
			}
		}
}
}	   
     }
    
// Les dates trasmises///////
//echo "<Strong>Les dates Transmises via le datepicker : </Strong><br>";

if($data = $mform->get_data()){
$newtimestamp = $data->lastaccesseddate;

}

if($data = $mform->get_data()){
$newtimestamp2 = $data->currentdate;

}




$mform->display();


if(!empty($table->data)){
if($courseid!=0){
echo "<Strong>La liste des Etudiants inactifs Dans ce cours est :</Strong><br>";
}
else echo "<Strong>La liste des Etudiants inactifs Dans la formation est :</Strong><br>";


  echo html_writer::table($table);
  echo $OUTPUT->single_button("index.php?export=1&cid=$data->currentdate&ld=$data->lastaccesseddate",get_string('exportcsv','report_lastaccess'));
}


///////////////////////////////////////////////////////// Liste des élèves inscrits au cours sélectionné
$table2 = new html_table();
$table2->head = array("Prenom", "Nom", "Mail");
try
{
$bdd = new PDO('mysql:host=localhost;dbname=moodle', 'root', '');
}
catch(Exception $e)
{
die('Erreur : '.$e->getMessage());
}
$courseid = $_POST[course];
if($courseid!=0)
{
echo "<Strong>La liste des Etudiants inscrits A ce cours est :</Strong><br>";
$reponse = $bdd->query('SELECT id FROM mdl_enrol WHERE courseid=\''.$_POST[course].'\'');

while ($donnees = $reponse->fetch())
{
$reponse2 = $bdd->query('SELECT DISTINCT(userid) FROM mdl_user_enrolments WHERE userid NOT IN (SELECT userid FROM mdl_role_assignments WHERE roleid!=5) AND enrolid=\''.$donnees['id'].'\'');
	while ($donnees2 = $reponse2->fetch())
	{
			$reponse3 = $bdd->query('SELECT firstname,lastname,email FROM mdl_user WHERE id=\''.$donnees2['userid'].'\'');
			while ($donnees3 = $reponse3->fetch())
			{
				$table2->data[] = array($donnees3['firstname'],$donnees3['lastname'], $donnees3['email']);
			}
	}
}
}
else
{
echo "<Strong>La liste des Etudiants inscrits A la formation est :</Strong><br>";
$reponse = $bdd->query('SELECT id FROM mdl_enrol');
while ($donnees = $reponse->fetch())
{
$reponse2 = $bdd->query('SELECT DISTINCT(userid) FROM mdl_user_enrolments WHERE userid NOT IN (SELECT userid FROM mdl_role_assignments WHERE roleid!=5) AND enrolid=\''.$donnees['id'].'\'');
	while ($donnees2 = $reponse2->fetch())
	{
		$inscrits[]=$donnees2['userid'];
	}
}
$inscritspromo = array_unique($inscrits);
foreach($inscritspromo as $element)
		{
		$reponse3 = $bdd->query('SELECT firstname,lastname,email FROM mdl_user WHERE id=\''.$element.'\'');
			while ($donnees3 = $reponse3->fetch())
			{
				$table2->data[] = array($donnees3['firstname'],$donnees3['lastname'], $donnees3['email']);
			}
		
		}


}
if(!empty($table2->data)){
  echo html_writer::table($table2);
  }
////////////////////////////////////////////////////////////////////////////////////////////////////////
echo $OUTPUT->footer();
?>