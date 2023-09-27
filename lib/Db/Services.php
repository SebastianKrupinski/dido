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
		$dc = $this->DataStore->getQueryBuilder();
		$dc->select('*')
			->from($this->DataStoreTable)
			->where($dc->expr()->eq('id', $dc->createNamedParameter($id)));
		// execute command and return result
		return $this->findEntity($dc);
		
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
		$dc = $this->DataStore->getQueryBuilder();
		$dc->select('*')
			->from($this->DataStoreTable)
			->where($dc->expr()->eq('service_id', $dc->createNamedParameter($id)));
		// execute command
		$result = $dc->execute();
		// return result
		return $result->fetch();
		
	}

	/**
	 * create a service entry in the data store
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param array $entry		collection of field names and values
	 * 
	 * @return array 		
	 */
	public function create(array $entry) : int {

		// construct data store command
		$dc = $this->DataStore->getQueryBuilder();
		$dc->insert($this->DataStoreTable);
		foreach ($entry as $column => $value) {
			$dc->setValue($column, $dc->createNamedParameter($value));
		}
		// execute command
		$dc->execute();
	
		// Get the ID of the newly inserted record (if needed)
		return $this->DataStore->lastInsertId();
		
	}

}
