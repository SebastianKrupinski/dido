<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Sebastian Krupinski <krupinski01@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Dido\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;

class Application extends App implements IBootstrap {
	public const APP_ID = 'dido';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}

    public function register(IRegistrationContext $context): void {
		/** @var IUserManager $userManager */
		$userManager = $this->getContainer()->get(\OCP\IUserManager::class);

		/* Register our own user backend */
		$userBackend = $this->getContainer()->get(\OCA\Dido\User\Backend::class);
		$userManager->registerBackend($userBackend);
		//OC_User::useBackend($backend);
	}

    public function boot(IBootContext $context): void {
        
	}

}
