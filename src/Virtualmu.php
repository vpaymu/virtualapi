<?php

namespace Virtualdev\Virtualapi;

class Virtualmu
{
    private string $endpoint;
    private string $endpoint2;
    public function __construct(string $endpoint, string $endpoint2)
    {
        $this->endpoint = $endpoint;
        $this->endpoint2 = $endpoint2;
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
        $url = "{$this->endpoint}/v1/dok-games";

        return $this->sendGetRequest($url);
    }

    public function cekDataBank(): array
    {
        $url = "{$this->endpoint}/v1/bank/kode";

        return $this->sendGetRequest($url);
    }

    public function cekDataEmoney(): array
    {
        $url = "{$this->endpoint}/v1/emoney/kode";

        return $this->sendGetRequest($url);
    }

    public function cekDataBank2(): array
    {
        $url = "{$this->endpoint2}/listBank";

        return $this->sendGetRequest($url);
    }

    public function cekDataEmoney2(): array
    {
        $url = "{$this->endpoint2}/listEwallet";

        return $this->sendGetRequest($url);
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
    public function cekIdBank2(string $slug, string $tujuan, string $kode): array
    {
        $url = "{$this->endpoint2}/{$slug}?bankCode={$kode}&accountNumber={$tujuan}";

        return $this->sendGetRequest($url);
    }
    public function cekIdPln(string $id): array
    {
        $url = "{$this->endpoint}/v1/pln?id={$id}";

        return $this->sendGetRequest($url);
    }
}
