<?php
//declare(strict_types=1);

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

namespace OCA\Data\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class Services {

	private IDBConnection $DataStore;
	private string $DataStoreTable = 'data_services';

	public function __construct(IDBConnection $db) {
		$this->DataStore = $db;
	}

	/**
	 * retrieve all services from the data store
	 * 
	 * @since Release 1.0.0
	 * 
	 * @return array 		
	 */
	public function listByUserId(string $uid) : array {

		// construct data store command
		$cmd = $this->DataStore->getQueryBuilder();
		$cmd->select('DS.*', 'CC.displayname AS data_collection_name')
			->from($this->DataStoreTable, 'DS')
			->leftJoin('DS', 'addressbooks', 'CC', 'DS.data_type = "CC" AND DS.data_collection = CC.id');
		// evaluate, if id is present
		if (!empty($uid)) {
			$cmd->where($cmd->expr()->eq('uid', $cmd->createNamedParameter($uid)));
		}
		// execute command
		$rs = $cmd->executeQuery()->fetchAll();
		$cmd->executeQuery()->closeCursor();
		// return result or null
		if (is_array($rs) && count($rs) > 0) {
			return $rs;
		}
		else {
			return [];
		}
		
	}

	/**
	 * fetch a service entry from the data store
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param array $id			entry id
	 * 
	 * @return array 		
	 */
	public function fetch(int $id) : array {

		// construct data store command
		$cmd = $this->DataStore->getQueryBuilder();
		$cmd->select('*')
			->from($this->DataStoreTable)
			->where($cmd->expr()->eq('id', $cmd->createNamedParameter($id)));
		// execute command and return result
		return $this->findEntity($cmd);
		
	}

	/**
	 * fetch a service entry by service id from the data store
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param array $id			entry service id
	 * 
	 * @return array 		
	 */
	public function fetchByServiceId(string $id) : array {

		// construct data store command
		$cmd = $this->DataStore->getQueryBuilder();
		$cmd->select('*')
			->from($this->DataStoreTable)
			->where($cmd->expr()->eq('service_id', $cmd->createNamedParameter($id)));
		// execute command
		$result = $cmd->execute();
		// return result
		return $result->fetch();
		
	}

	/**
	 * create a service entry in the data store
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $uid		nextcloud user id
	 * @param array $data		entry data
	 * 
	 * @return bool
	 */
	public function create(array $data) : bool {

		// construct data store command
		$cmd = $this->DataStore->getQueryBuilder();
		$cmd->insert($this->DataStoreTable);
		foreach ($data as $column => $value) {
			$cmd->setValue($column, $cmd->createNamedParameter($value));
		}
		// execute command
		$cmd->execute();
		// return result
		return true;
		
	}
	/**
	 * modify a service entry in the data store
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $id		entry id
	 * @param array $data		entry data
	 * 
	 * @return bool
	 */
	public function modify(string $id, array $data) : bool {

		// construct data store command
		$cmd = $this->DataStore->getQueryBuilder();
		$cmd->update($this->DataStoreTable)
			->where($cmd->expr()->eq('id', $cmd->createNamedParameter($id)));
		foreach ($data as $column => $value) {
			$cmd->set($column, $cmd->createNamedParameter($value));
		}
		// execute command
		$cmd->execute();
		// return result
		return true;
		
	}
	/**
	 * modify a service entry accessed data in the data store
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $id		entry id
	 * @param array $on			accessed timestamp
	 * @param array $from		accessed from
	 *  
	 * @return bool
	 */
	public function modifyAccessed(string $id, int $on, string $from) : bool {

		// construct data store command
		$cmd = $this->DataStore->getQueryBuilder();
		$cmd->update($this->DataStoreTable)
			->where($cmd->expr()->eq('id', $cmd->createNamedParameter($id)))
			->set('accessed_on', $cmd->createNamedParameter($on))
			->set('accessed_from', $cmd->createNamedParameter($from));
		// execute command
		$cmd->execute();
		// return result
		return true;
		
	}
	/**
	 * delete a service entry from the data store
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $id		entry id
	 * 
	 * @return bool
	 */
	public function delete(string $id) : bool {

		// construct data store command
		$cmd = $this->DataStore->getQueryBuilder();
		$cmd->delete($this->DataStoreTable)
			->where($cmd->expr()->eq('id', $cmd->createNamedParameter($id)));
		// execute command
		$cmd->execute();
		// return result
		return true;
		
	}

}
