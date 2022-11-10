<?php
	header("Pragma: no-cache");
	header("Cache-Control: no-cache");
	header("Expires: 0");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Merchant Check Out Page</title>
<meta name="GENERATOR" content="Evrsoft First Page">
</head>
<body>
	<h1>Merchant Check Out Page</h1>
	<pre>
	</pre>
	<form method="post" action="BankTransfer.php">
		<table border="1">
			<tbody>
				<tr>
					<th>S.No</th>
					<th>Label</th>
					<th>Value</th>
				</tr>
				<tr>
					<td>1</td>
					<td><label>ORDER_ID::*</label></td>
					<td><input id="ORDER_ID" tabindex="1" maxlength="20" size="20"
						name="ORDER_ID" autocomplete="off"
						value="<?php echo  "ORDS" . rand(10000,99999999)?>">
					</td>
				</tr>
				<tr>
					<td>2</td>
					<td><label>amount ::*</label></td>
					<td><input id="amount" tabindex="2" maxlength="12" size="12" name="amount" autocomplete="off" value="1.00"></td>
				</tr>
				<tr>
					<td>3</td>
					<td><label>beneficiaryIFSC ::*</label></td>
					<td><input id="beneficiaryIFSC" tabindex="4" maxlength="12" size="12" name="beneficiaryIFSC" autocomplete="off" value="CITI0100000"></td>
				</tr>
				<tr>
					<td>4</td>
					<td><label>beneficiaryAccount ::*</label></td>
					<td><input id="beneficiaryAccount" tabindex="4" maxlength="12"
						size="12" name="beneficiaryAccount" autocomplete="off" value="5683433111">
					</td>
				</tr>
				
				<tr>
					<td></td>
					<td></td>
					<td><input value="CheckOut" type="submit"	onclick=""></td>
				</tr>
			</tbody>
		</table>
		* - Mandatory Fields
	</form>
</body>
</html>