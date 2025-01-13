<?php

declare(strict_types=1);

namespace PTB\PPLApi\Shipment\Request\Setting;

use JsonSerializable;
use PTB\PPLApi\Exception\PPLException;
use PTB\PPLApi\Label\Enum\LabelFormatEnum;
use PTB\PPLApi\Label\Enum\LabelPageSizeEnum;

class LabelSetting implements JsonSerializable
{
	public function __construct(
		public LabelFormatEnum $format,
		public LabelPageSizeEnum $pageSize,
		public int $offset = 0,
	) {
		if ($this->offset > $this->pageSize->getMaxOffset()) {
			throw new PPLException(sprintf(
				'The max offset for given label page size is %d',
				$this->pageSize->getMaxOffset(),
			));
		}
	}

	public function jsonSerialize(): array
	{
		return [
			'format' => $this->format->value,
			'completeLabelSettings' => [
				'isCompleteLabelRequested' => true,
				'pageSize' => $this->pageSize->value,
				'position' => $this->offset + 1,
			],
		];
	}
}