<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: SalesPlatform Ltd
 * The Initial Developer of the Original Code is SalesPlatform Ltd.
 * All Rights Reserved.
 * If you have any questions or comments, please email: devel@salesplatform.ru
 ************************************************************************************/
chdir (dirname(__FILE__) . '/..');
include_once 'vtigerversion.php';
include_once 'data/CRMEntity.php';
// SalesPlatform.ru begin
include_once 'config.inc.php';
// SalesPlatform.ru end

@session_start();

if(isset($_REQUEST['username']) && isset($_REQUEST['password'])){
	global $root_directory, $log;
	$userName = $_REQUEST['username'];
	$password = $_REQUEST['password'];

	$user = CRMEntity::getInstance('Users');
	$user->column_fields['user_name'] = $userName;
	if ($user->doLogin($password)) {
		$zip = new ZipArchive();
		$fileName = 'vtiger6.zip';
		if ($zip->open($fileName)) {
			for ($i = 0; $i < $zip->numFiles; $i++) {
				$log->fatal('Filename: ' . $zip->getNameIndex($i) . '<br />');
			}
			if ($zip->extractTo($root_directory)) {
				$zip->close();
				
				$userid = $user->retrieve_user_id($userName);
				$_SESSION['authenticated_user_id'] = $userid;
				// SalesPlatform.ru begin
				$_SESSION['app_unique_key'] = $application_unique_key;
				// SalesPlatform.ru end

				header('Location: ../index.php?module=Migration&view=Index&mode=step1');
			} else {
				$errorMessage = '<p>ОШИБКА ПРИ РАСПАКОВКЕ ZIP АРХИВА!</p>';
				header('Location: index.php?error='.$errorMessage);
			}
		} else {
			$errorMessage = 'ОШИБКА ЧТЕНИЯ ZIP АРХИВА!';
			header('Location: index.php?error='.$errorMessage);
		}
	} else {
		$errorMessage = 'НЕВЕРНЫЕ ДАННЫЕ';
		header('Location: index.php?error='.$errorMessage);
	}
}
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Обновление</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script type="text/javascript" src="resources/js/jquery-min.js"></script>
		<link href="resources/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link href="resources/css/mkCheckbox.css" rel="stylesheet">
		<link href="resources/css/style.css" rel="stylesheet">
    </head>
    <body>
		<div class="container-fluid page-container">
			<div class="row-fluid">
				<div class="span6">
					<div class="logo">
						<img src="resources/images/logo.png" alt="Vtiger Logo"/>
					</div>
				</div>
			</div>
			<div class="row-fluid main-container">
				<div class="span12 inner-container">
					<div class="row-fluid">
						<div class="span10">
							<h4 class=""> Менеджер миграции </h4>
						</div>
						<div class="span2">
							<a href="http://salesplatform.ru/wiki/index.php/SalesPlatform_vtiger_crm_630" target="_blank" class="pull-right">
								<img src="resources/images/help40.png" alt="Help-Icon"/>
							</a>
						</div>
					</div>
					<hr>
					<div class="row-fluid">
						<div class="span4 welcome-image">
							<img src="resources/images/migration_screen.png" alt="Vtiger Logo"/>
						</div>
						<div class="span8">
							<?php $currentVersion = explode('.', $vtiger_current_version);
							if(($currentVersion[0] >= 5 && $currentVersion[1] >= 4) || ($currentVersion[0] >= 6 && $currentVersion[1] >= 0)){?>
							<div>
								<h3> Добро пожаловать в Менеджер миграции </h3>
								<?php
								if(isset($_REQUEST['error'])) {
									echo '<span><font color="red"><b>'.$_REQUEST['error'].'</b></font></span><br><br>';
								}?>
								<p>Обнаружена установленная <strong>SalesPlatform Vtiger <?php echo $vtiger_current_version?> </strong>версии. <br> <br> </p>
								<p>
									<strong> Предупреждение: </strong>Пожалуйста, учтите что возврат к SalesPlatform Vtiger <?php echo $vtiger_current_version?> невозможен после обновления до SalesPlatform Vtiger 6.3.0-201507 <br><br>
									Необходимо сделать бэкап установленной SalesPlatform Vtiger <?php echo $vtiger_current_version?> включая исходный код и базу данных.</p>
								<form action="index.php" method="POST">
									<div><input type="checkbox" id="checkBox1" name="checkBox1"/> <div class="chkbox"></div> Я сделал(-а) бэкап базы данных <a href="http://salesplatform.ru/wiki/index.php/SalesPlatform_vtiger_crm_630_%D0%9C%D0%B8%D0%B3%D1%80%D0%B0%D1%86%D0%B8%D1%8F" target="_blank" >(руководство)</a> </div><br>
									<div><input type="checkbox" id="checkBox2" name="checkBox2"/> <div class="chkbox"></div> Я сделал(-а) бэкап папки с исходным кодом <a href="http://salesplatform.ru/wiki/index.php/SalesPlatform_vtiger_crm_630_%D0%9C%D0%B8%D0%B3%D1%80%D0%B0%D1%86%D0%B8%D1%8F" target="_blank" >(руководство)</a></div><br>
									<br><div>
										<span id="error"></span>
										Пользователь <span class="no">&nbsp;</span>
										<input type="text" value="" name="username" id="username" />&nbsp;&nbsp;
										Пароль <span class="no">&nbsp;</span>
										<input type="password" value="" name="password" id="password" />&nbsp;&nbsp;
									</div>
									<br><br><br>
									<div class="button-container">
										<input type="submit" class="btn btn-large btn-primary" id="startMigration" name="startMigration" value="Начать" />
									</div>
								</form>
							</div>
							<?php } else if($currentVersion[0] < 6){?>
							<div><br><br><br><br><br>
								<h3><font color='red'>Предупреждение: Невозможно продолжить миграцию</font></h3>
								<p>
									Обнаружена версия <strong>SalesPlatform Vtiger </strong><?php if($vtiger_current_version < 6 ) { echo '<b>'.$vtiger_current_version.'</b>'; } ?>.
									Пожалуйста, обновите версию системы до <strong>SalesPlatform Vtiger 5.4.0-201310</strong> перед тем как использовать Менеджер миграции. <a href="http://salesplatform.ru/wiki/index.php/SalesPlatform_vtiger_crm_630_%D0%9C%D0%B8%D0%B3%D1%80%D0%B0%D1%86%D0%B8%D1%8F" target="_blank" >(руководство)</a>
								</p>
								<br><br><br><br>
								<div class="button-container">
										<input type="button" onclick="window.location.href='index.php'" class="btn btn-large btn-primary" value="Закончить"/>
								</div>
							</div>
							<?php } else {?><br><br><br><br>
								<h3><font color='red'>Предупреждение: Невозможно продолжить миграцию</font></h3>
								<p>
									<strong>Система обновлена до последней версии.</strong>
								</p>
								<br><br><br><br>
								<div class="button-container">
										<input type="button" onclick="window.location.href='index.php'" class="btn btn-large btn-primary" value="Закончить"/>
								</div>
							<?php }?>
						</div>
					</div>
				</div>
			</div>
			<script>
				$(document).ready(function(){
					$('input[name="startMigration"]').click(function(){
						if($("#checkBox1").is(':checked') == false || $("#checkBox2").is(':checked') == false){
							alert('Пожалуйста, сделайте бэкап папки с иходным кодом и базы данных');
							return false;
						}
						if($('#username').val() == '' || $('#password').val() == ''){
							alert('Пожалуйста, введите логин и пароль Администратора системы');
							return false;
						}
						return true;
					});
				});
			</script>
    </body>
</html>

