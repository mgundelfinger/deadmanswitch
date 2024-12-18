<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
declare(strict_types=1);

namespace OCA\DeadManSwitch\Db;

use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

use OCP\AppFramework\Db\DoesNotExistException;

class TaskMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'task', Task::class);
	}

	/**
	 * @param int $id
	 * @return Task
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 */
	public function getTask(int $id): Task {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity($qb);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @return Task
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function getTaskOfUser(int $id, string $userId): Task {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			)
			->andWhere(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);

		return $this->findEntity($qb);
	}

	/**
	 * @param string $userId
	 * @return Task[]
	 * @throws Exception
	 */
	public function getActiveTasksOfUser(string $userId): array {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			)
			->andWhere(
				$qb->expr()->eq('active', $qb->createNamedParameter(True, IQueryBuilder::PARAM_BOOL))
			);

		return $this->findEntities($qb);
	}

	/**
	 * @param string $userId
	 * @return Task[]
	 * @throws Exception
	 */
	public function getTasksOfUser(string $userId): array {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);

		return $this->findEntities($qb);
	}

	/**
	 * @return Task[]
	 * @throws Exception
	 */
	public function getActiveTasks(): array {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('active', $qb->createNamedParameter(true, IQueryBuilder::PARAM_BOOL))
			);

		return $this->findEntities($qb);
	}

	/**
	 * @return String[]
	 */
	public function getUserIdsWithActiveTasks(): array {
		$qb = $this->db->getQueryBuilder();

		$result = $qb->select('user_id')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('active', $qb->createNamedParameter(true, IQueryBuilder::PARAM_BOOL))
			)
			->executeQuery()
			;

		return $result->fetchAll();
	}

	/**
	 * @param string $userId
	 * @param string $name
	 * @param int $contactsGroupId
     * @param int $jobsGroupId
     * @param int $deathDays
     * @param bool $active
	 * @return Task
	 * @throws Exception
	 */
	public function createTask(string $userId, string $name, int $contactsGroupId, int $jobsGroupId, int $deathDays, bool $active): Task {
		$task = new Task();
		$task->setUserId($userId);
		$task->setName($name);
        $task->setContactsGroupId($contactsGroupId);
        $task->setJobsGroupId($jobsGroupId);
        $task->setDeathDays($deathDays);
		$task->setActive($active);
		return $this->insert($task);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @return Task|null
	 * @throws Exception
	 */
	public function deleteTask(int $id, string $userId): ?Task {
		try {
			$task = $this->getTaskOfUser($id, $userId);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}

		return $this->delete($task);
	}

	/**
	 * @param string $userId
	 * @return void
	 * @throws Exception
	 */
	public function deleteTasksOfUser(string $userId): void {
		$qb = $this->db->getQueryBuilder();

		$qb->delete($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);
		$qb->executeStatement();
		$qb->resetQueryParts();
	}

	public function getTasksOfUserTotal(string $userId): int {
		$qb = $this->db->getQueryBuilder();

		$result = $qb->select($qb->func()->count('*', 'tasks_count'))
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			)
			->executeQuery();
		return $result->fetch()['tasks_count'];
	}

	/**
	 * @param int $id
	 * @param bool $active
	 * @return Task|null
	 */
	public function toggleActive(int $id, bool $active) {
		try {
			$task = $this->getTask($id);
		} catch (DoesNotExistException | MultipleObjectsReturnedException) {
			return null;
		}
		$task->setActive($active);

		return $this->update($task);
	}

}
