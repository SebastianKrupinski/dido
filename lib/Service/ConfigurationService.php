<?php
declare(strict_types=1);

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

namespace OCA\Data\Service;

use Exception;
use Psr\Log\LoggerInterface;

use OCP\IConfig;
use OCP\Security\ICrypto;
use OCP\IUserManager;
use OCP\App\IAppManager;

use OCA\EWS\AppInfo\Application;

class ConfigurationService {

	/**
	 * Default System Configuration 
	 * @var array
	 * */
	private const _SYSTEM = [];

	/**
	 * Default System Secure Parameters 
	 * @var array
	 * */
	private const _SYSTEM_SECURE = [];

	/**
	 * Default User Configuration 
	 * @var array
	 * */
	private const _USER = [];

	/**
	 * Default User Secure Parameters 
	 * @var array
	 * */
	private const _USER_SECURE = [];

	/** @var LoggerInterface */
	private $_logger;

	/** @var IConfig */
	private $_ds;
	
	/** @var ICrypto */
	private $_cs;

	/** @var IUserManager */
	private $_usermanager;

	/** @var IAppManager */
	private $_appmanager;

	public function __construct(LoggerInterface $logger, IConfig $config, ICrypto $crypto, IUserManager $userManager, IAppManager $appManager)
	{
		$this->_logger = $logger;
		$this->_ds = $config;
		$this->_cs = $crypto;
		$this->_usermanager = $userManager;
		$this->_appmanager = $appManager;
	}

	/**
	 * Retrieves collection of system configuration parameters
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $uid		nextcloud user id
	 * @param array $keys		collection of configuration parameter keys
	 * 
	 * @return array of key/value pairs, of configuration parameter
	 */
	public function retrieveUser(string $uid, ?array $keys = null): array {

		// define parameters place holder
		$parameters = [];
		// evaluate if we are looking for specific parameters
		if (!isset($keys) || count($keys) == 0) {
			// retrieve all user configuration keys
			$keys = array_keys(self::_USER);
			// retrieve all user configuration values
			foreach ($keys as $entry) {
				$parameters[$entry] = $this->retrieveUserValue($uid, $entry);
			}
		}
		else {
			// retrieve specific user configuration values
			foreach ($keys as $entry) {
				$parameters[$entry] = $this->retrieveUserValue($uid, $entry);
			}
		}
		// return configuration parameters
		return $parameters;

	}

	/**
	 * Deposit collection of system configuration parameters
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $uid			nextcloud user id
	 * @param array $parameters		collection of key/value pairs, of parameters
	 * 
	 * @return void
	 */
	public function depositUser($uid, array $parameters): void {
		
		// deposit system configuration parameters
		foreach ($parameters as $key => $value) {
			$this->depositUserValue($uid, $key, $value);
		}

	}

	/**
	 * Destroy collection of system configuration parameters
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $uid		nextcloud user id
	 * @param array $keys		collection of configuration parameter keys
	 * 
	 * @return void
	 */
	public function destroyUser(string $uid, ?array $keys = null): void {

		// evaluate if we are looking for specific parameters
		if (!isset($keys) || count($keys) == 0) {
			$keys = $this->_ds->getUserKeys($uid, Application::APP_ID);
		}
		// destroy system configuration parameter
		foreach ($keys as $entry) {
			$this->destroyUserValue($uid, $entry);
		}

	}
	
	/**
	 * Retrieves single system configuration parameter
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $uid		nextcloud user id
	 * @param string $key		configuration parameter key
	 * 
	 * @return string configuration parameter value
	 */
	public function retrieveUserValue(string $uid, string $key): string {

		// retrieve configured parameter value
		$value = $this->_ds->getUserValue($uid, Application::APP_ID, $key);
		// evaluate if value was returned
		if ($value != '') {
			// evaluate if parameter is on the secure list and is not empty
			if (isset(self::_USER_SECURE[$key]) && !empty($value)) {
				try {
					$value = $this->_cs->decrypt($value);
				} catch (\Throwable $th) {
					// Do nothing just return the original value
				}
			}
			// return configuration parameter value
			return $value;
		} else {
			// return default system configuration value
			return self::_USER[$key];
		}

	}

	/**
	 * Deposit single system configuration parameter
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $uid		nextcloud user id
	 * @param string $key		configuration parameter key
	 * @param string $value		configuration parameter value
	 * 
	 * @return void
	 */
	public function depositUserValue(string $uid, string $key, string $value): void {
		
		// trim whitespace
		$value = trim($value);
		// evaluate if parameter is on the secure list
		if (isset(self::_USER_SECURE[$key]) && !empty($value)) {
			$value = $this->_cs->encrypt($value);
		}
		// deposit user configuration parameter value
		$this->_ds->setUserValue($uid, Application::APP_ID, $key, $value);

	}

	/**
	 * Destroy single user configuration parameter
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $uid		nextcloud user id
	 * @param string $key		configuration parameter keys
	 * 
	 * @return void
	 */
	public function destroyUserValue(string $uid, string $key): void {

		// destroy user configuration parameter
		$this->_ds->deleteUserValue($uid, Application::APP_ID, $key);

	}

	/**
	 * Retrieves collection of system configuration parameters
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param array $keys	collection of configuration parameter keys
	 * 
	 * @return array of key/value pairs, of configuration parameter
	 */
	public function retrieveSystem(?array $keys = null): array {

		// evaluate if we are looking for specific parameters
		if (!isset($keys) || count($keys) == 0) {
			$keys = array_keys(self::_SYSTEM);
		}
		// retrieve system configuration values
		$parameters = [];
		foreach ($keys as $entry) {
			$parameters[$entry] = $this->retrieveSystemValue($entry);
		}
		// return configuration parameters
		return $parameters;

	}

	/**
	 * Deposit collection of system configuration parameters
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param array $parameters	collection of key/value pairs, of parameters
	 * 
	 * @return void
	 */
	public function depositSystem(array $parameters): void {
		
		// deposit system configuration parameters
		foreach ($parameters as $key => $value) {
			$this->depositSystemValue($key, $value);
		}

	}

	/**
	 * Destroy collection of system configuration parameters
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param array $keys	collection of configuration parameter keys
	 * 
	 * @return void
	 */
	public function destroySystem(?array $keys = null): void {

		// evaluate if we are looking for specific parameters
		if (!isset($keys) || count($keys) == 0) {
			$keys = $this->_ds->getAppKeys(Application::APP_ID);
		}
		// destroy system configuration parameter
		foreach ($keys as $entry) {
			$this->destroySystemValue($entry);
		}

	}

	/**
	 * Retrieves single system configuration parameter
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $key	configuration parameter key
	 * 
	 * @return string configuration parameter value
	 */
	public function retrieveSystemValue(string $key): string {

		// retrieve configured parameter value
		$value = $this->_ds->getAppValue(Application::APP_ID, $key);
		// evaluate if value was returned
		if ($value != '') {
			// evaluate if parameter is on the secure list and is not empty
			if (isset(self::_SYSTEM_SECURE[$key])  && !empty($value)) {
				try {
					$value = $this->_cs->decrypt($value);
				} catch (\Throwable $th) {
					// Do nothing just return the original value
				}
			}
			// return configuration parameter value
			return $value;
		} else {
			// return default system configuration value
			return self::_SYSTEM[$key];
		}

	}

	/**
	 * Deposit single system configuration parameter
	 * 
	 * @since Release 1.0.0
	 * 
	 * @param string $key		configuration parameter key
	 * @param string $value		configuration parameter value
	 * 
	 * @return void
	 */
	public function depositSystemValue(string $key, string $value): void {
		
		// trim whitespace
		$value = trim($value);
		// evaluate if parameter is on the secure list
		if (isset(self::_SYSTEM_SECURE[$key]) && !empty($value)) {
			$value = $this->_cs->encrypt($value);
		}
		// deposit system configuration parameter value
		$this->_ds->setAppValue(Application::APP_ID, $key, $value);

	}

	/**
	 * Destroy single system configuration parameter
	 * 
	 * @since Release 1.0.0
	 * 
	 * @return void
	 */
	public function destroySystemValue(string $key): void {

		// destroy system configuration parameter
		$this->_ds->deleteAppValue(Application::APP_ID, $key);

	}

	/**
	 * retrieve contacts app status
	 * 
	 * @since Release 1.0.0
	 * 
	 * @return bool
	 */
	public function isMailAvailable(string $uid): bool {

		$user = $this->_usermanager->get($uid);
		return $this->_appmanager->isEnabledForUser('mail', $user);

	}

	/**
	 * retrieve contacts app status
	 * 
	 * @since Release 1.0.0
	 * 
	 * @return bool
	 */
	public function isContactsAvailable(string $uid): bool {

		$user = $this->_usermanager->get($uid);
		return $this->_appmanager->isEnabledForUser('contacts', $user);

	}

	/**
	 * retrieve calendar app status
	 * 
	 * @since Release 1.0.0
	 * 
	 * @return bool
	 */
	public function isCalendarAvailable(string $uid): bool {

		$user = $this->_usermanager->get($uid);
		return $this->_appmanager->isEnabledForUser('calendar', $user);

	}

	/**
	 * retrieve task app status
	 * 
	 * @since Release 1.0.0
	 * 
	 * @return bool
	 */
	public function isTasksAvailable(string $uid): bool {

		$user = $this->_usermanager->get($uid);
		return $this->_appmanager->isEnabledForUser('tasks', $user);

	}

	/**
	 * encrypt string
	 * 
	 * @since Release 1.0.0
	 * 
	 * @return string
	 */
	public function encrypt(string $value): string {

		return $this->_cs->encrypt($value);

	}

	/**
	 * decrypt string
	 * 
	 * @since Release 1.0.0
	 * 
	 * @return string
	 */
	public function decrypt(string $value): string {

		return $this->_cs->decrypt($value);

	}
	
}
