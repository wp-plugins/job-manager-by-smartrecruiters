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
		
		<?php if(@ini_get('allow_url_fopen') == false): ?>
		<p style="color: red;">Plugin requires enable allow_url_fopen option in PHP server configuration. If you can't enable it, we recommend use our <a href="http://www.smartrecruiters.com/static/widgets/" target="_blank">Job Widget</a>.</p>
		<?php endif; ?>
	
	</form>
	
	<h2>Hashcode generator</h2>
	
	<div id="string_generator">
	
		<div class="StringGeneratorSection">
		
			<h3 class="StringGeneratorSectionHeader">Departments</h3>
			
			
			<select class="StringGeneratorSectionSelect" id="string_generator_department" multiple="multiple" name="department">
				<option value="" selected="selected">all</option>
				
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
					
					<?php foreach($departments as $department): ?>
					
						<option value="<?php echo $department; ?>"><?php echo $department; ?></option>
						
					<?php endforeach; ?>
					
				<?php endif; ?>
				
			</select>
			
		</div>
	
		<div class="StringGeneratorSection">
		
			<h3 class="StringGeneratorSectionHeader">Locations</h3>
			
			<select class="StringGeneratorSectionSelect" id="string_generator_location" multiple="multiple" name="location">
				<option value="" selected="selected">all</option>
				
				<?php if(isset($jobs['jobs']['job']) && count($jobs['jobs']['job']) && $jobs['jobs']['job']): ?>
					
					<?php
						$locations = array();
						
						if(isset($jobs['jobs']['job']['@attributes'])){
							$locations[] = $jobs['jobs']['job']['job-location']['city'];
						}else{
							foreach($jobs['jobs']['job'] as $job){
							
								if(isset($job['job-location']['city']) && !is_null($job['job-location']['city'])){
									if(!in_array($job['job-location']['city'], $locations)){
										$locations[] = $job['job-location']['city'];
									}
								}
							}
						}
						
						sort($locations);
					?>
				
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

    <br/>

    <form method="post" action="plugins.php?page=smartrecruiters-config">

        <p>Choose template for Job Detail Page:</p>

        <select class="selectJobDetailTemplate" name="template">
            <option value="">Default</option>
            <?php foreach( $available_templates as $name => $file ) { ?>
                <option value="<?php echo $file; ?>" <?php echo $active_template==$file ? 'selected="selected"' : '' ?>><?php echo $name; ?></option>
            <?php } ?>
        </select>

        <input type="submit" class="button-primary" value="<?php _e('Save') ?>" />

    </form>
	
</div>