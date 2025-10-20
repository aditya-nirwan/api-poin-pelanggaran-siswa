<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class FonnteService
{
  private $token;
  private $apiUrl = 'https://api.fonnte.com/send';

  public function __construct()
  {
    $this->token = config('services.fonnte.token');
  }

  public function sendMessage(string $phoneNumber, string $message): array
  {
    $payload = [
      'target' => $this->formatPhoneNumber($phoneNumber),
      'message' => $message,
      'countryCode' => '62',
      'delay' => '5'
    ];

    try {
      $response = Http::withHeaders([
        'Authorization' => $this->token
      ])->timeout(30)->post($this->apiUrl, $payload);

      $this->logResponse($payload, $response->json());

      return [
        'status' => $response->successful(),
        'status_code' => $response->status(),
        'response' => $response->json()
      ];
    } catch (\Exception $e) {
      Log::error('Fonnte API Error: ' . $e->getMessage());

      return [
        'status' => false,
        'error' => $e->getMessage(),
        'status_code' => 500
      ];
    }
  }

  private function formatPhoneNumber(string $phone): string
  {
    $cleaned = preg_replace('/[^0-9]/', '', $phone);

    if (str_starts_with($cleaned, '0')) {
      return '62' . substr($cleaned, 1);
    }

    if (!str_starts_with($cleaned, '62')) {
      return '62' . $cleaned;
    }

    return $cleaned;
  }

  private function logResponse(array $payload, ?array $response): void
  {
    Log::channel('whatsapp')->info('Fonnte API Request', [
      'target' => $payload['target'],
      'message_length' => strlen($payload['message']),
      'response' => $response,
      'timestamp' => now()->toDateTimeString()
    ]);

    if (isset($response['status']) && !$response['status']) {
      Log::channel('whatsapp')->error('Fonnte API Failed', [
        'error' => $response['reason'] ?? 'Unknown error',
        'phone' => $payload['target']
      ]);
    }
  }
}
