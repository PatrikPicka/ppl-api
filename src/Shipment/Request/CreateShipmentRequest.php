<?php

declare(strict_types=1);

namespace PTB\PPLApi\Shipment\Request;

use JsonSerializable;
use PTB\PPLApi\Shipment\Request\Data\ShipmentData;
use PTB\PPLApi\Shipment\Request\Setting\LabelSetting;

class CreateShipmentRequest implements JsonSerializable
{
	public function __construct(
		public LabelSetting $labelSetting,
		public ShipmentData $shipmentData,
	) {
	}

	public function jsonSerialize(): array
	{
		return [
			'labelSettings' => $this->labelSetting->jsonSerialize(),
			'shipments' => [
				$this->shipmentData->jsonSerialize()
			],
		];
	}
}