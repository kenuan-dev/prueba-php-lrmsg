<?php
#.................................................................
# init
require_once 'common.php';

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
#.................................................................
$ejemplo   = cli\choose('Indique el numero del ejemplo: ', "123", "1");

#.................................................................
# main_logic
$data_storage   = "data/example_00".$ejemplo.".json";
$data_json      = file_get_contents($data_storage);
$data_array     = json_decode($data_json, true);
$truck_limit    = $data_array["truck"]["weight_limit"];

$headers      = array('index', 'weight', '');
$cows_best = array();
$cows_add  = array();
$cows_weight = 0;
$cows_milk = 0;

foreach ($data_array["cows"] as $key => $value) {
    array_push($cows_best,
        array(
            $key + 1,
            $value["weight"],
            $value["milk_daily"],
            round($value["milk_daily"] / ($value["weight"] / $value["milk_daily"] ), 6)
    ));
}

usort($cows_best, function($a, $b) {return strcmp($a[3], $b[3]);});

foreach ($cows_best as $value) {
    if($cows_weight + $value[1] <= $truck_limit){
        $cows_weight = $cows_weight + $value[1];
        $cows_milk = $cows_milk + $value[2];
        array_push($cows_add,  array($value[0], $value[1], $value[2]));
    }
}

echo "\nCamion soporta = " . $truck_limit . "kg\n";

$headers = array('Vaca', 'Peso en kilogramos', 'Produccion de leche por dia');
$table = new \cli\Table();
$table->setHeaders($headers);
$table->setRows($cows_add);
$table->setRenderer(new \cli\table\Ascii([10, 10, 20, 5]));
$table->sort(0);
$table->display();

echo "\nResultado = " . $cows_milk . " litros\n\n\n";

#.................................................................

