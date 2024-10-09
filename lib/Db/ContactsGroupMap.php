<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
declare(strict_types=1);

namespace OCA\DeadManSwitch\Db;

use OCP\AppFramework\Db\Entity;

class ContactsGroupMap extends Entity implements \JsonSerializable {

    /** @var int */
	protected $contactId;
	/** @var int */
	protected $contactsGroupId;

	public function __construct() {
		$this->addType('contact_id', 'integer');
		$this->addType('contacts_group_id', 'integer');
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize(): mixed {
		return [
			'id' => $this->id,
			'contact_id' => $this->contactId,
			'contacts_group_id' => $this->contactsGroupId,
		];
	}
}