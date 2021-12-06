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

$sms_datetime = core_display_datetime(core_get_datetime());

if (isset($requests['sms'])) {
	foreach ($requests['sms'] as $in) {
		// incoming message
		$sms_sender = isset($in['number']) ? $in['number'] : '';
		$sms_receiver = '';

		// this is the smsc value pushed by dinstar, not the same as the smsc value in playSMS
		$sms_center = isset($in['smsc']) ? $in['smsc'] : '';

		$message = htmlspecialchars_decode(urldecode(isset($in['text']) ? $in['text'] : ''));
		$port = isset($in['port']) ? (int) $in['port'] : '';
		$sn = isset($in['sn']) ? $in['sn'] : '';

		// from sn and port we should be able to get smsc
		$smsc = '';

		if ($message) {
			_log("incoming sn:" . $sn . " port:" . $port . " from:" . $sms_sender . " m:[" . $message . "] smsc:[" . $smsc . "] sms_center:" . $sms_center, 2, "dinstar callback");

			$sms_sender = addslashes($sms_sender);
			$sms_receiver = addslashes($sms_receiver);
			$message = addslashes($message);
			$smsc = addslashes($smsc);

			recvsms($sms_datetime, $sms_sender, $message, $sms_receiver, $smsc);
		}
	}
}
