<?php

declare(strict_types=1);

namespace OCA\DeadManSwitch\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method string|null getUserId()
 * @method void setUserId(?string $userId)
 * @method string getName()
 * @method void setName(string $name)
 * @method string getContent()
 * @method void setContent(string $content)
 * @method int getLastModified()
 * @method void setLastModified(int $lastModified)
 */
class Trigger extends Entity implements \JsonSerializable {

    /** @var string */
	protected $userId;
	/** @var string */
	protected $name;
	/** @var int */
	protected $delay;

	public function __construct() {
		$this->addType('user_id', 'string');
		$this->addType('name', 'string');
		$this->addType('delay', 'integer');
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return [
			'id' => $this->id,
			'user_id' => $this->userId,
			'name' => $this->name,
			'email_subject' => $this->delay,
		];
	}
}