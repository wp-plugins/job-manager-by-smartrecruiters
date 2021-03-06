<link rel="stylesheet" href="<?php echo plugins_url('style.css', __FILE__); ?>" type="text/css" />

<?php if(isset($jobs['jobs']['job']) && count($jobs['jobs']['job']) && $jobs['jobs']['job']): ?>

    <?php
        if(!function_exists('get_job_url')) {
            function get_job_url ( $code ) {
                $url = $_SERVER["REQUEST_URI"];

                if ( strpos($url, 'srjob') )
                    $url = substr($url, 0, strpos($url, 'srjob') );

                if(get_option('permalink_structure') == '')
                    if(strpos($url, '?')) $url .= '&srjob='; else $url .= '?srjob=';
                else
                    if(substr($url, -1) == '/') $url .= 'srjob/'; else $url .= '/srjob/';
                return $url.$code;
            }
        }
		
		if(!function_exists('print_job_location')) {
			function print_job_location($location) {
				echo $location['city']; 
				if(isset($location['country']) && $location['country'] && $location['country']->iso ) {
					if (isset($location['region']) && $location['region'] && $location['country']->iso == "US") {
						echo ', '.$location['region'];
					} else {
						echo ', '.$location['country'];
					}
					 
				}
				
					
			}
		}
    ?>

	<!-- SmartRecruiters Jobs List -->
	
	<ul class="smartrecruitersJobList">
		<?php if(isset($jobs['jobs']['job']['@attributes'])): ?>
		
			<li class="smartrecruitersJobListElement">
                <h2 class="smartrecruitersJobListElementHeader">
                    <a href="<?php echo get_job_url($jobs['jobs']['job']['@attributes']['code']); ?>" title="<?php echo $jobs['jobs']['job']['title']; ?>"><?php echo $jobs['jobs']['job']['title']; ?></a>
				</h2>
								
				<?php if(isset($jobs['jobs']['job']['job-location']['city']) && $jobs['jobs']['job']['job-location']['city']): ?>
					<ul class="smartrecruitersJobListDetails">
						<li class="smartrecruitersJobListDetailsElement">
							<?php 
								print_job_location($jobs['jobs']['job']['job-location']);
							?>
						</li>
					</ul>
				<?php endif; ?>
			
			</li>
			
		<?php else: ?>
		
			<?php foreach($jobs['jobs']['job'] as $job): ?>

				<?php if((count($locations) && in_array($job['job-location']['city'], $locations)) || !count($locations)): ?>
					
					<?php if((count($departments) && in_array($job['department'], $departments)) || !count($departments)): ?>
				
                        <li class="smartrecruitersJobListElement">

                            <h2 class="smartrecruitersJobListElementHeader">
                                <a href="<?php echo get_job_url($job['@attributes']['code']); ?>" title="<?php echo $job['title']; ?>"><?php echo $job['title']; ?></a>
                            </h2>

                            <?php if(isset($job['job-location']['city']) && $job['job-location']['city']): ?>
                                <ul class="smartrecruitersJobListDetails">
                                    <li class="smartrecruitersJobListDetailsElement"><?php 
										print_job_location($job['job-location']);
									?></li>
                                </ul>
                            <?php endif; ?>

                        </li>
					
					<?php endif; ?>
					
				<?php endif; ?>
				
			<?php endforeach; ?>
			
		<?php endif; ?>
		
	</ul>
	
	<!-- /SmartRecruiters Jobs List -->
	
<?php endif; ?>
