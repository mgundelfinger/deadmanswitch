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

class UserSettingsMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'user_settings', UserSettings::class);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @return UserSettings
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function getSettingsOfUser(string $userId): UserSettings {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);

		try {
			return $this->findEntity($qb);
		} catch(DoesNotExistException $e) {
			return new UserSettings();
		}
	}

}
