<?php

declare(strict_types=1);

namespace PTB\PPLApi\Shipment\Request\Data;

use JsonSerializable;
use PTB\PPLApi\Shipment\Request\Setting\CODSetting;
use PTB\PPLApi\Shipment\Request\Setting\PickupPointSetting;
use PTB\PPLApi\Shipment\Request\Setting\RecipientSetting;
use PTB\PPLApi\Shipment\Request\Setting\SenderSetting;
use PTB\PPLApi\Shipment\Request\Setting\ShipmentSetting;

class ShipmentData implements JsonSerializable
{
	public function __construct(
		public ShipmentSetting $shipmentSetting,
		public SenderSetting $senderSetting,
		public RecipientSetting $recipientSetting,
		public PickupPointSetting $pickupPointSetting,
		public ?CODSetting $codSetting = null,
	) {
	}

	public function jsonSerialize(): array
	{
		$shipmentData = $this->shipmentSetting->jsonSerialize();
		$shipmentData['sender'] = $this->senderSetting->jsonSerialize();
		$shipmentData['recipient'] = $this->recipientSetting->jsonSerialize();
		$shipmentData['specificDelivery'] = $this->pickupPointSetting->jsonSerialize();

		if ($this->codSetting !== null) {
			$shipmentData['cashOnDelivery'] = $this->codSetting->jsonSerialize();
		}

		return $shipmentData;
	}
}