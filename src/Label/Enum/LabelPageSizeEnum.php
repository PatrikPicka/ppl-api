<?php

declare(strict_types=1);

namespace PTB\PPLApi\Label\Enum;

enum LabelPageSizeEnum: string
{
	case A4 = 'A4';

	public function getMaxOffset(): int
	{
		return match ($this) {
			self::A4 => 3,
		};
	}
}
