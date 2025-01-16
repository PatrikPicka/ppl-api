<?php

declare(strict_types=1);

namespace PTB\PPLApi\Shipment\Response;

use PTB\PPLApi\Common\ResponseInterface;

class ShipmentBatchResponse implements ResponseInterface
{
	public function __construct(
		private ?string $labelsUrl,
		/** @var ShipmentResponse[] $shipments */
		private array $shipments,
	) {
	}

	public static function fromArrayResponse(array $data): self
	{
		$labelsUrl = null;
		if (isset($data['completeLabel']['labelUrls']) === true) {
			$labelsUrl = $data['completeLabel']['labelUrls'][0];
		}

		$shipments = [];
		foreach ($data['items'] as $shipmentData) {
			$shipments[] = ShipmentResponse::fromArrayResponse($shipmentData);
		}

		return new self($labelsUrl, $shipments);
	}

	public function getLabelsUrl(): ?string
	{
		return $this->labelsUrl;
	}

	/**
	 * @return ShipmentResponse[]
	 */
	public function getShipments(): array
	{
		return $this->shipments;
	}
}