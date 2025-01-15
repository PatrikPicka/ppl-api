<?php

declare(strict_types=1);

namespace PTB\PPLApi;

use Exception;
use GuzzleHttp\Client;
use League\OAuth2\Client\Provider\GenericProvider;
use Psr\Http\Message\ResponseInterface;
use PTB\PPLApi\Exception\PPLException;
use PTB\PPLApi\Label\Response\PdfResponse;
use PTB\PPLApi\Shipment\Request\CreateShipmenRequest;
use PTB\PPLApi\Shipment\Request\CreateShipmentBatchRequest;
use PTB\PPLApi\Shipment\Response\ShipmentBatchResponse;
use PTB\PPLApi\Shipment\Response\ShipmentResponse;

class PPLApi
{
	private Client $httpClient;
	private GenericProvider $oauthProvider;
	private string $accessToken;
	private string $apiUrl;

	public function __construct(
		string $clientId,
		string $clientSecret,
		string $accessTokenUrl,
		string $apiUrl = 'https://api-dev.dhl.com/ecs/ppl/myapi2',
		string $scope = 'myapi2',
		?string $accessToken = null,
	) {
		$this->httpClient = new Client();
		$this->apiUrl = rtrim($apiUrl, '/');

		$this->oauthProvider = new GenericProvider([
			'clientId'                => $clientId,
			'clientSecret'            => $clientSecret,
			'urlAccessToken'          => $accessTokenUrl,
			'urlAuthorize'            => '',
			'urlResourceOwnerDetails' => '',
			'scope'                   => $scope,
		]);

		if ($accessToken === null) {
			$this->refreshToken();
		} else {
			$this->accessToken = $accessToken;
		}
	}

	/**
	 * Creates a shipment and returns shipment batch ID
	 *
	 * @param CreateShipmentBatchRequest|CreateShipmenRequest $shipmentData
	 * @return string
	 *
	 * @throws PPLException
	 */
	public function createShipment(CreateShipmentBatchRequest|CreateShipmenRequest $shipmentData): string
	{
		$response = $this->request('/shipment/batch', $shipmentData->jsonSerialize());

		if (isset($response->getHeader('Location')[0]) === false) {
			throw new PPLException('There was an error while trying to retrieve shipments details');
		}

		$locationExploded = explode('/', $response->getHeader('Location')[0]);

		return end($locationExploded);
	}

	public function getShipmentBatch(string $shipmentBatchId): ShipmentBatchResponse
	{
		$response = $this->request("/shipment/batch/{$shipmentBatchId}", [], 'GET');

		$responseData = json_decode($response->getBody()->getContents(), true);

		if ($responseData === null) {
			throw new PPLException(sprintf(
				'There was an error while trying to retrieve shipment batch details. Shipment ID: %s',
				$shipmentBatchId,
			));
		}

		return ShipmentBatchResponse::fromArrayResponse($responseData);
	}

	public function getShipment(string $shipmentId): ShipmentResponse
	{
		$response = $this->request("/shipment/batch/{$shipmentId}", [], 'GET');

		$responseData = json_decode($response->getBody()->getContents(), true);
		if ($responseData === null || count($responseData['items']) > 1) {
			throw new PPLException(sprintf(
				'There was an error while trying to retrieve shipment details. Shipment ID: %s',
				$shipmentId,
			));
		}

		return ShipmentResponse::fromArrayResponse($responseData['items'][0]);
	}

	public function getLabelPdf(string $labelUrl): PdfResponse
	{
		$response = $this->request(str_replace($this->apiUrl, '', $labelUrl), [], 'GET');

		return PdfResponse::fromArrayResponse([
			'pdfContent' => $response->getBody()->getContents(),
		]);
	}

	public function getLocations(array $filters = []): array
	{
		$response = $this->httpClient->get("{$this->apiUrl}/locations", [
			'headers' => [
				'Authorization' => "Bearer {$this->accessToken}",
			],
			'query' => $filters,
		]);

		if ($response->getStatusCode() >= 400) {
			throw new PPLException("Failed to fetch locations: {$response->getBody()}");
		}

		return json_decode($response->getBody()->getContents(), true);
	}

	public function isTokenValid(): bool
	{
		try {
			$response = $this->httpClient->get("{$this->apiUrl}/codelist/ageCheck?limit=1&offset=0", [
				'headers' => [
					'Authorization' => "Bearer {$this->accessToken}",
				],
			]);

			return $response->getStatusCode() === 200;
		} catch (Exception $e) {

			return false;
		}
	}

	public function refreshAndGetToken(): string
	{
		$this->refreshToken();

		return $this->accessToken;
	}

	private function refreshToken(): void
	{
		try {
			$accessToken = $this->oauthProvider->getAccessToken('client_credentials');
			$this->accessToken = $accessToken->getToken();
		} catch (Exception $e) {
			throw new PPLException("Failed to get access token: " . $e->getMessage());
		}
	}

	private function getToken(): string
	{
		return $this->accessToken;
	}

	private function request(string $endpoint, array $data = [], string $method = 'POST', $shouldRetry = true): ResponseInterface
	{
		if (empty($this->accessToken)) {
			$this->refreshToken();
		}

		$response = $this->httpClient->request($method, "{$this->apiUrl}{$endpoint}", [
			'headers' => [
				'Authorization' => "Bearer {$this->accessToken}",
				'Content-Type' => 'application/json',
			],
			'body' => json_encode($data),
		]);

		if ($response->getStatusCode() === 401 && $shouldRetry === true) {
			$this->refreshToken();

			return $this->request($endpoint, $data, $method, false);
		}

		if ($response->getStatusCode() >= 400 ) {
			throw new PPLException("API call failed: {$response->getBody()}");
		}

		return $response;
	}
}