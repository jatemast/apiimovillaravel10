<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ImgbbService
{
    protected $apiKey;
    protected $apiUrl;

    public function __construct()
    {
        $this->apiKey = env('IMGBB_API_KEY');
        $this->apiUrl = 'https://api.imgbb.com/1/upload';
    }

    public function uploadImage(string $imageData, string $name = null, int $expiration = null)
    {
        $data = [
            'key' => $this->apiKey,
            'image' => $imageData, // Puede ser base64, un archivo binario o una URL
        ];

        if ($name) {
            $data['name'] = $name;
        }

        if ($expiration) {
            $data['expiration'] = $expiration;
        }

        $response = Http::post($this->apiUrl, $data);

        if ($response->successful()) {
            return $response->json('data.url');
        }

        throw new \Exception('Error al subir imagen a Imgbb: ' . $response->body());
    }
}