<?php

/**
* @copyright Copyright (c) 2023 Sebastian Krupinski <krupinski01@gmail.com>
*
* @author Sebastian Krupinski <krupinski01@gmail.com>
*
* @license AGPL-3.0-or-later
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU Affero General Public License for more details.
*
* You should have received a copy of the GNU Affero General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
*
*/

return [
	'routes' => [
		[
			'name' => 'Data#csv',
			'url' => '/csv/{id}/',
			'verb' => 'GET'
		],
		[
			'name' => 'Data#json',
			'url' => '/json/{id}/',
			'verb' => 'GET'
		],
		[
			'name' => 'Data#xml',
			'url' => '/xml/{id}/',
			'verb' => 'GET'
		],
		[
			'name' => 'Data#grandstream',
			'url' => '/grandstream/{id}/phonebook.xml',
			'verb' => 'GET'
		],
		[
			'name' => 'Data#phone',
			'url' => '/phone/{id}/',
			'verb' => 'GET'
		],
		[
			'name' => 'Settings#listUsers',
			'url' => '/list-users',
			'verb' => 'GET'
		],
		[
			'name' => 'Settings#listTypes',
			'url' => '/list-types',
			'verb' => 'GET'
		],
		[
			'name' => 'Settings#listCollections',
			'url' => '/list-collections',
			'verb' => 'GET'
		],
		[
			'name' => 'Settings#listFormats',
			'url' => '/list-formats',
			'verb' => 'GET'
		],
		[
			'name' => 'Settings#listServices',
			'url' => '/list-services',
			'verb' => 'GET'
		],
		[
			'name' => 'Settings#createService',
			'url' => '/create-service',
			'verb' => 'PUT'
		],
		[
			'name' => 'Settings#modifyService',
			'url' => '/modify-service',
			'verb' => 'PUT'
		],
		[
			'name' => 'Settings#deleteService',
			'url' => '/delete-service',
			'verb' => 'PUT'
		],
		[
			'name' => 'Settings#probeServiceId',
			'url' => '/probe-services-id',
			'verb' => 'PUT'
		],
		[
			'name' => 'Settings#fetchSystemSettings',
			'url' => '/fetch-system-settings',
			'verb' => 'GET'
		],
		[
			'name' => 'Settings#depositSystemSettings',
			'url' => '/deposit-system-settings',
			'verb' => 'PUT'
		],
	]
];
