jQuery(function($){

	$(document).ready(function(){
		var parts = window.location.search.substr(1).split("&");
		var report = parts[0].split('=');
		$('#'+report[1]).addClass('active');
	});

	$(document).ready( function() {
		$('.dropdown-toggle').dropdown();
	});

	$(".newReportEdit").change(function(){
		$.ajax({
		  type: "POST",
		  url: "/expiration/public/reports/new_report",
		  data: {name : $("#name").val(),
		  		 date_from : $("#date_from").val(),  
		  		 date_to : $("#date_to").val()},
		  success: function(data){
		  	console.log(data);
		  }, 
		  error: function(error){
		  	console.log(error);
		  }
		});
	})
	$('.glyphicon-credit-card').click(function(){
		var vendor = $(this).parent().parent().find('.vendor').text();
		var vendorno = $(this).parent().parent().find('.vendorno').text();
		var id = $(this).parent().parent().find('td:first').attr('class');
		$.ajax({
		  type: "POST",
		  url: "/expiration/public/orders/update_order_vendor",
		  data: {vdrname : vendor,
		  		 vdrno : vendorno,
		  		 ident : id},
		  success: function(data){
		  	console.log(data);
		  }, 
		  error: function(error){
		  	console.log(error);
		  }
		});
	})

	$(".errorPara").click(function(){
		$.ajax({
		  type: "POST",
		  url: "/expiration/public/reports/reset_error",
		  success: function(data){
		  	$(".errorPara").fadeOut();
		  	// $(".errorPara").remove();
		  }, 
		  error: function(error){
		  	console.log(error);
		  }
		});
	})

	$(".reportInputs").change(function(){
		$.ajax({
		  type: "POST",
		  url: "/expiration/public/reports/set_itemValue",
		  data: {name : $(this).parent().attr('class'),
		  		 ident : $(this).parent().parent().attr('id'),  
		  		 value : $(this).val()},
		  success: function(data){
		  	console.log(data);
		  }, 
		  error: function(error){
		  	console.log(error);
		  }
		});
	})

	$(".reportInput").change(function(){
		$.ajax({
		  type: "POST",
		  url: "/expiration/public/reports/update_itemValue",
		  data: {name : $(this).parent().attr('class'),
		  		 ident : $(this).parent().parent().attr('id'),  
		  		 value : $(this).val()},
		  success: function(data){
		  	console.log(data);
		  }, 
		  error: function(error){
		  	console.log(error);
		  }
		});
	})

	$("#newitem").change(function(){
		$("#newItemsForm").submit();
	})

	$('.setpass').click(function(event){
		if($('.newpass').val() == $('.newpass2').val())
		{
			$('.errorDiv').empty();
			$('.newpass').css('border', 'none');
			$('.newpass2').css('border', 'none');
			$('#setpassform').submit();
		}
		else
		{
			$('.errorDiv').append('<p class="bg-danger">Both passwords must match</p>');
			$('.newpass').css('border', '1px solid red');
			$('.newpass2').css('border', '1px solid red');
			event.preventDefault();
		}
	})
});