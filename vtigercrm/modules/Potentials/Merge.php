<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/

require_once('include/database/PearDatabase.php');
require_once('config.php');
require_once('include/utils/MergeUtils.php');
include_once 'modules/Potentials/SPPotentialsPDFController.php';
global $currentModule;
global $app_strings;
global $default_charset;

function sort_by_length_desc($a, $b) {
    return strlen($b) - strlen($a);
}


// Fix For: http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/2107
$randomfilename = "vt_" . str_replace(array("."," "), "", microtime());

$templateid = $_REQUEST['mergefile'];

if($templateid == "")
{
	die("Select Mail Merge Template");
}
//get the particular file from db and store it in the local hard disk.
//store the path to the location where the file is stored and pass it  as parameter to the method 
$sql = "select filename,data,filesize from vtiger_wordtemplates where templateid=?";
$result = $adb->pquery($sql, array($templateid));
$temparray = $adb->fetch_array($result);

$fileContent = $temparray['data'];
$filename=html_entity_decode($temparray['filename'], ENT_QUOTES, $default_charset);
$extension=GetFileExtension($filename);
// Fix For: http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/2107
$filename= $randomfilename . "_mmrg.$extension";

$filesize=$temparray['filesize'];
$wordtemplatedownloadpath =$root_directory ."/test/wordtemplatedownload/";


$handle = fopen($wordtemplatedownloadpath .$filename,"wb");
fwrite($handle,base64_decode($fileContent),$filesize);
fclose($handle);

if (GetFileExtension($filename)=="doc") {
echo "<html>
<body>
<script>
if (document.layers)
{
    document.write(\"This feature requires IE 5.5 or higher for Windows on Microsoft Windows 2000, Windows NT4 SP6, Windows XP.\");
    document.write(\"<br><br>Click <a href='#' onclick='window.history.back();'>here</a> to return to the previous page\");
}   
else if (document.layers || (!document.all && document.getElementById))
{
    document.write(\"This feature requires IE 5.5 or higher for Windows on Microsoft Windows 2000, Windows NT4 SP6, Windows XP.\");
    document.write(\"<br><br>Click <a href='#' onclick='window.history.back();'>here</a> to return to the previous page\"); 
}
else if(document.all)
{
    document.write(\"<br><br>Click <a href='#' onclick='window.history.back();'>here</a> to return to the previous page\");
    document.write(\"<OBJECT Name='vtigerCRM' codebase='modules/Settings/vtigerCRM.CAB#version=1,5,0,0' id='objMMPage' classid='clsid:0FC436C2-2E62-46EF-A3FB-E68E94705126' width=0 height=0></object>\");
}
</script>";    
}

//<<<<<<<<<<<<<<<<<<<<<<<<<<<for mass merge>>>>>>>>>>>>>>>>>>>>>>>>>>>
$mass_merge = $_REQUEST['allselectedboxes'];
$single_record = $_REQUEST['record'];

if($mass_merge != "")
{	
	$mass_merge = explode(";",$mass_merge);
	$temp_mass_merge = $mass_merge;
	if(array_pop($temp_mass_merge)=="")
		array_pop($mass_merge);
	//$mass_merge = implode(",",$mass_merge);
}
else if($single_record != "")
{
	$mass_merge = array($single_record);
}
else
{
	die("Record Id is not found, cannot merge the document");
}

$avail_pick_arr = getAccessPickListValues('Potentials');

$controller = new SalesPlatform_PotentialsPDFController($currentModule, 0);
$mergevalue = array();
foreach($mass_merge as $idx => $entityid) {
    $controller->loadRecord($entityid);

    $model = $controller->buildDocumentModel();

    if(!isset($csvheader)) {
        $csvheader = implode(',', $model->keys());
    }

    $keys_array = explode(',', $csvheader);
    
    // Sort array by key length desc
    usort($keys_array, "sort_by_length_desc");
    $csvheader = implode(',', $keys_array);

    $x = 0;
    $actual_values = array();
    foreach($keys_array as $key) {
        $value = $model->get($key);

        if(trim($value) == "--None--" || trim($value) == "--none--")
        {
            $value = "";
        }

        $actual_values[$x] = $value;
        $actual_values[$x] = str_replace('"'," ",$actual_values[$x]);

        //if value contains any line feed or carriage return replace the value with ".value."
        if (preg_match ("/(\r?\n)/", $actual_values[$x]))
        {
            $actual_values[$x] = '"'.$actual_values[$x].'"';
        }
        
        if (preg_match ("/^[0-9, ]+$/", $actual_values[$x])) {
    	    $actual_values[$x] = decode_html(str_replace(",",".",$actual_values[$x]));
        } else {
    	    $actual_values[$x] = decode_html(str_replace(","," ",$actual_values[$x]));
    	}

        $x++;
    }

    $mergevalue[] = implode($actual_values,",");
}
$csvdata = implode($mergevalue,"###");

echo "<br><br><br>";
$extension = GetFileExtension($filename);
if($extension == "doc")
{
    // Fix for: http://trac.vtiger.com/cgi-bin/trac.cgi/ticket/2107
    $datafilename = $randomfilename . "_data.csv";
 
    $handle = fopen($wordtemplatedownloadpath.$datafilename,"wb");
    fwrite($handle,$csvheader."\r\n");
    fwrite($handle,str_replace("###","\r\n",$csvdata));
    fclose($handle);
}
else if($extension == "odt")
{
    //delete old .odt files in the wordtemplatedownload directory
    foreach (glob("$wordtemplatedownloadpath/*.odt") as $delefile) 
    {
        unlink($delefile);
    }
    if (!is_array($mass_merge)) $mass_merge = array($mass_merge);
    foreach($mass_merge as $idx => $entityid) {
        $temp_dir=entpack($filename,$wordtemplatedownloadpath,$fileContent);
        $concontent=file_get_contents($wordtemplatedownloadpath.$temp_dir.'/content.xml');
        unlink($wordtemplatedownloadpath.$temp_dir.'/content.xml');
        $new_filecontent=crmmerge($csvheader,$concontent,$idx,'htmlspecialchars');
        $stycontent=file_get_contents($wordtemplatedownloadpath.$temp_dir.'/styles.xml');
        unlink($wordtemplatedownloadpath.$temp_dir.'/styles.xml');
        $new_filestyle=crmmerge($csvheader,$stycontent,$idx,'htmlspecialchars');
        packen($entityid.$filename,$wordtemplatedownloadpath,$temp_dir,$new_filecontent,$new_filestyle);

        echo "&nbsp;&nbsp;<font size=+1><b><a href=test/wordtemplatedownload/$entityid$filename>".$app_strings['DownloadMergeFile']."</a></b></font><br>";
        remove_dir($wordtemplatedownloadpath.$temp_dir);
    }
}
else if($extension == "docx")
{
    //delete old .docx files in the wordtemplatedownload directory
    foreach (glob("$wordtemplatedownloadpath/*.docx") as $delefile) 
    {
        unlink($delefile);
    }
    if (!is_array($mass_merge)) $mass_merge = array($mass_merge);
    foreach($mass_merge as $idx => $entityid) {
        $temp_dir=entpack($filename,$wordtemplatedownloadpath,$fileContent);
        $concontent=file_get_contents($wordtemplatedownloadpath.$temp_dir.'/word/document.xml');
        unlink($wordtemplatedownloadpath.$temp_dir.'/word/document.xml');
        $new_filecontent=crmmerge($csvheader,$concontent,$idx,'htmlspecialchars');
        $stycontent=file_get_contents($wordtemplatedownloadpath.$temp_dir.'/word/styles.xml');
        unlink($wordtemplatedownloadpath.$temp_dir.'/word/styles.xml');
        $new_filestyle=crmmerge($csvheader,$stycontent,$idx,'htmlspecialchars');
        packendocx($entityid.$filename,$wordtemplatedownloadpath,$temp_dir,$new_filecontent,$new_filestyle);

        echo "&nbsp;&nbsp;<font size=+1><b><a href=test/wordtemplatedownload/$entityid$filename>".$app_strings['DownloadMergeFile']."</a></b></font><br>";
        remove_dir($wordtemplatedownloadpath.$temp_dir);
    }
}
else if($extension == "rtf")
{
    foreach (glob("$wordtemplatedownloadpath/*.rtf") as $delefile) 
    {
        unlink($delefile);
    }
    $filecontent = base64_decode($fileContent);
    if (!is_array($mass_merge)) $mass_merge = array($mass_merge);
    foreach($mass_merge as $idx => $entityid) {
        $handle = fopen($wordtemplatedownloadpath.$entityid.$filename,"wb");
        $new_filecontent = crmmerge($csvheader,$filecontent,$idx,'utf8Unicode');
        fwrite($handle,$new_filecontent);
        fclose($handle);
        echo "&nbsp;&nbsp;<font size=+1><b><a href=test/wordtemplatedownload/$entityid$filename>".$app_strings['DownloadMergeFile']."</a></b></font><br>";
    }
}
else
{
    die("unknown file format");
}

?>
<script>
if("<?php echo GetFileExtension($filename); ?>" == "doc") {
    if (window.ActiveXObject)
    {
        try 
        {
            ovtigerVM = eval("new ActiveXObject('vtigerCRM.ActiveX');");
            if(ovtigerVM)
            {
                var filename = "<?php echo $filename?>";
                if(filename != "")
                {
                    if(objMMPage.bDLTempDoc("<?php echo $site_URL?>/test/wordtemplatedownload/<?php echo $filename?>","MMTemplate.doc"))
                    {
                        try
                        {
                            if(objMMPage.Init())
                            {
                                objMMPage.vLTemplateDoc();
                                objMMPage.bBulkHDSrc("<?php echo $site_URL;?>/test/wordtemplatedownload/<?php echo $datafilename ?>");
                                objMMPage.vBulkOpenDoc();
                                objMMPage.UnInit()
                                    window.history.back();
                            }		
                        }catch(errorObject)
                        {
                            document.write("Error while processing mail merge operation");
                        }
                    }
                    else
                    {
                        document.write("Cannot get template document");
                    }
                }
            }
        }
        catch(e)
        {
            document.write("Requires to download ActiveX Control from vtigerCRM. Please, ensure that you have administration privilage");
        }
        document.write("</body></html>");
    }
}
</script>
