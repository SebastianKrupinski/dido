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

namespace OCA\Dido\Utile;

class Validator {

    private const _fqdn = '/(?=^.{1,254}$)(^(?:(?!\d|-)[a-z0-9\-]{1,63}(?<!-)\.)+(?:[a-z]{2,})$)/i';
    private const _ip4 = '/^(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/';
    private const _ip6 = "/^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$/"; 
    private const _ip4_cidr = '/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\/([0-9]|[1-2][0-9]|3[0-2])$/';
    private const _ip6_cidr = '/^([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}\/[0-9]{1,3}$/';

    /**
     * validate fully quntified domain name
     * 
     * @since Release 1.0.0
     * 
	 * @param string $fqdn - FQDN to validate
	 * 
	 * @return bool
	 */
    static function fqdn(string $fqdn): bool {

        return (!empty($fqdn) && preg_match(self::_fqdn, $fqdn) > 0);

    }

    /**
     * validate IPv4 address
     * 
     * @since Release 1.0.0
     * 
	 * @param string $ip - IPv4 address to validate
	 * 
	 * @return bool
	 */
    static function ip4(string $ip): bool {

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * validate IPv6 address
     * 
     * @since Release 1.0.0
     * 
	 * @param string $ip - IPv6 address to validate
	 * 
	 * @return bool
	 */
    static function ip6(string $ip): bool {

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return true;
        } else {
            return false;
        }

    }
    
    /**
     * validate IPv4 cidr notation
     * 
     * @since Release 1.0.0
     * 
	 * @param string $cidr - IPv4 cidr to validate
	 * 
	 * @return bool
	 */
    static function ip4cidr(string $cidr): bool {

        return (!empty($cidr) && preg_match(self::_ip4_cidr, $cidr) > 0);

    }

    /**
     * validate IPv6 cidr notation
     * 
     * @since Release 1.0.0
     * 
	 * @param string $cidr - IPv6 cidr to validate
	 * 
	 * @return bool
	 */
    static function ip6cidr(string $cidr): bool {

        return (!empty($cidr) && preg_match(self::_ip6_cidr, $cidr) > 0);

    }

    static function ipInCidr(string $ip, string $cidr): bool {

        if (str_contains($cidr, '/')) {
            // split cidr and convert to parameters
            list($cidr_net, $cidr_mask) = explode('/', $cidr);
            // convert ip address and cidr network to binary
            $ip = inet_pton($ip);
            $cidr_net = inet_pton($cidr_net);
            // evaluate, if ip is valid
            if ($ip === false) {
                throw new InvalidArgumentException('Invalid IP Address');
            }
            // evaluate, if cidr network is valid
            if ($cidr_net === false) {
                throw new InvalidArgumentException('Invalid CIDR Network');
            }
            // evaluate, if ip and network are the same version
            if (strlen($ip) != strlen($cidr_net)) {
                throw new InvalidArgumentException('IP Address and CIDR Network version do not match');
            }
            
            // determain the amount of full bit bytes and add them
            $mask = str_repeat(chr(255), (int) floor($cidr_mask / 8));
            // determain, if any bits are remaing
            if ((strlen($mask) * 8) < $cidr_mask) {
                $mask .= chr(1 << (8 - ($cidr_mask - (strlen($mask) * 8))));
            }
            // determain, the amount of empty bit bytes and add them
            $mask = str_pad($mask, strlen($cidr_net), chr(0));

            // Compare the mask
            return ($ip & $mask) === ($cidr_net & $mask);
            
        }
        else {
            // return comparison
            return inet_pton($ip) === inet_pton($cidr);
        }
        
    }

    static function ipInRange(string $ip, string $start, string $end): bool {

        if(inet_pton($ip)>=inet_pton($start) && inet_pton($ip)<=inet_pton($end)) {
            return true;
        }
        else {
            return false;  
        }
           
    }
    
     /**
     * validate host
     * 
     * @since Release 1.0.0
     * 
	 * @param string $host - FQDN/IPv4/IPv6 address to validate
	 * 
	 * @return bool
	 */
    static function host(string $host): bool {

        if (self::fqdn($host)) {
            return true;
        }

        if (self::ip4($host)) {
            return true;
        }

        if (self::ip6($host)) {
            return true;
        }

        return false;

    }

    /**
     * validate email address
     * 
     * @since Release 1.0.0
     * 
	 * @param string $address - email address to validate
	 * 
	 * @return bool
	 */
    static function email(string $address): bool {

        return (!empty($address) && filter_var($address, FILTER_VALIDATE_EMAIL));

    }

    /**
     * validate username
     * 
     * @since Release 1.0.0
     * 
	 * @param string $username - username to validate
	 * 
	 * @return bool
	 */
    static function username(string $username): bool {

        if (self::email($username)) {
            return true;
        }
        
        // TODO: Windows Login Validator
        /*
        if (self::windows_username($username)) {
            return true;
        }
        */

        return false;

    }

}
