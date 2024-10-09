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

	/**
	 * @param int $id
	 * @param string $userId
	 * @return Trigger|null
	 * @throws Exception
	 */
	public function deleteTrigger(int $id): ?Trigger {
		try {
			$trigger = $this->getTrigger($id);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}

		return $this->delete($trigger);
	}
}