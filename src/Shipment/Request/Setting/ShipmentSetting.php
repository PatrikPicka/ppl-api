<?php

declare(strict_types=1);

namespace PTB\PPLApi\Shipment\Request\Setting;

use JsonSerializable;
use PTB\PPLApi\Shipment\Enum\ShipmentTypeEnum;

class ShipmentSetting implements JsonSerializable
{
	public function __construct(
		public string $referenceId,
		public ShipmentTypeEnum $shipmentType,
		public float $shipmentWeight,
	) {
	}

	public function jsonSerialize(): array
	{
		return [
			'referenceId' => $this->referenceId,
			'productType' => $this->shipmentType->value,
			'shipmentSet' => [
				'numberOfShipments' => 1,
				'shipmentSetItems' => [
					[
						'weighedShipmentInfo' => [
							'weight' => $this->shipmentWeight,
						],
					],
				],
			],
		];
	}
}