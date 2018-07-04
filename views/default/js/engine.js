$(document).ready(function(){

	$('.select_region').change(function(){
		$this = $(this);
		$('.box_region2').addClass('ukryty').find('select').attr("disabled", true);
		var id = $this.val();
		$('#region2_'+id).removeClass('ukryty').find('select').prop("disabled", false);
	})
	
	$('.gwiazdka').click(function(){
		$this = $(this);
		$.ajax({
			url:'php/funkcje_ajax.php',
			type:"POST",
			data: {'akcja': 'ocena_profilu','ocena': $this.data('ocena'), 'id': $this.data('id')},
			dataType:"json",
			success: function(data){
				if(data){
					$('.gwiazdka_stala').removeClass('gwiazdka_stala gwiazdka_stala_temp');
					$(".gwiazdka").slice(0,Math.round(data.srednia)).addClass('gwiazdka_stala gwiazdka_stala_temp');
					$('#srednia_ocen').text(data.srednia);
					$('#ilosc_ocen').text(data.ilosc);
				}else{
					alert('Oddałeś już głos na tego użytkownika!');
				}
			}
		})
	})
	
	$('#gwiazdki').hover(function(){
		$(this).find('.gwiazdka').removeClass('gwiazdka_stala');
	},function(){
		$(this).find('.gwiazdka_stala_temp').addClass('gwiazdka_stala');
	})
	
	$('.gwiazdka').hover(function(){
		$(this).addClass('gwiazdka_hover').prevAll().addClass('gwiazdka_hover');
	},function(){
		$('.gwiazdka_hover').removeClass('gwiazdka_hover');
	})
	
	$("#wyszukiwarka form").submit(function() {
		$(this).find(":input").not('[name="szukaj"]').filter(function () {
			return !this.value;
		}).attr("disabled", true);
		return true;
	});

	$('.pokaz_ukryte_dane').on("click", function(){
		$this = $(this);
		$this.text($this.data('dane')).contents().unwrap();
		return false;
	})
	
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
		var minDate = $this.attr('min'), maxDate = $this.attr('max'), changeMonth = $this.data('changemonth'), changeYear = $this.data('changeyear');
        $this.datepicker({minDate:minDate,maxDate:maxDate, changeMonth: changeMonth,  changeYear: changeYear}); 
    });
	
	$('#link_przewin').on("click", function(){
		$('html, body').stop().animate({scrollTop: 0}, 300);
		$(this).blur();
		return false;
	})
	
	$('.nieaktywny').click(function(){
		return false;
	})
	
	$('.infobox .zamknij').click(function(){
		$(this).parents('.infobox').slideUp();
		return false;
	})
	
	$(".ajax_confirm").not('nieaktywny').click(function(){
		$this = $(this);
		var is_confirmed = confirm($this.data('title'));
		if (is_confirmed) {
			var mydata = $this.data();
			$.post('php/funkcje_ajax.php', {
				'data' : mydata,
				'send': 'ok'}, 
				function(data) {
					if($this.data('redirect')){
						window.location.href = $this.data('redirect');
					}else{
						window.location.href = window.location;
					}
			});
		}
        return false;   
    });

	$("#pokaz_menu_kategorie").click(function(){
		$('#kategorie ul').slideToggle();
        return false;   
    });
	
	$('#ogloszenie_owl').owlCarousel({items : 5,autoPlay:true, stopOnHover:true, itemsCustom: [[0, 2], [500, 3], [650, 4], [800, 5], [950, 6],[1000, 4],[1300, 5]]});
	
	$('#slider_dol').owlCarousel({items : 4,autoPlay:true, stopOnHover:true, pagination: false, navigation:true, navigationText: ["wstecz","dalej"], itemsCustom: [[0, 1], [700, 2], [1000, 3], [1200, 4]]});
	
	$('#slider').owlCarousel({singleItem : true,autoPlay:true, stopOnHover:true, pagination:false,autoHeight:true});

	$("#facebook2_2").hover(function(){$(this).stop(true,false).animate({right: "0px"}, 500 );},
		function(){$(this).stop(true,false).animate({right: "-304px"}, 500 );});
		
	$("#newsletter_panel").hover(function(){$(this).stop(true,false).animate({left: "0px"}, 500 );},
		function(){$(this).stop(true,false).animate({left: "-304px"}, 500 );});

	$(window).scroll(function(){
		if($(this).scrollTop()>150){      		
			$('#link_przewin').removeClass('schowany');
		}else{
			$('#link_przewin').addClass('schowany');
		}
	})
	
	$(window).resize(function(){
		if($(this).width()>1000){      		
			$('#kategorie ul').slideDown();
		}
	})
	
	$('.checkbox_ukryj').change(function() {
		var $this = $(this);
		if($this.is(":checked")){
			$('.ukryj_'+$this.data('cel')).slideUp();
		}else{
			$('.ukryj_'+$this.data('cel')).slideDown();
		}
	})
	
	$(".dialog").dialog({
		modal: true,
		open: function() {
			$(this).dialog("option", "title", $(this).data('title'));
		},
		autoOpen:false
	});
	$('.dialog input[type=reset]').click(function(){
		$(this).parents('.dialog').dialog( "close" );
	})
	
	$('.open_dialog').click(function(){
		$($(this).attr('href')).dialog('open');
		return false;
	})
	
	if (window.location.href.indexOf('#_=_') > 0) {
		window.location = window.location.href.replace(/#.*/, '');
	}
	
	$('#pp_statystyki').submit(function(){
		var $this = $(this), $pp_submit = $this.find('input[type=submit]'), $pp_statystyki_table = $this.find('table');
		$pp_submit.prop( "disabled", true );
		$.ajax({
			type: 'POST', 
			url: 'php/pp_ajax.php',
			dataType :"json",
			data: {
				'akcja' : 'pp_statystyki',
				'data_od' : $this.find('input[name=data_od]').val(),
				'data_do' : $this.find('input[name=data_do]').val()
			},
			success: function(data) {
				$pp_statystyki_table.find("tr").not(':first').not(':last').remove();
				if(data.ogloszenia){
					$.each(data.ogloszenia, function(i, item) {
						if(item.ogloszenie){
							pp_ogloszenie = '<a href="'+item.ogloszenie.id+','+item.ogloszenie.prosty_tytul+'" target="_blank" title="Ogłoszenie: '+item.ogloszenie.tytul+'">'+item.ogloszenie.tytul+'</a>';
						}else{
							pp_ogloszenie = item.id_ogloszenia;
						}
						if(item.login){
							pp_login = '<a href="profil,'+item.login+'" target="_blank" title="Profil: '+item.login+'">'+item.login+'</a>';
						}else{
							pp_login = item.id_uzytkownika;
						}
						$pp_statystyki_table.find('tr:first-child').after('<tr><td>'+pp_ogloszenie+'</td><td>'+pp_login+'</td><td class="right"><b>'+item.prowizja+'</b></td><td>'+item.data+'</td></tr>');
					});
					$this.find('#pp_statystyki_suma').text(data.suma);
				}else{
					$pp_statystyki_table.find('tr:first-child').after('<tr><td colspan="4" class="center" style="color: #d35555;">Nic nie znaleziono</td></tr>');
					$this.find('#pp_statystyki_suma').text('0,00');
				}
				$pp_submit.prop( "disabled", false );
			}
		})
		return false
	})
	$('#pp_statystyki').submit();
});

$(document).on('click', '#wyslij_mail', function(){

	var regex = /^[a-zA-Z0-9._-]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,4}$/;
	var validate = true;
	var $email = $('#email'), $imie = $('#imie'), $wiadomosc = $('#wiadomosc');
	
	$('.red').html('');
	
	if($imie.val() == ''){ 
		validate = false;
		$('#info_imie').html('Podaj swoje imię.');	
	}
	if($email.val() == ''){ 
		validate = false;
		$('#info_email').html('Podaj swój adres e-mail.');
	}else if(regex.test($email.val()) === false){
		validate = false;
		$('#info_email').html('Podaj poprawny adres e-mail.');
	}
	if($wiadomosc.val() == ''){ 
		validate = false;
		$('#info_wiadomosc').html('Wpisz wiadomość.');
	}
	
	if(typeof(id_ogloszenia) != "undefined" && id_ogloszenia !== null) {
		kontakt_id_ogloszenia = id_ogloszenia;
		kontakt_id_profilu = 0;
		cel = 'mail_ogloszenie.php';
		temat = '';
	}else if(typeof(id_profilu) != "undefined" && id_profilu !== null) {
		kontakt_id_ogloszenia = 0;
		kontakt_id_profilu = id_profilu;
		cel = 'mail_profil.php';
		temat = '';
	}else{
		kontakt_id_ogloszenia = kontakt_id_profilu = 0;
		cel = 'mail.php';
		temat = $('#temat').val();
	}
	
	if(validate == true){
		$('#wyslij_mail').attr('disabled','disabled');
		
		var m_data = new FormData();    
		m_data.append('akcja', 'formularz_kontaktowy');
        m_data.append('imie', $imie.val());
        m_data.append('email', $email.val());
        m_data.append('wiadomosc', $wiadomosc.val());
        m_data.append('id_ogloszenia', kontakt_id_ogloszenia);
		m_data.append('id_profilu', kontakt_id_profilu);
		m_data.append('temat', temat);
		if($('#zalacznik').length && $('#zalacznik')[0].files[0]){
			m_data.append('zalacznik', $('#zalacznik')[0].files[0]);
			$('#info_ok').html('Czekaj... Trwa przesyłanie załącznika.');
		}
		
		$.ajax({
			url: 'php/'+cel,
			data: m_data,
			contentType: false,
			processData: false,
			type: 'POST',
			success: function(data){
				if(data){
					$imie.val('');
					$wiadomosc.val('');
					$('#zalacznik').val('');
					$('#info_ok').html('Wiadomość została poprawnie wysłana.');
				}else{
					$('#info_ok').html('Błąd w wysyłaniu wiadomości...');
				}
				$('#wyslij_mail').removeAttr('disabled');
			}
		})
		
	}
});

$(document).on('click', '#wyslij_naduzycie', function(){

	var regex = /^[a-zA-Z0-9._-]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,4}$/;
	var validate = true;
	var email = $('#zglos_email').val(), wiadomosc = $('#zglos_wiadomosc').val();
	
	$('.red').html('');
	
	if(email == ''){ 
		validate = false;
		$('#zglos_p_email').html('Podaj swój adres e-mail');
	}else if(regex.test(email) === false){
		validate = false;
		$('#zglos_p_email').html('Podaj poprawny adres e-mail');
	}
	if(wiadomosc == ''){ 
		validate = false;
		$('#zglos_p_wiadomosc').html('Wpisz wiadomość');
	}
	
	if(validate == true){
		$('#wyslij_naduzycie').attr('disabled','disabled');
		$.post('php/mail_ogloszenie.php', {
			'akcja' : 'zglos_naduzycie',
			'email' : email,
			'wiadomosc' : wiadomosc,
			'id_ogloszenia' : id_ogloszenia,
			'send': 'ok'}, 
			function(data) {
				if(data){
					$('#zglos_email').val('');
					$('#zglos_wiadomosc').val('');
					$('#zglos_ok').html('Wiadomość została poprawnie wysłana');
				}else{
					$('#zglos_ok').html('Błąd w wysyłaniu wiadomości...');
				}
				$('#wyslij_naduzycie').removeAttr('disabled');
		});
	}
});

$(document).on('click', '#wyslij_powiadomienie', function(){

	var regex = /^[a-zA-Z0-9._-]+@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,4}$/;
	var validate = true;
	var email = $('#powiadom_email').val(), wiadomosc = $('#powiadom_wiadomosc').val();
	
	$('.red').html('');
	
	if(email == ''){ 
		validate = false;
		$('#powiadom_p_email').html('Podaj adres e-mail');
	}else if(regex.test(email) === false){
		validate = false;
		$('#powiadom_p_email').html('Podaj poprawny adres e-mail');
	}
	if(wiadomosc == ''){ 
		validate = false;
		$('#powiadom_p_wiadomosc').html('Wpisz wiadomość');
	}
	
	if(validate == true){
		$('#wyslij_powiadomienie').attr('disabled','disabled');
		$.post('php/mail_ogloszenie.php', {
			'akcja' : 'wyslij_powiadomienie',
			'email' : email,
			'wiadomosc' : wiadomosc,
			'id_ogloszenia' : id_ogloszenia,
			'send': 'ok'}, 
			function(data) {
				if(data){
					$('#powiadom_email').val('');
					$('#powiadom_wiadomosc').val('');
					$('#powiadom_ok').html('Wiadomość została poprawnie wysłana');
				}else{
					$('#powiadom_ok').html('Błąd w wysyłaniu wiadomości...');
				}
				$('#wyslij_powiadomienie').removeAttr('disabled');
		});
	}
});