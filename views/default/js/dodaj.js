$(document).ready(function () {

	$(".policz_znaki").on('keyup', function(event) {
		var $this = $(this), count = $this.val().length;
		$this.parent().find('.licznik_znakow span').text(count);
	})
	$(".policz_znaki").keyup();
	
	$("#podglad_zdjec_inside" ).sortable({});
	
	$('.select_kategoria').change(function(){
		$this = $(this);
		$this.nextAll().addClass('ukryty').attr("disabled", true).val('');
		var id = $this.val();
		$('#podkategoria_'+id).removeClass('ukryty').prop("disabled", false);
		$('.fieldset_opcja_dodatkowa').not('.fieldset_opcja_dodatkowa_wszystkie').not('.fieldset_opcja_dodatkowa_'+id).slideUp().find('input, select').attr("disabled", true);
		$('.fieldset_opcja_dodatkowa_'+id).slideDown().find('input, select').attr("disabled", false);
	})
	
	$('#button_wybierz_zdjecia').click(function(){
		$("#input_wybierz_zdjecia").click();
	})
	$("#input_wybierz_zdjecia").change(function (){
		$this = $(this);
		var ile_obrazkow = $this[0].files.length;
		if(ile_obrazkow){
			$('#podglad_laduje').removeClass('ukryty');
			$('#zdjecia_info').html('');
			ile_dodano = $('.zdjecie_box').length;
			$('#form_dodaj_ogloszenie input[type=submit]').prop("disabled", true); 
		
			$.each($this[0].files, function(index, value){
				if(limit_zdjec!=-1 && (index + ile_dodano >= limit_zdjec)){
					$('#zdjecia_info').html('Przekroczono limit zdjęć');
					$('#podglad_laduje').addClass('ukryty');
					$('#form_dodaj_ogloszenie input[type=submit]').prop("disabled", false); 
				}else{
					data_zdjecia =  new FormData();
					data_zdjecia.append('file', $this[0].files[index]);
					$.ajax({
						url: "php/zdjecia_ajax.php",
						type: "POST", 
						data: data_zdjecia,
						dataType :"json",
						contentType: false,
						cache: false,
						processData:false,
						success: function(data){
							if(data && data!=''){
								if(data[0]){
									$("#podglad_zdjec_inside").append('<div class="zdjecie_box"><img src="'+data[3]+'" alt="'+data[2]+'" class="zdjecie"><a href="#" title="Usuń zdjęcie" class="usun_zdjecie"></a><input type="hidden" name="zdjecia[]" value="'+data[1]+'"></div>');
								}else{
									$('#zdjecia_info').html(data[1]);
								}
							}
							if(index===(ile_obrazkow-1)){
								$('#podglad_laduje').addClass('ukryty');
								$('#form_dodaj_ogloszenie input[type=submit]').prop("disabled", false); 
							}
						}
					});
				}
			});
		}
		$this.val('');
	});

	$('#form_dodaj_ogloszenie').submit(function(){
		$last = $('select[name=kategoria]:enabled').last();
		if($last.val()==''){
			$last.attr("disabled", true);
		}
	})
});

$(document).on('click', '.usun_zdjecie', function(){
	$(this).parents('.zdjecie_box').remove();
	$('#zdjecia_info').html('');
	return false;
})