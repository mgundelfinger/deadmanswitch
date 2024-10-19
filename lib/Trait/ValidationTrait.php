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
					if(strlen($this->{$property}) < $param) {
						$errors[$property] = 'Minimal length is ' . $param;
					}
				} elseif($validator == 'max') {
					if(strlen($this->{$property}) > $param) {
						$errors[$property] = 'Maximal length is ' . $param;
					}
				}
			}
		}
		return $errors;
	}
}
