<?php

/**
 *@license
 *Copyright (c) 2019 Cisco and/or its affiliates.
 *
 *This software is licensed to you under the terms of the Cisco Sample
 *Code License, Version 1.1 (the "License"). You may obtain a copy of the
 *License at
 *
 *			   https://developer.cisco.com/docs/licenses
 *
 *All use of the material herein must be in accordance with the terms of
 *the License. All rights not expressly granted by the License are
 *reserved. Unless required by applicable law or agreed to separately in
 *writing, software distributed under the License is distributed on an "AS
 *IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express
 *or implied.
 */




/**
*@author	Gary Oppel (gaoppel@cisco.com)
*@author	Hosuk Won (howon@cisco.com)
*@contributor	Drew Betz (anbetz@cisco.com)
*/
	
	ini_set('display_errors', 'Off');
	session_start();

	$license = <<< TEXT
	Copyright (c) 2019 Cisco and/or its affiliates.

	This software is licensed to you under the terms of the Cisco Sample
	Code License, Version 1.1 (the "License"). You may obtain a copy of the
	License at

				   https://developer.cisco.com/docs/licenses

	All use of the material herein must be in accordance with the terms of
	the License. All rights not expressly granted by the License are
	reserved. Unless required by applicable law or agreed to separately in
	writing, software distributed under the License is distributed on an "AS
	IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express
	or implied.
TEXT;

	$installerOutput = "";
	$platformDetails = "";
	$platformValid = true;

	//Installer Local Functions

	function generatePassword( $length = 8 ){
		$generatedPsk = "";
		
		//Define the Alphabet for Generating Random Passwords or PSK
		$alphabet = "abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ123456789!?#$%@*()";
		
		//Limit the maximum length to 64 Characters
		if($length > 64){ $length = 64;}

		//Loop through and select random characters from the alphabet
		for($char = 0; $char < $length; $char++){
			$generatedPsk .= substr($alphabet, random_int(0,strlen($alphabet)),1);
		}
		
		//Return Randomly generated Password or PSK
		return $generatedPsk;
	}
	
	function generateEncryptionKey(){
		$key = random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
		return base64_encode($key);
	}
	
	//Main Installer Code
	if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['step'])){
		if($_POST['step'] == 1){
			$installerOutput = <<< HTML
			<form method="POST" action="/installer.php">
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col"><textarea style="height: 350px; width: 750px;" readonly>$license</textarea></div>
					<div class="col-2"></div>
				</div>
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col"><strong>Click Next to continue Setup.</strong></div>
					<div class="col-2"></div>
				</div>
				<div class="row">
					<div class="col"></div>
					<div class="col-2"><input type="hidden" name="step" value="2"><input type="submit" id="btnNext" value="Next >" class="btn btn-primary shadow"></div>
				</div>
			</form>
			<script type="text/javascript">

			</script>
HTML;
		}elseif($_POST['step'] == 2){
			
			//Check PHP Version & Extensions for Proper Operation
			if (version_compare(PHP_VERSION, '7.2.0') >= 0){ $platformDetails .=  "<div><span style=\"color: #2d8c32\" data-feather=\"check-circle\"></span> PHP Version <strong>'".PHP_VERSION."'</strong></div>"; $platformValid = true; }else{ $platformDetails .=  "<div><span style=\"color: #ff0000\" data-feather=\"x-circle\"></span> PHP Version '".PHP_VERSION."' - <strong>Version > 7.2 Required</strong></div>"; $platformValid = false;}
			if (extension_loaded('mbstring')){ $platformDetails .=  "<div><span style=\"color: #2d8c32\" data-feather=\"check-circle\"></span> PHP Extension <strong>'mbstring'</strong> Installed</div>"; }else{ $platformDetails .=  "<div><span style=\"color: #ff0000\" data-feather=\"x-circle\"></span> PHP Extension <strong>'mbstring'</strong> is NOT Installed</div>"; $platformValid = false;}
			if (extension_loaded('ldap')){ $platformDetails .=  "<div><span style=\"color: #2d8c32\" data-feather=\"check-circle\"></span> PHP Extension <strong>'ldap'</strong> Installed</div>"; }else{ $platformDetails .= "<div><span style=\"color: #ff0000\" data-feather=\"x-circle\"></span> PHP Extension <strong>'ldap'</strong> is NOT Installed</div>"; $platformValid = false;}
			if (extension_loaded('mysqli')){ $platformDetails .=  "<div><span style=\"color: #2d8c32\" data-feather=\"check-circle\"></span> PHP Extension <strong>'mysqli'</strong> Installed</div>"; }else{ $platformDetails .= "<div><span style=\"color: #ff0000\" data-feather=\"x-circle\"></span> PHP Extension <strong>'mysqli'</strong> is NOT Installed</div>"; $platformValid = false;}
			if (extension_loaded('mysqlnd')){ $platformDetails .=  "<div><span style=\"color: #2d8c32\" data-feather=\"check-circle\"></span> PHP Extension <strong>'mysqlng'</strong> Installed</div>"; }else{ $platformDetails .= "<div><span style=\"color: #ff0000\" data-feather=\"x-circle\"></span> PHP Extension <strong>'mysqlng'</strong> is NOT Installed</div>"; $platformValid = false;}
			if (extension_loaded('curl')){ $platformDetails .=  "<div><span style=\"color: #2d8c32\" data-feather=\"check-circle\"></span> PHP Extension <strong>'curl'</strong> Installed</div>"; }else{ $platformDetails .= "<div><span style=\"color: #ff0000\" data-feather=\"x-circle\"></span> PHP Extension <strong>'curl'</strong> is NOT Installed</div>"; $platformValid = false;}  
			if (extension_loaded('simplexml')){ $platformDetails .=  "<div><span style=\"color: #2d8c32\" data-feather=\"check-circle\"></span> PHP Extension <strong>'simplexml'</strong> Installed</div>";}else{ $platformDetails .= "<div><span style=\"color: #ff0000\" data-feather=\"x-circle\"></span> PHP Extension <strong>'simplexml'</strong> is NOT Installed</div>"; $platformValid = false;}
			
			if($platformValid == true){
				$nextText = "Click Next to continue to Database Setup.";
				$nextButton = '<input type="hidden" name="step" value="3"><input type="submit" id="btnNext" value="Next >" class="btn btn-primary shadow">';
			}else{
				$nextText = "System Requirement check failure, please confirm system prerequisites before proceeding.";
				$nextButton = '';
			}
				
			$installerOutput = <<< HTML
			<form method="POST" action="/installer.php">
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col text-left"><h4>PHP Validation Checks</h4> $platformDetails</div>
					<div class="col-2"></div>
				</div>
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col"><strong>$nextText</strong></div>
					<div class="col-2"></div>
				</div>
				<div class="row">
					<div class="col"></div>
					<div class="col-2">$nextButton</div>
				</div>
			</form>
			<script type="text/javascript">
				$(function() {	
					feather.replace()
				});
			</script>

HTML;
		}elseif($_POST['step'] == 3){
			$installerOutput = <<< HTML
			<form method="POST" action="/installer.php">
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col text-left"><h4>MySQL Database Parameters</h4></div>
					<div class="col-2"></div>
				</div>
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col text-left">
						<label class="font-weight-bold" for="dbhostname">MySQL Server IP/FQDN:</label>
						<div class="form-group input-group-sm font-weight-bold">
							<input type="text" my-field-state="required" class="form-control shadow my-form-field" id="dbhostname" name="dbhostname">
							<div class="invalid-feedback">Please enter a valid Username</div>
						</div>
						<label class="font-weight-bold" for="dbusername">iPSK Database Username:</label>
						<div class="form-group input-group-sm font-weight-bold">
							<input type="text" my-field-state="required" class="form-control shadow my-form-field" id="dbusername" name="dbusername">
							<div class="invalid-feedback">Please enter a Name</div>
						</div>
						<label class="font-weight-bold" for="iseusername">Cisco ISE ODBC Username:</label>
						<div class="form-group input-group-sm font-weight-bold">
							<input type="text" my-field-state="required" class="form-control shadow my-form-field" id="iseusername" name="iseusername">
							<div class="invalid-feedback">Please enter a Name</div>
						</div>
						<label class="font-weight-bold" for="databasename">iPSK Database Name:</label>
						<div class="form-group input-group-sm font-weight-bold">
							<input type="text" my-field-state="required" class="form-control shadow my-form-field" id="databasename" name="databasename">
							<div class="invalid-feedback">Please enter a Name</div>
						</div>
						<label class="font-weight-bold" for="rootusername">MySQL Admin/Root Username:</label>
						<div class="form-group input-group-sm font-weight-bold">
							<input type="text" class="form-control shadow" id="rootusername" name="rootusername">
						</div>
						<label class="font-weight-bold" for="rootpassword">MySQL Admin/Root Password:</label>
						<div class="form-group input-group-sm font-weight-bold">
							<input type="password" my-field-state="required" class="form-control shadow my-form-field" id="rootpassword" name="rootpassword">
							<div class="invalid-feedback">Please enter a password</div>
						</div>
					</div>
					<div class="col-2"></div>
				</div>
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col"><strong>Click Next to continue Setup.</strong></div>
					<div class="col-2"></div>
				</div>
				<div class="row">
					<div class="col"></div>
					<div class="col-2"><input type="hidden" name="step" value="4"><input type="submit" id="btnNext" value="Next >" class="btn btn-primary shadow"></div>
				</div>
			</form>
			<script type="text/javascript">

			</script>
HTML;
		}elseif($_POST['step'] == 4){
			$_SESSION['dbhostname'] = (isset($_POST['dbhostname'])) ? $_POST['dbhostname'] : '';
			$_SESSION['dbusername'] = (isset($_POST['dbusername'])) ? $_POST['dbusername'] : '';
			$_SESSION['iseusername'] = (isset($_POST['iseusername'])) ? $_POST['iseusername'] : '';
			$_SESSION['databasename'] = (isset($_POST['databasename'])) ? $_POST['databasename'] : '';
			$_SESSION['rootusername'] = (isset($_POST['rootusername'])) ? $_POST['rootusername'] : '';
			$_SESSION['rootpassword'] = (isset($_POST['rootpassword'])) ? $_POST['rootpassword'] : '';
			
			$installerOutput = <<< HTML
			<form method="POST" action="/installer.php">
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col text-left"><h4>iPSK Manager's Administrator Password</h4></div>
					<div class="col-2"></div>
				</div>
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col text-left">
						<label class="font-weight-bold" for="password">Administrator Password:</label>
						<div class="form-group input-group-sm font-weight-bold">
							<input type="password" my-field-state="required" class="form-control shadow my-form-field my-password-field" id="password" name="password">
							<div class="invalid-feedback">Please enter a password</div>
						</div>
						<label class="font-weight-bold" for="confirmpassword">Confirm Administrator Password:</label>
						<div class="form-group input-group-sm font-weight-bold">
							<input type="password" my-field-state="required" class="form-control shadow my-form-field" id="confirmpassword" value="">
							<div class="invalid-feedback">Please confirm your password</div>
							<div class="font-weight-bold small" id="passwordfeedback"></div>
						</div>
					</div>
					<div class="col-2"></div>
				</div>
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col"><strong>Click Next to continue Setup.</strong></div>
					<div class="col-2"></div>
				</div>
				<div class="row">
					<div class="col"></div>
					<div class="col-2"><input type="hidden" name="step" value="5"><input type="submit" id="btnNext" value="Next >" class="btn btn-primary shadow"></div>
				</div>
			</form>
			<script type="text/javascript">			
				$("#password,#confirmpassword").keyup(function(event){
					var pass = $("#password").val();
					var confirmpass = $("#confirmpassword").val();

					if(pass != confirmpass){
						$("#passwordfeedback").removeClass('text-success');
						$("#passwordfeedback").addClass('text-danger');
						$("#passwordfeedback").html('Passwords must match and be at least 6 characters long!');
					}else{
						$("#passwordfeedback").addClass('text-success');
						$("#passwordfeedback").removeClass('text-danger');
						$("#passwordfeedback").html('Passwords Match!');
					}
					pass = "";
					confirmpass = "";
					
				});
			</script>
HTML;
		}elseif($_POST['step'] == 5){
			$_SESSION['adminpassword'] = (isset($_POST['password'])) ? $_POST['password'] : '';
			
			$installerOutput = <<< HTML
			<form method="POST" action="/installer.php">
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col text-center"><h5>Please confirm the following setup parameters:</h5></div>
					<div class="col-2"></div>
				</div>
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col text-left"><strong>MySQL Server IP/FQDN:</strong> {$_SESSION['dbhostname']}</div>
					<div class="col-2"></div>
				</div>
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col text-left"><strong>iPSK Database Username:</strong> {$_SESSION['dbusername']}</div>
					<div class="col-2"></div>
				</div>
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col text-left"><strong>Cisco ISE ODBC Username:</strong> {$_SESSION['iseusername']}</div>
					<div class="col-2"></div>
				</div>
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col text-left"><strong>iPSK Database Name:</strong> {$_SESSION['databasename']}</div>
					<div class="col-2"></div>
				</div>
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col text-left"><strong>MySQL Admin/Root Username:</strong> {$_SESSION['rootusername']}</div>
					<div class="col-2"></div>
				</div>
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col"><strong>Click Install to begin Installation.</strong></div>
					<div class="col-2"></div>
				</div>
				<div class="row">
					<div class="col"></div>
					<div class="col-2"><input type="hidden" name="step" value="6"><input type="submit" id="btnNext" value="Install" class="btn btn-primary shadow"></div>
				</div>
			</form>
			<script type="text/javascript">			
				
			</script>
HTML;
		}elseif($_POST['step'] == 6){
			$installProgress = "";
			$_SESSION['installSuccess'] = false;
			
			$orgTime = time();
			
			$baseSid = "S-1-9";
			$orgSid = "$orgTime-$orgTime";
			$systemSid = "1";
			
			$systemSID = "$baseSid-$orgSid-1";
			$adminSID = "$baseSid-$orgSid-500";
			
			$managerDbPassword = generatePassword(16);
			$iseDbPassword = generatePassword(16);
			
			$encryptionKey = generateEncryptionKey();
			
			$ipskManagerAdminPassword = password_hash($_SESSION['adminpassword'], PASSWORD_DEFAULT);
			
			$dbConnection = new mysqli($_SESSION['dbhostname'], $_SESSION['rootusername'], $_SESSION['rootpassword']);
			
			if($dbConnection->connect_error) {
				$installProgress .= "<div><span style=\"color: #ff0000\" data-feather=\"x-circle\"></span>'MySQL Error: (".$dbConnection->connect_errno.") ".$dbConnection->connect_error."</div>";
				goto Bail;
			}else{
				$installProgress .= "<div><span style=\"color: #2d8c32\" data-feather=\"check-circle\"></span>MySQL Connection Successful</div>";
			}
							
			if($dbConnection->select_db($_SESSION['databasename'])){
				$installProgress .= "<div><span style=\"color: #ff0000\" data-feather=\"x-circle\"></span>Database Already Exists</div>";
				goto Bail;
			}else{
				$installProgress .= "<div><span style=\"color: #2d8c32\" data-feather=\"check-circle\"></span>Database is not in use</div>";
			}
			
			$dbConnection->select_db("mysql");
			$createDbQuery = sprintf("CREATE DATABASE `%s`", $dbConnection->real_escape_string($_SESSION['databasename']));
			
			if(!$dbConnection->query($createDbQuery)){
				$installProgress .= "<div><span style=\"color: #ff0000\" data-feather=\"x-circle\"></span>Database Create Error: (".$dbConnection->connect_errno.") ".$dbConnection->connect_error."</div>";
				goto Bail;
			}else{
				$installProgress .= "<div><span style=\"color: #2d8c32\" data-feather=\"check-circle\"></span>Database Created Successfully</div>";
			}
			
			$dbConnection->select_db($_SESSION['databasename']);
			$actionValid = true;
			
			include_once("installer.inc.php");
			
			for($sqlCount = 0; $sqlCount < count($sqlProcedure); $sqlCount++){
				if(!$dbConnection->query($sqlProcedure[$sqlCount])){
					$actionValid = false;
				}				
			}
			
			if(!$actionValid){
				$installProgress .= "<div><span style=\"color: #ff0000\" data-feather=\"x-circle\"></span>Stored Procedure Creation Failure</div>";
				goto Bail;
			}else{
				$installProgress .= "<div><span style=\"color: #2d8c32\" data-feather=\"check-circle\"></span>Stored Procedures Created Successfully</div>";
			}
			
			for($sqlCount = 0; $sqlCount < count($sqlTable); $sqlCount++){
				if(!$dbConnection->query($sqlTable[$sqlCount])){
					$actionValid = false;
				}				
			}
			
			if(!$actionValid){
				$installProgress .= "<div><span style=\"color: #ff0000\" data-feather=\"x-circle\"></span>Table Creation Failure</div>";
				goto Bail;
			}else{
				$installProgress .= "<div><span style=\"color: #2d8c32\" data-feather=\"check-circle\"></span>Tables Created Successfully</div>";
			}

			for($sqlCount = 0; $sqlCount < count($sqlAlterTable); $sqlCount++){
				if(!$dbConnection->query($sqlAlterTable[$sqlCount])){
					$actionValid = false;
				}				
			}
			
			if(!$actionValid){
				$installProgress .= "<div><span style=\"color: #ff0000\" data-feather=\"x-circle\"></span>Altering of Tables Failed</div>";
				goto Bail;
			}else{
				$installProgress .= "<div><span style=\"color: #2d8c32\" data-feather=\"check-circle\"></span>Alerting of Tables Successful</div>";
			}
			
			for($sqlCount = 0; $sqlCount < count($sqlConstraint); $sqlCount++){
				if(!$dbConnection->query($sqlConstraint[$sqlCount])){
					$actionValid = false;
				}				
			}
			
			if(!$actionValid){
				$installProgress .= "<div><span style=\"color: #ff0000\" data-feather=\"x-circle\"></span>Setting of Contraints Failed</div>";
				goto Bail;
			}else{
				$installProgress .= "<div><span style=\"color: #2d8c32\" data-feather=\"check-circle\"></span>Setting of Contraints Successful</div>";
			}
			
			for($sqlCount = 0; $sqlCount < count($sqlInsert); $sqlCount++){
				if(!$dbConnection->query($sqlInsert[$sqlCount])){
					$actionValid = false;
				}				
			}
			
			if(!$actionValid){
				$installProgress .= "<div><span style=\"color: #ff0000\" data-feather=\"x-circle\"></span>Inserting of inital data Failed</div>";
				goto Bail;
			}else{
				$installProgress .= "<div><span style=\"color: #2d8c32\" data-feather=\"check-circle\"></span>Inserting of initial data Successful</div>";
			}
			
			for($sqlCount = 0; $sqlCount < count($managerSqlPermissions); $sqlCount++){
				if(!$dbConnection->query($managerSqlPermissions[$sqlCount])){
					$actionValid = false;
				}				
			}
			
			if(!$actionValid){
				$installProgress .= "<div><span style=\"color: #ff0000\" data-feather=\"x-circle\"></span>Creation of iPSK Manager MySQL User Failed</div>";
				goto Bail;
			}else{
				$installProgress .= "<div><span style=\"color: #2d8c32\" data-feather=\"check-circle\"></span>Creation of iPSK Manager MySQL User Successful</div>";
			}

			for($sqlCount = 0; $sqlCount < count($iseSqlPermissions); $sqlCount++){
				if(!$dbConnection->query($iseSqlPermissions[$sqlCount])){
					$actionValid = false;
				}				
			}
			
			if(!$actionValid){
				$installProgress .= "<div><span style=\"color: #ff0000\" data-feather=\"x-circle\"></span>Creation of Cisco ISE MySQL User Failed</div>";
				goto Bail;
			}else{
				$installProgress .= "<div><span style=\"color: #2d8c32\" data-feather=\"check-circle\"></span>Creation of Cisco ISE MySQL User Successful</div>";
			}			
			
			if(!file_put_contents("../supportfiles/include/config.php", $configurationFile)){
				$installProgress .= "<div><span style=\"color: #ff0000\" data-feather=\"x-circle\"></span>Error creating config.php file</div>";
				goto Bail;
			}else{
				$installProgress .= "<div><span style=\"color: #2d8c32\" data-feather=\"check-circle\"></span>Successfully Created config.php</div>";
				$_SESSION['installSuccess'] = true;
				$_SESSION['installDetails'] = $installDetails;
			}
			
			Bail:
			
			if($_SESSION['installSuccess']){
				$finalizeButton = '<input type="hidden" name="step" value="7"><input type="submit" id="btnFinalize" value="Finalize Installation" class="btn btn-primary shadow">';
				$finalizeButtonScript = '$("#btnFinalize").click( function(event) {coaTimer = setInterval("redirectToAdminPortal()", 5000);});';
				$finalErrorText = '<strong>Click Finalize Installation to finish the installation. <strong><br/> <p class="text-danger">Please Note: After you click finalize a file download will commence providing you with the Installation details and encryption key.</p>';
			}else{
				$finalizeButton = '';
				$finalizeButtonScript = '';
				$finalErrorText = '<p class="text-danger"><strong>The installation failed to finish correctly. </strong></p>';
			}
			
			$installerOutput = <<< HTML
			<form method="POST" action="/installer.php">
				<div class="row">
					<div class="col-2"></div>
					<div class="col float-rounded mx-auto shadow-lg p-2 bg-white text-left"
						<strong>Installation Results:</strong>
						<div class="row">
							<div class="col-1"></div>
							<div class="col border border-primary text-left">$installProgress</div>
							<div class="col-1"></div>
						</div>
					</div>
					<div class="col-2"></div>
				</div>
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col">$finalErrorText</div>
					<div class="col-2"></div>
				</div>
				<div class="row">
					<div class="col"></div>
					<div class="col-3">$finalizeButton</div>
				</div>
			</form>
			<script type="text/javascript">
				$(function() {	
					feather.replace()
				});
				
				$finalizeButtonScript
				
				function redirectToAdminPortal(){
					window.location = "/";
				}
			</script>
HTML;

		}elseif($_POST['step'] == 7){
			if($_SESSION['installSuccess']){
				header('Content-Description: File Transfer');
				header('Content-Type: plain/text');
				header('Content-Disposition: attachment; filename=DONOTDELETE-iPSKManager-Install.txt'); 
				header('Content-Transfer-Encoding: text');
				header('Content-Length: '.strlen($_SESSION['installDetails']));
				echo $_SESSION['installDetails'];
				
				session_destroy();
				unlink("installer.inc.php");
				unlink("installer.php");
				
				exit(0);
			}
		}
	}else{
		$installerOutput = <<< HTML
			<form method="POST" action="/installer.php">
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col text-left"><p>Welcome to the installer for iPSK Manager.  The installer will perform the intial setup and database population as well as create the proper credentials for both iPSK Manager and Cisco ISE for the ODBC integration.<br /><br />
					The installer is assuming that the installation will take place on a single server with a local MySQL database, for a non-standard deployment, please refer to the Installation Directions in the README File.<br /><br />
					The installer will setup the MySQL database and permissions required for proper operation.  It will also auto-generate the MySQL Database Passwords and Encryption Key for specific data stored within the Database.
					</p></div>
					<div class="col-2"></div>
				</div>
				<div class="row m-2">
					<div class="col-2"></div>
					<div class="col"><strong>Click Next to continue and check the system requirements.</strong></div>
					<div class="col-2"></div>
				</div>
				<div class="row">
					<div class="col"></div>
					<div class="col-2"><input type="hidden" name="step" value="1"><input type="submit" id="btnNext" value="Next >" class="btn btn-primary shadow"></div>
				</div>
			</form>
			<script type="text/javascript">
			</script>

HTML;

	}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>iPSK Manager Installer</title>

    <!-- Bootstrap core CSS -->
    <link href="styles/bootstrap.min.css" rel="stylesheet">

    <link href="styles/installer.css" rel="stylesheet">
	
	<script type="text/javascript" src="scripts/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="scripts/feather.min.js"></script>
	
  </head>

  <body class="text-center">
    <div class="float-rounded mx-auto shadow-lg p-2 bg-white window-install">
		<div class="mt-2 mb-4">
			<img src="images/iPSK-Logo.svg" width="108" height="57" />
		</div>
		<h1 class="h3 mt-2 mb-4 font-weight-normal">iPSK Manager Installer</h1>
		<?php print $installerOutput;?>
		<p class="mt-5 mb-0 text-muted">Copyright &copy; 2019 Cisco and/or its affiliates.</p>
	</div>
  </body>
</html>