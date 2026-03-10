<?php
class ModelToolCompany extends Model
{

    public function getCompanyByEik($eik)
    {
        if (!$eik) {
            return ['error' => 'Липсва ЕИК'];
        }

        $url = "https://api.companybook.bg/api/companies/" . $eik . "?with_data=true";

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 0,
            CURLOPT_USERAGENT => 'CompanyLookupBot/1.0'
        ]);

        $response = curl_exec($ch);
        $error_no = curl_errno($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error_no) {
            return ['error' => 'Проблем с мрежовата връзка, моля опитайте по-късно! (Код: ' . $error_no . ')'];
        }

        // РАЗГРАНИЧАВАНЕ НА ГРЕШКИТЕ

        switch ($http) {
            case 404:
                return ['error' => 'Фирмата не е намерена'];
            case 400:
                return ['error' => 'Невалиден формат на ЕИК'];
            case 200:
                break;
            default:
                return ['error' => 'Грешка от сървъра на регистъра, моля опитайте по-късно! (Код: ' . $http . ')'];
        }
      

        $data = json_decode($response, true);

        if (!$data || empty($data['company'])) {
            return ['error' => 'Няма данни за тази компания'];
        }

        $company = $data['company'];


        $address_parts = [];
        if (!empty($company['seat']['street'])) $address_parts[] = $company['seat']['street'];
        if (!empty($company['seat']['streetNumber'])) $address_parts[] = $company['seat']['streetNumber'];
        $address = implode(' ', $address_parts);


        $manager = '';
        if (!empty($company['managers'][0]['name'])) {
            $manager = $company['managers'][0]['name'];
        } elseif (!empty($company['boardOfDirectors'][0]['name'])) {
            $manager = $company['boardOfDirectors'][0]['name'];
        } elseif (!empty($company['physicalPersonTrader']['name'])) {
            $manager = $company['physicalPersonTrader']['name'];
        }

        return [
            'name'    => $company['companyName']['name'] ?? 'Неизвестно име',
            'city'    => $company['seat']['settlement'] ?? '',
            'address' => $address,
            'manager' => $manager
        ];
    }
}
