
$(document).ready(function(){
	if($('input[name=url]').attr('value')==''){
		$('input[name=url]').attr('value',window.location.href.slice(0, -8));
	}
	$("form").submit( function () {   
		$('.red').css({'display':'none'});
		if($('input[name=haslocms]').val()!=$('input[name=haslocms_powtorz]').val()){
			$('.red').css({'display':'block'});
			return false;
		}
    });  
});

