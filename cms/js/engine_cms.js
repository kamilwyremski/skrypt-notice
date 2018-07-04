$(document).ready(function(){
	
	/* globalne funkcje cms-a */
	
	$('.nieaktywny, .nieaktywna').click(function(){
		return false;
	})
	
	$(".ajax").not('.nieaktywna, .nieaktywny').click(function(){
		var mydata = $(this).data();
		$.post('php/funkcje_ajax.php', {
			'data' : mydata,
			'send': 'ok'}, 
			function() {
				window.location.href = window.location;
		});
        return false;   
    });
	
	$(".ajax_confirm").not('nieaktywny').click(function(){
		$this = $(this);
		var is_confirmed = confirm($this.data('title'));
		if (is_confirmed) {
			var mydata = $this.data();
			$.post('php/funkcje_ajax.php', {
				'data' : mydata,
				'send': 'ok'}, 
				function(data) {
					window.location.href = window.location;
			});
		}
        return false;   
    });
	
	$('#menu').slimmenu({resizeWidth: '750',collapserTitle: 'Menu',animSpeed:'medium',indentChildren: true});
	
	$(".dialog").dialog({
		modal: true,
		open: function() {
			$(this).dialog("option", "title", $(this).data('title'));
		},
		autoOpen:false,
		buttons: [{
			text: "OK",
			click: function(){$(this).find("[type=submit]").click()},
			type: "submit"
		},{
			text: "ANULUJ",
			click: function() {
				$(this).find("[type=reset]").click()
				$(this).dialog( "close" );
			}
		}]
	});
	
	$('.open_dialog').click(function(){
		$($(this).attr('href')).dialog('open');
		return false;
	})
	/* funkcje dla tej strony */
	
	$.datepicker.regional['pl'] = {
        closeText: 'Zamknij',
        prevText: '&#x3c;Poprzedni',
        nextText: 'Następny&#x3e;',
        currentText: 'Dziś',
        monthNames: ['Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec',
        'Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'],
        monthNamesShort: ['Sty','Lu','Mar','Kw','Maj','Cze',
        'Lip','Sie','Wrz','Pa','Lis','Gru'],
        dayNames: ['Niedziela','Poniedziałek','Wtorek','Środa','Czwartek','Piątek','Sobota'],
        dayNamesShort: ['Nie','Pn','Wt','Śr','Czw','Pt','So'],
        dayNamesMin: ['N','Pn','Wt','Śr','Cz','Pt','So'],
        weekHeader: 'Tydz',
        dateFormat: 'yy-mm-dd',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['pl']);
	$('input[type=date]').each( function() {
        var $this = $(this);
		var minDate = $this.attr('min'), maxDate = $this.attr('max');
        $this.datepicker({minDate:minDate,maxDate:maxDate}); 
    });
	
	$('.select_kategoria').change(function(){
		$this = $(this);
		$this.nextAll().filter('select').addClass('ukryty').attr("disabled", true);
		$this.parents('.dialog').find('.podkategoria_'+$this.val()).removeClass('ukryty').prop("disabled", false);;
	})
	
	$('.zaznacz_checkboxy').click(function(){
		$this = $(this);
		if ($this.is(':checked')) {
			$this.parents('.parent_zaznacz_checkboxy').find('input[type=checkbox]').prop('checked', true);
		}else{
			$this.parents('.parent_zaznacz_checkboxy').find('input[type=checkbox]').prop('checked', false);
		}
	}) 
	
	$('.opcje_dodatkowe_select').change(function(){
		$this = $(this);
		if($this.val()=='select'){
			$this.parents('form').find('.opcje_dodatkowe_label').removeClass('ukryty').find('textarea').attr("disabled", false);
		}else{
			$this.parents('form').find('.opcje_dodatkowe_label').addClass('ukryty').find('textarea').attr("disabled", true);
		}
	})
	
	$('.opcje_dodatkowe_wszystkie').click(function(){
		$this = $(this);
		if ($this.is(':checked')) {
			$this.parents('form').find('.parent_zaznacz_checkboxy').slideToggle().find('input[type=checkbox]').prop('checked', true);
		}else{
			$this.parents('form').find('.parent_zaznacz_checkboxy').slideToggle().find('input[type=checkbox]').prop('checked', false);
		}
	}) 
	
	$('.platnosc_checkbox').click(function(){
		$cel = $('.'+$(this).data('typ'));
		if ($(this).is(':checked')) {
			$cel.prop('required', true);
		}else{
			$cel.prop('required', false);
		}
	}) 
	
	$('.zaznacz_opcje').click(function(){
		$this = $(this);
		opcja = $this.data('opcja');
		if($this.next().data('opcja')>=opcja){
			$checkbox = $this.find('input[type=checkbox]');
			if($checkbox.is(':checked')) {
				$this.nextUntil(".opcja_"+opcja).not(".opcja_"+(opcja-1)).find('input[type=checkbox]').prop('checked', true);
			}else{
				$this.nextUntil(".opcja_"+opcja).not(".opcja_"+(opcja-1)).find('input[type=checkbox]').prop('checked', false);
			}
		}
	}) 
	
	$('.link_do_ukrytych_opcji').click(function(){
		$this = $(this);
		if($this.hasClass('active')){
			$('.ukryta_opcja_'+$this.data('id')).css('display','none');
			$this.removeClass('active').find('.inactive').hide().end().find('.active').show();
		}else{
			$('.ukryta_opcja_'+$this.data('id')).css('display','block');
			$this.addClass('active').find('.active').hide().end().find('.inactive').show();
		}
		return false;
	}) 
	
	$('.select_pp_status_realizacji').change(function(){
		$this = $(this);
		if($this.val()=='zrealizowany'){
			$this.parents('div').find('.pp_realizacja_kwota').removeClass('ukryty').find('input[type=number]').attr("disabled", false);
		}else{
			$this.parents('div').find('.pp_realizacja_kwota').addClass('ukryty').find('input[type=number]').attr("disabled", true);
		}
	})
})

$(document).on('submit', '.form_edytuj_kategorie', function(){
	$last = $(this).find('select[name=kategoria]:enabled').last();	
	if($last.val()==''){
		$last.attr("disabled", true);
	}
})

$(document).on('click', '.otworz_roxy', function(){
	$('.roxy_cel').removeClass('roxy_cel');
	$(this).find('img').addClass('roxy_cel');
	$('#roxyCustomPanel').dialog({modal:true, width:875,height:600});
	return false;
})
	
function closeCustomRoxy(){
	$roxy_cel = $('.roxy_cel');
	$("[name='"+$roxy_cel.data('roxy_name')+"']").val($roxy_cel.attr('src'));
	$('#roxyCustomPanel').dialog('close');
}
