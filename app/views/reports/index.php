<?php include_once '/../header.php'; ?>

<?php include_once '/../menu.php'; ?>

<?php  
	$colspan="7";
	if($_SESSION['orders']['role'] == 6)
	{
		$colspan="6";
	}
$status = array(0 => "OPEN", 1 => "CLOSED");
?>

<div class="row newReport">
	
	<table class="table table-bordered">
		  <thead>
		  	<tr><th colspan="<?= $colspan ?>">REPORTS</th></tr>
		  	<tr>
			  	<th>REPORT NAME</th>
			  	<th>REPORT DATE</th>
			  	<th>SALES FROM DATE</th>
			  	<th>SALES TO DATE</th>
			  	<th>USER</th>
			  	<th>STATUS</th>
			  	<?php  
			  		if($_SESSION['orders']['role'] == 7)
			  		{
			  			echo '<th class="tdminus"></th>';
			  		}
			  	?>
		  	</tr>
		  </thead>
		  <tbody>
		  	<?php 
		  	if(!empty($data['reports']))
		  	{
		  		for($i=0;$i<count($data['reports']);$i++)
		  		{
		  			echo "<tr>";
		  			echo "<td><a href='/orders/public/reports/single/".$data['reports'][$i]['id']."'>".$data['reports'][$i]['name']."</a></td>";
		  			echo "<td>".date("D, F d-Y H:i:s",strtotime($data['reports'][$i]['timestamp']))."</td>";
		  			echo "<td>".$data['reports'][$i]['date_from']."</td>";
		  			echo "<td>".$data['reports'][$i]['date_to']."</td>";
		  			echo "<td>".$data['reports'][$i]['user_firstname']." ".$data['reports'][$i]['user_lastname']."</td>";
		  			echo "<td>".$status[$data['reports'][$i]['status']]."</td>";
		  			if($_SESSION['orders']['role'] == 7)
			  		{
		  				echo "<td class = 'tdminus'><a href='/orders/public/reports/delete_report/".$data['reports'][$i]['id']."'><span class='glyphicon glyphicon-minus'></span></a></td>";
			  		}
			  		
		  			echo "<tr>";
		  		}
		  	}
		  	?>
		  </tbody>
		  <tfoot>
		  	<tr><th colspan="<?= $colspan ?>"></th></tr>
		  </tfoot>
		</table>
</div>

<?php include_once '/../footer.php'; ?>