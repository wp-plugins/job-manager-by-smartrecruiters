<link rel="stylesheet" href="<?php echo plugins_url('style.css', __FILE__); ?>" type="text/css" />

<?php if(isset($jobs['jobs']['job']) && count($jobs['jobs']['job']) && $jobs['jobs']['job']): ?>

	<!-- SmartRecruiters Jobs List -->
	
	<ul class="smartrecruitersJobList">
		<?php if(isset($jobs['jobs']['job']['@attributes'])): ?>
		
			<li class="smartrecruitersJobListElement">
				<h2 class="smartrecruitersJobListElementHeader">
					<a href="<?php echo $guid.'&srjob='.$jobs['jobs']['job']['@attributes']['code']; ?>" title="<?php echo $jobs['jobs']['job']['title']; ?>"><?php echo $jobs['jobs']['job']['title']; ?></a>
				</h2>
								
				<?php if(isset($jobs['jobs']['job']['job-location']['city']) && $jobs['jobs']['job']['job-location']['city']): ?>
					<ul class="smartrecruitersJobListDetails">
						<li class="smartrecruitersJobListDetailsElement"><?php echo $jobs['jobs']['job']['job-location']['city']; ?></li>
					</ul>
				<?php endif; ?>
			
			</li>
			
		<?php else: ?>
		
			<?php foreach($jobs['jobs']['job'] as $job): ?>

				<?php if((count($locations) && in_array($job['job-location']['city'], $locations)) || !count($locations)): ?>
					
					<?php if((count($departments) && in_array($job['department'], $departments)) || !count($departments)): ?>
				
						<li class="smartrecruitersJobListElement">
								<h2 class="smartrecruitersJobListElementHeader">
									<a href="<?php echo $guid.'&srjob='.$job['@attributes']['code']; ?>" title="<?php echo $job['title']; ?>"><?php echo $job['title']; ?></a>
								</h2>
								
								<?php if(isset($job['job-location']['city']) && $job['job-location']['city']): ?>
									<ul class="smartrecruitersJobListDetails">
										<li class="smartrecruitersJobListDetailsElement"><?php echo $job['job-location']['city']; ?></li>
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
