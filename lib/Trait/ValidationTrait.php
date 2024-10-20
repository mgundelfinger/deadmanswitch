<?php
// SPDX-FileCopyrightText: Marlon Gundelfinger <marlonqgundelfinger@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
declare(strict_types=1);

namespace OCA\DeadManSwitch\Trait;

trait ValidationTrait
{
	public function loadData($data)
	{
		foreach($data as $name => $value) {
			$name = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));
			if(property_exists($this, $name)) {
				$method = 'set' . ucfirst($name);
				call_user_func_array([$this, $method], [$value]);
			}
		}
	}

	public function validate()
	{
		$errors = [];
		foreach($this->rules() as $property => $rules) {
			foreach($rules as $validator => $param) {
				if($validator == 'min') {
					if(strlen((string) $this->{$property}) < $param) {
						$errors[$property] = 'Minimal length is ' . $param;
					}
				} elseif($validator == 'max') {
					if(strlen((string) $this->{$property}) > $param) {
						$errors[$property] = 'Maximal length is ' . $param;
					}
				} elseif($validator == 'email') {
					if(!filter_var((string) $this->{$property}, FILTER_VALIDATE_EMAIL)) {
						$errors[$property] = 'Email is not valid';
					}
				}
			}
		}
		return $errors;
	}

	public function isModified(): bool {
		$updatedFields = $this->getUpdatedFields();
		if(!empty($updatedFields['id'])) {
			unset($updatedFields['id']);
		}
		return !empty($updatedFields);
	}
}
