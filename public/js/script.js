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
		  url: "/orders/public/reports/new_report",
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

	$("#vendorImport").click(function(){
		var regex = /[0-9]/;
		var vendorNumber = prompt('Enter the vendor Number : ');
		if(regex.test(vendorNumber))
		{
			$("#vendorNumber").val(vendorNumber);
			document.forms["vendorImport"].submit();
		}
		else
		{
			alert("There are no vendor numbers with more than six digits. Try again.");
		}
	})

	$("#reportStatusChange").change(function(){
		$("#updateReportStatusForm").submit();
	})

	$("#sectionImport").click(function(){
		var regex = /[0-9]/;
		var vendorNumber = prompt('Enter the section Number : ');
		if(regex.test(vendorNumber))
		{
			$("#sectionNumber").val(vendorNumber);
			document.forms["sectionImport"].submit();
		}
		else
		{
			alert("There are no section numbers with more than four digits. Try again.");
		}
	})

	$("#vendorSectionImport").click(function(){
		var regex = /[0-9]/;
		var vendorNumber = prompt('Enter the vendor number :')
		var sectionNumber = prompt('Enter the section Number : ');
		if(regex.test(sectionNumber) && regex.test(vendorNumber))
		{
			$("#svendorNumber").val(vendorNumber); // vendor number
			$("#sctvendorNumber").val(sectionNumber); // section number 
			document.forms["vendorSectionImport"].submit();
		}
		else
		{
			alert("Verify that the numbers are correct.");
		}
	})

	$("#upcExcelImport").change(function(){
		$("#updExcelImportForm").submit();
	})
	$('.glyphicon-credit-card').click(function(){
		var vendor = $(this).parent().parent().find('.vendor').text();
		var lo = $(this).parent().parent().find('.lo').text();
		var lod = $(this).parent().parent().find('.lod').text();
		var ccost = $(this).parent().parent().find('.casecost').text();
		var ccode = $(this).parent().parent().find('.certcode').text();
		var vendorno = $(this).parent().parent().find('.vendorno').text();
		var pac = $(this).parent().parent().find('.pack').text();
		var id = $(this).parent().parent().find('td:first').attr('class');

		var vno = $('#'+id).find(".vdrNo").text();
		var vname = $('#'+id).find(".vdrName").text();
		var pack = $('#'+id).find(".pack").text();
		var casec = $('#'+id).find(".casecost").text();
		var lasto =	$('#'+id).find(".lo").text();
		var lastod = $('#'+id).find(".lod").text();
		var certc =	$('#'+id).find(".certcode").text();

		$(this).parent().parent().find('.vendor').text(vname);
		$(this).parent().parent().find('.lo').text(lasto);
		$(this).parent().parent().find('.lod').text(lastod);
		$(this).parent().parent().find('.pack').text(pack);
		$(this).parent().parent().find('.casecost').text(casec);
		$(this).parent().parent().find('.certcode').text(certc);
		$(this).parent().parent().find('.vendorno').text(vno);
		$.ajax({
		  type: "POST",
		  url: "/orders/public/orders/update_order_vendor",
		  data: {vdrname : vendor,
		  		 vdrno : vendorno,
		  		 ident : id, 
		  		 pck : pac,
		  		 casecost : ccost, 
		  		 lastorder : lo,
		  		 lastorderdate : lod, 
		  		 certcode : ccode},
		  success: function(data){
		  	console.log(data);
		  	$('#'+id).find(".vdrNo").text(vendorno);
		  	$('#'+id).find(".vdrName").text(vendor);
		  	$('#'+id).find(".casecost").text(ccost);
		  	$('#'+id).find(".pack").text(pac);
		  	$('#'+id).find(".lo").text(lo);
		  	$('#'+id).find(".lod").text(lod);
		  	$('#'+id).find(".certcode").text(ccode);
		  }, 
		  error: function(error){
		  	console.log(error);
		  }
		});
	})

	$(".errorPara").click(function(){
		$.ajax({
		  type: "POST",
		  url: "/orders/public/reports/reset_error",
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
		  url: "/orders/public/reports/set_itemValue",
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
		  url: "/orders/public/reports/update_itemValue",
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

	$("#newBatchItem").change(function(){
		$("#batchReport").append("<tr><td class='upcTD'>" + $(this).val() + "</td><td class='redTD colorTD'></td></tr>");
		$(this).val("");
	})


	$("#resetBatch").click(function(e){
		e.preventDefault();
		$("#batchReport > tr").each(function(){
			var tr = $(this);
			$.ajax({
			  type: "POST",
			  url: "/orders/public/reports/updateBatch",
			  data: {upc : $(this).find(".upcTD").text()},
			  success: function(data){
			  	console.log(data);
			  	tr.find(".colorTD").removeClass("redTD");
			  	tr.find(".colorTD").addClass("greenTD");
			  }, 
			  error: function(error){
			  	console.log(error);
			  }
			});
		 });
		
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

	if($("#anchorvalue").val() != undefined){
		location.href = $("#anchorvalue").val();
	}

	$("#resetButton").click(function(){
		return confirm("Are you sure you would like to reset this report?");
	})
});