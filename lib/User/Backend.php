<?php
/**
 * @copyright Copyright (c) 2018 Alexey Abel <dev@abelonline.de>
 *
 * @license GNU AGPL version 3 or any later version
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

namespace OCA\Dido\User;

use Psr\Log\LoggerInterface;

use OCP\IRequest;
use OCP\ISession;
use OCP\User\Backend\ABackend;
use OCP\User\Backend\ICheckPasswordBackend;

class Backend extends ABackend implements ICheckPasswordBackend {

	private IRequest $_Request;
	private ISession $_Session;
	
	public function __construct(IRequest $request,
								ISession $session) {
		$this->_Request = $request;							
		$this->_Session = $session;
	}

	/**
	 * Backend name to be shown in user management
	 * 
	 * @since Release 1.0.0
	 * 
	 * @return string the name of the backend to be shown
	 */
	public function getBackendName(): string {
		return Application::APP_ID;
	}

	/**
	 * @since 1.0.0
	 *
	 * @param string $login The loginname
	 * @param string $secret The password
	 * 
	 * @return string|false The uid on success false on failure
	 */
	public function checkPassword(string $login, string $secret) {
		
		if (str_starts_with($this->_Request->__get('server')['PATH_INFO'], '/apps/dido')) {
			return 'dido-' . $login . '-' . $secret;
		}
		else {
			return false;
		}
		
	}

	/**
	 * delete a user
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $uid The username of the user to delete
	 * 
	 * @return bool
	 */
	public function deleteUser($uid) {
		return false;
	}

	/**
	 * Get a list of all users
	 *
	 * @since Release 1.0.0
	 * 
	 * @param string $search
	 * @param null|int $limit
	 * @param null|int $offset
	 * 
	 * @return string[] an array of all uids
	 */
	public function getUsers($search = '', $limit = null, $offset = null) {
		return [];
	}

	/**
	 * check if a user exists
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $uid the username
	 * 
	 * @return boolean
	 */
	public function userExists($uid) {
		return false;
	}

	/**
	 * get display name of the user
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $uid user ID of the user
	 * 
	 * @return string display name
	 */
	public function getDisplayName($uid) {
		return '';
	}

	/**
	 * Get a list of all display names and user ids.
	 *
	 * @since Release 1.0.0
	 * 
	 * @param string $search
	 * @param int|null $limit
	 * @param int|null $offset
	 * 
	 * @return array an array of all displayNames (value) and the corresponding uids (key)
	 */
	public function getDisplayNames($search = '', $limit = null, $offset = null) {
		return [];
	}

	/**
	 * Check if a user list is available or not
	 * 
	 * @since Release 1.0.0
	 * 
	 * @return boolean if users can be listed or not
	 */
	public function hasUserListings() {
		return false;
	}

}
