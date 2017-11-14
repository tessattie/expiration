<?php  
	$roles = array(7 => "Order 0", 6 => "Order 1", 5 => "Order 2", 8 => "Order 5");
?>
<table class="table table-bordered">
		<thead>
			<tr><th colspan="7">Users</th></tr>
			<tr><th>Last name</th><th>First name</th><th>Username</th><th>Email</th><th>Access</th><th>Vendors</th><th>Actions</th></tr>
			<form method = "POST" action = "/orders/public/account/edit/<?= $data['user']['id'] ?>">
				<tr><th><input type="hidden" name="id" value = <?= $data['user']['id'] ?>><input type="text" class="form-control" name="lastname" placeholder="Last name" required value = <?= $data['user']['lastname'] ?>></th>
					<th><input type="text" class="form-control" name="firstname" placeholder="First name" required value = <?= $data['user']['firstname'] ?>></th>
					<th><input type="text" class="form-control" name="username" placeholder="Username" required value = <?= $data['user']['username'] ?>></th>
					<th><input type="email" class="form-control" name="email" placeholder="Email" required value = <?= $data['user']['email'] ?>></th>
					<th>
						<select class= "form-control" name="role">
							<option value = "7" <?= ($data['user']['role'] == 7) ? "selected" : "" ?>>Order 0</option>
							<option value = "6" <?= ($data['user']['role'] == 6) ? "selected" : "" ?>>Order 1</option>
							<option value = "5" <?= ($data['user']['role'] == 5) ? "selected" : "" ?>>Order 2</option>
							<option value = "8" <?= ($data['user']['role'] == 8) ? "selected" : "" ?>>Order 5</option>
						</select>
					</th>
					<th><input type="text" class="form-control" name="vendors" placeholder="Vendors" required value = "<?= $data['user']['vendors'] ?>"></th>
					<th><input type='submit' class="btn btn-default" value='Submit' name="submit"></th>
				</tr>
			</form>
		</thead>
		<tbody>
			<?php  
				$count = count($data['users']);
				for($i=0;$i<$count;$i++)
				{
					echo "<tr>";
					echo "<td>" . strtoupper($data['users'][$i]['lastname']) . "</td>";
					echo "<td>" . $data['users'][$i]['firstname'] . "</td>";
					echo "<td>" . $data['users'][$i]['username'] . "</td>";
					echo "<td>" . $data['users'][$i]['email'] . "</td>";
					echo "<td>" . $roles[$data['users'][$i]['role']] . "</td>";
					if(!empty($data['users'][$i]['vendors'])){
						echo "<td>" . $data['users'][$i]['vendors'] . "</td>";
					}else{
						echo "<td>ALL</td>";
					}
					echo "<td><a href='/orders/public/account/delete/" . $data['users'][$i]['id'] . "'><input type='submit' class='btn btn-default' value='Delete'></a>
							  <a href='/orders/public/account/reset/" . $data['users'][$i]['id'] . "'><input type='submit' class='btn btn-default' value='Reset'></a>
							  <a href='/orders/public/account/edit/" . $data['users'][$i]['id'] . "'><input type='submit' class='btn btn-default' value='Edit'></a></td></td>";
					echo "</tr>";
				}
			?>
		</tbody>
	</table>