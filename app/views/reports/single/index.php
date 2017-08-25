<?php include_once '/../../header.php'; ?>

<?php include_once '/../../menu.php'; ?>

<?php 
	$status = array(0 => "OPEN", 1 => "CLOSED");
	$type = array(
		0 => "Normal", 
		1 => "Vendor",
		2 => "Section", 
		3 => "Vendor section",
		4 => "Excel"
		);
	$r_status = array(1 => "New", 2 => "Edited", 3 => "Pending", 4 => "Received", 5 => "Ignored");
?>

<div class="row">
<div class="row">
<form method="post" action="/orders/public/reports/updateReportStatus" id="updateReportStatusForm">
<input type="hidden" name="id" value = "<?= $data['report'][0]['rid'] ?>">
<select class="form-control reportStatusChange" id="reportStatusChange" name="status">
		<option <?= ($data['report'][0]['received_status'] == "1") ? "selected" : "" ?> value="1">New</option>
		<option <?= ($data['report'][0]['received_status'] == "2") ? "selected" : "" ?>  value="2">Edited</option>
		<option <?= ($data['report'][0]['received_status'] == "3") ? "selected" : "" ?>  value="3">Pending</option>
		<option <?= ($data['report'][0]['received_status'] == "4") ? "selected" : "" ?>  value="4">Received</option>
		<option <?= ($data['report'][0]['received_status'] == "5") ? "selected" : "" ?>  value="5">Ignored</option>
	</select>
	</form>
<?php 
	if(!empty($data['anchor']))
	{
		echo "<input type = 'hidden' value='#".$data['anchor']."' id = 'anchorvalue'";
	}
	if($_SESSION['orders']['role'] == 7)
	{
		if($data['report'][0]['status'] == 0){
			echo '<button type="button" class="btn btn-primary single"><a style="color:white" href="/orders/public/reports/close/'.$data['report'][0]['report_id'].'/1"><span class="glyphicon glyphicon-remove"></span> Close</a></button>';			
		}
		else
		{
			echo '<button type="button" class="btn btn-primary single"><a style="color:white" href="/orders/public/reports/close/'.$data['report'][0]['report_id'].'/0"><span class="glyphicon glyphicon-remove"></span> Open</a></button>';
		}
	}
?>
	
	<button type="button" class="btn btn-primary single"><a style="color:white" target="_blank" href="/orders/public/reports/edit/<?= $data['report'][0]['report_id']?>"><span class="glyphicon glyphicon-pencil"></span> Edit</a></button>
	<button type="button" class="btn btn-primary single"><a style="color:white" target="_blank" href="/orders/public/reports/duplicate/<?= $data['report'][0]['report_id']?>"><span class="glyphicon glyphicon-duplicate"></span> Duplicate</a></button>
	<button type="button" class="btn btn-primary single"><a style="color:white" target="_blank" href="/orders/public/export/reportExport/<?= $data['report'][0]['report_id']?>"><span class="glyphicon glyphicon-export"></span> Export</a></button>
	<button type="button" class="btn btn-primary single"><a style="color:white" target="_blank" href="/orders/public/reports"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></button>
	<button type="button" class="btn btn-primary single"><a style="color:white" href="/orders/public/reports/single/<?= $data['report_id']?>"><span class="glyphicon glyphicon-refresh"></span> Refresh</a></button>
</div>
<table class="table table-bordered">
	<thead>
	  	<tr><th colspan="16"><?= strtoupper($data['report'][0]['name']) ?></th><th colspan="3">Report type : <?= $type[$data['report'][0]['type']]?></th></tr>
	  	<tr>
	  		<th colspan="4">Generated by : <?= $data['report'][0]['user_firstname']." ".$data['report'][0]['user_lastname']?></th>
	  		<th colspan="3">Timestamp : <?= date("D, F d-Y H:i:s",strtotime($data['report'][0]['timestamp']))?></th>
	  		<th colspan="3">Sales from : <?= $data['report'][0]['date_from']?></th>
	  		<th colspan="3">sales to : <?= $data['report'][0]['date_to']?></th>
	  		<th colspan="3">Status : <?= $status[$data['report'][0]['rstat']]?></th>
	  		<th colspan="3">Items count : <?= count($data['report'])?></th>
	  	</tr>
	  	<tr>
	  		<th class="tdminus"></th>
		  	<th>UPC</th>
		  	<th>VRD ITEM #</th>
		  	<th>BRAND</th>
		  	<th>ITEM DESCRIPTION</th>
		  	<th>PACK</th>
		  	<th>SIZE</th>
		  	<th>CASE COST</th>
		  	<th>RETAIL</th>
		  	<th>ON HAND</th>
		  	<th>ORDER QTY</th>
		  	<th>EXPIRATION</th>
		  	<th>EXP QTY UNIT</th>
		  	<th>LAST RECEIVING</th>
		  	<th>LAST RECEIVING DATE</th>
		  	<th>SALES</th>
		  	<th>VDR NO</th>
		  	<th>VDR NAME</th>
		  	<th></th>
	  	</tr>
	  </thead>
	  <tbody>
	  	<?php  
	  		if(!empty($data['report']))
		  	{
		  		$increment = 0; 
				$condition = 'ht';
				$vdrCondition = 'vd';
		  		for($i=0;$i<count($data['report']);$i++)
		  		{
		  			if($data['report'][$i]['lastorder'] == ".0000")
		  			{
		  				$data['report'][$i]['lastorder'] = "";
		  			}
		  			else
		  			{
		  				$data['report'][$i]['lastorder'] = round($data['report'][$i]['lastorder']);
		  			}

		  			if($data['report'][$i]['tprprice'] == ".00")
		  			{
		  				 $data['report'][$i]['tprprice'] = '';
		  				 $data['report'][$i]['tprstart'] = '';
		  				 $data['report'][$i]['tprend'] = '';
		  			}
		  			if($increment == 0 || $condition != $data['report'][$i]['SctNo'] || $vdrCondition != $data['report'][$i]['vdrno'])
			    	{
						echo '<tr class = "section_name">
						<td></td>
						<td></td>';
			            echo '<td></td><td></td>
			            <td class="SectionName">SECTION '.$data['report'][$i]['SctNo'].' - '.$data['report'][$i]['SctName'].'</td>
			            <td></td>
			            <td></td>
			            <td></td>
			            <td></td>
			            <td></td>
			            <td></td>
			            <td></td>
			            <td></td>
			            <td></td>
			            <td></td>
			            <td></td>
			            <td></td>
			            <td></td>
			            <td></td>';
			            echo '</tr>';
					}
					if($data['report'][$i]['description'] == "ITEM NOT FOUND")
					{
						echo "<tr id='".$data['report'][$i]['id']."' class='bg-danger'>";
						if($i > 0)
			  			{
			  				echo "<td class = 'tdminus'><a href='/orders/public/reports/delete_item/".$data['report'][$i]['id']."/".$data['report'][$i]['report_id']."/".$data['report'][$i-1]['upc']."'><span class='glyphicon glyphicon-minus'></span></a></td>";
			  			}else{
			  				echo "<td class = 'tdminus'><a href='/orders/public/reports/delete_item/".$data['report'][$i]['id']."/".$data['report'][$i]['report_id']."'><span class='glyphicon glyphicon-minus'></span></a></td>";
			  			}
						if(!empty($data['upcPriceCompare']) && $data['upc'] == $data['report'][$i]['upc'])
			  			{
			  				echo "<td>".$data['report'][$i]['upc']."</td>";
			  			}
			  			else
			  			{
			  				echo "<td>".$data['report'][$i]['upc']."</td>";
			  			}
			  			echo "<td  class='certcode'></td>";
			  			echo "<td></td>";
			  			echo "<td class='textLeft'>".$data['report'][$i]['description']."</td>";
			  			echo "<td></td>";
			  			echo "<td></td>";
			  			echo "<td></td>";
			  			echo "<td></td>";
			  			echo "<td></td>";
			  			echo "<td></td>";
			  			echo "<td></td>";
			  			echo "<td></td>";
			  			echo "<td></td>";
			  			echo "<td></td>";
			  			echo "<td></td>";
			  			echo "<td></td>";
			  			echo "<td></td>";
			  			echo "<td></td>";

			  			
			  			echo "</tr>";

			  			$increment = $increment + 1 ;
	    				$condition = $data['report'][$i]['SctNo'];
	    				$vdrCondition = $data['report'][$i]['vdrno'];
					}
					else
					{
						echo "<tr id='".$data['report'][$i]['id']."'>";
						if($i > 0)
			  			{
			  				echo "<td class = 'tdminus'><a href='/orders/public/reports/delete_item/".$data['report'][$i]['id']."/".$data['report'][$i]['report_id']."/".$data['report'][$i-1]['upc']."'><span class='glyphicon glyphicon-minus'></span></a></td>";
			  			}else{
			  				echo "<td class = 'tdminus'><a href='/orders/public/reports/delete_item/".$data['report'][$i]['id']."/".$data['report'][$i]['report_id']."'><span class='glyphicon glyphicon-minus'></span></a></td>";
			  			}
						if(!empty($data['upcPriceCompare']) && $data['upc'] == $data['report'][$i]['upc'])
			  			{
			  				echo "<td>".$data['report'][$i]['upc']."</td>";
			  			}
			  			else
			  			{
			  				echo "<td><a href = '/orders/public/reports/single/".$data['report_id']."/".$data['report'][$i]['upc']."/".$data['report'][$i]['upc']."'>".$data['report'][$i]['upc']."</a></td>";
			  			}
			  			echo "<td id='".$data['report'][$i]['upc']."' class='certcode'>".$data['report'][$i]['itemcode']."</td>";
			  			echo "<td>".$data['report'][$i]['brand']."</td>";
			  			echo "<td class='textLeft'>".$data['report'][$i]['description']."</td>";
			  			echo "<td>".$data['report'][$i]['pack']."</td>";
			  			echo "<td>".$data['report'][$i]['size']."</td>";
			  			echo "<td  class='casecost'>".number_format($data['report'][$i]['casecost'], 2, ".", "")."</td>";
			  			echo "<td>".number_format($data['report'][$i]['retail'], 2, ".", '')."</td>";
			  			if(round($data['report'][$i]['onhand']) < 0){
			  				echo "<td class='negative'>".round($data['report'][$i]['onhand'])."</td>";
			  			}else{
			  				echo "<td>".round($data['report'][$i]['onhand'])."</td>";
			  			}
			  			echo "<td class='orderqty'><input value='".$data['report'][$i]['orderqty']."' placeholder='Order qty' class='reportInput' tabindex='".($i+1)."'></td>";
			  			echo "<td class='expiration_date'><input type='date' placeholder='Exp' class='reportInput expdate' value='".$data['report'][$i]['expiration_date']."'></td>";
			  			echo "<td class='expiration'><input type='text' placeholder='Exp Qty' class='reportInput' value='".$data['report'][$i]['expiration']."'></td>";
			  			echo "<td class='lo'>".$data['report'][$i]['lastorder']."</td>";
			  			echo "<td class='lod'>".$data['report'][$i]['lastorderdate']."</td>";
			  			echo "<td>".$data['report'][$i]['sales']."</td>";
			  			echo "<td class='vdrNo'>".$data['report'][$i]['vdrno']."</td>";
			  			echo "<td class='vdrName'>".$data['report'][$i]['vdrname']."</td>";
			  			echo "<td class='order_".strtolower($r_status[$data['report'][$i]['status']])."'>".$r_status[$data['report'][$i]['status']][0]."</td>";
			  			
			  			echo "</tr>";

			  			if(!empty($data['upcPriceCompare']) && $data['upc'] == $data['report'][$i]['upc'])
			  			{
			  				for($j=0;$j<count($data['upcPriceCompare']);$j++)
			  				{
			  					if($data['upcPriceCompare'][$j]['VdrName'] != $data['report'][$i]['vdrname'])
			  					{
			  						if($data['upcPriceCompare'][$j]['lastReceiving'] == ".0000")
						  			{
						  				$data['upcPriceCompare'][$j]['lastReceiving'] = "";
						  			}
						  			else
						  			{
						  				$data['upcPriceCompare'][$j]['lastReceiving'] = round($data['upcPriceCompare'][$j]['lastReceiving']);
						  			}

						  			if($data['upcPriceCompare'][$j]['tpr'] == ".00")
						  			{
						  				 $data['upcPriceCompare'][$j]['tpr'] = '';
						  				 $data['upcPriceCompare'][$j]['tprStart'] = '';
						  				 $data['upcPriceCompare'][$j]['tprEnd'] = '';
						  			}
				  					echo "<tr class='upcPriceCompareTr'>";
				  					echo "<td class = '".$data['report'][$i]['id']."'></td>";
					  				echo "<td class = '".$data['report'][$i]['id']."'>".$data['upcPriceCompare'][$j]['UPC']."</td>";
						  			echo "<td  class = 'certcode'>".$data['upcPriceCompare'][$j]['CertCode']."</td>";
						  			echo "<td  class = 'certcode'>".$data['upcPriceCompare'][$j]['Brand']."</td>";
						  			echo "<td class = 'ItemDescription'>".$data['upcPriceCompare'][$j]['ItemDescription']."</td>";
						  			echo "<td>".$data['upcPriceCompare'][$j]['Pack']."</td>";
						  			echo "<td>".$data['upcPriceCompare'][$j]['SizeAlpha']."</td>";
						  			echo "<td class = 'casecost'>".number_format($data['upcPriceCompare'][$j]['CaseCost'], 2, ".", "")."</td>";
						  			echo "<td>".number_format($data['upcPriceCompare'][$j]['Retail'], 2, ".", '')."</td>";
						  			echo "<td>".round($data['upcPriceCompare'][$j]['onhand'])."</td>";
						  			echo "<td class='order'></td>";
						  			echo "<td class='expiration_date'></td>";
						  			echo "<td class='expiration'></td>";
						  			echo "<td class = 'lo'>".$data['upcPriceCompare'][$j]['lastReceiving']."</td>";
						  			echo "<td class = 'lod'>".$data['upcPriceCompare'][$j]['lastReceivingDate']."</td>";
						  			echo "<td>".$data['upcPriceCompare'][$j]['sales']."</td>";
						  			echo "<td class = 'vendorno'>".$data['upcPriceCompare'][$j]['VdrNo']."</td>";
						  			echo "<td class = 'vendor'>".$data['upcPriceCompare'][$j]['VdrName']."</td>";
						  			echo "<td><span class='glyphicon glyphicon-credit-card'></span></td>";
					  				echo "</tr>";
			  					}
			  					
			  				}
			  				
			  			}
			  			$increment = $increment + 1 ;
	    				$condition = $data['report'][$i]['SctNo'];
	    				$vdrCondition = $data['report'][$i]['vdrno'];
					}
		  		}
		  	}
	  	?>
	  </tbody>
	  <tfoot>
	  	<tr><th colspan="19"></th></tr>
	  </tfoot>
</table>
</div>
<?php include_once '/../../footer.php'; ?>