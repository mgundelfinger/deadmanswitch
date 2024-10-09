<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
declare(strict_types=1);

namespace OCA\DeadManSwitch\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method string|null getUserId()
 * @method void setUserId(?string $userId)
 * @method int getContactId()
 * @method void setContactId(int $contactId)
 * @method int getIntervalId()
 * @method void setIntervalId(int $intervalId)
 */
class Confirmator extends Entity implements \JsonSerializable {

    /** @var string */
	protected $userId;
	/** @var int */
	protected $contactId;
    /** @var int */
	protected $intervalId;

	public function __construct() {
		$this->addType('user_id', 'string');
		$this->addType('contact_id', 'integer');
        $this->addType('interval_id', 'integer');
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize(): mixed {
		return [
			'id' => $this->id,
			'user_id' => $this->userId,
			'contact_id' => $this->contactId,
            'interval_id' => $this->intervalId,
		];
	}
}