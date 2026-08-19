<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CareerApiClient
{
    private const TOKEN_CACHE_KEY = 'career_api.gateway_token';

    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, ['query' => $query])
            ->throw()
            ->json();
    }

    public function post(string $path, array $data = []): array
    {
        return $this->request('POST', $path, ['json' => $data])
            ->throw()
            ->json();
    }

    /**
     * Request tanpa auto-throw, dipakai untuk kasus yang butuh
     * penanganan error manual (mis. login, validasi 422).
     */
    public function postRaw(string $path, array $data = []): Response
    {
        return $this->request('POST', $path, ['json' => $data]);
    }

    /**
     * Request ke endpoint yang butuh Bearer user token (bukan gateway token saja),
     * misal /me, /logout.
     */
    public function postAuthenticated(string $path, string $userToken, array $data = []): Response
    {
        $header = config('services.career_api.application_token_header');

        return Http::acceptJson()
            ->withHeaders([$header => $this->gatewayToken()])
            ->withToken($userToken)
            ->connectTimeout((int) config('services.career_api.connect_timeout'))
            ->timeout((int) config('services.career_api.timeout'))
            ->post(rtrim(config('services.career_api.base_url'), '/').$path, $data);
    }

    /**
     * Request GET ke endpoint yang butuh Bearer user token, misal /me.
     */
    public function getAuthenticated(string $path, string $userToken, array $query = []): Response
    {
        $header = config('services.career_api.application_token_header');

        return Http::acceptJson()
            ->withHeaders([$header => $this->gatewayToken()])
            ->withToken($userToken)
            ->connectTimeout((int) config('services.career_api.connect_timeout'))
            ->timeout((int) config('services.career_api.timeout'))
            ->get(rtrim(config('services.career_api.base_url'), '/').$path, $query);
    }

    private function request(string $method, string $path, array $options = []): Response
    {
        $response = $this->send($method, $path, $options, $this->gatewayToken());

        // token mungkin sudah kedaluwarsa/dicabut, refresh sekali
        if ($response->status() === 401) {
            Cache::forget(self::TOKEN_CACHE_KEY);
            $response = $this->send($method, $path, $options, $this->gatewayToken());
        }

        return $response;
    }

    private function send(string $method, string $path, array $options, string $token): Response
    {
        $header = config('services.career_api.application_token_header');

        return Http::acceptJson()
            ->withHeaders([$header => $token])
            ->connectTimeout((int) config('services.career_api.connect_timeout'))
            ->timeout((int) config('services.career_api.timeout'))
            ->send($method, rtrim(config('services.career_api.base_url'), '/').$path, $options);
    }

    private function gatewayToken(): string
    {
        $encrypted = Cache::get(self::TOKEN_CACHE_KEY);

        if (is_string($encrypted)) {
            return Crypt::decryptString($encrypted);
        }

        $response = Http::acceptJson()
            ->connectTimeout(5)
            ->timeout(15)
            ->post(rtrim(config('services.career_api.base_url'), '/').'/service/token', [
                'client_id' => config('services.career_api.client_id'),
                'client_secret' => config('services.career_api.client_secret'),
                'purpose' => 'gateway',
            ])
            ->throw();

        $token = (string) $response->json('access_token');
        $expiresIn = (int) $response->json('expires_in', 600);

        if ($token === '') {
            throw new RuntimeException('API tidak mengembalikan gateway token.');
        }

        Cache::put(self::TOKEN_CACHE_KEY, Crypt::encryptString($token), max(60, $expiresIn - 60));

        return $token;
    }
}