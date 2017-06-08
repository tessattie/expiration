<table class="table table-bordered">
		<thead>
			<tr><th colspan="6">Users</th></tr>
			<tr><th>Last name</th><th>First name</th><th>Username</th><th>Email</th><th>Access</th><th>Actions</th></tr>
			<form method = "POST" action = "/orders/public/account/index">
				<tr><th><input type="text" class="form-control" name="lastname" placeholder="Last name" required></th>
					<th><input type="text" class="form-control" name="firstname" placeholder="First name" required></th>
					<th><input type="text" class="form-control" name="username" placeholder="Username" required></th>
					<th><input type="email" class="form-control" name="email" placeholder="Email" required></th>
					<th>
						<select class= "form-control" name="role">
							<option value = "7">Order 0</option>
							<option value = "6">Order 1</option>
							<option value = "5">Order 2</option>
						</select>
					</th>
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
					echo "<td>" . $data['users'][$i]['role'] . "</td>";
					echo "<td><a href='/orders/public/account/delete/" . $data['users'][$i]['id'] . "'><input type='submit' class='btn btn-default' value='Delete'></a><a href='/orders/public/account/reset/" . $data['users'][$i]['id'] . "'><input type='submit' class='btn btn-default' value='Reset'></a></td>";
					echo "</tr>";
				}
			?>
		</tbody>
	</table>