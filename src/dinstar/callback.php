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
error_reporting(0);

$requests = [];

if (!$called_from_hook_call) {
	chdir("../../../");

	// ignore CSRF
	$core_config['init']['ignore_csrf'] = TRUE;

	include "init.php";
	include $core_config['apps_path']['libs'] . "/function.php";
	chdir("plugin/gateway/dinstar/");

	$json = file_get_contents("php://input");
	_log("pushed json:" . $json, 2, "dinstar callback");

	$requests = json_decode($json, true);
}

// incoming message
$sms_datetime = core_display_datetime(core_get_datetime());
$sms_sender = isset($requests['sms'][0]['number']) ? $requests['sms'][0]['number'] : '';
$message = htmlspecialchars_decode(urldecode(isset($requests['sms'][0]['text']) ? $requests['sms'][0]['text'] : ''));
$sms_receiver = core_sanitize_sender(isset($requests['sms'][0]['port']) ? $requests['sms'][0]['port'] : '');
$smsc = '';

if ($message) {
	_log("incoming smsc:" . $smsc . " from:" . $sms_sender . " port:" . $sms_receiver . " m:[" . $message . "] smsc:[" . $smsc . "]", 2, "dinstar callback");
	$sms_sender = addslashes($sms_sender);
	$message = addslashes($message);
	recvsms($sms_datetime, $sms_sender, $message, $sms_receiver, $smsc);
}
