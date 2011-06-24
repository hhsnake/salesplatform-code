function edittaskscript($){

	function NumberBox(element){
		var elementId = element.attr("id");
		var boxId = '#'+elementId+'-number-box';
		var str = "";
		for(var i = 1; i <= 30; i++){
			str += '<a href="#'+i+'" class="box_cel">'+(i < 10? ("0"+i) : i)+'</a> ';
			if(!(i % 5)){
				str+="<br>";
			}
		}
		element.after('<div id="'+elementId+'-number-box" style="display:none;" class="box">'+str+'</div>');
		element.focus(function(){
			var pos = element.position();
			$(boxId).css('display', 'block');
			$(boxId).css({
				position: 'absolute',
				top: (pos.top+25)+'px'
			});
		});

		element.blur(function(){
			setTimeout(function(){$(boxId).css('display', 'none');},500);
		});

		$('.box_cel').click(function(){
			element.attr('value', parseInt($(this).text(), 10));
		});
	}



	$(document).ready(function(){
		validator = new VTFieldValidator($('#new_task_form'));
		validator.mandatoryFields = ['summary'];
		$('.time_field').timepicker();
		NumberBox($('#select_date_days'));
        //UI to set the date for executing the task.
    	$('#check_select_date').click(function(){
    	    if($(this).attr('checked')){
    	        $('#select_date').css('display', 'block');
                // SalesPlatform.ru begin
    	        $('#select_period').css('display', 'none');
                $('#check_select_period').attr('checked', '');
                // SalesPlatform.ru end
    	    }else{
    	        $('#select_date').css('display', 'none');
    	    }
    	});
        // SalesPlatform.ru begin
    	$('#check_select_period').click(function(){
    	    if($(this).attr('checked')){
    	        $('#select_period').css('display', 'block');
    	        $('#select_date').css('display', 'none');
                $('#check_select_date').attr('checked', '');
    	    }else{
    	        $('#select_period').css('display', 'none');
    	    }
    	});
        // SalesPlatform.ru end
			$('#edittask_cancel_button').click(function(){
				window.location=returnUrl;
			});
    });
}