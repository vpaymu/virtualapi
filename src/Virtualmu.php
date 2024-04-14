<?php

namespace Virtualdev\Virtualapi;

class Virtualmu
{
    private string $endpoint;
    public function __construct(string $endpoint)
    {
        $this->endpoint = $endpoint;
    }
    private function sendGetRequest(string $url): array
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true);

        $response = curl_exec($ch);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new \Exception($error);
        }

        return json_decode($response, true);
    }

    public function cekDataGame(): array
    {
        $url = "{$this->endpoint}/";

        return $this->sendGetRequest($url);
    }

    public function cekDataBank(): array
    {
        $url = "{$this->endpoint}/rekening";

        return $this->sendGetRequest($url);
    }

    public function cekDataPln(): array
    {
        $url = "{$this->endpoint}/pln";

        return $this->sendGetRequest($url);
    }

    public function cekDataPdam(): array
    {
        $url = "{$this->endpoint}/pdam";

        return $this->sendGetRequest($url);
    }
}
