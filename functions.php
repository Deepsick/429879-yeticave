<?php 
function include_template ($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
};

function format_number ($number) {
    $rounded_number = ceil($number);
    $formatted_number = number_format($rounded_number, 0, ',', ' ');
    $price = $formatted_number . ' ₽';
    return $price;
};
?>