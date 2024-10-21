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

		if (!$schema->hasTable('confirmator')) {
			$table = $schema->createTable('confirmator');
			$table->addColumn('id', Types::BIGINT, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
            $table->addColumn('user_id', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('contact_id', Types::BIGINT, [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'confirmator_uid');
			$table->addForeignKeyConstraint('oc_contact', ['contact_id'], ['id']);
		} else {
            $table = $schema->getTable('confirmator');
            $keys = $table->getForeignKeys();
            foreach ($keys as $k) {
                if (in_array('interval_id', $k->getLocalColumns())) {
                    $table->removeForeignKey($k->getName());
                }
            };
            $table->dropColumn('interval_id');
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
            $table->addColumn('confirmators_group_id', Types::BIGINT, [
				'notnull' => true,
			]);
            $table->addColumn('interval_id', Types::BIGINT, [
				'notnull' => true,
			]);
            $table->addColumn('trigger_id', Types::BIGINT, [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'task_uid');
			$table->addForeignKeyConstraint('oc_contacts_group', ['contacts_group_id'], ['id']);
            $table->addForeignKeyConstraint('oc_jobs_group', ['jobs_group_id'], ['id']);
            $table->addForeignKeyConstraint('oc_confirmators_group', ['confirmators_group_id'], ['id']);
            $table->addForeignKeyConstraint('oc_checkup_interval', ['interval_id'], ['id']);
            $table->addForeignKeyConstraint('oc_trigger', ['trigger_id'], ['id']);
		} else {
            $table = $schema->getTable('task');
			$table->addColumn('last_checkup', Types::DATETIME_IMMUTABLE, [
				'notnull' => true,
			]);
            $table->addColumn('interval_id', Types::BIGINT, [
				'notnull' => true,
			]);
            $table->addForeignKeyConstraint('oc_checkup_interval', ['interval_id'], ['id']);
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