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

// hook_sendsms
// called by main sms sender
// return true for success delivery
// $smsc : smsc
// $sms_sender : sender mobile number
// $sms_footer : sender sms footer or sms sender ID
// $sms_to : destination sms number
// $sms_msg : sms message tobe delivered
// $gpid : group phonebook id (optional)
// $uid : sender User ID
// $smslog_id : sms ID
function dinstar_hook_sendsms($smsc, $sms_sender, $sms_footer, $sms_to, $sms_msg, $uid = '', $gpid = 0, $smslog_id = 0, $sms_type = 'text', $unicode = 0)
{
	global $plugin_config;

	_log("enter smsc:" . $smsc . " smslog_id:" . $smslog_id . " uid:" . $uid . " to:" . $sms_to, 3, "dinstar_hook_sendsms");

	// override plugin gateway configuration by smsc configuration
	$plugin_config = gateway_apply_smsc_config($smsc, $plugin_config);

	$sms_footer = stripslashes($sms_footer);
	$sms_msg = stripslashes($sms_msg);

	if ($sms_footer) {
		$sms_msg = $sms_msg . $sms_footer;
	}

	if ($plugin_config['dinstar']['gateway_host'] && $plugin_config['dinstar']['gateway_port'] && $sms_to && $sms_msg) {
		$api_url = 'http://' . $plugin_config['dinstar']['gateway_host'] . ":" . $plugin_config['dinstar']['gateway_port'] . '/api/send_sms';
		$api_username = $plugin_config['dinstar']['username'];
		$api_password = $plugin_config['dinstar']['password'];

		$port = $plugin_config['dinstar']['port'];
		if (trim($port) == '-' || trim($port) === '') {
			$port = '-';
			$fill_port = '';
		} else {
			$port = (int) $port;
			$fill_port = '"port":"' . $port . '",';
		}

		$sn = isset($plugin_config['dinstar']['sn']) ? $plugin_config['dinstar']['sn'] : '';
		$unicode = ($unicode ? 'unicode' : 'gsm-7bit');
		$json = '{"text":"' . $sms_msg . '","param":[{"number":"' . $sms_to . '","user_id":"' . $smslog_id . '",' . $fill_port . '"encoding":"' . $unicode . '"}]}';

		_log('request api_url:' . $api_url . ' sn:' . $sn . ' port:' . $port . ' json:' . $json, 3, 'dinstar_hook_sendsms');

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $api_url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => $json,
			CURLOPT_HTTPHEADER => array(
				'Authorization: Basic ' . base64_encode($api_username . ':' . $api_password),
				'Content-Type: application/json',
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		$data = json_decode($response, true);

		_log('response raw:' . trim($response) . ' error_code:' . $data['error_code'] . ' sms_in_queue:' . $data['sms_in_queue'] . ' task_id:' . $data['task_id'], 3, 'dinstar_hook_sendsms');

		if ($data['error_code'] == '202') {
			$p_status = 1;
			dlr($smslog_id, $uid, $p_status);
		} else {
			$p_status = 2;
			dlr($smslog_id, $uid, $p_status);
		}
	}

	return TRUE;
}
