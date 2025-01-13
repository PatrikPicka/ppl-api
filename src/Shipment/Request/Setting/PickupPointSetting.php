<?php

declare(strict_types=1);

namespace PTB\PPLApi\Shipment\Request\Setting;

use JsonSerializable;

class PickupPointSetting implements JsonSerializable
{
	public function __construct(
		public string $parcelShopCode,
	) {
	}

	public function jsonSerialize(): array
	{
		return [
			'parcelShopCode' => $this->parcelShopCode,
		];
	}
}