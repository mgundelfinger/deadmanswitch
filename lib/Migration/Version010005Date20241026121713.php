<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DeadManSwitch\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\DB\Types;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * FIXME Auto-generated migration step: Please modify to your needs!
 */
class Version010005Date20241026121713 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure(): ISchemaWrapper $schemaClosure
	 * @param array $options
	 */
	public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {
	}

	/**
	 * @param IOutput $output
	 * @param Closure(): ISchemaWrapper $schemaClosure
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('user_settings')) {
			$table = $schema->createTable('user_settings');
			$table->addColumn('id', Types::BIGINT, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
			$table->addColumn('user_id', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);

			$table->addColumn('locale', Types::STRING, [
				'notnull' => false,
				'length' => 64,
			]);

			$table->addColumn('color', Types::STRING, [
				'notnull' => false,
				'length' => 64,
			]);
			$table->setPrimaryKey(['id']);
			$table->addIndex(['user_id'], 'settings_uid');

		} else {
			$table = $schema->getTable('user_settings');
			$table->dropIndex('task_uid');
			$table->addIndex(['user_id'], 'settings_uid');
		}

		if (!$schema->hasTable('reset_token')) {
			$table = $schema->createTable('reset_token');
			$table->addColumn('id', Types::BIGINT, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);

			$table->addColumn('contact_id', Types::BIGINT, [
				'notnull' => true,
			]);

			$table->addColumn('alive_status_id', Types::INTEGER, [
				'notnull' => true,
			]);

			$table->addColumn('token', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->setPrimaryKey(['id']);
			$table->addForeignKeyConstraint('oc_contact', ['contact_id'], ['id'], ['onDelete' => 'cascade']);
			$table->addForeignKeyConstraint('oc_alive_status', ['alive_status_id'], ['id'], ['onDelete' => 'cascade']);
		}

		return $schema;
	}

	/**
	 * @param IOutput $output
	 * @param Closure(): ISchemaWrapper $schemaClosure
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {
	}
}
