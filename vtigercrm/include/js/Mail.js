/*********************************************************************************

** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
//added by raju for emails

function eMail(module,oButton)
{
	var select_options  =  document.getElementById('allselectedboxes').value;
    //Added to remove the semi colen ';' at the end of the string.done to avoid error.
    var x = select_options.split(";");
    var count=x.length
    var viewid =getviewId();
    var idstring = "";
    select_options=select_options.slice(0,(select_options.length-1));

    if (count > 1)
    {
            idstring=select_options.replace(/;/g,':')
            document.getElementById('idlist').value=idstring;
    }
	else
	{
                // SalesPlatform.ru begin : Send Emails to all Records from current filter
		if (!confirm(alert_arr.SELECT_MASS)) {
                    return false;
                }
                document.getElementById('idlist').value='-1:-1';
		//alert(alert_arr.SELECT);
		//return false;
                // SalesPlatform.ru end
	}
	allids = document.getElementById('idlist').value;
	fnvshobj(oButton,'sendmail_cont');
	sendmail(module,allids);
}


function massMail(module)
{

	var select_options  =  document.getElementsByName('selected_id');
	x = select_options.length;
	var viewid =getviewId();		
	idstring = "";

	xx = 0;
	for(i = 0; i < x ; i++)
	{
		if(select_options[i].checked)
		{
			idstring = select_options[i].value +";"+idstring
				xx++
		}
	}
	if (xx != 0)
	{
		document.getElementById('idlist').value=idstring;
	}
	else
	{
		alert(alert_arr.SELECT);
		return false;
	}
	document.massdelete.action="index.php?module=CustomView&action=SendMailAction&return_module="+module+"&return_action=index&viewname="+viewid;
}

// SalesPlatform.ru begin
function select_return_emails(entity_id, parentname, module) {
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: "module=Emails&return_module="+module+"&action=EmailsAjax&file=mailSelect&idlist="+entity_id+"&sp_mode=popup",
                        onComplete: function(response) {
					if(response.responseText == "Mail Ids not permitted" || response.responseText == "No Mail Ids")
					{
						alert(alert_arr[response.responseText]);
					}
					else {
						getObj('sendmail_cont').innerHTML=response.responseText;
					}
                        }
                }
        );
}

function validate_sendmail_popup(idlist,parentname,module) {
	var j=0;
	var chk_emails = document.SendMail.elements.length;
	var oFsendmail = document.SendMail.elements
	email_type = new Array();
	for(var i=0 ;i < chk_emails ;i++)
	{
		if(oFsendmail[i].type != 'button')
		{
			if(oFsendmail[i].checked != false)
			{
				email_type [j++]= oFsendmail[i].value;
			}
		}
	}
	if(email_type != '')
	{
		for(var i=0 ;i < email_type.length ;i++)
		{
			var fieldid = email_type[i];
			var email = document.SendMail.elements["semail_val_"+fieldid].value;
			set_return_emails(idlist, fieldid, parentname, email, email, 1);
			window.close();
		}
	}
	else
	{
		alert(alert_arr.SELECT_MAILID);
	}
}

// SalesPlatform.ru end

//added by rdhital for better emails
function set_return_emails(entity_id,email_id,parentname,emailadd,emailadd2,perm){
	if(perm == 0 || perm == 3)
	{
		if(emailadd2 == '')
		{			
			alert(alert_arr.LBL_DONT_HAVE_EMAIL_PERMISSION);
			return false;
		}
		else
			emailadd = emailadd2;
	}
	else
	{
		if(emailadd == '')
			emailadd = emailadd2;
	}	
	if(emailadd != '')
	{
		window.opener.document.EditView.parent_id.value = window.opener.document.EditView.parent_id.value+entity_id+'@'+email_id+'|';
		window.opener.document.EditView.parent_name.value = window.opener.document.EditView.parent_name.value+parentname+'<'+emailadd+'>,';
		window.opener.document.EditView.hidden_toid.value = emailadd+','+window.opener.document.EditView.hidden_toid.value;
		window.close();
	}else
	{
		alert('"'+parentname+alert_arr.DOESNOT_HAVE_AN_MAILID);
		return false;
	}
}	
//added by raju for emails

// SalesPlatform.ru begin
function return_document_file(att_id, file_name){
        var multi_selector = window.opener.document.getElementById( 'files_list' ).multi_selector;
        if (att_id > 0) {
            if (!multi_selector.current_element.disabled) {
                multi_selector.current_element.setHiddenValue(att_id, file_name);
            }
        } else	{
            alert(alert_arr.SELECT_DOCWITHFILE);
            return false;
	}
        return true;
}
// SalesPlatform.ru end

function validate_sendmail(idlist,module)
{
	var j=0;
	var chk_emails = document.SendMail.elements.length;
	var oFsendmail = document.SendMail.elements
	email_type = new Array();
	for(var i=0 ;i < chk_emails ;i++)
	{
		if(oFsendmail[i].type != 'button')
		{
			if(oFsendmail[i].checked != false)
			{
				email_type [j++]= oFsendmail[i].value;
			}
		}
	}
	if(email_type != '')
	{
		var field_lists = email_type.join(':');
		var url= 'index.php?module=Emails&action=EmailsAjax&pmodule='+module+'&file=EditView&sendmail=true&idlist='+idlist+'&field_lists='+field_lists;
                // SalesPlatform.ru begin
		openPopUp('xComposeEmail',this,url,'createemailWin',855,733,'menubar=no,toolbar=no,location=no,status=no,resizable=no');
		//openPopUp('xComposeEmail',this,url,'createemailWin',820,689,'menubar=no,toolbar=no,location=no,status=no,resizable=no');
                // SalesPlatform.ru end
		fninvsh('roleLay');
		return true;
	}
	else
	{
		alert(alert_arr.SELECT_MAILID);
	}
}
function sendmail(module,idstrings)
{
        new Ajax.Request(
                'index.php',
                {queue: {position: 'end', scope: 'command'},
                        method: 'post',
                        postBody: "module=Emails&return_module="+module+"&action=EmailsAjax&file=mailSelect&idlist="+idstrings,
                        onComplete: function(response) {
					if(response.responseText == "Mail Ids not permitted" || response.responseText == "No Mail Ids")
					{
						var url= 'index.php?module=Emails&action=EmailsAjax&pmodule='+module+'&file=EditView&sendmail=true';
                                                // SalesPlatform.ru begin
				                openPopUp('xComposeEmail',this,url,'createemailWin',855,733,'menubar=no,toolbar=no,location=no,status=no,resizable=no');
				                //openPopUp('xComposeEmail',this,url,'createemailWin',820,689,'menubar=no,toolbar=no,location=no,status=no,resizable=no');
                                                // SalesPlatform.ru end
					}	
					else
						getObj('sendmail_cont').innerHTML=response.responseText;
                        }
                }
        );
}

function rel_eMail(module,oButton,relmod)
{
	var select_options='';
	var allids='';
	var cookie_val=get_cookie(relmod+"_all");
	if(cookie_val != null)
		select_options=cookie_val;
	//Added to remove the semi colen ';' at the end of the string.done to avoid error.
	var x = select_options.split(";");
	var viewid ='';
	var count=x.length
		var idstring = "";
	select_options=select_options.slice(0,(select_options.length-1));

	if (count > 1)
	{
		idstring=select_options.replace(/;/g,':')
			allids=idstring;
	}
	else
	{
                // SalesPlatform.ru begin : Send Emails to all Records from current filter
		if (!confirm(alert_arr.SELECT_MASS)) {
                    return false;
                }
                allids='-1:-1';
		//alert(alert_arr.SELECT);
		//return false;
                // SalesPlatform.ru end
	}
	fnvshobj(oButton,'sendmail_cont');
	sendmail(relmod,allids);
	set_cookie(relmod+"_all","");
}
