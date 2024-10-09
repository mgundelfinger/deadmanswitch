<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
declare(strict_types=1);

namespace OCA\DeadManSwitch\Db;

use OCP\AppFramework\Db\Entity;

class JobsGroupMap extends Entity implements \JsonSerializable {

    /** @var int */
	protected $jobId;
	/** @var int */
	protected $jobsGroupId;

	public function __construct() {
		$this->addType('job_id', 'integer');
		$this->addType('jobs_group_id', 'integer');
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize(): mixed {
		return [
			'id' => $this->id,
			'job_id' => $this->jobId,
			'jobs_group_id' => $this->jobsGroupId,
		];
	}
}