<?php

declare(strict_types=1);

namespace PTB\PPLApi\Shipment\Response;

use PTB\PPLApi\Common\ResponseInterface;
use PTB\PPLApi\Shipment\Enum\ShipmentStatusEnum;

class ShipmentResponse implements ResponseInterface
{
	public function __construct(
		private string $referenceId,
		private ?string $shipmentNumber,
		private ?string $labelUrl,
		private ShipmentStatusEnum $status,
	) {
	}

	public static function fromArrayResponse(array $data): self
	{
		return new self(
			$data['referenceId'],
			$data['shipmentNumber'] ?? null,
			$data['labelUrl'] ?? null,
			ShipmentStatusEnum::from($data['importState']),
		);
	}

	public function getReferenceId(): string
	{
		return $this->referenceId;
	}

	public function getShipmentNumber(): ?string
	{
		return $this->shipmentNumber;
	}

	public function getLabelUrl(): ?string
	{
		return $this->labelUrl;
	}

	public function getStatus(): ShipmentStatusEnum
	{
		return $this->status;
	}
}