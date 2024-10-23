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

class Version010002Date20241009131200 extends SimpleMigrationStep {

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

        if (!$schema->hasTable('jobs_group')) {
			$table = $schema->createTable('jobs_group');
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
				'length' => 64,
			]);
			$table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'jobs_group_uid');
		}

        if (!$schema->hasTable('contacts_group')) {
			$table = $schema->createTable('contacts_group');
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
				'length' => 64,
			]);
			$table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'contacts_group_uid');
		}

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
			$table->addColumn('interval_id', Types::BIGINT, [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'confirmator_uid');
			$table->addForeignKeyConstraint('oc_contact', ['contact_id'], ['id']);
			$table->addForeignKeyConstraint('oc_checkup_interval', ['interval_id'], ['id']);
		}

        if (!$schema->hasTable('confirmators_group')) {
			$table = $schema->createTable('confirmators_group');
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
				'length' => 64,
			]);
			$table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'confirmators_group_uid');
		}

		if (!$schema->hasTable('jobs_group_map')) {
			$table = $schema->createTable('jobs_group_map');
			$table->addColumn('id', Types::BIGINT, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
            $table->addColumn('job_id', Types::BIGINT, [
				'notnull' => true,
			]);
            $table->addColumn('jobs_group_id', Types::BIGINT, [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
            $table->addForeignKeyConstraint('oc_job', ['job_id'], ['id']);
            $table->addForeignKeyConstraint('oc_jobs_group', ['jobs_group_id'], ['id']);
		}

        if (!$schema->hasTable('contacts_group_map')) {
			$table = $schema->createTable('contacts_group_map');
			$table->addColumn('id', Types::BIGINT, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
            $table->addColumn('contact_id', Types::BIGINT, [
				'notnull' => true,
			]);
            $table->addColumn('contacts_group_id', Types::BIGINT, [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
            $table->addForeignKeyConstraint('oc_contact', ['contact_id'], ['id']);
            $table->addForeignKeyConstraint('oc_contacts_group', ['contacts_group_id'], ['id']);
		}

        if (!$schema->hasTable('confirmators_group_map')) {
			$table = $schema->createTable('confirmators_group_map');
			$table->addColumn('id', Types::BIGINT, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
            $table->addColumn('confirmator_id', Types::BIGINT, [
				'notnull' => true,
			]);
            $table->addColumn('confirmators_group_id', Types::BIGINT, [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
            $table->addForeignKeyConstraint('oc_confirmator', ['confirmator_id'], ['id']);
            $table->addForeignKeyConstraint('oc_confirmators_group', ['confirmators_group_id'], ['id']);
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
            $table->addColumn('trigger_id', Types::BIGINT, [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'task_uid');
			$table->addForeignKeyConstraint('oc_contacts_group', ['contacts_group_id'], ['id']);
            $table->addForeignKeyConstraint('oc_jobs_group', ['jobs_group_id'], ['id']);
            $table->addForeignKeyConstraint('oc_trigger', ['trigger_id'], ['id']);
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