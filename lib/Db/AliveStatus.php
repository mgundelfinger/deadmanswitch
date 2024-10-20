<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
declare(strict_types=1);

namespace OCA\DeadManSwitch\Db;

use OCA\DeadManSwitch\Trait\ValidationTrait;
use OCP\AppFramework\Db\Entity;

/**
 * @method string getName()
 * @method void setName(string $name)
 */
class AliveStatus extends Entity implements \JsonSerializable {

	use ValidationTrait;

	/** @var string */
	protected $name;

	public function __construct() {
		$this->addType('name', 'string');
	}

	public function rules() {
		return [
			'name' => [
				'min' => 3,
				'max' => 64
			],
		];
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize(): mixed {
		return [
			'id' => $this->id,
			'name' => $this->name,
		];
	}
}
