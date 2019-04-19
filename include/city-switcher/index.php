<?php
/**
 * Created by PhpStorm.
 * User: Pashted
 * Date: 01.03.2018
 * Time: 16:22
 */

define("SW_CITIES_IBLOCK_ID", 32);
define("SW_USERS_IBLOCK_ID", 35);
define("SW_ROOT_DOMAIN", 'transformator.me');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");


    if (isset($_REQUEST['method']) && $_REQUEST['method'] === 'save' && CModule::IncludeModule("iblock")) {

        require "Switcher.php";

        $switcher = new Switcher();

        $switcher->save_user();
    }

} else {


//    global $USER;


//    if ($USER->IsAdmin()) {

        require "Switcher.php";

        $switcher = new Switcher();

        $switcher->render();
//    }

}