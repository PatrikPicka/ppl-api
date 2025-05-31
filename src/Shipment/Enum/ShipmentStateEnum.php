<?php

declare(strict_types=1);

namespace PTB\PPLApi\Shipment\Enum;

enum ShipmentStateEnum: string
{
	case ACTIVE = 'Active';
	case PICKED_UP_FROM_SENDER = 'PickedUpFromSender';
    case TAKEN_OVER_FROM_SENDER = 'ShipmentInTransport.TakeOverFromSender';
	case DELIVERED_TO_PICKUP_POINT = 'DeliveredToPickupPoint';
	case DELIVERED = 'Delivered';
    case DELIVERED_PARCEL_SHOP = 'Delivered.Parcelshop';
    case BACK_TO_SENDER = 'BackToSender';
	case CANCELED = 'Canceled';
	case REJECTED = 'Rejected';
	case NOT_DELIVERED = 'NotDelivered';
	case OUT_FOR_DELIVERY = 'OutForDelivery';
}
