<?php

declare(strict_types=1);

namespace PTB\PPLApi\Shipment\Request\Setting;

use JsonSerializable;

class CODSetting implements JsonSerializable
{
	public function __construct(
		public float $codPrice,
		public string $codCurrency,
	) {
	}

	public function jsonSerialize(): array
	{
		return [
			'codPrice' => $this->codPrice,
			'codCurrency' => $this->codCurrency,
		];
	}
}