<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
declare(strict_types=1);

namespace OCA\DeadManSwitch\Db;

use OCP\AppFramework\Db\Entity;

class ConfirmatorsGroupMap extends Entity implements \JsonSerializable {

    /** @var int */
	protected $confirmatorId;
	/** @var int */
	protected $confirmatorsGroupId;

	public function __construct() {
		$this->addType('confirmator_id', 'integer');
		$this->addType('confirmators_group_id', 'integer');
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize(): mixed {
		return [
			'id' => $this->id,
			'confirmator_id' => $this->confirmatorId,
			'confirmators_group_id' => $this->confirmatorsGroupId,
		];
	}
}