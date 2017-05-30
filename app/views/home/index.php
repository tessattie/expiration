<?php include_once '/../header.php'; ?>

<?php include_once '/../menu.php'; ?>

<!-- NEW REPORT IN MAIN PAGE OF APPLICATION -->
<div class="row">
	<div class="col-md-4">
		<?php  
			if(!empty($_SESSION['error']))
			{
				echo '<p class="bg-danger errorPara">'.$_SESSION['error'].'</p>';
			}
		?>
	</div>
</div>
<div class="row newReport">
	<form method="POST" action="/expiration/public/reports/add_item" class="form-inline" id="newItemsForm">
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
		    <input type="text" class="form-control" name="newitem" id="newitem" placeholder="Add item" autofocus>
	  	</div>
	  	<button type="button" class="btn btn-primary" id="reset" name="reset"><a style="color:white" href="/expiration/public/reports/reset"><span class="glyphicon glyphicon-refresh"> </span> RESET</a></button>
	  	<button type="button" class="btn btn-primary" id="reset" name="reset"  style="margin-right:10px"><a style="color:white" href="/expiration/public/reports/addItems"><span class="glyphicon glyphicon-repeat"> </span> UPDATE</a></button>
	  	<button type="button" class="btn btn-success" id="save" name="save"><a style="color:white" href="/expiration/public/reports/save_report"><span class="glyphicon glyphicon-save"> </span> SAVE</a></button>
	    <table class="table">
		  <thead>
		  	<tr><th colspan="17">NEW REPORT</th></tr>
		  	<tr>
			  	<th>UPC</th>
			  	<th>VRD ITEM #</th>
			  	<th>ITEM DESCRIPTION</th>
			  	<th>PACK</th>
			  	<th>SIZE</th>
			  	<th>RETAIL</th>
			  	<th>ON HAND</th>
			  	<th>ORDER QTY</th>
			  	<th>EXPIRATION</th>
			  	<th>EXP QTY UNIT</th>
			  	<th>LAST RECEIVING</th>
			  	<th>LAST RECEIVING DATE</th>
			  	<th>SALES</th>
			  	<th>TPR PRICE</th>
			  	<th>TPR START</th>
			  	<th>TPR END</th>
			  	<th class="tdminus"></th>
		  	</tr>
		  </thead>
		  <tbody>
		  	<?php 
		  	if(!empty($_SESSION['report']['items']))
		  	{
		  		foreach($_SESSION['report']['items'] as $key => $value)
		  		{
		  			if($value['lastReceiving'] == ".0000")
		  			{
		  				$value['lastReceiving'] = "";
		  			}
		  			else
		  			{
		  				$value['lastReceiving'] = round($value['lastReceiving']);
		  			}

		  			if($value['tpr'] == ".00")
		  			{
		  				 $value['tpr'] = '';
		  				 $value['tprStart'] = '';
		  				 $value['tprEnd'] = '';
		  			}
		  			echo "<tr id='".$key."'>";
		  			echo "<td>".$value['UPC']."</td>";
		  			echo "<td>".$value['CertCode']."</td>";
		  			echo "<td class='textLeft'>".$value['ItemDescription']."</td>";
		  			echo "<td>".$value['Pack']."</td>";
		  			echo "<td>".$value['SizeAlpha']."</td>";
		  			echo "<td>".number_format($value['Retail'], 2, ".", '')."</td>";
		  			if(round($value['onhand']) < 0){
		  				echo "<td class='negative'>".round($value['onhand'])."</td>";
		  			}else{
		  				echo "<td>".round($value['onhand'])."</td>";
		  			}
		  			echo "<td class='order'><input type='text' placeholder='Order qty' class='reportInputs' value='".$value['order']."'></td>";
		  			echo "<td class='expiration_date'><input type='date' placeholder='Exp' class='reportInputs expdate' value='".$value['expiration_date']."'></td>";
		  			echo "<td class='expiration'><input type='text' placeholder='Exp Qty' class='reportInputs' value='".$value['expiration']."'></td>";
		  			echo "<td>".$value['lastReceiving']."</td>";
		  			echo "<td>".$value['lastReceivingDate']."</td>";
		  			echo "<td>".$value['sales']."</td>";
		  			echo "<td>".$value['tpr']."</td>";
		  			echo "<td>".$value['tprStart']."</td>";
		  			echo "<td>".$value['tprEnd']."</td>";
		  			echo "<td class = 'tdminus'><a href='/expiration/public/reports/removeItem/".$key."'><span class='glyphicon glyphicon-minus'></span></a></td>";
		  			echo "</tr>";
		  		}
		  	}
		  	?>
		  </tbody>
		  <tfoot>
		  	<tr><th colspan="17"></th></tr>
		  </tfoot>
		</table>
	</form>
</div>


<?php include_once '/../footer.php'; ?>