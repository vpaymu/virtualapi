<?php

class Tokovoucher
{
    private string $memberCode;
    private string $secret;
    private string $endpoint = "https://api.tokovoucher.id/";

    public function __construct(string $memberCode, string $secret)
    {
        $this->memberCode = $memberCode;
        $this->secret = $secret;
    }

    private function createSignature(string ...$parts): string
    {
        return hash("md5", implode(":", $parts));
    }

    private function sendRequest(string $url, array $options): array
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($options));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

        $response = curl_exec($ch);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new Exception($error);
        }

        return json_decode($response, true);
    }

    public function cekSaldoTV(): array
    {
        $signature = $this->createSignature($this->memberCode, $this->secret);
        $url = "{$this->endpoint}member?member_code={$this->memberCode}&signature={$signature}";

        return $this->sendRequest($url, []);
    }

    public function transaksiTV(string $ref_id, string $produk, string $tujuan, string $server_id = ""): array
    {
        $signature = $this->createSignature($this->memberCode, $this->secret, $ref_id);
        $data = [
            "ref_id" => $ref_id,
            "produk" => $produk,
            "tujuan" => $tujuan,
            "server_id" => $server_id,
            "member_code" => $this->memberCode,
            "signature" => $signature,
        ];
        $options = [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
        ];

        return $this->sendRequest("{$this->endpoint}v1/transaksi", $options);
    }

    public function cekTrxTV(string $ref_id): array
    {
        $signature = $this->createSignature($this->memberCode, $this->secret, $ref_id);
        $data = [
            "ref_id" => $ref_id,
            "member_code" => $this->memberCode,
            "signature" => $signature,
        ];
        $options = [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
        ];

        return $this->sendRequest("{$this->endpoint}v1/transaksi/status", $options);
    }

    public function kategoriTV(): array
    {
        $signature = $this->createSignature($this->memberCode, $this->secret);
        $url = "{$this->endpoint}member/produk/category/list?member_code={$this->memberCode}&signature={$signature}";

        return $this->sendRequest($url, []);
    }

    public function operatorTV(string $kategoriID): array
    {
        $signature = $this->createSignature($this->memberCode, $this->secret);
        $url = "{$this->endpoint}member/produk/operator/list?member_code={$this->memberCode}&signature={$signature}&id={$kategoriID}";

        return $this->sendRequest($url, []);
    }

    public function jenisTV(string $operatorID): array
    {
        $signature = $this->createSignature($this->memberCode, $this->secret);
        $url = "{$this->endpoint}member/produk/jenis/list?member_code={$this->memberCode}&signature={$signature}&id={$operatorID}";

        return $this->sendRequest($url, []);
    }

    public function produkTV(string $jenisID): array
    {
        $signature = $this->createSignature($this->memberCode, $this->secret);
        $url = "{$this->endpoint}member/produk/list?member_code={$this->memberCode}&signature={$signature}&id_jenis={$jenisID}";

        return $this->sendRequest($url, []);
    }

    public function produkByKodeTV(string $kodeProduk): array
    {
        $signature = $this->createSignature($this->memberCode, $this->secret);
        $url = "{$this->endpoint}produk/code?member_code={$this->memberCode}&signature={$signature}&kode={$kodeProduk}";

        return $this->sendRequest($url, []);
    }
}
