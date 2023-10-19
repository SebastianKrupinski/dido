<?php
declare(strict_types=1);

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

namespace OCA\Dido\Service;

use Exception;

use OCA\Dido\Db\Services;
use OCA\Dido\Service\ConfigurationService;

class CoreService {

    private ConfigurationService $_ConfigurationService;
	private Services $_ServicesStore;

	public function __construct(ConfigurationService $ConfigurationService, Services $Services) {
		$this->_ConfigurationService = $ConfigurationService;
		$this->_ServicesStore = $Services;
	}

	/**
	 * retrieve all users
	 * 
	 * @since Release 1.0.0
	 * 
	 * @return array 			of users
	 */
	public function listUsers(): array {

		// load helper 
		$UserUtile = \OC::$server->get(\OCA\Dido\Db\UsersUtile::class);
		// retrieve users
		$users = $UserUtile->listUsers();
		// return data
		return $users;

	}

	/**
	 * retrieve types for specific user
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $uid		nextcloud user id
	 * 
	 * @return array 			of types
	 */
	public function listTypes(string $uid): array {

		// construct response object
		$types = [];
		// evaluate, if contacts app is available
		if ($this->_ConfigurationService->isContactsAvailable($uid)) {
			$types[] = ['id' => 'CC', 'label' => 'Contacts'];
		}
		// evaluate, if calendar app is available
		if ($this->_ConfigurationService->isCalendarAvailable($uid)) {
			// TODO: Enable after adding calendar support
			// $types[] = ['id' => 'EC', 'label' => 'Calendars'];
		}
		// evaluate, if tasks app is available
		if ($this->_ConfigurationService->isTasksAvailable($uid)) {
			// TODO: Enable after adding tasks support
			// $types[] = ['id' => 'TC', 'label' => 'Tasks'];
		}
		// return data
		return $types;

	}

	/**
	 * retrieve all formats for specific collection type
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $type		collection type
	 * 
	 * @return array			of format(s)
	 */
	public function listFormats(string $type): array {

		// construct response object
		$response['Formats'];
		$response['Formats'][] = ['id' => 'CVS', 'label' => 'CVS'];
		$response['Formats'][] = ['id' => 'JSON', 'label' => 'JSON'];
		$response['Formats'][] = ['id' => 'XML', 'label' => 'XML'];
		// retrieve all formats
		if ($type == 'CC') {
			$files = scandir(dirname(__DIR__) . '/Resources/Contacts/');
			foreach ($files as $file) {
				if (strstr($file, '.tpl')) {
					$response['Formats'][] = ['id' => $file, 'label' => str_replace(".tpl", "", $file)];
				}
			}
		}
		if ($type == 'EC') {
			$files = scandir(dirname(__DIR__) . '/Resources/Events/');
			foreach ($files as $file) {
				if (strstr($file, '.tpl')) {
					$response['Formats'][] = ['id' => $file, 'label' => str_replace(".tpl", "", $file)];
				}
			}
		}
		if ($type == 'TC') {
			$files = scandir(dirname(__DIR__) . '/Resources/Tasks/');
			foreach ($files as $file) {
				if (strstr($file, '.tpl')) {
					$response['Formats'][] = ['id' => $file, 'label' => str_replace(".tpl", "", $file)];
				}
			}
		}
		// return response
		return $response;

	}

	/**
	 * retrieve all services for specific user
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $uid		nextcloud user id
	 * 
	 * @return array			of service(s)
	 */
	public function listServices(string $uid): array {

		// construct response object
		$response = [];
		// retrieve all services
		$response = $this->_ServicesStore->listByUserId($uid);
		// return response
		return $response;

	}

	/**
	 * create a service entry for specific user in the data store
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $uid		nextcloud user id
	 * @param array $data		entry data
	 * 
	 * @return bool			
	 */
	public function createService(string $uid, array $data): bool {

		// remove id column if present
		unset($data['id']);
		// force read only permissions until write is implemented
		$data['permissions'] = 'R';
		// set create on
		$data['created_on'] = time();
		// set create by
		$data['created_by'] = $uid;
		// create service data in the data store
		$rs = $this->_ServicesStore->create($data);
		// return response
		return $rs;

	}

	/**
	 * modify a specific service entry for specific user in the data store
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $uid		nextcloud user id
	 * @param string $id		entry id
	 * @param array $data		entry data
	 * 
	 * @return bool			
	 */
	public function modifyService(string $uid, string $id, array $data): bool {

		// remove id column if present
		unset($data['id']);
		// force read only permissions until write is implemented
		$data['permissions'] = 'R';
		// modify service entry in the data store
		$rs = $this->_ServicesStore->modify($id, $data);
		// return response
		return $rs;

	}

	/**
	 * delete a specific service entry for specific user from the data store
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $uid		nextcloud user id
	 * @param string $id		entry id
	 * 
	 * @return bool			
	 */
	public function deleteService(string $uid, string $id): bool {

		// delete a service entry from the data store
		$rs = $this->_ServicesStore->delete($id);
		// return response
		return $rs;

	}

	/**
	 * probe if service id exists
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $id		service id
	 * 
	 * @return bool
	 */
	public function probeServiceId(string $id): bool {

		$rs = $this->_ServicesStore->fetchByServiceId($id);

		if ($rs === false) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * authorize transaction
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $id		service id
	 * @param string $meta		client meta data
	 * 
	 * @return array|bool		service data on success / false on failure
	 */
	public function authorize(string $id, array $meta): array|bool {

		$service = $this->_ServicesStore->fetchByServiceId($id);

		// evaluate, if a service was found
		if (!is_array($service)) {
			return false;
		}
		// evaluate, if token matches
		if ($service['service_token'] !== $meta['token']) {
			return false;
		}

		// evaluate, if id address restriction is set
		if (!empty($service['restrict_ip'])) {
			$addresses = explode(' ', $service['restrict_ip']);
			$valid = false;
			foreach ($addresses as $entry) {
				// evaluate, if ip address matches
				if (\OCA\Dido\Utile\Validator::ipInCidr($meta['address'], $entry)) {
					$valid = true;
					break;
				}
			}
			// evaluate, if no matching range was found
			if ($valid === false) {
				return false;
			}
		}

		// evaluate, if mac address restriction is set
		if (!empty($service['restrict_mac'])) {
			// evaluate, if mac matches
			if ($service['restrict_mac'] != $meta['mac']) {
				return false;
			}
		}

		// evaluate, if agent restriction is set
		if (!empty($service['restrict_agent'])) {
			// evaluate, if mac matches
			if (!empty($meta['agent']) && !(preg_match('/' . $service['restrict_agent'] . '/', $meta['agent']) > 0)) {
				return false;
			}
		}

		// modify service entry accessed in the data store
		$this->_ServicesStore->modifyAccessed((string) $service['id'], time(), $meta['address']);

		return $service;
	}

}
