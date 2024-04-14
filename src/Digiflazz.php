<?php

namespace Virtualdev\Virtualapi;

class Digiflazz
{
    private string $username;
    private string $apikey;
    private string $endpoint = "https://api.digiflazz.com/v1/";

    public function __construct(string $username, string $apikey)
    {
        $this->username = $username;
        $this->apikey = $apikey;
    }

    private function generateSign(string $suffix): string
    {
        return md5($this->username . $this->apikey . $suffix);
    }

    private function makeRequest(string $endpoint, array $data): array
    {
        $url = $this->endpoint . $endpoint;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    private function handleException(\Exception $err): void
    {
        throw new \Exception($err->getMessage());
    }

    public function cekSaldoDF(): array
    {
        $sign = $this->generateSign('depo');
        $data = ['cmd' => 'deposit', 'username' => $this->username, 'sign' => $sign];

        try {
            return $this->makeRequest('cek-saldo', $data)['data'];
        } catch (\Exception $err) {
            $this->handleException($err);
            throw $err;
        }
    }

    public function daftarHargaDF(): array
    {
        $sign = $this->generateSign('pricelist');
        $data = ['username' => $this->username, 'sign' => $sign];

        try {
            return $this->makeRequest('price-list', $data)['data'];
        } catch (\Exception $err) {
            $this->handleException($err);
            throw $err;
        }
    }

    public function inqPlnDF(string $customer_no): array|string
    {
        $data = ['commands' => 'pln-subscribe', 'customer_no' => $customer_no];

        try {
            $respData = $this->makeRequest('transaction', $data)['data'];
            return ($respData['name'] !== '') ? $respData : 'Data tidak ditemukan';
        } catch (\Exception $err) {
            $this->handleException($err);
            throw $err;
        }
    }

    public function depositDF(int $amount, string $bank, string $owner_name): array
    {
        $sign = $this->generateSign('deposit');
        $data = [
            'username' => $this->username,
            'amount' => $amount,
            'Bank' => $bank,
            'owner_name' => $owner_name,
            'sign' => $sign,
        ];

        try {
            return $this->makeRequest('deposit', $data)['data'];
        } catch (\Exception $err) {
            $this->handleException($err);
            throw $err;
        }
    }

    public function transaksiDF(
        ?string $cmd = null,
        string $buyer_sku_code,
        string $customer_no,
        string $ref_id,
        ?string $amount = null
    ): array {
        $sign = $this->generateSign($ref_id);
        $data = [
            'cmd' => $cmd,
            'username' => $this->username,
            'buyer_sku_code' => $buyer_sku_code,
            'customer_no' => $customer_no,
            'ref_id' => $ref_id,
            'amount' => $amount,
            'sign' => $sign,
        ];

        if ($cmd === 'CEK') $data['commands'] = 'inq-pasca';
        if ($cmd === 'BAYAR') $data['commands'] = 'pay-pasca';
        if ($cmd === 'STATUS') $data['commands'] = 'status-pasca';

        try {
            return $this->makeRequest('transaction', $data)['data'];
        } catch (\Exception $err) {
            $this->handleException($err);
            throw $err;
        }
    }
}