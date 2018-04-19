<?php
/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ***********************************************************************************/

chdir (dirname(__FILE__) . '/..');
include_once 'vtigerversion.php';
include_once 'data/CRMEntity.php';
include_once 'includes/main/WebUI.php';
/* SalesPlatform.ru begin */
include_once 'Version.php';

$currentVersion = new Version($vtiger_current_version);
/* SalesPlatform.ru end */
$errorMessage = $_REQUEST['error'];
if (!$errorMessage) {
	/* 7.x module compatability check when coming from earlier version */
	if ($currentVersion->compare(new Version("7.0.0")) < 0) {
		/* NOTE: Add list-of modules that you own / sure to upgrade later */
		$skipCheckForModules = array();

		$extensionStoreInstance = Settings_ExtensionStore_Extension_Model::getInstance();
        //SalesPlatform.ru begin
		//$vtigerStandardModules = array('Accounts', 'Assets', 'Calendar', 'Campaigns', 'Contacts', 'CustomerPortal',
		//	'Dashboard', 'Emails', 'EmailTemplates', 'Events', 'ExtensionStore',
		//	'Faq', 'Google', 'HelpDesk', 'Home', 'Import', 'Invoice', 'Leads',
		//	'MailManager', 'Mobile', 'ModComments', 'ModTracker',
		//	'PBXManager', 'Portal', 'Potentials', 'PriceBooks', 'Products', 'Project', 'ProjectMilestone',
		//	'ProjectTask', 'PurchaseOrder', 'Quotes', 'RecycleBin', 'Reports', 'Rss', 'SalesOrder',
		//	'ServiceContracts', 'Services', 'SMSNotifier', 'Users', 'Vendors',
		//	'Webforms', 'Webmails', 'WSAPP');

        $vtigerStandardModules = array('Accounts', 'Assets', 'Calendar', 'Campaigns', 'Contacts', 'CustomerPortal',
			'Dashboard', 'Emails', 'EmailTemplates', 'Events', 'ExtensionStore',
			'Faq', 'Google', 'HelpDesk', 'Home', 'Import', 'Invoice', 'Leads',
			'MailManager', 'Mobile', 'ModComments', 'ModTracker',
			'PBXManager', 'Portal', 'Potentials', 'PriceBooks', 'Products', 'Project', 'ProjectMilestone',
			'ProjectTask', 'PurchaseOrder', 'Quotes', 'RecycleBin', 'Reports', 'Rss', 'SalesOrder',
			'ServiceContracts', 'Services', 'SMSNotifier', 'Users', 'Vendors',
			'Webforms', 'Webmails', 'WSAPP', 'Act', 'Documents', 'SPKladr', 'SPPDFTemplates',
            'SPPayments', 'SPSocialConnector', 'SPUnits', 'Search',
        );
        //SalesPlatform.ru end

		$skipCheckForModules = array_merge($skipCheckForModules, $vtigerStandardModules);

		$nonPortedExtns = array();
		$moduleModelsList = array();
		$db = PearDatabase::getInstance();
		$result = $db->pquery('SELECT name FROM vtiger_tab WHERE isentitytype != ? AND presence != ? AND trim(name) NOT IN ('.generateQuestionMarks($skipCheckForModules).')', array(1, 1, $skipCheckForModules));
		if ($db->num_rows($result)) {
			$moduleModelsList = $extensionStoreInstance->getListings();
		}

		$moduleModelsListByName = array();
		$moduleModelsListByLabel = array();
		foreach ($moduleModelsList as $moduleId => $moduleModel) {
			if ($moduleModel->get('name') != $moduleModel->get('label')) {
				$moduleModelsListByName[$moduleModel->get('name')] = $moduleModel;
			} else {
				$moduleModelsListByLabel[$moduleModel->get('label')] = $moduleModel;
			}
		}

		if ($moduleModelsList) {
			while($row = $db->fetch_row($result)) {
				$moduleName = $row['name'];//label
				if ($moduleName) {
					unset($moduleModel);
					if (array_key_exists($moduleName, $moduleModelsListByName)) {
						$moduleModel = $moduleModelsListByName[$moduleName];
					} else if (array_key_exists($moduleName, $moduleModelsListByLabel)) {
						$moduleModel = $moduleModelsListByLabel[$moduleName];
					}

					if ($moduleModel) {
						$vtigerVersion = $moduleModel->get('vtigerVersion');
						$vtigerMaxVersion = $moduleModel->get('vtigerMaxVersion');
						if (($vtigerVersion && strpos($vtigerVersion, '7.') === false)
								&& ($vtigerMaxVersion && strpos($vtigerMaxVersion, '7.') === false)) {
							$nonPortedExtns[] = $moduleName;
						}
					}
				}
			}

			if ($nonPortedExtns) {
				$portingMessage = 'Following custom modules are not compatible with Vtiger 7. Please disable these modules to proceed.';
				foreach ($nonPortedExtns as $moduleName) {
					$portingMessage .= "<li>$moduleName</li>";
				}
				$portingMessage .= '</ul>';
			}
		}
	}
}
?>
<!doctype>
<html>
	<head>
        <!-- SalesPlatform.ru begin -->
        <!--<title>Vtiger CRM Setup</title>-->
		<title><?php echo vtranslate('Vtiger CRM Setup', 'Migration') ?></title>
        <!-- SalesPlatform.ru end -->
        <!-- SalesPlatform.ru begin -->
        <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- SalesPlatform.ru end -->

		<script type="text/javascript" src="resources/js/jquery-min.js"></script>
		<link href="resources/todc/css/bootstrap.min.css" rel="stylesheet">
		<link href="resources/todc/css/todc-bootstrap.min.css" rel="stylesheet">
		<link href="resources/css/mkCheckbox.css" rel="stylesheet">
		<link href="resources/css/style.css" rel="stylesheet">
	</head>
	<body style="font-size: 14px !important;">
		<div class="container-fluid page-container">
                        <!-- SalesPlatform.ru begin -->
                        <!--<div class="row">-->
                        <div class="row head">
			<!-- SalesPlatform.ru end -->
				<div class="col-lg-6">
					<div class="logo">
                        <!-- SalesPlatform.ru begin -->
						<!-- <img src="resources/images/vt1.png" alt="Vtiger Logo"/> -->
                        <?php if ($currentVersion->compare(new Version("7.0.0")) < 0) { ?>
                            <img src="../layouts/vlayout/skins/images/logo.png"/>
                        <?php } else { ?>
                            <img src="../layouts/v7/skins/images/logo.png"/>
                        <?php } ?>
                        <!-- SalesPlatform.ru end -->
					</div>
				</div>
				<div class="col-lg-6">
                                        <!-- SalesPlatform.ru begin -->
					<!--<div class="head pull-right">-->
                                        <div class="pull-right">
						<!--  <h3>Migration Wizard</h3>  -->
                        <h3><?php echo vtranslate('Migration Wizard', 'Migration') ?></h3>
                        <!-- SalesPlatform.ru end -->
					</div>
				</div>
			</div>
			<div class="row main-container">
				<div class="col-lg-12 inner-container">
					<div class="row">
						<div class="col-lg-10">
                            <!-- SalesPlatform.ru begin -->
							<!--<h4 class="">Welcome</h4>-->
                            <h4 class=""><?php echo vtranslate('Welcome', 'Migration') ?></h4>
                            <!-- SalesPlatform.ru end -->
						</div>
						<div class="col-lg-2">
							<a href="http://salesplatform.ru/wiki/index.php/SalesPlatform_vtiger_crm_640_%D0%A2%D0%B5%D1%85%D0%BD%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%B5_%D1%80%D1%83%D0%BA%D0%BE%D0%B2%D0%BE%D0%B4%D1%81%D1%82%D0%B2%D0%BE#.D0.9C.D0.B8.D0.B3.D1.80.D0.B0.D1.86.D0.B8.D1.8F_.D1.81_.D0.BF.D1.80.D0.B5.D0.B4.D1.8B.D0.B4.D1.83.D1.89.D0.B8.D1.85_.D0.B2.D0.B5.D1.80.D1.81.D0.B8.D0.B9" target="_blank" class="pull-right">
								<img src="resources/images/help40.png" alt="Help-Icon"/>
							</a>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-lg-4 welcome-image">
							<img src="resources/images/migration_screen.png" alt="Vtiger Logo" style="width: 100%; margin-left: 15px;"/>
						</div>
						<?php
                                                        /* SalesPlatform.ru begin */
							//$currentVersion = explode('.', $vtiger_current_version);
                                                        /* SalesPlatform.ru end */
							 if ($portingMessage) { ?>
								<div class="col-lg-1"></div>
								<div class="col-lg-7">
                                    <!-- SalesPlatform.ru begin -->
									<!--<h3><font color="red">WARNING : Cannot continue with Migration</font></h3><br>-->
                                    <h3><font color="red"><?php echo vtranslate('WARNING : Cannot continue with Migration', 'Migration') ?></font></h3><br>
                                    <!-- SalesPlatform.ru end -->
									<p><?php echo $portingMessage;?></p>
								</div>
							</div>
							<div class="button-container col-lg-12">
								<div class="pull-right">
									<form action="../index.php?module=Migration&action=DisableModules&mode=fromMig" method="POST">
										<input type="hidden" name="modulesList" <?php echo 'value="'.Vtiger_Util_Helper::toSafeHTML(Zend_JSON::encode($nonPortedExtns)).'"'; ?> />
										<input type="submit" class="btn btn-warning" value="Disable modules & Proceed"/>
										<input type="button" onclick="window.location.href='../index.php'" class="btn btn-default" value="Close"/>
									</form>
								</div>
                        <!-- SalesPlatform.ru begin -->
                        <?php /* } else if($currentVersion[0] >= 6 && $currentVersion[1] >= 0) { */ ?>
			<?php // } else if($currentVersion[0] >= 6 && $currentVersion[1] >= 0 && $vtiger_current_version != '7.0.1-201711') { ?>
                        <?php } else if(($currentVersion->compare(new Version("5.4.0")) >= 0) && !$currentVersion->isLastVersion()) { ?>
                        <!-- SalesPlatform.ru end -->
							<div class="col-lg-8" style="padding-left: 30px;">
								<!-- SalesPlatform.ru begin -->
                                <!--<h3> Welcome to Vtiger Migration</h3>-->
                                <h3> <?php echo vtranslate('Welcome to Vtiger Migration', 'Migration') ?></h3>
                                <!-- SalesPlatform.ru end -->
								<?php if(isset($errorMessage)) {
									echo '<span><font color="red"><b>'.filter_var($errorMessage, FILTER_SANITIZE_STRING).'</b></font></span><br><br>';
								} ?>
                                <!-- SalesPlatform.ru begin -->
								<!--<p>We have detected that you have <strong>Vtiger <?php /* echo $vtiger_current_version */ ?></strong> installation.<br><br></p>-->
                                <p><?php echo vtranslate('We have detected that you have', 'Migration') ?> <strong><?php echo $currentVersion->asString() ?></strong> <?php echo vtranslate('installation', 'Migration') ?><br><br></p>
                                <!-- SalesPlatform.ru end -->
								<p>
                                    <!-- SalesPlatform.ru begin -->
									<!--<strong>Warning: </strong>-->
                                    <strong><?php echo vtranslate('Warning', 'Migration') ?>: </strong>
                                    <!-- SalesPlatform.ru end -->

                                    <!-- SalesPlatform.ru begin -->
									<!--Please note that it is not possible to revert back to <?php /* echo $vtiger_current_version */ ?>&nbsp;after the upgrade to vtiger 7 <br>-->
									<!--So, it is important to take a backup of the <?php /* echo $vtiger_current_version */ ?> installation, including the source files and database.-->
								    <?php 
                                                                        $lastVersion = new Version();
                                                                        echo vtranslate('Please note that it is not possible to revert back to', 'Migration') . ' ' . $currentVersion->asString() ?>&nbsp;<?php echo vtranslate('after the upgrade to vtiger 7', 'Migration') . " " . $lastVersion->asString() ?> <br>
									<?php echo vtranslate('So, it is important to take a backup of the', 'Migration') . ' ' . $currentVersion->asString() ?> <?php echo vtranslate('installation, including the source files and database', 'Migration') ?>

                                    <!-- SalesPlatform.ru end -->
                                </p><br>
								<form action="../index.php?module=Migration&action=Extract&mode=fromMig" method="POST">
                                    <!-- SalesPlatform.ru begin -->
									<!--<div><input type="checkbox" id="checkBox1" name="checkBox1"/><div class="chkbox"></div> I have taken the backup of database <a href="http://community.vtiger.com/help/vtigercrm/administrators/backup.html" target="_blank" >(how to?)</a></div><br>-->
									<!--<div><input type="checkbox" id="checkBox2" name="checkBox2"/><div class="chkbox"></div> I have taken the backup of source folder <a href="http://community.vtiger.com/help/vtigercrm/administrators/backup.html" target="_blank" >(how to?)</a></div><br>-->
									<div><input type="checkbox" id="checkBox1" name="checkBox1"/><div class="chkbox"></div> <?php echo vtranslate('I have taken the backup of database', 'Migration') ?> <a href="http://salesplatform.ru/wiki/index.php/SalesPlatform_vtiger_crm_640_%D0%A2%D0%B5%D1%85%D0%BD%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%B5_%D1%80%D1%83%D0%BA%D0%BE%D0%B2%D0%BE%D0%B4%D1%81%D1%82%D0%B2%D0%BE#.D0.91.D1.8D.D0.BA.D0.B0.D0.BF_.D0.B4.D0.B0.D0.BD.D0.BD.D1.8B.D1.85" target="_blank" ><?php echo vtranslate('how to?', 'Migration') ?></a></div><br>
									<div><input type="checkbox" id="checkBox2" name="checkBox2"/><div class="chkbox"></div> <?php echo vtranslate('I have taken the backup of source folder', 'Migration') ?> <a href="http://salesplatform.ru/wiki/index.php/SalesPlatform_vtiger_crm_640_%D0%A2%D0%B5%D1%85%D0%BD%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%B5_%D1%80%D1%83%D0%BA%D0%BE%D0%B2%D0%BE%D0%B4%D1%81%D1%82%D0%B2%D0%BE#.D0.91.D1.8D.D0.BA.D0.B0.D0.BF_.D0.B4.D0.B0.D0.BD.D0.BD.D1.8B.D1.85" target="_blank" ><?php echo vtranslate('how to?', 'Migration') ?></a></div><br>
                                    <!-- SalesPlatform.ru end -->
                                    <br>
									<div>
										<span id="error"></span>
                                        <!-- SalesPlatform.ru begin -->
										<!--User Name <span class="no">&nbsp;</span>-->
                                        <?php echo vtranslate('User Name', 'Migration') ?> <span class="no">&nbsp;</span>
                                        <!-- SalesPlatform.ru end -->
										<input type="text" value="" name="username" id="username" />&nbsp;&nbsp;
                                        <!-- SalesPlatform.ru begin -->
										<!--Password <span class="no">&nbsp;</span>-->
                                        <?php echo vtranslate('Password', 'Migration') ?> <span class="no">&nbsp;</span>
                                        <!-- SalesPlatform.ru end -->
										<input type="password" value="" name="password" id="password" />&nbsp;&nbsp;
									</div>
									<br><br><br>
									<div class="button-container">
                                        <!-- SalesPlatform.ru begin -->
                                        <!--<input type="submit" class="btn btn-primary" id="startMigration" name="startMigration" value="Start Migration" />-->
										<input type="submit" class="btn btn-primary" id="startMigration" name="startMigration" value="<?php echo vtranslate('Start Migration', 'Migration') ?>" />
									    <!-- SalesPlatform.ru end -->
                                    </div>
								</form>
							</div>
                        <!-- SalesPlatform.ru begin -->
						<?php /* } else if($currentVersion[0] < 6) { */ ?>
                        <?php // } else if($currentVersion[0] == 5 && $currentVersion[1] < 4) { ?>
                        <?php } else if(($currentVersion->compare(new Version("5.4.0")) < 0)) { ?>
                        <!-- SalesPlatform.ru end -->
							<div class="col-lg-1"></div>
							<div class="col-lg-7">
                                <!-- SalesPlatform.ru begin -->
								<!--<h3><font color="red">WARNING : Cannot continue with Migration</font></h3><br>-->
								<!--<p>We detected that this installation is running <strong>Vtiger CRM</strong>-->
                                <h3><font color="red"><?php echo vtranslate('WARNING : Cannot continue with Migration', 'Migration') ?></font></h3><br>
								<p><?php echo vtranslate('We detected that this installation is running', 'Migration') ?>
                                <!-- SalesPlatform.ru end -->
										<?php
                                                                                        /* SalesPlatform.ru begin */
//											if($vtiger_current_version < 6 ) {
                                                                                        /* SalesPlatform.ru end */
												echo '<b>'.$currentVersion->asString().'</b>';
                                                                                        /* SalesPlatform.ru begin */
//											}
                                                                                        /* SalesPlatform.ru end */
										?>.
                                    <!-- SalesPlatform.ru begin -->
									<!--Please upgrade to <strong>5.4.0</strong> first before continuing with this wizard.-->
                                    <?php echo vtranslate('Please upgrade to', 'Migration') ?> <strong>5.4.0</strong> <?php echo vtranslate('first before continuing with this wizard', 'Migration') ?>
                                    <!-- SalesPlatform.ru end -->
								</p>
							</div>
							<div class="button-container col-lg-12">
                                                                <!-- SalesPlatform.ru begin -->
								<!--<input type="button" onclick="window.location.href='index.php'" class="btn btn-primary pull-right" value="Finish"/>-->
                                                                <input type="button" onclick="window.location.href='../index.php'" class="btn btn-primary pull-right" value="<?php echo vtranslate('Finish', 'Migration') ?>"/>
                                                                <!-- SalesPlatform.ru end -->
						<?php } else { ?>
							<div class="col-lg-1"></div>
							<div class="col-lg-7">
                                <!-- SalesPlatform.ru begin -->
								<!--<h3><font color="red">WARNING : Cannot continue with Migration</font></h3>-->
                                <h3><font color="red"><?php echo vtranslate('WARNING : Cannot continue with Migration', 'Migration') ?></font></h3>
                                <!-- SalesPlatform.ru end -->
								<br>
								<p>
                                    <!-- SalesPlatform.ru begin -->
									<!--We detected that this source is upgraded latest version.-->
                                    <?php echo vtranslate('We detected that this source is upgraded latest version', 'Migration') ?>
                                    <!-- SalesPlatform.ru end -->
								</p>
							</div>
							<div class="button-container col-lg-12">
                            <!-- SalesPlatform.ru begin -->
						    <!--<input type="button" onclick="window.location.href='index.php'" class="btn btn-primary pull-right" value="Finish"/>-->
                            <input type="button" onclick="window.location.href='../index.php'" class="btn btn-primary pull-right" value="<?php echo vtranslate('Finish', 'Migration') ?>"/>
                            <!-- SalesPlatform.ru end -->
						<?php } ?>
					</div>
				</div>
			</div>
			<script>
				$(document).ready(function(){
					$('input[name="startMigration"]').click(function(){
						if($("#checkBox1").is(':checked') == false || $("#checkBox2").is(':checked') == false){
							alert('Before starting migration, please take your database and source backup');
							return false;
						}
						if($('#username').val() == '' || $('#password').val() == ''){
							alert('Please enter Admin credentials to start Migration');
							return false;
						}
						return true;
					});
				});
			</script>
	</body>
</html>
