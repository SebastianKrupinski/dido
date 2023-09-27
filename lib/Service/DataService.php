<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Sebastian Krupinski <krupinski01@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Data\Service;

use Exception;

use Sabre\VObject\Reader;

use OCA\Data\Db\Services;
use OCA\Data\Service\ContactsService;

class DataService {

	/**
	 * @var ContactsService
	 */
    private $ContactsService;

	public function __construct(Services $Services, ContactsService $ContactsService) {
		$this->Services = $Services;
		$this->ContactsService = $ContactsService;
	}

	public function authorize(string $id, array $meta): array|bool {

		$service = $this->Services->fetchByServiceId($id);

		// evaluate, if a service was found
		if (!is_array($service)) {
			return false;
		}
		// evaluate, if token matches
		if ($service['service_token'] !== $meta['token']) {
			return false;
		}
		// evaluate, if restrictions are set
		if (!empty($service['restrictions'])) {
			$restrictions = json_decode($service['restrictions']);
			// evaluate, id address restriction is set
			if (isset($restrictions->addresses)) {
				$valid = false;
				foreach ($restrictions->addresses as $entry) {
					if (\OCA\Data\Utile\Validator::ipInCidr($meta['address'], $entry)) {
						$valid = true;
						break;
					}
				}
				// evaluate, if no matching range was found
				if ($valid === false) {
					return false;
				}
			}
		}

		return $service;
	}

	public function generate(array $service) {

		// instance template service
		$TemplateService = new TemplateService();
		// load template
		$TemplateService->fromFile(dirname(__DIR__) . '/Resources/' . $service['format'] . '.tpl');
		// load entities
		$entities = $this->ContactsService->listEntities($service['collection_id']);
		// document start
		yield $TemplateService->generateStart();
		// document iteration
		foreach ($entities as $lo) {
			// convert to contact object
            $co = $this->ContactsService->toContactObject(Reader::read($lo['carddata']));
            $co->ID = $lo['uri'];
            $co->CID = $lo['addressbookid'];
            $co->ModifiedOn = new \DateTime(date("Y-m-d H:i:s", $lo['lastmodified']));
            $co->State = trim($lo['etag'],'"');
			
			yield $TemplateService->generateIteration($co);
		}
		// document end
		yield $TemplateService->generateEnd();

	}
}
