$(function(){

	var sr_string_prefix = '#smartrecruiters_job_list';
	
	$('.StringGeneratorSectionSelect').change(function(){
		
		var new_sr_string = sr_string_prefix;
		
		var selects = $('.StringGeneratorSectionSelect');
		
		var is_first = true;
		
		for(var i = 0; i < selects.length; i++){
			
			var key = $(selects[i]).attr('name');
			var value = $(selects[i]).val();
			
			if(value != null && value != ''){
				
				var select_string = key+'='+value;
				
				if(!is_first){
					select_string = '&'+select_string;
				}else{
					select_string = ':'+select_string;
				}
				
				is_first = false;
				
				new_sr_string += select_string;
			}
		}
		
		$('#string_generator_result').val(new_sr_string);
		
	});
	
});