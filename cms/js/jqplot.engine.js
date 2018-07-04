$(document).ready(function(){
    
	function generuj_wykres() {
		var ret = null;
		$.ajax({
			type: 'POST', 
			async: false,
			url: 'php/statystyki_ajax.php',
			dataType :"json",
			data: {'select_1': $('#statystyki_select_1').val(),'select_2': $('#statystyki_select_2').val(), 'data_od': $('#statystyki_data_od').val(), 'data_do': $('#statystyki_data_do').val()},
			success: function(data) {
				ret = data;
				$('#jqplot.before').removeClass('before');
				$('#jqplot_info').remove();
				$('#wykres_generuj').removeAttr('disabled');
				$.jqplot('jqplot', data, CreateBarChartOptions()).replot();
			}
		});
	};
	$('#wykres_generuj').click(function(){
		$(this).attr('disabled','disabled');
		generuj_wykres();
	});
	
	function CreateBarChartOptions(xAxis) {
		var optionsObj = {
			axes:{
				xaxis:{
					renderer:$.jqplot.DateAxisRenderer,
					tickOptions:{
						formatString: '%d-%m-%Y'
					}
				},
				yaxis: {              
					min: 0
				}
			},
			highlighter: {
				show: true,
				sizeAdjust: 7.5
			},
			cursor: {
				show: false
			}
		};
		return optionsObj;
	}
});