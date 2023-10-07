<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Sebastian Krupinski <krupinski01@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Data\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

use OCA\Data\AppInfo\Application;
use OCA\Data\Service\DataService;

class UserConfigurationController extends Controller {
	private DataService $DataService;

	use Errors;

	public function __construct(IRequest $request,
								DataService $DataService,
								string $userId) {
		parent::__construct(Application::APP_ID, $request);
		$this->DataService = $DataService;
		$this->userId = $userId;
	}
	/**
	 * handels collections list requests
	 * 
	 * @NoAdminRequired
	 *
	 * @return DataResponse
	 */
	public function listCollections(string $type): DataResponse {

		// evaluate if user id is present
		if ($this->userId === null) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
		// retrieve collections
		$rs = $this->DataService->listCollections($this->userId, $type);
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
		$rs = $this->DataService->listFormats($type);
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
	public function listServices(): DataResponse {

		// evaluate if user id is present
		if ($this->userId === null) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
		// retrieve formats
		$rs = $this->DataService->listServices($this->userId);
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
			// assign user id
			$data['uid'] = $this->userId;
			// create service
			$rs = $this->DataService->createService($this->userId, $data);
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
		if (!empty($data['id']) && !empty($data['service_id']) && !empty($data['service_token']) &&
			!empty($data['data_type']) && !empty($data['data_collection']) && !empty($data['format'])) {
			// force read only permissions until write is implemented
			$data['permissions'] = 'R';
			// modify service
			$rs = $this->DataService->modifyService($this->userId,(string) $data['id'], $data);
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
			$rs = $this->DataService->deleteService($this->userId,(string) $data['id']);
		}
		// return response
		if (isset($rs)) {
			return new DataResponse($rs);
		} else {
			return new DataResponse($rs['error'], 401);
		}

	}
	
}
