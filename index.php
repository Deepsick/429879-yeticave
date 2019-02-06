<?php
require_once('functions.php');
require_once('data.php');

$data = get_data ();

$index_content = include_template ('index.php', $data);
$clone_data = $data;
$clone_data['page_content'] = $index_content;
$index_page = include_template ('layout.php', $clone_data);

print($index_page);
?>
