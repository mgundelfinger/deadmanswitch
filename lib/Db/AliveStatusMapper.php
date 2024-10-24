<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
declare(strict_types=1);

namespace OCA\DeadManSwitch\Db;

use DateTimeImmutable;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class AliveStatusMapper extends QBMapper {

	const STATUS_ALIVE = 0;
	const STATUS_PENDING = 1;
	const STATUS_DEAD = 2;

	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'alive_status', AliveStatus::class);
	}

	/**
	 * @param string $userId
	 * @return AliveStatus|null
	 */
	public function getAliveStatusOfUser(string $userId): ?AliveStatus {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);

		try {
			return $this->findEntity($qb);
		} catch (DoesNotExistException | MultipleObjectsReturnedException) {
			return null;
		}
	}

	/**
	 * @param string $userId
	 * @return AliveStatus
	 * @throws Exception
	 */
	public function createAliveStatus(string $userId, int $aliveDays = 14, int $pendingDays = 7): AliveStatus {
		$aliveStatus = new AliveStatus();
		$aliveStatus->setUserId($userId);
		$aliveStatus->setStatus(self::STATUS_ALIVE);
		$aliveStatus->setLastChangeAsDate(new DateTimeImmutable());
		$aliveStatus->setAliveDays($aliveDays);
		$aliveStatus->setPendingDays($pendingDays);
		return $this->insert($aliveStatus);
	}

	/**
	 * @param int $id
	 * @return AliveStatus|null
	 */
	public function getAliveStatus(int $id): ?AliveStatus {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		try {
			return $this->findEntity($qb);
		} catch (DoesNotExistException | MultipleObjectsReturnedException) {
			return null;
		}
		
	}

	/**
	 * @param int $id
	 * @param int $status
	 * @return AliveStatus|null
	 */
	public function updateAliveStatus(int $id, int $status): ?AliveStatus {
		if (!in_array($status, [self::STATUS_ALIVE, self::STATUS_PENDING, self::STATUS_DEAD])) {
			return null;
		}
		try {
			$aliveStatus = $this->getAliveStatus($id);
		} catch (DoesNotExistException | MultipleObjectsReturnedException) {
			return null;
		}
		$aliveStatus->setStatus($status);
		$aliveStatus->setLastChangeAsDate(new DateTimeImmutable());

		return $this->update($aliveStatus);
	}

}
