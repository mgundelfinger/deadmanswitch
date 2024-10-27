<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
declare(strict_types=1);

namespace OCA\DeadManSwitch\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use Random\RandomException;

class ResetTokenMapper extends QBMapper {

	const STATUS_ALIVE = 0;
	const STATUS_PENDING = 1;
	const STATUS_DEAD = 2;

	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'reset_token', ResetToken::class);
	}

	/**
	 * @param int $aliveStatusId
	 * @return ResetToken[]
	 */
	public function getResetTokensOfAliveStatus(int $aliveStatusId): array {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('alive_status_id', $qb->createNamedParameter($aliveStatusId, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntities($qb);
	}

    /**
	 * @param string $token
	 * @return ResetToken|null
	 */
	public function getResetTokenByToken(string $token): ?ResetToken {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('token', $qb->createNamedParameter($token, IQueryBuilder::PARAM_STR))
			);

        try {
            return $this->findEntity($qb);
        } catch (MultipleObjectsReturnedException | DoesNotExistException) {
            return null;
        }
	}

	/**
	 * @param int $contactId
     * @param int $aliveStatusId
	 * @return ResetToken
	 * @throws Exception
	 */
	public function createResetToken(int $contactId, int $aliveStatusId): ResetToken {
		$resetToken = new ResetToken();
		$resetToken->setContactId($contactId);
		$resetToken->setAliveStatusId($aliveStatusId);
        $resetToken->setToken($this->generateToken());
        
		return $this->insert($resetToken);
	}

    /**
     * @param int $length
     * @return string
     * @throws RandomException
     */
    private function generateToken(int $length = 32) {
        $token = random_bytes($length);
        return bin2hex($token);
    }

    /**
	 * @param int $userId
	 * @throws Exception
	 */
	public function deleteResetTokensOfAliveStatus(int $aliveStatusId) {
		$resetTokens = $this->getResetTokensOfAliveStatus($aliveStatusId);

        foreach ($resetTokens as $rt) {
            $this->delete($rt);
        }
	}

}
