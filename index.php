<?php

declare(strict_types=1);

use GuzzleHttp\Exception\ClientException;
use PTB\PPLApi\Label\Enum\LabelFormatEnum;
use PTB\PPLApi\Label\Enum\LabelPageSizeEnum;
use PTB\PPLApi\PPLApi;
use PTB\PPLApi\Shipment\Enum\ShipmentTypeEnum;
use PTB\PPLApi\Shipment\Request\CreateShipmentRequest;
use PTB\PPLApi\Shipment\Request\Data\ShipmentData;
use PTB\PPLApi\Shipment\Request\Setting\CODSetting;
use PTB\PPLApi\Shipment\Request\Setting\LabelSetting;
use PTB\PPLApi\Shipment\Request\Setting\PickupPointSetting;
use PTB\PPLApi\Shipment\Request\Setting\RecipientSetting;
use PTB\PPLApi\Shipment\Request\Setting\SenderSetting;
use PTB\PPLApi\Shipment\Request\Setting\ShipmentSetting;

require __DIR__ . '/vendor/autoload.php';

function saveToken(string $token, string $path = './token.json'): void
{
	$data = ['token' => $token];

	$json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	if ($json === false) {
		throw new RuntimeException('Error while encoding JSON.');
	}

	if (file_put_contents($path, $json) === false) {
		throw new RuntimeException('There was an saving the token into the file.');
	}

	echo "Token successfully saved into file: '{$path}'.\n";
}

function getTokenFromFile(string $path = './token.json'): ?string
{
	if (!file_exists($path)) {
		throw new RuntimeException("File '{$path}' does not exist.");
	}

	$obsah = file_get_contents($path);
	if ($obsah === false) {
		throw new RuntimeException("Error while loading file '{$path}'.");
	}

	$data = json_decode($obsah, true);
	if ($data === null) {
		throw new RuntimeException("Invalid JSON format in file '{$path}'.");
	}

	if (array_key_exists('token', $data) === false) {
		throw new RuntimeException("File '{$path}' does not contains token.");
	}

	return $data['token'];
}

$clientId = 'your_client_id';
$clientSecret = 'your_client_secret';
$apiUrl = 'https://api-dev.dhl.com/ecs/ppl/myapi2';
$scope = 'your_scope';

$api = new PPLApi(
	$clientId,
	$clientSecret,
	$apiUrl,
	$scope,
	getTokenFromFile(),
);

if ($api->isTokenValid() === false) {
	echo 'Token is invalid. Refreshing and saving.';
	saveToken($api->refreshAndGetToken());
}

$createShipmentRequest = new CreateShipmentRequest(
	new LabelSetting(
		LabelFormatEnum::PDF,
		LabelPageSizeEnum::A4,
		0,
	),
	new ShipmentData (
		new ShipmentSetting(
			'012500000001',
			ShipmentTypeEnum::SMART_WITH_COD,
			3.5,
		),
		new SenderSetting(
			'Company s.r.o.',
			'PTB',
			'Random 123',
			'City',
			'11122',
			'CZ',
			'+420123456789',
			'info@example.com',
		),
		new RecipientSetting(
			'FirstName LastName',
			'Random 123',
			'City',
			'11122',
			'CZ',
			'+420987654321',
			'recipient@example.com',
		),
		new PickupPointSetting(
			'KM12345678', //Change for your parcel shop code
		),
		new CODSetting(
			250.00,
			'CZK',
			'1',
		),
	),
);

echo "<pre>";
try {
	print_r($api->createShipment($createShipmentRequest));
} catch (ClientException $e) {
	$responseBody = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : 'No Response';
	throw new \RuntimeException("API Error: {$e->getMessage()} \nResponse: {$responseBody}");
}
echo "</pre>";

//try {
//	$shipmentId = 'shipment-id';
//	$shipment = $api->getShipment($shipmentId);
//
//	header('Content-type: application/pdf');
//
//	echo $api->getLabelPdf($shipment->getLabelUrl())->getContent();
//	die();
//} catch (\GuzzleHttp\Exception\ClientException $e) {
//	$responseBody = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : 'Žádná odpověď';
//	throw new \RuntimeException("API chyba: {$e->getMessage()} \nOdpověď: {$responseBody}");
//}
