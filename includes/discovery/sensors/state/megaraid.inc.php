<?php


$name = 'megaraid';

try {
    $output = snmp_get($device, 'nsExtendOutputFull.' . string_to_oid($name), '-Oqv', 'NET-SNMP-EXTEND-MIB');
    $megaraid_data = json_decode(stripslashes($output), true);
} catch (JsonAppException $e) {
    return;
}

$states = [
    ['value' => 0, 'generic' => 2, 'graph' => 0, 'descr' => 'Error'],
    ['value' => 1, 'generic' => 0, 'graph' => 0, 'descr' => 'Healthy'],
    ['value' => 2, 'generic' => 0, 'graph' => 0, 'descr' => 'Healthy (Unconfigured)'],
    ['value' => 3, 'generic' => 0, 'graph' => 0, 'descr' => 'Healthy (Hotspare)'],
    ['value' => 4, 'generic' => 1, 'graph' => 0, 'descr' => 'Not Present'],

];

if (!empty($megaraid_data['data'] && $megaraid_data['error'] == 0)) {

    foreach ($megaraid_data['data'] as $state_name => $collection) {

        create_state_index($state_name, $states);

        foreach ($collection as $index => $item) {
            //Discover Sensors
            discover_sensor($valid['sensor'], 'state', $device, string_to_oid($name).$index, $index, $item['name'], $item['name'], '1', '1', null, null, null, null, $item['state'], 'snmp', $index);

            //Create Sensor To State Index
            create_sensor_to_state_index($device, $state_name, $index);
        }
    }
}