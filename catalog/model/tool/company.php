<?php
class ModelToolCompany extends Model {

    public function getCompanyByEik($eik) {

        $json = [];

        if (!$eik) {
            return $json;
        }

        $url = "https://api.companybook.bg/api/companies/" . $eik . "?with_data=true";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        if (!empty($data['company'])) {

            $company = $data['company'];

            $address = '';

            if (!empty($company['seat']['street'])) {
                $address .= $company['seat']['street'];
            }

            if (!empty($company['seat']['streetNumber'])) {
                $address .= ' ' . $company['seat']['streetNumber'];
            }

            $manager = '';

            // ООД - управител
            if (!empty($company['managers'][0]['name'])) {
                $manager = $company['managers'][0]['name'];
            }

            // АД - борд на директорите
            if (!empty($company['boardOfDirectors'][0]['name'])) {
                $manager = $company['boardOfDirectors'][0]['name'];
            }

            // Физическо лице - търговец
            if (!empty($company['physicalPersonTrader']['name'])) {
                $manager = $company['physicalPersonTrader']['name'];
            }

            $json = [
                'name' => $company['companyName']['name'] ?? '',
                'city' => $company['seat']['settlement'] ?? '',
                'address' => $address,
                'manager' => $manager
            ];
        }

        return $json;
    }
}