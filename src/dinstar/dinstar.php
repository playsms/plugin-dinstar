<?php

/**
 * This file is part of playSMS.
 *
 * playSMS is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * playSMS is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with playSMS. If not, see <http://www.gnu.org/licenses/>.
 */
defined('_SECURE_') or die('Forbidden');

if (!auth_isadmin()) {
	auth_block();
}

include $core_config['apps_path']['plug'] . "/gateway/dinstar/config.php";

$callback_url = $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/plugin/gateway/dinstar/callback.php";
$callback_url = str_replace("//", "/", $callback_url);
$callback_url = "http://" . $callback_url;

switch (_OP_) {
	case "manage":
		$tpl = array(
			'name' => 'dinstar',
			'vars' => array(
				'DIALOG_DISPLAY' => _dialog(),
				'Manage Dinstar' => _('Manage Dinstar'),
				'Gateway name' => _('Gateway name'),
				'Gateway host' => _('Gateway host'),
				'Gateway port' => _('Gateway port'),
				'Username' => _('Username'),
				'Password' => _('Password'),
				'Port' => _('Port'),
				'Device SN' => _('Device SN'),
				'Module sender ID' => _('Module sender ID'),
				'Module timezone' => _('Module timezone'),
				'Save' => _('Save'),
				'Notes' => _('Notes'),
				'HINT_FILL_SECRET' => _hint(_('Fill to change the password')),
				'HINT_FILL_PORT' => _hint(_('Fill with specific port number or - to omit')),
				'HINT_FILL_SN' => _hint(_('Fill with device Serial Number')),
				'CALLBACK_URL_IS' => _('Your callback URL is'),
				'CALLBACK_URL_ACCESSIBLE' => _('Your callback URL should be accessible from Dinstar'),
				'BUTTON_BACK' => _back('index.php?app=main&inc=core_gateway&op=gateway_list'),
				'dinstar_param_gateway_host' => $plugin_config['dinstar']['gateway_host'],
				'dinstar_param_gateway_port' => $plugin_config['dinstar']['gateway_port'],
				'dinstar_param_username' => $plugin_config['dinstar']['username'],
				'dinstar_param_port' => $plugin_config['dinstar']['port'],
				'dinstar_param_sn' => $plugin_config['dinstar']['sn'],
				'callback_url' => $callback_url,
			)
		);
		_p(tpl_apply($tpl));
		break;
	case "manage_save":
		$_SESSION['dialog']['info'][] = _('Changes have been made');
		$items = array(
			'gateway_host' => $_REQUEST['up_gateway_host'],
			'gateway_port' => $_REQUEST['up_gateway_port'],
			'username' => $_REQUEST['up_username'],
			'password' => $_REQUEST['up_password'],
			'port' => $_REQUEST['up_port'],
			'sn' => $_REQUEST['up_sn'],
		);
		if ($_REQUEST['up_password']) {
			$items['password'] = $_REQUEST['up_password'];
		}
		registry_update(1, 'gateway', 'dinstar', $items);
		header("Location: " . _u('index.php?app=main&inc=gateway_dinstar&op=manage'));
		exit();
		break;
}
