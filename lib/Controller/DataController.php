<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Sebastian Krupinski <krupinski01@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Data\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\ISession;

use OCA\Data\AppInfo\Application;
use OCA\Data\Service\CoreService;
use OCA\Data\Service\DataService;
use OCA\Data\Http\GeneratedResponse;
use OCA\Data\Http\GeneratedStreamResponse;

class DataController extends Controller {
	private $userSession;
	private CoreService $CoreService;
	private DataService $DataService;

	public function __construct(IRequest $request,
								ISession $Session,
								CoreService $CoreService,
								DataService $DataService) {
		parent::__construct(Application::APP_ID, $request);
		$this->request = $request;
		$this->session = $Session;
		$this->CoreService = $CoreService;
		$this->DataService = $DataService;
	}

	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
	public function csv(string $id) {

		// construct place holder
		$meta = [];
		// evaluate, if token exists
		if (empty($this->request->getParam('token'))) {
			return null;
		}
		// collect meta data
		$meta['token'] = $this->request->getParam('token');
		$meta['address'] = $this->request->__get('server')['REMOTE_ADDR'];
		$meta['agent'] = $this->request->__get('server')['HTTP_USER_AGENT'];
		// authorize request
		$result = $this->CoreService->authorize($id, $meta);
		// evaluate, result
		if ($result === false) {
			return null;
		} else {
			return new GeneratedResponse($this->DataService->generateCSV($result), 'text/text; charset=UTF-8');
		}

	}
	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
	public function json(string $id) {
		
		// construct place holder
		$meta = [];
		// evaluate, if token exists
		if (empty($this->request->getParam('token'))) {
			return null;
		}
		// collect meta data
		$meta['token'] = $this->request->getParam('token');
		$meta['address'] = $this->request->__get('server')['REMOTE_ADDR'];
		$meta['agent'] = $this->request->__get('server')['HTTP_USER_AGENT'];
		// authorize request
		$result = $this->CoreService->authorize($id, $meta);
		// evaluate, result
		if ($result === false) {
			return null;
		} else {
			return new GeneratedResponse($this->DataService->generateJSON($result), 'application/json; charset=UTF-8');
		}

	}
	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
	public function xml(string $id) {
		
		// construct place holder
		$meta = [];
		// evaluate, if token exists
		if (empty($this->request->getParam('token'))) {
			return null;
		}
		// collect meta data
		$meta['token'] = $this->request->getParam('token');
		$meta['address'] = $this->request->__get('server')['REMOTE_ADDR'];
		$meta['agent'] = $this->request->__get('server')['HTTP_USER_AGENT'];
		// authorize request
		$result = $this->CoreService->authorize($id, $meta);
		// evaluate, result
		if ($result === false) {
			return null;
		} else {
			return new GeneratedResponse($this->DataService->generateXML($result), 'text/xml; charset=UTF-8');
		}
	
	}
	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
	public function device(string $id, string $token, array $meta, string $mime) {

		// evaluate, if token exists
		if (!isset($meta['token']) || empty($meta['token'])) {
			return null;
		}
		// authorize request
		$result = $this->CoreService->authorize($id, $meta);
		// evaluate, result
		if ($result === false) {
			return null;
		} else {
			return new GeneratedResponse($this->DataService->generateTemplate($result), $mime);
		}

	}
	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
	public function phone(string $id, string $token = '') {

		// evaluate, if token exists
		if (empty($token)) {
			return null;
		}
		// collect meta data
		$meta = [];
		$meta['token'] = $token;
		$meta['address'] = $this->request->__get('server')['REMOTE_ADDR'];
		$meta['agent'] = $this->request->__get('server')['HTTP_USER_AGENT'];
		$meta['mac'] = \OCA\Data\Utile\Extractor::mac($meta['agent'], true);

		return $this->device($id, $token, $meta, 'text/xml; charset=UTF-8');

	}
	/**
	 * @PublicPage
	 * @UseSession
	 * @OnlyUnauthenticatedUsers
     * @NoAdminRequired
     * @NoCSRFRequired
     */
	public function grandstream(string $id) {

		
		if (empty($this->request->__get('server')['HTTP_AUTHORIZATION']) ||
			str_starts_with($this->request->__get('server')['HTTP_AUTHORIZATION'], 'Basic ') === false) {
			return null;
		}

		// decode token
		$token = substr($this->request->__get('server')['HTTP_AUTHORIZATION'], 6);
		$token = explode(':', base64_decode($token), 2)[1];

		// collect meta data
		$meta = [];
		$meta['token'] = $token;
		$meta['address'] = $this->request->__get('server')['REMOTE_ADDR'];
		$meta['agent'] = $this->request->__get('server')['HTTP_USER_AGENT'];
		$meta['mac'] = \OCA\Data\Utile\Extractor::mac($meta['agent'], true);

		return $this->device($id, $token, $meta, 'text/xml; charset=UTF-8');

	}

}
