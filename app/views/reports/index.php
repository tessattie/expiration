<?php include_once '/../header.php'; ?>

<?php include_once '/../menu.php'; ?>


<div class="row newReport">
	
	<table class="table table-bordered">
		  <thead>
		  	<tr><th colspan="6">REPORTS</th></tr>
		  	<tr>
			  	<th>REPORT NAME</th>
			  	<th>REPORT DATE</th>
			  	<th>SALES FROM DATE</th>
			  	<th>SALES TO DATE</th>
			  	<th>USER</th>
			  	<th class="tdminus"></th>
		  	</tr>
		  </thead>
		  <tbody>
		  	<?php 
		  	if(!empty($data['reports']))
		  	{
		  		for($i=0;$i<count($data['reports']);$i++)
		  		{
		  			echo "<tr>";
		  			echo "<td><a href='/expiration/public/reports/single/".$data['reports'][$i]['id']."'>".$data['reports'][$i]['name']."</a></td>";
		  			echo "<td>".date("D, F d-Y H:i:s",strtotime($data['reports'][$i]['timestamp']))."</td>";
		  			echo "<td>".$data['reports'][$i]['date_from']."</td>";
		  			echo "<td>".$data['reports'][$i]['date_to']."</td>";
		  			echo "<td>".$data['reports'][$i]['user_firstname']." ".$data['reports'][$i]['user_lastname']."</td>";
		  			echo "<td class = 'tdminus'><a href='/expiration/public/reports/delete_report/".$data['reports'][$i]['id']."'><span class='glyphicon glyphicon-minus'></span></a></td>";
		  			echo "<tr>";
		  		}
		  	}
		  	?>
		  </tbody>
		  <tfoot>
		  	<tr><th colspan="6"></th></tr>
		  </tfoot>
		</table>
</div>

<?php include_once '/../footer.php'; ?>