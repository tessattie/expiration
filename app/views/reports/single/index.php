<?php include_once '/../../header.php'; ?>

<?php include_once '/../../menu.php'; ?>

<div class="row">
<div class="row">
	<button type="button" class="btn btn-primary single"><a style="color:white" target="_blank" href="/expiration/public/export/reportExport/<?= $data['report'][0]['report_id']?>"><span class="glyphicon glyphicon-export"></span> Export</a></button>
	<button type="button" class="btn btn-primary single"><a style="color:white" target="_blank" href="/expiration/public/reports"><span class="glyphicon glyphicon-arrow-left"></span> Back</a></button>
</div>
<table class="table table-bordered">
	<thead>
	  	<tr><th colspan="20"><?= $data['report'][0]['name']?></th></tr>
	  	<tr>
	  		<th colspan="4">Report generated by : <?= $data['report'][0]['user_firstname']." ".$data['report'][0]['user_lastname']?></th>
	  		<th colspan="5">Timestamp : <?= date("D, F d-Y H:i:s",strtotime($data['report'][0]['timestamp']))?></th>
	  		<th colspan="4">Sales from : <?= $data['report'][0]['date_from']?></th>
	  		<th colspan="4">sales to : <?= $data['report'][0]['date_to']?></th>
	  		<th colspan="3">Number of items : <?= count($data['report'])?></th>
	  	</tr>
	  	<tr>
		  	<th>UPC</th>
		  	<th>VRD ITEM #</th>
		  	<th>ITEM DESCRIPTION</th>
		  	<th>PACK</th>
		  	<th>SIZE</th>
		  	<th>CASE COST</th>
		  	<th>RETAIL</th>
		  	<th>ON HAND</th>
		  	<th>ORDER QTY</th>
		  	<th>EXPIRATION</th>
		  	<th>EXP QTY UNIT</th>
		  	<th>LAST ORDER</th>
		  	<th>LAST ORDER DATE</th>
		  	<th>SALES</th>
		  	<th>TPR PRICE</th>
		  	<th>TPR START</th>
		  	<th>TPR END</th>
		  	<th>VDR NO</th>
		  	<th>VDR NAME</th>
		  	<th class="tdminus"></th>
	  	</tr>
	  </thead>
	  <tbody>
	  	<?php  
	  		if(!empty($data['report']))
		  	{
		  		$increment = 0; 
				$condition = 'ht' ;
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
		  				 $data['report'][$i]['tprStart'] = '';
		  				 $data['report'][$i]['tprEnd'] = '';
		  			}
		  			if($increment == 0 || $condition != $data['report'][$i]['SctNo'])
			    	{
						echo '<tr class = "section_name"><td></td><td></td>';
			            echo '<td class="SectionName">SECTION '.$data['report'][$i]['SctNo'].' - '.$data['report'][$i]['SctName'].'</td>
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
			            <td></td>
			            <td></td>
			            <td></td>
			            <td></td>';
			            echo '</tr>';
					}
		  			echo "<tr id='".$data['report'][$i]['id']."'>";
		  			echo "<td>".$data['report'][$i]['upc']."</td>";
		  			echo "<td>".$data['report'][$i]['itemcode']."</td>";
		  			echo "<td>".$data['report'][$i]['description']."</td>";
		  			echo "<td>".$data['report'][$i]['pack']."</td>";
		  			echo "<td>".$data['report'][$i]['size']."</td>";
		  			echo "<td>".number_format($data['report'][$i]['casecost'], 2, ".", "")."</td>";
		  			echo "<td>".number_format($data['report'][$i]['retail'], 2, ".", '')."</td>";
		  			echo "<td>".round($data['report'][$i]['onhand'])."</td>";
		  			echo "<td class='order'>".$data['report'][$i]['orderqty']."</td>";
		  			echo "<td class='expiration_date'>".$data['report'][$i]['expiration_date']."</td>";
		  			echo "<td class='expiration'>".$data['report'][$i]['expiration']."</td>";
		  			echo "<td>".$data['report'][$i]['lastorder']."</td>";
		  			echo "<td>".$data['report'][$i]['lastorderdate']."</td>";
		  			echo "<td>".$data['report'][$i]['sales']."</td>";
		  			echo "<td>".$data['report'][$i]['tprprice']."</td>";
		  			echo "<td>".$data['report'][$i]['tprStart']."</td>";
		  			echo "<td>".$data['report'][$i]['tprEnd']."</td>";
		  			echo "<td>".$data['report'][$i]['vdrno']."</td>";
		  			echo "<td>".$data['report'][$i]['vdrname']."</td>";
		  			echo "<td class = 'tdminus'><a href='/expiration/public/reports/removeItem/".$data['report'][$i]['id']."'><span class='glyphicon glyphicon-minus'></span></a></td>";
		  			echo "</tr>";

		  			$increment = $increment + 1 ;
    				$condition = $data['report'][$i]['SctNo'];
		  		}
		  	}
	  	?>
	  </tbody>
	  <tfoot>
		  	<tr><th colspan="20"></th></tr>
		  </tfoot>
</table>
</div>

<?php include_once '/../../footer.php'; ?>