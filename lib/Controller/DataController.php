<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Sebastian Krupinski <krupinski01@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Data\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\TextPlainResponse;
use OCP\IRequest;
use OCP\ISession;

use OCA\Data\AppInfo\Application;
use OCA\Data\Service\CoreService;
use OCA\Data\Service\DataService;
use OCA\Data\Http\GeneratedResponse;

class DataController extends Controller {

	private IRequest $_Request;
	private ISession $_Session;
	private CoreService $_CoreService;
	private DataService $_DataService;

	public function __construct(IRequest $request,
								ISession $Session,
								CoreService $CoreService,
								DataService $DataService) {
		parent::__construct(Application::APP_ID, $request);
		$this->_Request = $request;
		$this->_Session = $Session;
		$this->_CoreService = $CoreService;
		$this->_DataService = $DataService;
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
		if (empty($this->_Request->getParam('token'))) {
			return null;
		}
		// collect meta data
		$meta['token'] = $this->_Request->getParam('token');
		$meta['address'] = $this->_Request->__get('server')['REMOTE_ADDR'];
		$meta['agent'] = $this->_Request->__get('server')['HTTP_USER_AGENT'];
		// authorize request
		$result = $this->_CoreService->authorize($id, $meta);
		// evaluate, result
		if ($result === false) {
			return new TextPlainResponse('', Http::STATUS_UNAUTHORIZED);
		} else {
			return new GeneratedResponse($this->_DataService->generateCSV($result), 'text/text; charset=UTF-8');
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
		if (empty($this->_Request->getParam('token'))) {
			return null;
		}
		// collect meta data
		$meta['token'] = $this->_Request->getParam('token');
		$meta['address'] = $this->_Request->__get('server')['REMOTE_ADDR'];
		$meta['agent'] = $this->_Request->__get('server')['HTTP_USER_AGENT'];
		// authorize request
		$result = $this->_CoreService->authorize($id, $meta);
		// evaluate, result
		if ($result === false) {
			return new TextPlainResponse('', Http::STATUS_UNAUTHORIZED);
		} else {
			return new GeneratedResponse($this->_DataService->generateJSON($result), 'application/json; charset=UTF-8');
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
		if (empty($this->_Request->getParam('token'))) {
			return null;
		}
		// collect meta data
		$meta['token'] = $this->_Request->getParam('token');
		$meta['address'] = $this->_Request->__get('server')['REMOTE_ADDR'];
		$meta['agent'] = $this->_Request->__get('server')['HTTP_USER_AGENT'];
		// authorize request
		$result = $this->_CoreService->authorize($id, $meta);
		// evaluate, result
		if ($result === false) {
			return new TextPlainResponse('', Http::STATUS_UNAUTHORIZED);
		} else {
			return new GeneratedResponse($this->_DataService->generateXML($result), 'text/xml; charset=UTF-8');
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
		$result = $this->_CoreService->authorize($id, $meta);
		// evaluate, result
		if ($result === false) {
			return new TextPlainResponse('', Http::STATUS_UNAUTHORIZED);
		} else {
			return new GeneratedResponse($this->_DataService->generateTemplate($result), $mime);
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
		$meta['address'] = $this->_Request->__get('server')['REMOTE_ADDR'];
		$meta['agent'] = $this->_Request->__get('server')['HTTP_USER_AGENT'];
		$meta['mac'] = \OCA\Data\Utile\Extractor::mac($meta['agent'], true);

		return $this->device($id, $token, $meta, 'text/xml; charset=UTF-8');

	}
	/**
	 * @PublicPage
     * @NoCSRFRequired
     */
	public function grandstream() {

		if (!str_starts_with($this->_Session->get('user_id'), 'dio-')) {
			return null;
		}

		[$id, $token] = explode('-', substr($this->_Session->get('user_id'), 4), 2);
		// collect meta data
		$meta = [];
		$meta['token'] = $token;
		$meta['address'] = $this->_Request->__get('server')['REMOTE_ADDR'];
		$meta['agent'] = $this->_Request->__get('server')['HTTP_USER_AGENT'];
		$meta['mac'] = \OCA\Data\Utile\Extractor::mac($meta['agent'], true);

		return $this->device($id, $token, $meta, 'text/xml; charset=UTF-8');

	}

}
