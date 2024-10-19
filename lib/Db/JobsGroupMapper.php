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
	 * @param string $userId
	 * @return Job[]
	 * @throws Exception
	 */
	public function getJobGroupsOfUser(string $userId, $limit = 10, $offset = 0): array {
		$qb = $this->db->getQueryBuilder();

		$qb
			->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			)
			->setFirstResult($offset)
			->setMaxResults($limit)
		;

		return $this->findEntities($qb);
	}

	public function getJobsGroupsOfUserTotal(string $userId): int {
		$qb = $this->db->getQueryBuilder();

		$result = $qb->select($qb->func()->count('*', 'job_groups_count'))
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			)
			->executeQuery();
		return $result->fetch()['job_groups_count'];
	}


	/**
	 * @param int $id
	 * @param string $userId
	 * @return Job
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function getJobGroupOfUser(int $id, string $userId): JobsGroup {
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

	public function getList(string $userId) : array {
		$qb = $this->db->getQueryBuilder();

		$qb
			->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			)
		;

		$list = [];
		$entities = $this->findEntities($qb);
		foreach($entities as $entity) {
			$list[$entity->getId()] = $entity->getName();
		}

		return $list;
	}

	public function getGroups(string $userId, array $groupsIds) {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId))
			)
			->andWhere(
				$qb->expr()->in('id', $qb->createNamedParameter($groupsIds, IQueryBuilder::PARAM_INT_ARRAY))
			)
		;

		return $this->findEntities($qb);
	}

	/**
	 * @param Job $job
	 * @param JobsGroup[] $groups
	 * @return void
	 */
	public function updateGroups(Job $job, array $groups) {
		$this->db->executeStatement("DELETE FROM `oc_jobs_group_map` WHERE `job_id` = :jobId", ['jobId' => $job->getId()]);

		foreach($groups as $group) {
			$this->db->executeStatement(
				"INSERT INTO `oc_jobs_group_map` (`job_id`, `jobs_group_id`) VALUES (:jobId, :groupId)", [
					'jobId' => $job->getId(), 'groupId' => $group->getId()
				]
			);
		}
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @return Job|null
	 * @throws Exception
	 */
	public function deleteJobGroup(int $id, string $userId): ?JobsGroup {
		try {
			$job = $this->getJobGroupOfUser($id, $userId);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}

		return $this->delete($job);
	}

}
