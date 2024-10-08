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

class Version010001Date20241008110000 extends SimpleMigrationStep {

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

        if (!$schema->hasTable('job')) {
			$table = $schema->createTable('job');
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
			$table->addColumn('email_subject', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
            $table->addColumn('email_body', Types::STRING, [
				'notnull' => true,
				'length' => 512,
			]);
			$table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'job_uid');
		} else {
            $table = $schema->getTable('job');
            $table->dropColumn('email_subject');
            $table->dropColumn('email_body');
            $table->addColumn('email_subject', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
            $table->addColumn('email_body', Types::STRING, [
				'notnull' => true,
				'length' => 512,
			]);
        }

        if (!$schema->hasTable('trigger')) {
			$table = $schema->createTable('trigger');
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
			$table->addColumn('delay', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'trigger_uid');
		} else {
            $table = $schema->getTable('trigger');
            $table->addColumn('user_id', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
            $table->addIndex(['user_id'], 'trigger_uid');
        }

        if (!$schema->hasTable('contact')) {
			$table = $schema->createTable('contact');
			$table->addColumn('id', Types::BIGINT, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
            $table->addColumn('user_id', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('first_name', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('last_name', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
            $table->addColumn('email', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'contact_uid');
		}

        if (!$schema->hasTable('alive_status')) {
			$table = $schema->createTable('alive_status');
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
			$table->addColumn('name', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->setPrimaryKey(['id']);
		}

        if (!$schema->hasTable('checkup_interval')) {
			$table = $schema->createTable('checkup_interval');
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
			$table->addColumn('interval', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
            $table->addIndex(['user_id'], 'checkup_interval_uid');
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