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

class JobMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'job', Job::class);
	}

	/**
	 * @param int $id
	 * @return Job
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 */
	public function getJob(int $id): Job {
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
	 * @return Job
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function getJobOfUser(int $id, string $userId): Job {
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
	 * @return Job[]
	 * @throws Exception
	 */
	public function getJobsOfUser(string $userId, $limit = 10, $offset = 0): array {
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

	public function getJobsOfUserTotal(string $userId): int {
		$qb = $this->db->getQueryBuilder();

		$result = $qb->select($qb->func()->count('*', 'jobs_count'))
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			)
			->executeQuery();
		return $result->fetch()['jobs_count'];
	}

	/**
	 * @param string $userId
	 * @param string $name
	 * @param string $emailSubject
     * @param string $emailBody
	 * @return Job
	 * @throws Exception
	 */
	public function createJob(string $userId, string $name, string $emailSubject, string $emailBody): Job {
		$job = new Job();
		$job->setUserId($userId);
		$job->setName($name);
		$job->setEmailSubject($emailSubject);
        $job->setEmailBody($emailBody);
		return $this->insert($job);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @param string|null $name
	 * @param string|null $emailSubject
     * @param string|null $emailBody
	 * @return Job|null
	 * @throws Exception
	 */
	public function updateJob(int $id, string $userId, ?string $name = null, ?string $emailSubject = null, ?string $emailBody = null): ?Job {
		if ($name === null && $emailSubject === null && $emailBody === null) {
			return null;
		}
		try {
			$job = $this->getJobOfUser($id, $userId);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}
		if ($name !== null) {
			$job->setName($name);
		}
		if ($emailSubject !== null) {
			$job->setEmailSubject($emailSubject);
		}
        if ($emailBody !== null) {
			$job->setEmailBody($emailBody);
		}
		return $this->update($job);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @return Job|null
	 * @throws Exception
	 */
	public function deleteJob(int $id, string $userId): ?Job {
		try {
			$job = $this->getJobOfUser($id, $userId);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}

		return $this->delete($job);
	}

	public function getGroups($job) {
		$ids = [];
		$data = $this->db->executeQuery(
			"SELECT `jobs_group_id` FROM `oc_jobs_group_map` WHERE `job_id` = :jobId", ['jobId' => $job->getId()]
		)->fetchAll();
		foreach($data as $d) {
			$ids[] = $d['jobs_group_id'];
		}
		return $ids;
	}

	/**
	 * @param string $userId
	 * @return void
	 * @throws Exception
	 */
	public function deleteJobsOfUser(string $userId): void {
		$qb = $this->db->getQueryBuilder();

		$qb->delete($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);
		$qb->executeStatement();
		$qb->resetQueryParts();
	}
}
