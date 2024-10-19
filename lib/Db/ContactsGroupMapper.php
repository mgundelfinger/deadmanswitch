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

class ContactsGroupMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'contacts_group', ContactsGroup::class);
	}

	/**
	 * @param string $userId
	 * @return Job[]
	 * @throws Exception
	 */
	public function getContactGroupsOfUser(string $userId, $limit = 10, $offset = 0): array {
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

	public function getContactsGroupsOfUserTotal(string $userId): int {
		$qb = $this->db->getQueryBuilder();

		$result = $qb->select($qb->func()->count('*', 'contact_groups_count'))
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			)
			->executeQuery();
		return $result->fetch()['contact_groups_count'];
	}


	/**
	 * @param int $id
	 * @param string $userId
	 * @return Job
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function getContactGroupOfUser(int $id, string $userId): ContactsGroup {
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
	 * @param int $id
	 * @param string $userId
	 * @return Job|null
	 * @throws Exception
	 */
	public function deleteContactGroup(int $id, string $userId): ?ContactsGroup {
		try {
			$job = $this->getContactGroupOfUser($id, $userId);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}

		return $this->delete($job);
	}

}
