<?php
/**
 * This example shows how to add a screenshot to an application with the id :application_id
 * @package    WebService
 *
 * @copyright  Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");
curl_setopt($ch, CURLOPT_URL, 'http://ws.localhost/screenshots?_method=post&application_id=:application_id');
curl_setopt($ch, CURLOPT_POST, true);

// Don't forgot to escape paths
curl_setopt($ch, CURLOPT_POSTFIELDS, array('screenshots[0]' => '@ss_path', 'screenshots[1]' => '@ss_path'));

$response = curl_exec($ch);
