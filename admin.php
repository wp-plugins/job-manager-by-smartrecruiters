<?php

	//ta funkcja ooglnie mowi ze bedziemy dodawac cos do admina - tzn ja to tak zrozumialem
	add_action('admin_menu', 'smartecruiters_config_page');

	//dodanie pozycji w menu plugins
	function smartecruiters_config_page(){
		if ( function_exists('add_submenu_page') ){
			add_submenu_page('plugins.php', 'Job Manager by SmartRecruiters', 'Job Manager by SmartRecruiters', 'manage_options', 'smartrecruiters-config', 'smartrecruiters_config');
		}
	}
	
	//wyswietlanie strony z konfiguracja pluginu
	function smartrecruiters_config(){
		
		$is_connected = get_option('sr_connected');
		
		if($is_connected){
			//aplikacja juz polaczona, trzeba pokazac mozliwosc odlaczenia
			
			//jesli kliknieto disconnect
			if(isset($_POST['sr_disconnect']) && $_POST['sr_disconnect']){
					//rozlaczyc i odswiezyc strone zeby pokazac formularz logowania
					update_option('sr_connected', 0);
					update_option('sr_company', null);
					$location = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
					echo '<script>window.location = "'.$location.'";</script>';
			}
			
			//pobieramy joby zeby meic departmenty i lokacje
			$company_name = get_option('srcompany');
			$url = 'https://www.smartrecruiters.com/cgi-bin/WebObjects/share.woa/wa/careersite?wpp_company='.$company_name;
		
			//pobieramy joby
			$get_jobs = @file_get_contents($url);
			$xml = @simplexml_load_string($get_jobs, 'SimpleXMLElement', LIBXML_NOCDATA);
			$jobs = json_decode(json_encode($xml), true);
			
			//widok konfigurancji i odlaczania
			include('configure.php');
		
		}else{
			
			if(!empty($_GET['srcompany']) && $_GET['srcompany']){
				update_option('sr_connected', 1);
				update_option('srcompany', $_GET['srcompany']);
				$location = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
				echo '<script>window.location = "'.$location.'";</script>';
			}
			
			
			//widok laczenia/logowania
			include('connect.php');
		}
	}
	
?>