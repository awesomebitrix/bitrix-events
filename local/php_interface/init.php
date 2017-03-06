<?
$eventManager = \Bitrix\Main\EventManager::getInstance();

// Подключить обработчики событий модуля инфоблоков
include('include/events/iblock.php');

// Подключить обработчики событий главного модуля
include('include/events/main.php');

// Подключить обработчики событий модуля sale
include('include/events/sale.php');