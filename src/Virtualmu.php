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
        $url = "{$this->endpoint}/bank";

        return $this->sendGetRequest($url);
    }

    public function cekDataPln(): array
    {
        $url = "{$this->endpoint}/pln";

        return $this->sendGetRequest($url);
    }
    public function cekIdGame(string $slug, string $id): array
    {
        $url = "{$this->endpoint}/api/game/{$slug}?id={$id}";

        return $this->sendGetRequest($url);
    }

    public function cekIdGameServer(string $slug, string $id, string $server): array
    {
        $url = "{$this->endpoint}/api/game/{$slug}?id={$id}&zone={$server}";

        return $this->sendGetRequest($url);
    }
    public function cekIdBank(string $tujuan, string $kode): array
    {
        $url = "{$this->endpoint}/api/bank?tujuan={$tujuan}&kode={$kode}";

        return $this->sendGetRequest($url);
    }

    public function cekIdEmoney(string $tujuan, string $kode): array
    {
        $url = "{$this->endpoint}/api/emoney?tujuan={$tujuan}&kode={$kode}";

        return $this->sendGetRequest($url);
    }
    public function cekIdPln(string $id): array
    {
        $url = "{$this->endpoint}/api/pln?id={$id}";

        return $this->sendGetRequest($url);
    }
}
