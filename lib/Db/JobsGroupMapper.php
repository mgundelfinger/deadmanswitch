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

class JobsGroupMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'jobs_group', JobsGroup::class);
	}

	/**
	 * @param int $id
	 * @return JobsGroup
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 */
	public function getJobsGroup(int $id): JobsGroup {
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
	 * @return JobsGroup
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function getJobsGroupOfUser(int $id, string $userId): JobsGroup {
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
	 * @return JobsGroup[]
	 * @throws Exception
	 */
	public function getJobsGroupsOfUser(string $userId): array {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);

		return $this->findEntities($qb);
	}

	/**
	 * @param string $userId
	 * @param string|null $name
	 * @return JobsGroup
	 * @throws Exception
	 */
	public function createJobsGroup(string $userId, string $name = null): JobsGroup {
		$jobsGroup = new JobsGroup();
		$jobsGroup->setUserId($userId);
		$jobsGroup->setName($name);
		return $this->insert($jobsGroup);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @param string $name
	 * @return JobsGroup|null
	 * @throws Exception
	 */
	public function updateJobsGroup(int $id, string $userId, string $name): ?JobsGroup {
		try {
			$jobsGroup = $this->getJobsGroupOfUser($id, $userId);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}
		$jobsGroup->setName($name);
		return $this->update($jobsGroup);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @return JobsGroup|null
	 * @throws Exception
	 */
	public function deleteJobsGroup(int $id, string $userId): ?JobsGroup {
		try {
			$jobsGroup = $this->getJobsGroupOfUser($id, $userId);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}
		return $this->delete($jobsGroup);
	}

	/**
	 * @param string $userId
	 * @return void
	 * @throws Exception
	 */
	public function deleteJobsGroupsOfUser(string $userId): void {
		$qb = $this->db->getQueryBuilder();

		$qb->delete($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);
		$qb->executeStatement();
		$qb->resetQueryParts();
	}
}