<?php 

if (!function_exists('mapValues')) {
    function mapValues($jsonString, $mappingArray, $default = 'Unknown')
    {
        $decodedArray = json_decode($jsonString, true);

        if (!is_array($decodedArray) && !empty($decodedArray)) {
            $decodedArray = json_decode($decodedArray, true);
        }

        $mappedValues = [];
        if (is_array($decodedArray) && !empty($decodedArray)) {
            foreach ($decodedArray as $item) {
                $mappedValues[] = $mappingArray[$item] ?? $default;
            }
        }

        return !empty($mappedValues) ? implode(', ', $mappedValues) : 'N/A';
    }
}

if (!function_exists('formatDate')) {
    function formatDate($dateString)
    {
        return \Carbon\Carbon::parse($dateString)->format('d-m-Y h:i A');
    }
}

?>