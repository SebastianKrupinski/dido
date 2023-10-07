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

	/**
	 * retrieve collections for specific user and collection type
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $uid		nextcloud user id
	 * @param string $type		collection type
	 * 
	 * @return array 			of collection(s) and attributes
	 */
	public function listCollections(string $uid, string $type): array {

		// construct response object
		$response = [];
		// retrieve all collections
		if ($type == 'CC') {
			$response['Collections'] = $this->ContactsService->listCollections($uid);
		}
		if ($type == 'EC') {
			$response['Collections'] = []; //$this->EventsService->listCollections($uid);
		}
		if ($type == 'TC') {
			$response['Collections'] = []; //$this->TasksService->listCollections($uid);
		}
		// return response
		return $response;

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
			$files = scandir(__DIR__ . '/../Resources/');
			foreach ($files as $file) {
				if (strstr($file, '.tpl')) {
					$response['Formats'][] = ['id' => $file, 'label' => str_replace(".tpl", "", $file)];
				}
			}
		}
		if ($type == 'EC') {
			$response['Formats'] = []; //$this->EventsService->listCollections($uid);
		}
		if ($type == 'TC') {
			$response['Formats'] = []; //$this->TasksService->listCollections($uid);
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
		$response = $this->Services->listByUserId($uid);
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
		$rs = $this->Services->create($data);
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
		$rs = $this->Services->modify($id, $data);
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
		$rs = $this->Services->delete($id);
		// return response
		return $rs;

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
			// evaluate, if id address restriction is set
			if (isset($restrictions->ip) && count($restrictions->ip) > 0) {
				$valid = false;
				foreach ($restrictions->ip as $entry) {
					// evaluate, if ip address matches
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

			// evaluate, if mac address restriction is set
			if (!empty($restrictions->mac)) {
				// evaluate, if mac matches
				if ($restrictions->mac != $meta['mac']) {
					return false;
				}
			}
		}

		return $service;
	}

	public function generateCSV(array $service) {

		// modify service entry accessed in the data store
		$this->Services->modifyAccessed((string) $service['id'], time(), '');
		// load entities
		$entities = $this->ContactsService->listEntities($service['data_collection']);
		// document start
		yield 'ID,UID,CID,State,CreatedOn,ModifiedOn,Label,' . 
			  'NameLast,NameFirst,NameOther,NamePrefix,NameSuffix,NamePhoneticLast,NamePhoneticFirst,NamePhoneticOther,NameAliases,' .
			  'BirthDay,Gender,Partner,AnniversaryDay,Address,Phone,Email,IMPP,' . 
			  'OccupationOrganization,OccupationTitle,OccupationRole,OccupationDepartment,' . PHP_EOL;
		// document iteration
		foreach ($entities as $lo) {

			// convert to contact object
            $co = $this->ContactsService->toContactObject(Reader::read($lo['carddata']));
            $co->ID = (string) $lo['uri'];
            $co->CID = (string) $lo['addressbookid'];
            $co->ModifiedOn = new \DateTime(date("Y-m-d H:i:s", $lo['lastmodified']));
            $co->State = trim((string) $lo['etag'],'"');
			
			$csv = '';
        	$csv .= "$co->ID,";
			$csv .= "$co->UID,";
			$csv .= "$co->CID,";
			$csv .= "$co->State,";
			$csv .= ($co->CreatedOn instanceof \DateTime) ? '"' . $co->CreatedOn->format(DATE_W3C) . ';' . $co->CreatedOn->getTimeZone()->getName() . '",' : '"",';
			$csv .= ($co->ModifiedOn instanceof \DateTime) ? '"' . $co->ModifiedOn->format(DATE_W3C) . ';' . $co->ModifiedOn->getTimeZone()->getName() . '",' : '"",';
			$csv .= '"' . $co->Label . '",';
			$csv .= '"' . $co->Name->Last . '",';
			$csv .= '"' . $co->Name->First . '",';
			$csv .= '"' . $co->Name->Other . '",';
			$csv .= '"' . $co->Name->Prefix . '",';
			$csv .= '"' . $co->Name->Suffix . '",';
			$csv .= '"' . $co->Name->PhoneticLast . '",';
			$csv .= '"' . $co->Name->PhoneticFirst . '",';
			$csv .= '"' . $co->Name->PhoneticOther . '",';
			$csv .= '"' . $co->Name->Aliases . '",';
			$csv .= ($co->BirthDay instanceof \DateTime) ? '"' . $co->BirthDay->format('Y-m-d') . '",' : '"",';
			$csv .= "$co->Gender,";
			$csv .= "$co->Partner,";
			$csv .= ($co->AnniversaryDay instanceof \DateTime) ? '"' . $co->AnniversaryDay->format('Y-m-d') . '",' : '"",';
			$csv .= '"';
			foreach ($co->Address as $entry) {
				$csv .= $entry->Type . "|" . 
				addcslashes($entry->Street, '"') . "|" . 
				addcslashes($entry->Locality, '"') . "|" . 
				addcslashes($entry->Region, '"') . "|" . 
				addcslashes($entry->Code, '"') . "|" . 
				addcslashes($entry->Country, '"') . ";";
			}
			$csv .= '",';
			$csv .= '"';
			foreach ($co->Phone as $entry) {
				$csv .= $entry->Type . "|" . 
				$entry->SubType . "|" . 
				addcslashes($entry->Number, '"') . ";";
			}
			$csv .= '",';
			$csv .= '"';
			foreach ($co->Email as $entry) {
				$csv .= $entry->Type . "|" . 
				$entry->Address . ";";
			}
			$csv .= '",';
			$csv .= '"';
			foreach ($co->IMPP as $entry) {
				$csv .= $entry->Type . "|" . 
				$entry->Address . ";";
			}
			$csv .= '",';
			$csv .= '"' . $co->Occupation->Organization . '",';
			$csv .= '"' . $co->Occupation->Title . '",';
			$csv .= '"' . $co->Occupation->Role . '",';
			$csv .= '"' . $co->Occupation->Department . '",';
			$csv .= '"';
			foreach ($co->Tags as $entry) {
				$csv .= $entry . ";";
			}
			$csv .= '",';
			$csv .= '"' . $co->Notes . '",';

			yield $csv . PHP_EOL;
			
		}

	}

	public function generateJSON(array $service) {

		// modify service entry accessed in the data store
		$this->Services->modifyAccessed((string) $service['id'], time(), '');
		// load entities
		$entities = $this->ContactsService->listEntities($service['data_collection']);
		// document start
		yield '[';
		// document iteration
		$count = count($entities);
		foreach ($entities as $lo) {
			
			$count -= 1;
			// convert to contact object
            $co = $this->ContactsService->toContactObject(Reader::read($lo['carddata']));
            $co->ID = (string) $lo['uri'];
            $co->CID = (string) $lo['addressbookid'];
            $co->ModifiedOn = new \DateTime(date("Y-m-d H:i:s", $lo['lastmodified']));
            $co->State = trim((string) $lo['etag'],'"');
			
			if ($count == 0)
				yield json_encode($co);
			else {
				yield json_encode($co) . ',';
			}
			
		}
		// document end
		yield ']';

	}
	
	public function generateTemplate(array $service) {

		// modify service entry accessed in the data store
		$this->Services->modifyAccessed((string) $service['id'], time(), '');
		// instance template service
		$TemplateService = new TemplateService();
		// load template
		$TemplateService->fromFile(dirname(__DIR__) . '/Resources/' . $service['format'] . '.tpl');
		// load entities
		$entities = $this->ContactsService->listEntities($service['data_collection']);
		// document start
		yield $TemplateService->generateStart();
		// document iteration
		foreach ($entities as $lo) {
			// convert to contact object
            $co = $this->ContactsService->toContactObject(Reader::read($lo['carddata']));
            $co->ID = (string) $lo['uri'];
            $co->CID = (string) $lo['addressbookid'];
            $co->ModifiedOn = new \DateTime(date("Y-m-d H:i:s", $lo['lastmodified']));
            $co->State = trim((string) $lo['etag'],'"');
			
			yield $TemplateService->generateIteration($co);
		}
		// document end
		yield $TemplateService->generateEnd();

	}

}
