<?php /* Smarty version Smarty-3.1.7, created on 2017-07-14 14:12:58
         compiled from "/u01/www/vt701/includes/runtime/../../layouts/v7/modules/Leads/ModuleSummaryView.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1603775685968a73abd77d3-04049117%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '52e557ced68afb298ce01079e1dc9616e4315718' => 
    array (
      0 => '/u01/www/vt701/includes/runtime/../../layouts/v7/modules/Leads/ModuleSummaryView.tpl',
      1 => 1500018755,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1603775685968a73abd77d3-04049117',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'MODULE_NAME' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.7',
  'unifunc' => 'content_5968a73abe755',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5968a73abe755')) {function content_5968a73abe755($_smarty_tpl) {?>
<div class="recordDetails"><?php echo $_smarty_tpl->getSubTemplate (vtemplate_path('SummaryViewContents.tpl',$_smarty_tpl->tpl_vars['MODULE_NAME']->value), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>
</div><?php }} ?>