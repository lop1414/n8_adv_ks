<?php

namespace App\Sdks\KuaiShou\Helper;

class Csv
{

    static public function getCsvData($csv): array
    {
        // 去除文本包含的BOM
        $csv = json_decode(str_replace('\ufeff','',json_encode($csv)),true);

        $csvData = str_getcsv($csv,"\n");
        $data = $fields = [];
        foreach ($csvData as $key => $raw){
            $raw = str_getcsv($raw,',');
            if($key == 0){
                foreach ($raw as $index => $field){
                    $fields[$index] = $field;
                }
                continue;
            }

            $item = [];

            foreach ($raw as $k => $v){
                $item[$fields[$k]] = $v;
            }

            $data[] = $item;
        }
        return $data;
    }
}
