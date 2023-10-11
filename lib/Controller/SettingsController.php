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

namespace OCA\Data\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

use OCA\Data\AppInfo\Application;
use OCA\Data\Service\ConfigurationService;
use OCA\Data\Service\CoreService;
use OCA\Data\Service\DataService;

class SettingsController extends Controller {

	use Errors;

	private DataService $CoreService;
	private DataService $DataService;
	private ConfigurationService $ConfigurationService;

	public function __construct(IRequest $request,
								ConfigurationService $ConfigurationService,
								CoreService $CoreService,
								DataService $DataService,
								string $userId) {
		parent::__construct(Application::APP_ID, $request);
		$this->ConfigurationService = $ConfigurationService;
		$this->CoreService = $CoreService;
		$this->DataService = $DataService;
		$this->userId = $userId;
	}

	/**
	 * handels user list requests
	 *
	 * @return DataResponse
	 */
	public function listUsers(): DataResponse {

		// evaluate if user id is present
		if ($this->userId === null) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
		// retrieve formats
		$rs = $this->CoreService->listUsers();
		// return response
		if (isset($rs)) {
			return new DataResponse($rs);
		} else {
			return new DataResponse($rs['error'], 401);
		}

	}

	/**
	 * handels types list requests
	 * 
	 * @NoAdminRequired
	 *
	 * @return DataResponse
	 */
	public function listTypes(string $user = null): DataResponse {

		// evaluate if user id is present
		if ($this->userId === null) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
		// retrieve types
		// evaluate, if user id is present
		// TODO: check for admin privileges
		if (!empty($user)) {
			$rs = $this->CoreService->listTypes($user);
		}
		else {
			$rs = $this->CoreService->listTypes($this->userId);
		}
		// return response
		if (isset($rs)) {
			return new DataResponse($rs);
		} else {
			return new DataResponse($rs['error'], 401);
		}

	}

	/**
	 * handels collections list requests
	 * 
	 * @NoAdminRequired
	 *
	 * @return DataResponse
	 */
	public function listCollections(string $type, string $user = null): DataResponse {

		// evaluate if user id is present
		if ($this->userId === null) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
		// retrieve colletions
		// evaluate, if user id is present
		// TODO: check for admin privileges
		if (!empty($user)) {
			$rs = $this->DataService->listCollections($user, $type);
		}
		else {
			$rs = $this->DataService->listCollections($this->userId, $type);
		}
		
		// return response
		if (isset($rs)) {
			return new DataResponse($rs);
		} else {
			return new DataResponse($rs['error'], 401);
		}

	}

	/**
	 * handels formats list requests
	 * 
	 * @NoAdminRequired
	 *
	 * @return DataResponse
	 */
	public function listFormats(string $type): DataResponse {

		// evaluate if user id is present
		if ($this->userId === null) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
		// retrieve formats
		$rs = $this->CoreService->listFormats($type);
		// return response
		if (isset($rs)) {
			return new DataResponse($rs);
		} else {
			return new DataResponse($rs['error'], 401);
		}

	}

	/**
	 * handels services list requests
	 * 
	 * @NoAdminRequired
	 *
	 * @return DataResponse
	 */
	public function listServices(bool $flagAdmin = false): DataResponse {

		// evaluate if user id is present
		if ($this->userId === null) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
		// retrieve services
		// evaluate, if admin flag is present
		// TODO: check for admin privileges
		if ($flagAdmin) {
			$rs = $this->CoreService->listServices('');
		}
		else {
			$rs = $this->CoreService->listServices($this->userId);
		}
		// return response
		if (isset($rs)) {
			return new DataResponse($rs);
		} else {
			return new DataResponse($rs['error'], 401);
		}

	}

	/**
	 * handels services create requests
	 * 
	 * @NoAdminRequired
	 *
	 * @return DataResponse
	 */
	public function createService(array $data): DataResponse {

		// evaluate if user id is present
		if ($this->userId === null) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
		// evaluate, if required data is present
		if (!empty($data['service_id']) && !empty($data['service_token']) &&
			!empty($data['data_type']) && !empty($data['data_collection']) && !empty($data['format'])) {
			// evaluate, if user id is present
			// TODO: check for admin privileges
			if (!empty($data['uid'])) {
				
			}
			else {
				// assign user id
				$data['uid'] = $this->userId;
			}
			// create service
			$rs = $this->CoreService->createService($this->userId, $data);
		}
		// return response
		if (isset($rs)) {
			return new DataResponse($rs);
		} else {
			return new DataResponse($rs['error'], 401);
		}

	}

	/**
	 * handels services modify requests
	 * 
	 * @NoAdminRequired
	 *
	 * @return DataResponse
	 */
	public function modifyService(array $data): DataResponse {

		// evaluate if user id is present
		if ($this->userId === null) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
		// evaluate, if required data is present
		if (!empty($data['id']) && !empty($data['uid']) && !empty($data['service_id']) && !empty($data['service_token']) &&
			!empty($data['data_type']) && !empty($data['data_collection']) && !empty($data['format'])) {
			// force read only permissions until write is implemented
			$data['permissions'] = 'R';
			// modify service
			$rs = $this->CoreService->modifyService($this->userId,(string) $data['id'], $data);
		}
		// return response
		if (isset($rs)) {
			return new DataResponse($rs);
		} else {
			return new DataResponse($rs['error'], 401);
		}

	}
	
	/**
	 * handels services delete requests
	 * 
	 * @NoAdminRequired
	 *
	 * @return DataResponse
	 */
	public function deleteService($data): DataResponse {

		// evaluate if user id is present
		if ($this->userId === null) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
		// evaluate, if required data is present
		if (!empty($data['id'])) {
			// delete service
			$rs = $this->CoreService->deleteService($this->userId,(string) $data['id']);
		}
		// return response
		if (isset($rs)) {
			return new DataResponse($rs);
		} else {
			return new DataResponse($rs['error'], 401);
		}

	}
	
	/**
	 * handles fetch settings requests
	 * 
	 * @NoAdminRequired
	 * 
	 * @return DataResponse
	 */
	public function fetchSystemSettings(): DataResponse {
		
		// evaluate if user id is present
		if ($this->userId === null) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
		// retrieve user configuration
		$rs = $this->ConfigurationService->retrieveSystem();
		// return response
		if (is_array($rs)) {
			return new DataResponse($rs);
		} else {
			return new DataResponse($rs['error'], 401);
		}

	}

	/**
	 * handles save settings requests
	 *
	 * @param array $data		key/value pairs to save
	 * 
	 * @return DataResponse
	 */
	public function depositSystemSettings(array $data): DataResponse {
		
		// evaluate if user id is present
		if ($this->userId === null) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
		// deposit user configuration
		$this->ConfigurationService->depositSystem($data);
		// return response
		return new DataResponse(true);

	}

}
