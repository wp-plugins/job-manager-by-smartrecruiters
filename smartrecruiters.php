<?php
/*
Plugin Name: Job Manager by SmartRecruiters
Plugin URI: http://dev.smartrecruiters.com
Description: The easiest way to post jobs and manage candidates in a WordPress site. Connects with SmartRecruiters, your workspace to find and hire great people.
Version: 1.1.6
Author: SmartRecruiters
Author URI: http://smartrecruiters.com
License: MIT
*/

if(is_admin()){
	require_once dirname( __FILE__ ) . '/admin.php';
}

function get_jobs($params = '', $guid, $slug){
	
	if (!class_exists('SrCountry')) {
		class SrCountry {
			public $text;
			public $iso;
			public function __construct($text, $iso) {
				$this->text = $text;
				$this->iso = $iso;
			}
		
			public function __toString() {
				return $this->text;
			}
		}
	}

	// company name taken from the WP database
	$company_name = get_option('srcompany');
	
	$url = 'http://www.smartrecruiters.com/cgi-bin/WebObjects/share.woa/wa/careersite?wpp_company='.$company_name.'&installed_url=http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

	//getting jobs
	$get_jobs = @file_get_contents($url); // @ is switching off error reporting
	$xml = @simplexml_load_string($get_jobs, 'SimpleXMLElement', LIBXML_NOCDATA);

	//konwertujemy to na jakis sensowny obiekt
	$jobs = json_decode(json_encode($xml), true);

	
	//assign country code info (it is lost during json_decode)
	if(isset($jobs['jobs']['job']['@attributes'])) {
		//single element
		$country = $xml->jobs->job[0]->{"job-location"}->country;
		$iso = (string)$country->attributes()["iso"];
		$countryName = (string)$country;
		$jobs["jobs"]['job']["job-location"]["country"] = new SrCountry($countryName, $iso);
	} else {
		//multiple elements
		if ($xml->jobs->job && $jobs["jobs"]['job']) {		
			for ($i = 0; $i < count($jobs["jobs"]["job"]); $i++) {
				$country = $xml->jobs->job[$i]->{"job-location"}->country;
				$iso = (string)$country->attributes()["iso"];
				$countryName = (string)$country;
				$jobs["jobs"]['job'][$i]["job-location"]["country"] = new SrCountry($countryName, $iso);
			}	
		}	
	}
	
	//add iso informations to countries (lost after json_encode)	
	
	//trzeba wziac pod uwage parametry takei jak location i departments
	$locations = array();
	$departments = array();
	
	if($params != ''){
		$tmp_params = explode(';', $params);
		
		foreach($tmp_params as $tmp_param){
			$explode_tmp = explode('=', $tmp_param);
			
			if($explode_tmp[0] == 'department'){
				$departments = explode(',', $explode_tmp[1]);
			}
			
			if($explode_tmp[0] == 'location'){
				$locations = explode(',', $explode_tmp[1]);
			}
		}
		
	}
	
	//teraz  dolaczymy plik list.php w nim "podstawia" sie wszystkei zmienne i wynik teg owszystkeigo wrzucamy do buffora - gdybysm ynei wrzucali do buffora to odrazu by nam wyswietlilo liste czego nie chcemy
	ob_start();
	
	include('list.php');
	
	$list_view = ob_get_contents();
	
	ob_end_clean();
	
	//izwracamy to co siedzi w bufforze
	return $list_view;
	
}

function replace_job_list($content){
	global $post;
	
	$is_connected = get_option('sr_connected');
	
	if($is_connected){
	
		$pattern = '/\#smartrecruiters\_job\_list[a-zA-Z0-9,\s\:\=\&amp;]*/';
		$search_string = preg_match_all($pattern, $content, $matches);
		
		//znaleziono przynajmniej jeden nasz string teraz trzeba go dzielic i dzielic az otrzymamy tablice parametrow np $params['location'] = cracow
		if($search_string){
			
			$new_content = $content;
			
			//lecimy po wszystkich znalezionych stringach
			foreach($matches[0] as $match){
			
				$base_match = $match;
				
				//tu pozniej wrzucimy nasze parametry - jesli ebda podane
				$params = '';
				
				//oddzielamy nasz glowny string #smartrecruiters_job_list od parametrow
				$match = explode(':', $match);
				
				if(count($match) > 1){
					//podano jakeis parametry
					
					$params = $match[1];
				}
				
				// podmieniamy nasze stringu w dotychczasowym contencie na job liste
				// $params - list of parameters (departments, locations)
				// guid - link to post (ugly url)
				// $post - link slug - link to post (nice url)
				$new_content = str_replace($base_match, get_jobs($params, $post -> guid, $post -> post_name), $new_content);
			}
			
			return $new_content;
			
		}else{
			return $content;
		}
	}else{
		return $content;
	}
	
}

function show_job(){
	global $post, $wp_query, $posts, $job_id;
	
	$pattern = '/srjob\/[0-9]{4,}/';
	if(preg_match_all($pattern, $_SERVER["REQUEST_URI"], $matches)){
		
		$guid=substr ($_SERVER["REQUEST_URI"],0,-1*strlen($matches[0][0]));
		
		if(isset($matches[0][0])){
	
			$match = explode('/', $matches[0][0]);			
			$job_id = $match[1];
		}
		
	}elseif(isset($_GET['srjob']) && $_GET['srjob']){
		$guid=$post -> guid;
		$job_id = $_GET['srjob'];
		
	}
	
	if(isset($job_id)){
	
		//nazwa firmy pobrana z bazy wp
		$company_name = get_option('srcompany');
	
		$url = 'http://www.smartrecruiters.com/cgi-bin/WebObjects/share.woa/wa/careersite?wpp_company='.$company_name .'&posting='.$job_id.'&installed_url=http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		
		$get_job = @file_get_contents($url);
		
		$xml = @simplexml_load_string($get_job, 'SimpleXMLElement', LIBXML_NOCDATA);
		
		$job = json_decode(json_encode($xml), true);

		if(isset($job['jobs']) && count($job['jobs'])){
		
		
	       //wp_reset_query();
	     
			$wp_query -> is_404 = false;
			$wp_query -> is_single = true;
			$wp_query -> post_count = 1;

            if ( !is_object( $post ) ) { $post = new stdClass(); }
			$post -> post_title = $job['jobs']['job']['title'];
			$post -> post_content =
                '<div class="smartrecruitersJobDetails">' .
                    '<div class="smartrecruitersBackLink">' . '<a href="'.$guid.'">&laquo; back to jobs list</a>' . '</div>' .
                    '<div class="smartrecruitersCompanyDescription smartrecruitersDescriptionBlock">' . $job['jobs']['job']['full-description']['company_description'] . '</div>'.
                    '<div class="smartrecruitersJobDescription smartrecruitersDescriptionBlock">' . $job['jobs']['job']['full-description']['job_description'] . '</div>'.
                    '<div class="smartrecruitersJobRequirements smartrecruitersDescriptionBlock">' . $job['jobs']['job']['full-description']['job_requirements'] . '</div>'.
			        '<div class="smartrecruitersApplyLink">' . '<a href="'.$job['jobs']['job']['apply-url'].'" target="_blank">Apply</a>' . '</div>' .
                '</div>';
	
			$post -> comment_status = 'close';
			$posts[0]=$post;

            $active_template = get_option('sr_jobDetailsPageTemplate');
            if ( $active_template ) {
                $tpl_file = TEMPLATEPATH . "/" . $active_template;
                if ( file_exists($tpl_file) ) {
                    include($tpl_file);
                    exit;
                }else{
                    return;
                }
            }else{
                return;
            }

		}
	
	}
	
}



add_filter( 'the_content', 'replace_job_list');
add_filter( 'widget_text', 'replace_job_list');
add_action( 'template_redirect', 'show_job');


?>