<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Sebastian Krupinski <krupinski01@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\Data\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
	'resources' => [
		//'data' => ['url' => '/data'],
		//'data_api' => ['url' => '/api/0.1/data']
	],
	'routes' => [
		//['name' => 'data#index', 'url' => '/', 'verb' => 'GET'],
		//['name' => 'data_api#preflighted_cors', 'url' => '/api/0.1/{path}',
		//	'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']],
		//['name' => 'UserConfiguration#Save', 'url' => '/save', 'verb' => 'GET'],
		//['name' => 'UserConfiguration#Test', 'url' => '/test', 'verb' => 'GET'],
	]
];
