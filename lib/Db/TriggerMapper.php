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

class TriggerMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'trigger', Trigger::class);
	}

	/**
	 * @param int $id
	 * @return Trigger
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 */
	public function getTrigger(int $id): Trigger {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity($qb);
	}

	/**
	 * @param string $userId
	 * @return Trigger[]
	 * @throws Exception
	 */
	public function getTriggersOfUser(string $userId, $limit = 10, $offset = 0): array {
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

	public function getTriggersOfUserTotal(string $userId): int {
		$qb = $this->db->getQueryBuilder();

		$result = $qb->select($qb->func()->count('*', 'triggers_count'))
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			)
			->executeQuery();
		return $result->fetch()['triggers_count'];
	}

	public function getTriggerOfUser(int $id, string $userId): Trigger {
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
	 * @param string $name
	 * @param int $delay
	 * @return Trigger
	 * @throws Exception
	 */
	public function createTrigger(string $userId, string $name, int $delay): Trigger {
		$trigger = new Trigger();
		$trigger->setUserId($userId);
		$trigger->setName($name);
		$trigger->setEmailSubject($delay);
		return $this->insert($trigger);
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

	/**
	 * @param int $id
	 * @param string $userId
	 * @return Trigger|null
	 * @throws Exception
	 */
	public function deleteTrigger(int $id, string $userId): ?Trigger {
		try {
			$trigger = $this->getTriggerOfUser($id, $userId);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}

		return $this->delete($trigger);
	}
}
