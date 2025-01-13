<?php

declare(strict_types=1);

namespace PTB\PPLApi\Shipment\Request\Setting;

use JsonSerializable;

class SenderSetting implements JsonSerializable
{
	public function __construct(
		public string $name,
		public ?string $name2,
		public string $street,
		public string $city,
		public string $zipCode,
		public string $country,
		public string $phone,
		public string $email,
	) {
	}

	public function jsonSerialize(): array
	{
		return [
			'name' => $this->name,
			'name2' => $this->name2,
			'street' => $this->street,
			'city' => $this->city,
			'zipCode' => $this->zipCode,
			'country' => $this->country,
			'phone' => $this->phone,
			'email' => $this->email,
		];
	}
}