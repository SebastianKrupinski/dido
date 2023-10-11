<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Sebastian Krupinski <krupinski01@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Data\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;

class Version010000Date20201010 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('data_services')) {
			$table = $schema->createTable('data_services');
			$table->addColumn('id', 'integer', [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('uid', 'string', [
				'length' => 255,
				'notnull' => true,
			]);
			$table->addColumn('service_id', 'string', [
				'length' => 255,
				'notnull' => true,
			]);
			$table->addColumn('service_token', 'string', [
				'length' => 255,
				'notnull' => true,
			]);
			$table->addColumn('service_name', 'string', [
				'length' => 255,
			]);
			$table->addColumn('data_type', 'string', [
				'length' => 4,
				'notnull' => true,
			]);
			$table->addColumn('data_collection', 'string', [
				'length' => 255,
				'notnull' => true,
			]);
			$table->addColumn('format', 'string', [
				'length' => 255,
				'notnull' => true,
			]);
			$table->addColumn('permissions', 'string', [
				'length' => 2,
				'notnull' => true,
			]);
			$table->addColumn('restrict_ip', 'text', []);
			$table->addColumn('restrict_mac', 'text', []);
			$table->addColumn('restrict_agent', 'text', []);
			$table->addColumn('accessed_on', 'integer', []);
			$table->addColumn('accessed_from', 'string', [
				'length' => 255,
			]);
			$table->addColumn('created_on', 'integer', []);
			$table->addColumn('created_by', 'string', [
				'length' => 255,
			]);

			$table->setPrimaryKey(['id']);
			$table->addIndex(['user_id'], 'data_user_id_index');
		}
		return $schema;
	}
}
