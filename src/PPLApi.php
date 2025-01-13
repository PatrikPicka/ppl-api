<?php

declare(strict_types=1);

namespace PTB\PPLApi;

use GuzzleHttp\Client;
use League\OAuth2\Client\Provider\GenericProvider;
use PTB\PPLApi\Exception\PPLException;
use PTB\PPLApi\Shipment\Request\CreateShipmentRequest;

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

	public function createShipment(CreateShipmentRequest $shipmentData): array
	{
		return $this->request('/shipment/batch', $shipmentData->jsonSerialize());
	}

	public function printLabel(string $shipmentId): string
	{
		$response = $this->request("/shipments/{$shipmentId}/label", [], 'GET');

		return $response['label'];
	}

	public function orderPickup(array $pickupData): array
	{
		return $this->request('/pickups', $pickupData);
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
		} catch (\Exception $e) {

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
		} catch (\Exception $e) {
			throw new PPLException("Failed to get access token: " . $e->getMessage());
		}
	}

	private function getToken(): string
	{
		return $this->accessToken;
	}

	private function request(string $endpoint, array $data = [], string $method = 'POST', $shouldRetry = true): array
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

		return json_decode($response->getBody()->getContents(), true); //TODO: Get informations for package from url which is in header "Location"
	}
}