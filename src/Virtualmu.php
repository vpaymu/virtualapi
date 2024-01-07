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

    public function cekIdGame(string $slug, string $id): array
    {
        $url = "{$this->endpoint}/v1/games/{$slug}?id={$id}";

        return $this->sendGetRequest($url);
    }

    public function cekIdGameServer(string $slug, string $id, string $server): array
    {
        $url = "{$this->endpoint}/v1/games/{$slug}?id={$id}&zone={$server}";

        return $this->sendGetRequest($url);
    }
    public function cekIdBank(string $slug, string $tujuan, string $kode): array
    {
        $url = "{$this->endpoint}/v1/{$slug}?tujuan={$tujuan}&kode={$kode}";

        return $this->sendGetRequest($url);
    }
    public function cekIdPln(string $id): array
    {
        $url = "{$this->endpoint}/v1/pln?id={$id}";

        return $this->sendGetRequest($url);
    }
}
