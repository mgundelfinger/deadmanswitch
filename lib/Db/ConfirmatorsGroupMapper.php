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

class ConfirmatorsGroupMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'confirmators_group', ConfirmatorsGroup::class);
	}

	/**
	 * @param string $userId
	 * @return Confirmator[]
	 * @throws Exception
	 */
	public function getConfirmatorGroupsOfUser(string $userId, $limit = 10, $offset = 0): array {
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

	public function getConfirmatorsGroupsOfUserTotal(string $userId): int {
		$qb = $this->db->getQueryBuilder();

		$result = $qb->select($qb->func()->count('*', 'confirmator_groups_count'))
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			)
			->executeQuery();
		return $result->fetch()['confirmator_groups_count'];
	}


	/**
	 * @param int $id
	 * @param string $userId
	 * @return Confirmator
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function getConfirmatorGroupOfUser(int $id, string $userId): ConfirmatorsGroup {
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
	 * @param Confirmator $confirmator
	 * @param ConfirmatorsGroup[] $groups
	 * @return void
	 */
	public function updateGroups(Confirmator $confirmator, array $groups) {
		$this->db->executeStatement("DELETE FROM `oc_confirmators_group_map` WHERE `confirmator_id` = :confirmatorId", ['confirmatorId' => $confirmator->getId()]);

		foreach($groups as $group) {
			$this->db->executeStatement(
				"INSERT INTO `oc_confirmators_group_map` (`confirmator_id`, `confirmators_group_id`) VALUES (:confirmatorId, :groupId)", [
					'confirmatorId' => $confirmator->getId(), 'groupId' => $group->getId()
				]
			);
		}
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @return Confirmator|null
	 * @throws Exception
	 */
	public function deleteConfirmatorGroup(int $id, string $userId): ?ConfirmatorsGroup {
		try {
			$confirmator = $this->getConfirmatorGroupOfUser($id, $userId);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}

		return $this->delete($confirmator);
	}

}
