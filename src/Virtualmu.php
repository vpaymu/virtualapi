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
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }

    private function buildUrl(string $path, array $params = []): string
    {
        $url = rtrim($this->endpoint, '/') . '/' . ltrim($path, '/');
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }

    private function getRequest(string $path, array $params = []): array
    {
        $url = $this->buildUrl($path, $params);
        return $this->sendGetRequest($url);
    }

    public function cekDataGame(): array
    {
        return $this->getRequest('/');
    }

    public function cekDataGameServer(string $slug): array
    {
        return $this->getRequest("/api/game/get-zone/$slug");
    }

    public function cekDataRekening(): array
    {
        return $this->getRequest('/rekening');
    }

    public function cekKodeRekening(string $slug): array
    {
        return $this->getRequest("/api/rekening/get-kode/$slug");
    }

    public function cekDataPln(): array
    {
        return $this->getRequest('/pln');
    }

    public function cekDataPdam(): array
    {
        return $this->getRequest('/pdam');
    }

    public function cekIdGame(string $slug, string $id): array
    {
        return $this->getRequest("/api/game/$slug", ['id' => $id]);
    }

    public function cekIdGameServer(string $slug, string $id, string $server): array
    {
        return $this->getRequest("/api/game/$slug", ['id' => $id, 'zone' => $server]);
    }

    public function cekIdRekening(string $slug, string $tujuan, string $kode): array
    {
        return $this->getRequest("/api/rekening/$slug", ['tujuan' => $tujuan, 'kode' => $kode]);
    }

    public function cekIdPln(string $slug, string $id): array
    {
        return $this->getRequest("/api/pln/$slug", ['id' => $id]);
    }

    public function cekIdPdam(string $slug, string $id): array
    {
        return $this->getRequest("/api/pdam/$slug", ['id' => $id]);
    }
}
