<?php
require_once 'db.php';
require_once 'functions.php';

$categories = get_categories($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lot = $_POST;
    
    $errors = validate_form();

    if ($_FILES['img_url']['tmp_name'] !== '') {
		$img_url = check_file();
	} 
	else {
		$errors['file'] = 'Вы не загрузили файл';
    }
    

    if (!count($errors)) {
        $found_key = array_search($lot['category'], array_column($categories, 'name'));
        $lot_properties = 
        [
            'title' => $lot['title'], 
            'category_id' => $categories[$found_key]['id'], 
            'description' => $lot['description'], 
            'img_url' => $img_url, 
            'start_price' => $lot['start_price'], 
            'bet_step' => $lot['bet_step'], 
            'date_expire' => $lot['date_expire'],
            'user_id' => 2
        ];
        
        $lot_id = insert_lot($connection, $lot_properties);
        if (is_null($lot_id)) {
            $error_page = include_template(
				'404.php',
				[
					'categories' => $categories,
					'page_title' => 'Yeticave - 404 not found'
				 ]
			);

            echo $error_page;
            exit;
        } 
        else {
            header("Location: lot.php?id=" . $lot_id);
        } 
    }
    else {
        $add_page_errors = include_template(
            'add-lot.php',
            [
                'categories' => $categories,
                'errors' => $errors,
                'lot' => $lot
            ]
        );
        
        echo $add_page_errors;
    }
}
else {
    $add_page = include_template(
        'add-lot.php',
        [
            'categories' => $categories
        ]
    );
    
    echo $add_page;
}