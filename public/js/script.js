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
		var description = $(this).parent().parent().find('.ItemDescription').text();
		var regex = /^\d+$/;
		var units = prompt("How many units of "+description+" would you like to order from "+vendor+" ?");
		if(regex.test(units))
		{
			// save order
			$.ajax({
			  type: "POST",
			  url: "/expiration/public/orders/newOrder",
			  data: {vdrname : vendor,
			  		 vdrno : vendorno,
			  		 desc : description,  
			  		 quantity : units},
			  success: function(data){
			  	console.log(data);
			  }, 
			  error: function(error){
			  	console.log(error);
			  }
			});
		}
		else
		{
			alert("You can only enter numbers");
		}
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