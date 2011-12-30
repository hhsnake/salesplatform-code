/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

new Ajax.Request(
        'index.php',
        {queue: {position: 'end', scope: 'command'},
                method: 'post',
                postBody: 'module='+moduleName+'&action='+moduleName+'Ajax&file=PDFTemplatesList&templateid='+templateid,
                onComplete: function(response)
                        {
                            document.getElementById("task-emailfieldsattachment-busyicon").style.display = 'none';
                            document.getElementById("task-emailfieldsattachment").innerHTML = response.responseText;
                            document.getElementById("task-emailfieldsattachment").style.display = 'block';

                            if(document.getElementById("save_att_template").value == '')
                                document.getElementById("save_att_template").value = document.getElementById("task-emailfieldsattachment").value;
                        }
        }
);

