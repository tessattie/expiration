<?php include_once '/../../header.php'; ?>

<?php include_once '/../../menu.php'; ?>

<div class="row newReport">
	<form method="POST" action="/orders/public/reports/add_item" class="form-inline" id="newItemsForm">
		<div class="form-group">
		    <label class="sr-only" for="name">Report name</label>
		    <?php  
		    	if(!empty($_SESSION["report"]["name"]))
		    	{
		    		echo '<input type="text" class="form-control newReportEdit" name="name" id="name" placeholder="Report name" value = "'.$_SESSION["report"]["name"].'">';
		    	}
		    	else
		    	{
		    		echo '<input type="text" class="form-control newReportEdit" name="name" id="name" placeholder="Report name">';
		    	}
		    ?>
	  	</div>
	    <div class="form-group">
	    	<label class="sr-only" for="date_from">from</label>
	    	<?php  
		    	if(!empty($_SESSION["report"]["date_from"]))
		    	{
		    		echo '<input type="date" class="form-control newReportEdit" id="date_from" name="date_from" placeholder="From" value="'.$_SESSION["report"]["date_from"].'">';
		    	}
		    	else
		    	{
		    		echo '<input type="date" class="form-control newReportEdit" id="date_from" name="date_from" placeholder="From" value="'.$data['from'].'">';
		    	}
		    ?>
	    </div>
	    <div class="form-group">
	    	<label class="sr-only" for="date_to">To</label>
	    	<?php  
		    	if(!empty($_SESSION["report"]["date_to"]))
		    	{
		    		echo '<input type="date" class="form-control newReportEdit" id="date_to" name="date_to" placeholder="To" value="'.$_SESSION["report"]["date_to"].'">';
		    	}
		    	else
		    	{
		    		echo '<input type="date" class="form-control newReportEdit" id="date_to" name="date_to" placeholder="To" value="'.$data['to'].'">';
		    	}
		    ?>
	    </div>
	    <div class="form-group">
		    <label class="sr-only" for="newitem">Add item</label>
		    <input type="text" class="form-control" name="newitem" id="newBatchItem" placeholder="Add item" autofocus>
	  	</div>
	  	<button type="button" class="btn btn-primary" id="reset" name="reset"><a style="color:white" href="/orders/public/reports/reset"><span class="glyphicon glyphicon-refresh"> </span> RESET</a></button>
	  	<button type="button" class="btn btn-primary" id="resetBatch" name="reset"  style="margin-right:10px"><a style="color:white" href="/orders/public/reports/addItems"><span class="glyphicon glyphicon-repeat"> </span> UPDATE</a></button>
	  	<button type="button" class="btn btn-primary" id="view" name="view"  style="margin-right:10px"><a style="color:white" href="/orders/public/home"><span class="glyphicon glyphicon-eye-open"> </span> VIEW</a></button>
	  	<button type="button" class="btn btn-success" id="save" name="save"><a style="color:white" href="/orders/public/reports/save_report"><span class="glyphicon glyphicon-save"> </span> SAVE</a></button>
	    <table class="table batchTable">
		  <thead>
		  	<tr><th colspan="2">BATCH REPORT</th></tr>
		  	<tr>
			  	<th>UPC</th>
			  	<th></th>
		  	</tr>
		  </thead>
		  <tbody id="batchReport">

		  </tbody>
		  <tfoot>
		  	<tr><th colspan="2"></th></tr>
		  </tfoot>
		</table>
	</form>
</div>

<?php include_once '/../../footer.php'; ?>