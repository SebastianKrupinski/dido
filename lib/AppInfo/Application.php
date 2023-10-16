<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Sebastian Krupinski <krupinski01@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Data\AppInfo;

use OCP\AppFramework\App;
use \OCA\Data\Middleware\AuthMiddleware;

class Application extends App {
	public const APP_ID = 'data';

	public function __construct() {
		parent::__construct(self::APP_ID);

		$container = $this->getContainer();
		/**
         * Middleware
         */
        $container->registerService('AuthMiddleware', function($c){
            return new AuthMiddleware();
        });

        // executed in the order that it is registered
        $container->registerMiddleware('AuthMiddleware');
	}
}
