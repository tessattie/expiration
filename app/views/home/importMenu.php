	<?php if($_SESSION['orders']['role'] == 8) : ?>
	<form class="form-inline" id = "vendorImport" method="post" action = "/orders/public/reports/importVendor">
		<a style="color:white" href="#"><button type="button" class="btn btn-primary" id="vendorImportButton" style="margin-right:10px"><span class="glyphicon glyphicon-import"> </span>IMPORT FROM VENDOR</button></a>
		<input type="hidden" name="vendorNumber" id="vendorNumber">
	</form>
	<?php else : ?>
	<form class="form-inline" id = "updExcelImportForm" method="post" enctype="multipart/form-data" action = "/orders/public/reports/addExcel">
		<label class="btn btn-primary btn-file">
		<span class="glyphicon glyphicon-import"></span> IMPORT FROM EXCEL <input type="file" style="display: none;" name="upcs" id="upcExcelImport">
		</label>
	</form>
	<form class="form-inline" id = "vendorSectionImport" method="post" action = "/orders/public/reports/importVendorSection">
		 <a style="color:white" href="#"><button type="button" class="btn btn-primary" id="vendorSectionImportButton" ><span class="glyphicon glyphicon-import"> </span> VDR - SCT</button></a>
		<input type="hidden" name="svendorNumber" id="svendorNumber">
		<input type="hidden" name="sctvendorNumber" id="sctvendorNumber">
	</form>
	<form class="form-inline" id = "sectionImport" method="post" action = "/orders/public/reports/importSection">
		  <a style="color:white" href="#"><button type="button" class="btn btn-primary" id="sectionImportButton" style="margin-right:10px"><span class="glyphicon glyphicon-import"> </span> SECTION</button></a>
		<input type="hidden" name="sectionNumber" id="sectionNumber">
	</form>
	<form class="form-inline" id = "vendorImport" method="post" action = "/orders/public/reports/importVendor">
		<a style="color:white" href="#"><button type="button" class="btn btn-primary" id="vendorImportButton" style="margin-right:10px"><span class="glyphicon glyphicon-import"> </span> VENDOR</button></a>
		<input type="hidden" name="vendorNumber" id="vendorNumber">
	</form>
	<?php endif ; ?>