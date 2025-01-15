<?php

declare(strict_types=1);

namespace PTB\PPLApi\Shipment\Enum;

enum ShipmentStatusEnum: string
{
	case ACCEPTED = 'Accepted';
	case IN_PROCESS = 'InProcess';
	case COMPLETE = 'Complete';
	case ERROR = 'Error';
}
