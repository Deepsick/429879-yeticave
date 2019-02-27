<?php
require_once 'session.php';
require_once 'db.php';
require_once 'functions.php';

$categories = get_categories($connection);
$lots = null;

if (isset($_GET['search']) && $_GET['search'] !== '') {
    $search = trim($_GET['search']) ?? '';
    
    if ($search) {
        $cur_page = $_GET['page'] ?? 1;
        $page_items = 9;
    
        $result = mysqli_query(
            $connection, 
            "SELECT 
                COUNT(*) 
            AS 
                count 
            FROM 
                `lots` `l`  
            JOIN 
                `categories` `c`
            ON 
                `c`.`id` = `l`.`category_id`;"
        );
    
        $items_count = mysqli_fetch_assoc($result)['count'];
    
        $pages_count = ceil($items_count / $page_items);
        $offset = ($cur_page - 1) * $page_items;
    
        $pages = range(1, $pages_count);

        $lots = search_lots($connection, $search, $page_items,  $offset);
    }

 

    $search_page = include_template(
        'search.php',
        [
            'page_title' => 'Результаты поиска',
            'categories' => $categories,
            'lots' => $lots,
            'search' => $search,
            'pages' => $pages,
            'pages_count' => $pages_count,
            'cur_page' => $cur_page
        ]
    );
    
    echo $search_page;
} 
else {
    header("Location: index.php");
}


