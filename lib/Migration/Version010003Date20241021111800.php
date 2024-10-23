<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
declare(strict_types=1);

namespace OCA\DeadManSwitch\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\DB\Types;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version010003Date20241021111800 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if ($schema->hasTable('confirmator')) {
			if ($schema->hasTable('confirmators_group_map')) {
				$schema->dropTable('confirmators_group_map');
				$schema->dropTable('confirmators_group');
				$schema->dropTable('confirmator');
			}
		}

		if (!$schema->hasTable('task')) {
			$table = $schema->createTable('task');
			$table->addColumn('id', Types::BIGINT, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
            $table->addColumn('user_id', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
            $table->addColumn('name', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
            $table->addColumn('active', Types::BOOLEAN, [
				'notnull' => false,
			]);
			$table->addColumn('contacts_group_id', Types::BIGINT, [
				'notnull' => true,
			]);
			$table->addColumn('jobs_group_id', Types::BIGINT, [
				'notnull' => true,
			]);
            $table->addColumn('death_days', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'task_uid');
			$table->addForeignKeyConstraint('oc_contacts_group', ['contacts_group_id'], ['id']);
            $table->addForeignKeyConstraint('oc_jobs_group', ['jobs_group_id'], ['id']);
		} else {
            $table = $schema->getTable('task');
			$keys = $table->getForeignKeys();
			foreach ($keys as $k) {
			    if (in_array('trigger_id', $k->getLocalColumns()) || 
					in_array('confirmators_group_id', $k->getLocalColumns())) {
						$table->removeForeignKey($k->getName());
				}
			};
			$table->dropColumn('trigger_id');
			$table->dropColumn('interval_id');
			$table->dropColumn('confirmators_group_id');
			$table->addColumn('death_days', Types::INTEGER, [
				'notnull' => true,
			]);
        }

		if (!$schema->hasTable('alive_status')) {
			$table = $schema->createTable('alive_status');
			$table->addColumn('id', Types::BIGINT, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
            $table->addColumn('user_id', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('status', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('contacts_group_id', Types::BIGINT, [
				'notnull' => true,
			]);
			$table->addColumn('alive_days', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('pending_days', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('last_change', Types::DATETIME_IMMUTABLE, [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
			$table->addIndex(['user_id'], 'alive_status_uid');
			$table->addForeignKeyConstraint('oc_contacts_group', ['contacts_group_id'], ['id']);
		} else {
			$table = $schema->getTable('alive_status');
			$table->dropColumn('name');
            $table->addColumn('user_id', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('status', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('contacts_group_id', Types::BIGINT, [
				'notnull' => true,
			]);
			$table->addColumn('alive_days', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('pending_days', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('last_change', Types::DATETIME_IMMUTABLE, [
				'notnull' => true,
			]);
			$table->addIndex(['user_id'], 'alive_status_uid');
			$table->addForeignKeyConstraint('oc_contacts_group', ['contacts_group_id'], ['id']);
		}

		return $schema;
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
	}
}