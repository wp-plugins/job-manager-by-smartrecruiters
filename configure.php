<link rel="stylesheet" href="<?php echo plugins_url('style.css', __FILE__); ?>" type="text/css" />
<script type="text/javascript" src="<?php echo plugins_url('jquery.js', __FILE__); ?>"></script>
<script type="text/javascript" src="<?php echo plugins_url('core.js', __FILE__); ?>"></script>

<div class="wrap">

	<h2>Job Manager by SmartRecruiters configuration</h2>
	
	<form method="post" action="plugins.php?page=smartrecruiters-config"> 
		
		<input type="hidden" name="sr_disconnect" value="1" />
		<p class="submit">
			The plugin is already configured. If you want to disconnect Job Manager by SmartRecruiters plugin from your account, click <input type="submit" class="button-primary" value="<?php _e('Disconnect') ?>" />
		</p>
	
	</form>
	
	<h2>Hashcode generator</h2>
	
	<div id="string_generator">
	
		<div class="StringGeneratorSection">
		
			<h3 class="StringGeneratorSectionHeader">Departments</h3>
			
			
			<select class="StringGeneratorSectionSelect" id="string_generator_department" multiple="multiple" name="department">
			
				<?php if(isset($jobs['jobs']['job'][0]['company']['company-departments']['department']) && count($jobs['jobs']['job'][0]['company']['company-departments']['department']) && $jobs['jobs']['job'][0]['company']['company-departments']['department']): ?>
					
					<?php
					
						$departments = array();
						
						foreach($jobs['jobs']['job'][0]['company']['company-departments']['department'] as $department){
						
							if(!in_array($department, $departments)){
								$departments[] = $department;
							}
						}
						
						sort($departments);
						
					?>
					
					<option value="" selected="selected">all</option>
					
					<?php foreach($departments as $department): ?>
					
						<option value="<?php echo $department; ?>"><?php echo $department; ?></option>
						
					<?php endforeach; ?>
					
				<?php endif; ?>
				
			</select>
			
		</div>
	
		<div class="StringGeneratorSection">
		
			<h3 class="StringGeneratorSectionHeader">Locations</h3>
			
			<select class="StringGeneratorSectionSelect" id="string_generator_location" multiple="multiple" name="location">
			
				<?php if(isset($jobs['jobs']['job']) && count($jobs['jobs']['job']) && $jobs['jobs']['job']): ?>
					
					<?php
						$locations = array();
						
						foreach($jobs['jobs']['job'] as $job){
						
							if(!in_array($job['job-location']['city'], $locations)){
								$locations[] = $job['job-location']['city'];
							}
						}
						
						sort($locations);
					?>
					<option value="" selected="selected">all</option>
				
					<?php foreach($locations as $location): ?>
					
						<option value="<?php echo $location; ?>"><?php echo $location; ?></option>
						
					<?php endforeach; ?>
					
				<?php endif; ?>
			</select>
			
		</div>
		
		<div class="StringGeneratorResultSection">
			<p>Copy hashcode and paste it to post or page content. It will be replaced with jobs list.</p>
			
			<input type="text" id="string_generator_result" value="#smartrecruiters_job_list" />
		</div>
		
	</div>
	
</div>