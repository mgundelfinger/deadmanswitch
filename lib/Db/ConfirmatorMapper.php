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

class ConfirmatorMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'confirmator', Confirmator::class);
	}

	/**
	 * @param int $id
	 * @return Confirmator
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 */
	public function getConfirmator(int $id): Confirmator {
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
	 * @return Confirmator
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function getConfirmatorOfUser(int $id, string $userId): Confirmator {
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
	 * @return Confirmator[]
	 * @throws Exception
	 */
	public function getConfirmatorsOfUser(string $userId, $limit = 10, $offset = 0): array {
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

	public function getConfirmatorsOfUserTotal(string $userId): int {
		$qb = $this->db->getQueryBuilder();

		$result = $qb->select($qb->func()->count('*', 'confirmators_count'))
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			)
			->executeQuery();
		return $result->fetch()['confirmators_count'];
	}

	/**
	 * @param string $userId
	 * @param string $name
	 * @param string $emailSubject
     * @param string $emailBody
	 * @return Confirmator
	 * @throws Exception
	 */
	public function createConfirmator(string $userId, string $name, string $emailSubject, string $emailBody): Confirmator {
		$confirmator = new Confirmator();
		$confirmator->setUserId($userId);
		$confirmator->setName($name);
		$confirmator->setEmailSubject($emailSubject);
        $confirmator->setEmailBody($emailBody);
		return $this->insert($confirmator);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @param string|null $name
	 * @param string|null $emailSubject
     * @param string|null $emailBody
	 * @return Confirmator|null
	 * @throws Exception
	 */
	public function updateConfirmator(int $id, string $userId, ?string $name = null, ?string $emailSubject = null, ?string $emailBody = null): ?Confirmator {
		if ($name === null && $emailSubject === null && $emailBody === null) {
			return null;
		}
		try {
			$confirmator = $this->getConfirmatorOfUser($id, $userId);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}
		if ($name !== null) {
			$confirmator->setName($name);
		}
		if ($emailSubject !== null) {
			$confirmator->setEmailSubject($emailSubject);
		}
        if ($emailBody !== null) {
			$confirmator->setEmailBody($emailBody);
		}
		return $this->update($confirmator);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @return Confirmator|null
	 * @throws Exception
	 */
	public function deleteConfirmator(int $id, string $userId): ?Confirmator {
		try {
			$confirmator = $this->getConfirmatorOfUser($id, $userId);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}

		return $this->delete($confirmator);
	}

	public function getGroups($confirmator) {
		$ids = [];
		$data = $this->db->executeQuery(
			"SELECT `confirmators_group_id` FROM `oc_confirmators_group_map` WHERE `confirmator_id` = :confirmatorId", ['confirmatorId' => $confirmator->getId()]
		)->fetchAll();
		foreach($data as $d) {
			$ids[] = $d['confirmators_group_id'];
		}
		return $ids;
	}

	/**
	 * @param string $userId
	 * @return void
	 * @throws Exception
	 */
	public function deleteConfirmatorsOfUser(string $userId): void {
		$qb = $this->db->getQueryBuilder();

		$qb->delete($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);
		$qb->executeStatement();
		$qb->resetQueryParts();
	}
}
