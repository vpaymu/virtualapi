<?php

namespace Virtualdev\Virtualapi;

class Linkqu
{
    private $user;
    private $pin;
    private $secretKey;
    private $clientId;
    private $clientSecret;
    private $endpoint = "https://gateway.linkqu.id/";

    public function __construct($username, $pin, $secretKey, $clientId, $clientSecret)
    {
        $this->user = $username;
        $this->pin = $pin;
        $this->secretKey = $secretKey;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    private function generateSignature($path, $method, $values)
    {
        $firstValue = $path . $method;
        $regex = "/[^0-9a-zA-Z]/";
        $secondValue = strtolower(preg_replace($regex, "", $values));
        return hash_hmac("sha256", $firstValue . $secondValue, $this->secretKey);
    }

    private function createHeaders()
    {
        return [
            "Content-Type: application/json",
            "client-id: " . $this->clientId,
            "client-secret: " . $this->clientSecret,
        ];
    }

    private function sendRequest($url, $method, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->createHeaders());

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch));
        }

        curl_close($ch);

        return json_decode($response, true);
    }

    public function cekSaldoLQ()
    {
        $url = $this->endpoint . "linkqu-partner/akun/resume?username=" . $this->user;
        $response = $this->sendRequest($url, "GET", []);
        return $response["data"];
    }

    public function dataBankLQ()
    {
        $url = $this->endpoint . "linkqu-partner/masterbank/list";
        $response = $this->sendRequest($url, "GET", []);
        return $response["data"];
    }

    public function dataEmoneyLQ()
    {
        $url = $this->endpoint . "linkqu-partner/data/emoney?username=" . $this->user;
        $response = $this->sendRequest($url, "GET", []);
        return $response["data"][0]["dataproduk"];
    }

    public function cekTrxLQ($partner_reff)
    {
        $url = $this->endpoint . "linkqu-partner/transaction/payment/checkstatus?username=" . $this->user . "&partnerreff=" . $partner_reff;
        $response = $this->sendRequest($url, "GET", []);
        $hasil = ["status_trx" => "pending"];
        return $response["rd"] === "Transaksi tidak ditemukan" ? $hasil : $response["data"];
    }

    public function cekDepoLQ($partner_reff)
    {
        $url = $this->endpoint . "linkqu-partner/transaction/payment/va/checkstatus?username=" . $this->user . "&partnerreff=" . $partner_reff;
        $response = $this->sendRequest($url, "GET", []);
        $hasil = ["status_trx" => "pending"];
        return $response["rd"] === "Transaksi tidak ditemukan" ? $hasil : $response["data"];
    }

    public function qrisLQ(
        $amount,
        $partner_reff,
        $customer_id,
        $customer_name,
        $expired,
        $customer_phone,
        $customer_email
    ) {
        $path = "/transaction/create/qris";
        $method = "POST";
        $values = $amount . $expired . $partner_reff . $customer_id . $customer_name . $customer_email . $this->clientId;
        $signature = $this->generateSignature($path, $method, $values);
        $data = [
            "amount" => $amount,
            "partner_reff" => $partner_reff,
            "customer_id" => $customer_id,
            "customer_name" => $customer_name,
            "expired" => $expired,
            "username" => $this->user,
            "pin" => $this->pin,
            "customer_phone" => $customer_phone,
            "customer_email" => $customer_email,
            "signature" => $signature,
        ];
        $url = $this->endpoint . "linkqu-partner/transaction/create/qris";
        $response = $this->sendRequest($url, "POST", $data);
        return $response;
    }

    public function vaLQ($amount, $partner_reff, $customer_id, $customer_name, $expired, $customer_phone, $customer_email, $bank_code)
    {
        $path = "/transaction/create/va";
        $method = "POST";
        $values = $amount . $expired . $bank_code . $partner_reff . $customer_id . $customer_name . $customer_email . $this->clientId;
        $signature = $this->generateSignature($path, $method, $values);

        $data = [
            'amount' => $amount,
            'partner_reff' => $partner_reff,
            'customer_id' => $customer_id,
            'customer_name' => $customer_name,
            'expired' => $expired,
            'username' => $this->user,
            'pin' => $this->pin,
            'customer_phone' => $customer_phone,
            'customer_email' => $customer_email,
            'bank_code' => $bank_code,
            'remark' => 'Pembayaran',
            'signature' => $signature,
        ];

        $url = $this->endpoint . 'linkqu-partner/transaction/create/va';
        return $this->sendRequest($url, "POST", $data);
    }

    public function retailLQ($amount, $partner_reff, $customer_id, $customer_name, $expired, $customer_phone, $customer_email, $retail_code)
    {
        $path = "/transaction/create/retail";
        $method = "POST";
        $values = $amount . $expired . $retail_code . $partner_reff . $customer_id . $customer_name . $customer_email . $this->clientId;
        $signature = $this->generateSignature($path, $method, $values);

        $data = [
            'amount' => $amount,
            'partner_reff' => $partner_reff,
            'customer_id' => $customer_id,
            'customer_name' => $customer_name,
            'expired' => $expired,
            'username' => $this->user,
            'pin' => $this->pin,
            'retail_code' => $retail_code,
            'customer_phone' => $customer_phone,
            'customer_email' => $customer_email,
            'remark' => 'Pembayaran',
            'signature' => $signature,
        ];

        $url = $this->endpoint . 'linkqu-partner/transaction/create/retail';
        return $this->sendRequest($url, "POST", $data);
    }

    public function inqBankLQ($bankcode, $accountnumber, $amount, $partner_reff)
    {
        $path = "/transaction/withdraw/inquiry";
        $method = "POST";
        $values = $amount . $accountnumber . $bankcode . $partner_reff . $this->clientId;
        $signature = $this->generateSignature($path, $method, $values);

        $data = [
            'username' => $this->user,
            'pin' => $this->pin,
            'bankcode' => $bankcode,
            'accountnumber' => $accountnumber,
            'amount' => $amount,
            'partner_reff' => $partner_reff,
            'signature' => $signature,
        ];

        $url = $this->endpoint . 'linkqu-partner/transaction/withdraw/inquiry';
        return $this->sendRequest($url, "POST", $data);
    }

    public function tfBankLQ($bankcode, $accountnumber, $amount, $partner_reff, $inquiry_reff)
    {
        $path = "/transaction/withdraw/payment";
        $method = "POST";
        $values = $amount . $accountnumber . $bankcode . $partner_reff . $inquiry_reff . $this->clientId;
        $signature = $this->generateSignature($path, $method, $values);

        $data = [
            'username' => $this->user,
            'pin' => $this->pin,
            'bankcode' => $bankcode,
            'accountnumber' => $accountnumber,
            'amount' => $amount,
            'partner_reff' => $partner_reff,
            'inquiry_reff' => $inquiry_reff,
            'signature' => $signature,
        ];

        $url = $this->endpoint . 'linkqu-partner/transaction/withdraw/payment';
        return $this->sendRequest($url, "POST", $data);
    }

    public function inqEmoneyLQ($bankcode, $accountnumber, $amount)
    {
        $path = "/transaction/reload/inquiry";
        $method = "POST";
        $values = $amount . $accountnumber . $bankcode . $this->clientId;
        $signature = $this->generateSignature($path, $method, $values);

        $data = [
            'username' => $this->user,
            'pin' => $this->pin,
            'bankcode' => $bankcode,
            'accountnumber' => $accountnumber,
            'amount' => $amount,
            'signature' => $signature,
        ];

        $url = $this->endpoint . 'linkqu-partner/transaction/reload/inquiry';
        return $this->sendRequest($url, "POST", $data);
    }

    public function tfEmoneyLQ($bankcode, $accountnumber, $amount, $partner_reff, $inquiry_reff)
    {
        $path = "/transaction/reload/payment";
        $method = "POST";
        $values = $amount . $accountnumber . $bankcode . $partner_reff . $inquiry_reff . $this->clientId;
        $signature = $this->generateSignature($path, $method, $values);

        $data = [
            'username' => $this->user,
            'pin' => $this->pin,
            'bankcode' => $bankcode,
            'accountnumber' => $accountnumber,
            'amount' => $amount,
            'partner_reff' => $partner_reff,
            'inquiry_reff' => $inquiry_reff,
            'signature' => $signature,
        ];

        $url = $this->endpoint . 'linkqu-partner/transaction/reload/payment';
        return $this->sendRequest($url, "POST", $data);
    }
}
