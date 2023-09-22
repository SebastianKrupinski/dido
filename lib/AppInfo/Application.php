<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Sebastian Krupinski <krupinski01@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Data\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
	public const APP_ID = 'data';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}
}
