<?php /* Smarty version Smarty-3.1.7, created on 2017-07-14 11:55:33
         compiled from "/u01/www/vt701/includes/runtime/../../layouts/v7/modules/Vtiger/NoComments.tpl" */ ?>
<?php /*%%SmartyHeaderCode:204187798359688705521429-73670838%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a94d06cf7ba0dbb838940631f12ec274b56df069' => 
    array (
      0 => '/u01/www/vt701/includes/runtime/../../layouts/v7/modules/Vtiger/NoComments.tpl',
      1 => 1500018753,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '204187798359688705521429-73670838',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE_NAME' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_59688705586e9',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_59688705586e9')) {function content_59688705586e9($_smarty_tpl) {?>
<div class="noCommentsMsgContainer noContent"><p class="textAlignCenter"> <?php echo vtranslate('LBL_NO_COMMENTS',$_smarty_tpl->tpl_vars['MODULE_NAME']->value);?>
</p></div><?php }} ?>