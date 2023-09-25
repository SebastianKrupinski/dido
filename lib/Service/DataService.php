<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Sebastian Krupinski <krupinski01@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Data\Service;

use Exception;

class DataService {

	public function __construct() {

	}

	public function authorize(string $id, string $token, string $format): bool {
		return true;
	}

	public function generate(string $id) {

		// Create an instance of the XMLTemplate class
		$TemplateService = new TemplateService();

		$TemplateService->fromFile(dirname(__DIR__) . '/Resources/SnomV2.tpl');

		// Data with an array of people
		$data = [];
		$data[] = (object) [
			'NameFirst' => 'John',
			'NameLast' => 'Doe',
		];

		$data[] = (object) [
			'NameFirst' => 'Alice',
			'NameLast' => 'Doe',
		];

		// document start
		yield $TemplateService->generateStart();
		// document iteration
		foreach ($data as $entry) {
			yield $TemplateService->generateIteration($entry);
		}
		// document end
		yield $TemplateService->generateEnd();

	}
}
