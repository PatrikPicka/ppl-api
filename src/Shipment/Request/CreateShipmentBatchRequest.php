<?php

declare(strict_types=1);

namespace PTB\PPLApi\Shipment\Request;

use JsonSerializable;
use PTB\PPLApi\Shipment\Request\Data\ShipmentData;
use PTB\PPLApi\Shipment\Request\Setting\LabelSetting;

class CreateShipmentBatchRequest implements JsonSerializable
{
	public function __construct(
		public LabelSetting $labelSetting,
		/** @var array<ShipmentData> $shipments */
		public array $shipments,
	) {
	}

	public function jsonSerialize(): array
	{
		return [
			'labelSettings' => $this->labelSetting->jsonSerialize(),
			'shipments' => array_map(fn (ShipmentData $shipmentData) => $shipmentData->jsonSerialize(), $this->shipments),
		];
	}
}