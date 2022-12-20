<?php

namespace App\Traits;
use GuzzleHttp\Client;

trait fetchApi
{
    /**
     * Get All latest records from API
     * 
     * @return array
     */
    public function fetchApi()
    {
        $client = new Client();
        $apiUrl = "https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr_crous_logement_france_entiere&q=&rows=100&facet=zone&facet=regions";
        // Fetch API Data
        $response = $client->request('GET', $apiUrl, [
            'verify'  => false,
        ]);
        // Get api response
        $responseBody = json_decode($response->getBody());

        return $responseBody->records;
    }
}