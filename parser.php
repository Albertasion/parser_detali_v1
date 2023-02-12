<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 0);
define('URL', 'https://detali.org.ua');
include_once 'functions.php';
require 'vendor/autoload.php';
require 'phpquery.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$spreadsheet = new Spreadsheet();
$writer = new Xlsx($spreadsheet);

$request = requests($_POST['name']);
$output = phpQuery::newDocument($request);
//назва категорії для формування назви файла
$category_name = $output->find('.cat-name')->text();
$category_name_mod = str_replace(" ", "_", $category_name);
echo $category_name;
echo $category_name_mod;

$fLag_pagination = true;


if ($fLag_pagination) {
  $menu = $output->find('.top-pagination-content a');
  foreach ($menu as $key => $value) {
    $pq = pq($value);
    $src_menu[$key] = $pq->attr("href");
  }
  //назва категорії для формування назви файла
  $category_name = $output->find('.cat-name')->text();
  $category_name = str_replace(" ", "_", $category_name);
  echo $category_name;

  //удаляємо останній елемент пагинаці. Він веде на 2 сторінку
  $trash_page = array_pop($src_menu);
  //беремо в масиві останю сторінку
  $last_page_url = end($src_menu);
  //розбиваємо останнню сторінку на =
  $last_page_number_array = explode("=", $last_page_url);
  //дістаємо номер останьої сторінки
  $last_page_number = $last_page_number_array[1];
  //дістаємо всі можливі сторінки пагінації в змінну $full_url
  for ($n = $last_page_number; $n > 0; $n--) {
    $full_url = URL . $last_page_number_array[0] . "=" . $n;
    // echo $full_url. '<br>';
    $request_all_pages_paginagination = requests($full_url);

    $output_all_pages_paginagination = phpQuery::newDocument($request_all_pages_paginagination);

    $all_product_links = $output_all_pages_paginagination->find('.product-name-container a');
    foreach ($all_product_links as $key => $value) {
      $pq2 = pq($value);
      $all_products_links_array[] = $pq2->attr("href");
    }
  }
  $result_product = parse_product($all_products_links_array);
  phpQuery::unloadDocuments();
  //функція додання даних в таблицю
  pull_data_sheet($result_product, $spreadsheet, $category_name, $writer, $category_name_mod);

} 
else {
$all_product_links = $output->find('.product-name-container a');
foreach ($all_product_links as $key => $value){
  $pq2 = pq($value);
$all_products_links_array[] = $pq2->attr("href");
}
$result_product = parse_product($all_products_links_array);
format($result_product);
phpQuery::unloadDocuments();
//функція додання даних в таблицю
pull_data_sheet($result_product, $spreadsheet, $category_name, $writer, $category_name_mod);

}






























