<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
declare(strict_types=1);

namespace OCA\DeadManSwitch\Db;

use DateTime;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

use OCP\AppFramework\Db\DoesNotExistException;

class ContactMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'contact', Contact::class);
	}

	/**
	 * @param int $id
	 * @return Contact
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 */
	public function getContact(int $id): Contact {
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
	 * @return Contact
	 * @throws DoesNotExistException
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException
	 */
	public function getContactOfUser(int $id, string $userId): Contact {
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
	public function getContactsOfUser(string $userId, $limit = 10, $offset = 0): array {
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

	public function getContactsOfUserTotal(string $userId): int {
		$qb = $this->db->getQueryBuilder();

		$result = $qb->select($qb->func()->count('*', 'contacts_count'))
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			)
			->executeQuery();
		return $result->fetch()['contacts_count'];
	}


	/**
	 * @param string $userId
	 * @param string $firstName
	 * @param string $lastName
     * @param string $email
	 * @return Contact
	 * @throws Exception
	 */
	public function createContact(string $userId, string $firstName, string $lastName, string $email): Contact {
		$contact = new Contact();
		$contact->setUserId($userId);
		$contact->setFirstName($firstName);
		$contact->setLastName($lastName);
        $contact->setEmail($email);
		return $this->insert($contact);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @param string|null $firstName
	 * @param string|null $lastName
     * @param string|null $email
	 * @return Contact|null
	 * @throws Exception
	 */
	public function updateContact(int $id, string $userId, ?string $firstName = null, ?string $lastName = null, ?string $email = null): ?Contact {
		if ($firstName === null && $lastName === null && $email === null) {
			return null;
		}
		try {
			$contact = $this->getContactOfUser($id, $userId);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}
		if ($firstName !== null) {
			$contact->setFirstName($firstName);
		}
		if ($lastName !== null) {
			$contact->setLastName($lastName);
		}
        if ($email !== null) {
			$contact->setEmail($email);
		}
		return $this->update($contact);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @return Contact|null
	 * @throws Exception
	 */
	public function deleteContact(int $id, string $userId): ?Contact {
		try {
			$contact = $this->getContactOfUser($id, $userId);
		} catch (DoesNotExistException | MultipleObjectsReturnedException $e) {
			return null;
		}

		return $this->delete($contact);
	}

	/**
	 * @param string $userId
	 * @return void
	 * @throws Exception
	 */
	public function deleteContactsOfUser(string $userId): void {
		$qb = $this->db->getQueryBuilder();

		$qb->delete($this->getTableName())
			->where(
				$qb->expr()->eq('user_id', $qb->createNamedParameter($userId, IQueryBuilder::PARAM_STR))
			);
		$qb->executeStatement();
		$qb->resetQueryParts();
	}
}
