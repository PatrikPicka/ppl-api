<?php

declare(strict_types=1);

namespace PTB\PPLApi\Shipment\Request;

use JsonSerializable;
use PTB\PPLApi\Shipment\Request\Setting\CODSetting;
use PTB\PPLApi\Shipment\Request\Setting\LabelSetting;
use PTB\PPLApi\Shipment\Request\Setting\PickupPointSetting;
use PTB\PPLApi\Shipment\Request\Setting\RecipientSetting;
use PTB\PPLApi\Shipment\Request\Setting\SenderSetting;
use PTB\PPLApi\Shipment\Request\Setting\ShipmentSetting;

class CreateShipmentRequest implements JsonSerializable
{
	public function __construct(
		public LabelSetting $labelSetting,
		public ShipmentSetting $shipmentSetting,
		public SenderSetting $senderSetting,
		public RecipientSetting $recipientSetting,
		public PickupPointSetting $pickupPointSetting,
		public ?CODSetting $codSetting = null,
	) {
	}

	public function jsonSerialize(): array
	{
		$data = [
			'labelSettings' => $this->labelSetting->jsonSerialize(),
			'shipments' => [
				'referenceId' => $this->shipmentSetting->referenceId,
				'productType' => $this->shipmentSetting->shipmentType,
				'shipmentSet' => [
					'numberOfShipments' => 1,
					'shipmentSetItems' => [
						[
							'weighedShipmentInfo' => [
								'weight' => $this->shipmentSetting->shipmentWeight,
							],
						],
					],
				],
				'sender' => $this->senderSetting->jsonSerialize(),
				'recipient' => $this->recipientSetting->jsonSerialize(),
				'pickupPoint' => $this->pickupPointSetting->jsonSerialize(),
			],
		];

		if ($this->codSetting !== null) {
			$data['shipments']['cashOnDelivery'] = $this->codSetting->jsonSerialize();
		}

		return $data;
	}
}