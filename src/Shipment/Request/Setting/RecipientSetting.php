<?php

declare(strict_types=1);

namespace PTB\PPLApi\Shipment\Request\Setting;

use JsonSerializable;

class RecipientSetting implements JsonSerializable
{
	public function __construct(
		public string $name,
		public string $street,
		public string $city,
		public string $zip,
		public string $country,
		public string $phone,
		public string $email,
	) {
	}

	public function jsonSerialize(): array
	{
		return [
			'name' => $this->name,
			'street' => $this->street,
			'city' => $this->city,
			'zipCode' => $this->zip,
			'country' => $this->country,
			'phone' => $this->phone,
			'email' => $this->email,
		];
	}
}