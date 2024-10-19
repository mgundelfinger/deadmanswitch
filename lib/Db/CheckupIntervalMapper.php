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

class CheckupIntervalMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'checkup_interval', CheckupInterval::class);
	}

	/**
	 * @param int $id
	 * @return CheckupInterval
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 */
	public function getCheckupInterval(int $id): CheckupInterval {
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
	 * @return CheckupInterval
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function getCheckupIntervalOfUser(int $id, string $userId): CheckupInterval {
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
	 * @return CheckupInterval[]
	 * @throws Exception
	 */
	public function getCheckupIntervalsOfUser(string $userId, $limit = 10, $offset = 0): array {
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

	public function getCheckupIntervalsOfUserTotal(string $userId): int {
		$qb = $this->db->getQueryBuilder();

		$result = $qb->select($qb->func()->count('*', 'checkup_intervals_count'))
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			)
			->executeQuery();
		return $result->fetch()['checkup_intervals_count'];
	}

	/**
	 * @param string $userId
	 * @param string $name
	 * @param string $emailSubject
     * @param string $emailBody
	 * @return CheckupInterval
	 * @throws Exception
	 */
	public function createCheckupInterval(string $userId, string $name, string $emailSubject, string $emailBody): CheckupInterval {
		$checkup_interval = new CheckupInterval();
		$checkup_interval->setUserId($userId);
		$checkup_interval->setName($name);
		$checkup_interval->setEmailSubject($emailSubject);
        $checkup_interval->setEmailBody($emailBody);
		return $this->insert($checkup_interval);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @param string|null $name
	 * @param string|null $emailSubject
     * @param string|null $emailBody
	 * @return CheckupInterval|null
	 * @throws Exception
	 */
	public function updateCheckupInterval(int $id, string $userId, ?string $name = null, ?string $emailSubject = null, ?string $emailBody = null): ?CheckupInterval {
		if ($name === null && $emailSubject === null && $emailBody === null) {
			return null;
		}
		try {
			$checkup_interval = $this->getCheckupIntervalOfUser($id, $userId);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}
		if ($name !== null) {
			$checkup_interval->setName($name);
		}
		if ($emailSubject !== null) {
			$checkup_interval->setEmailSubject($emailSubject);
		}
        if ($emailBody !== null) {
			$checkup_interval->setEmailBody($emailBody);
		}
		return $this->update($checkup_interval);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @return CheckupInterval|null
	 * @throws Exception
	 */
	public function deleteCheckupInterval(int $id, string $userId): ?CheckupInterval {
		try {
			$checkup_interval = $this->getCheckupIntervalOfUser($id, $userId);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}

		return $this->delete($checkup_interval);
	}

	/**
	 * @param string $userId
	 * @return void
	 * @throws Exception
	 */
	public function deleteCheckupIntervalsOfUser(string $userId): void {
		$qb = $this->db->getQueryBuilder();

		$qb->delete($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);
		$qb->executeStatement();
		$qb->resetQueryParts();
	}
}
