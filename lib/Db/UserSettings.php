<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
declare(strict_types=1);

namespace OCA\DeadManSwitch\Db;

use OCA\DeadManSwitch\Trait\ValidationTrait;
use OCP\AppFramework\Db\Entity;

/**
 * @method string|null getUserId()
 * @method void setUserId(?string $userId)
 * @method string getColor()
 * @method void setColor(string $color)
 * @method string getLocale()
 * @method void setLocale(string $locale)
 */
class UserSettings extends Entity implements \JsonSerializable {

	use ValidationTrait;

    /** @var string */
	protected $userId;

	/** @var string */
	protected $color;

	/** @var string */
	protected $locale;


	public function __construct() {
		$this->addType('user_id', 'string');
		$this->addType('color', 'string');
		$this->addType('locale', 'string');
	}

	public function rules() {
		return [
		];
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize(): mixed {
		return [
			'id' => $this->id,
			'user_id' => $this->userId,
			'color' => $this->color,
			'locale' => $this->locale,
		];
	}
}
