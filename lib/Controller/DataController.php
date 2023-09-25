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
use OCA\Data\Http\GeneratedResponse;
use OCA\Data\Http\GeneratedStreamResponse;

class DataController extends Controller {
	private DataService $DataService;

	use Errors;

	public function __construct(IRequest $request,
								DataService $DataService) {
		parent::__construct(Application::APP_ID, $request);
		$this->DataService = $DataService;
	}

	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
	public function csv(string $id): DataResponse {
		return new DataResponse('CVS ' . time());
	}
	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
	public function json(string $id): DataResponse {
		return new DataResponse('JSON ' . time());
	}
	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
	public function xml(string $id): DataResponse {
		return new DataResponse('XML ' . time());
	}
	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
	public function snom(string $id) {

		// evaluate, if token exists
		if (empty($this->request->getParam('token'))) {
			return null;
		}

		if (!$this->DataService->authorize($id, $this->request->getParam('token'), 'SNOM')) {
			return null;
		}

		return new GeneratedResponse($this->DataService->generate($id), 'text/xml; charset=UTF-8');
	}
	
}
