<?php
defined('_SECURE_') or die('Forbidden');

// get kannel config from registry
$data = registry_search(1, 'gateway', 'dinstar');
$plugin_config['dinstar'] = $data['gateway']['dinstar'];
$plugin_config['dinstar']['name'] = 'dinstar';
$plugin_config['dinstar']['gateway_port'] = isset($plugin_config['dinstar']['gateway_port']) ? (int) $plugin_config['dinstar']['gateway_port'] : 855;
$plugin_config['dinstar']['port'] = isset($plugin_config['dinstar']['port']) ? (int) $plugin_config['dinstar']['port'] : 0;

// smsc configuration
$plugin_config['dinstar']['_smsc_config_'] = array(
	'gateway_host' => _('Gateway host'),
	'gateway_port' => _('Gateway port'),
	'username' => _('Username'),
	'password' => _('Password'),
	'port' => _('Port'),
);

//$gateway_number = $plugin_config['dinstar']['module_sender'];

// insert to left menu array
//if (isadmin()) {
//	$menutab_gateway = $core_config['menutab']['gateway'];
//	$menu_config[$menutab_gateway][] = array("index.php?app=main&inc=gateway_dinstar&op=manage", _('Manage dinstar'));
//}