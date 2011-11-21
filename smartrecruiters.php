<?php
/*
Plugin Name: Job Manager by SmartRecruiters
Plugin URI: http://dev.smartrecruiters.com
Description: The easiest way to post jobs and manage applicants in a WordPress site. Connects with SmartRecruiters, the free Open SaaS recruiting software.
Version: 1.0.3
Author: SmartRecruiters
Author URI: http://smartrecruiters.com
License: MIT
*/

if(is_admin()){
	require_once dirname( __FILE__ ) . '/admin.php';
}

function get_jobs($params = '', $guid, $slug){
	

	//nazwa firmy pobrana z bazy wp
	$company_name = get_option('srcompany');
	
	
	$url = 'https://www.smartrecruiters.com/cgi-bin/WebObjects/share.woa/wa/careersite?wpp_company='.$company_name;

	//pobieramy joby
	$get_jobs = @file_get_contents($url);
	
	$xml = @simplexml_load_string($get_jobs, 'SimpleXMLElement', LIBXML_NOCDATA);
	
	//konwertujemy to na jakis sensowny obiekt
	$jobs = json_decode(json_encode($xml), true);
	
	
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
	
	//teraz  dolaczymy plik list.php w nim "postawia" sie wszystkei zmienne i wynik teg owszystkeigo wrzucamy do buffora - gdybysm ynei wrzucali do buffora to odrazu by nam wyswietlilo liste czego nie chcemy
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
	
		$pattern = '/\#smartrecruiters\_job\_list[a-zA-Z0-9,\:\=\&amp;]*/';
		
		$search_string = preg_match_all($pattern, $content, $matches);
		
		//znaleziono przynajmniej jeden nasz string terz trzeba go dzielic i dzielic az otrzymamy tablice parametrow np $params['location'] = cracow
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
				
				//podmieniamy nasze stringu w dotychczasowym contencie na job liste
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
	global $post, $wp_query;
	
	if($wp_query -> is_404){
	
		$pattern = '/srjob\/[0-9]{4,}/';
		
		$search_string = preg_match_all($pattern, $_SERVER["REQUEST_URI"], $matches);
		
		if($search_string && isset($matches[0][0])){
	
			$match = explode('/', $matches[0][0]);
			
			$job_id = $match[1];
		}
		
	}elseif(isset($_GET['srjob']) && $_GET['srjob']){
		
		$job_id = $_GET['srjob'];
		
	}
	
	if(isset($job_id)){
	
		//nazwa firmy pobrana z bazy wp
		$company_name = get_option('srcompany');
	
		$url = 'https://www.smartrecruiters.com/cgi-bin/WebObjects/share.woa/wa/careersite?wpp_company=' . $company_name .'&posting=' . $job_id;
		
		$get_job = @file_get_contents($url);
		
		$xml = @simplexml_load_string($get_job, 'SimpleXMLElement', LIBXML_NOCDATA);
		$job = json_decode(json_encode($xml), true);
	
		
		if(isset($job['jobs']) && count($job['jobs'])){
	
			$wp_query -> is_404 = false;
			$wp_query -> is_single = true;
		
			//tutaj trzeba pobrac ogoszenie z danym id i zrob ten sma myk z bufforem co w przypadku listy i nadpisac dane posta
			$post -> post_title = $job['jobs']['job']['title'];
			$post -> post_content = '<div class="smartrecruitersJobDetails">'.implode('', $job['jobs']['job']['full-description']);
			$post -> post_content .= '<p><a class="smartrecruitersApplyLink" href="'.$job['jobs']['job']['apply-url'].'" target="_blank">Apply</a></div>';
	
			$post -> comment_status = 'close';
		
		}
	
	}
	
}

add_filter( 'the_content', 'replace_job_list');
add_action( 'template_redirect', 'show_job');

?>