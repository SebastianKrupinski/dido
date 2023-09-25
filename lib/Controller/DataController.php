<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Sebastian Krupinski <krupinski01@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Data\Controller;

use OCA\Data\AppInfo\Application;
use OCA\Data\Service\DataService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class DataController extends Controller {
	private DataService $DataService;

	use Errors;

	public function __construct(IRequest $request,
								DataService $DataService) {
		parent::__construct(Application::APP_ID, $request);
		$this->DataService = $DataService;
	}

	/**
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function csv(string $id): DataResponse {
		return new DataResponse('CVS ' . time());
	}
	/**
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function json(string $id): DataResponse {
		return new DataResponse('JSON ' . time());
	}
	/**
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function xml(string $id): DataResponse {
		return new DataResponse('XML ' . time());
	}
	/**
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function snom(string $id): DataResponse|null {

		// evaluate, if token exists
		if (empty($this->request->getParam('token'))) {
			return null;
		}

		if (!$this->DataService->permitService($id, $this->request->getParam('token'), 'SNOM')) {
			return null;
		}

		return new DataResponse('SNOM ' . time());
	}
}
