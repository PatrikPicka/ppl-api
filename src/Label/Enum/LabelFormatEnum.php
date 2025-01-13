<?php

declare(strict_types=1);

namespace PTB\PPLApi\Label\Enum;

enum LabelFormatEnum: string
{
	case ZPI = 'Zpi';
	case PDF = 'Pdf';
	case JPEG = 'Jpeg';
	case PNG = 'Png';
	case SVG = 'Svg';
}
