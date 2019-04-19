<?php
/**
 * Created by PhpStorm.
 * User: Pashted
 * Date: 28.03.2018
 * Time: 14:29
 */

define('_VENDOR_PATH', $_SERVER['DOCUMENT_ROOT']);
define('_GIP2_PATH', $_SERVER['DOCUMENT_ROOT'] . '/vendor/geoip2/geoip2');

require _VENDOR_PATH . "/vendor/autoload.php";

use GeoIp2\Database\Reader;


/**
 * Расширение Функционала переключателя городов в шапке сайта
 *
 * Class Switcher
 */
class Switcher
{
    /**
     * IP-адрес посетителя
     *
     * @var string
     */
    public $user_ip;


    /**
     * Полное наименование города (ru), определенное модулем геолокации по IP
     *
     * @var string
     */
    public $user_city;


    /**
     * URL домена, связанного с городом, определенным модулем геолокации по IP
     *
     * @var string
     */
    public $user_city_url = '';


    /**
     * Список городов и их поддоменов, добавленных на сайт.
     * Используется в шаблоне модального окна.
     *
     * @var array
     */
    public $cities_list;


    /**
     * Порядковый номер города из списка городов, соответствующий текущему домену.
     * Используется в шаблоне модального окна.
     *
     * @var int
     */
    public $current_city_id = 0;


    function __construct()
    {
        $this->user_ip = $_SERVER['REMOTE_ADDR'];
//        $this->user_ip = '109.205.253.39'; // spb
//        $this->user_ip = '85.26.235.232'; // samara
//        $this->user_ip = '94.25.169.63'; // msk
//        $this->user_ip = '80.83.239.112'; // vladivostok
//        $this->user_ip = '213.87.121.182';

        self::set_user_city();
    }


    /**
     * Определяет город посетителя по его IP-адресу
     */
    function set_user_city()
    {

        $reader = new Reader(_GIP2_PATH . '/GeoLite2-City.mmdb');

        $this->user_city = $reader->city($this->user_ip)->city->names['ru'];
    }


    /**
     * Создаёт список городов на основе элементов инфоблока "Города"
     */
    function set_cities()
    {
        $arSelect = Array(
            "ID",
            "NAME",
            "CODE",
            "DATE_ACTIVE_FROM",
            "PROPERTY_WF_SUBDOMAIN"
        );
        $arFilter = Array(
            "IBLOCK_ID"   => SW_CITIES_IBLOCK_ID,
            "ACTIVE_DATE" => "Y",
            "ACTIVE"      => "Y"
        );
        $res = CIBlockElement::GetList(Array("NAME" => "ASC"), $arFilter, false, Array("nPageSize" => 150), $arSelect);

        $i = 1;

        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $this->cities_list[$i] = array(
                'name'   => $arFields['NAME'],
                'domain' => ($arFields['PROPERTY_WF_SUBDOMAIN_VALUE'] ? $arFields['PROPERTY_WF_SUBDOMAIN_VALUE'] . '.' : '') . SW_ROOT_DOMAIN
            );

            if ($arFields['NAME'] === $this->user_city)
                $this->user_city_url = $this->cities_list[$i]['domain'];

            if ($this->cities_list[$i]['domain'] == $_SERVER['SERVER_NAME']) {
                $this->current_city_id = $i;
            }
            $i++;
        }
    }


    /**
     * Проверяет, есть ли посещение в инфоблоке и возвращает кол-во совпадений
     *
     * @return int
     */
    function check_user()
    {
        $arSelect = Array(
            "ID",
            "NAME",
            "DETAIL_TEXT"
        );
        $arFilter = Array(
            "IBLOCK_ID"   => SW_USERS_IBLOCK_ID,
            "ACTIVE_DATE" => "Y",
            "ACTIVE"      => "Y",
            "NAME"        => $this->user_ip
        );
        $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 5), $arSelect);

        $result = 0;
        while ($ob = $res->GetNextElement()) {
            $result++;
        }
        return $result;
    }


    /**
     * Сохраняет IP-адрес пользователя в новом элементе инфоблока
     */
    function save_user()
    {
        date_default_timezone_set('Europe/Moscow');
        $time = date('d.m.Y H:i:s');

        $arFields = array(
            "ACTIVE"      => "Y",
            "IBLOCK_ID"   => SW_USERS_IBLOCK_ID,
            "NAME"        => $this->user_ip,
            "DETAIL_TEXT" => "Дата и время визита по МСК:\n" .
                "\t$time\n\n" .
                "Страница:\n" .
                "\t" . $_REQUEST['page'] . "\n\n" .
                "IP-адрес:\n" .
                "\t$this->user_ip\n\n" .
                "Город на основе IP-адреса:\n" .
                "\t$this->user_city\n\n" .
                "Ваш город $this->user_city?\n" .
                "\t {$_REQUEST['result']}",
        );
        $elem = new CIBlockElement();
        $new_id = $elem->Add($arFields, false, false, true);

        echo json_encode(array($time, $new_id));
    }


    /**
     * Выводит на экран переключатель и модальные окна
     */
    function render()
    {
        self::set_cities();

        global $APPLICATION;
        $APPLICATION->AddHeadScript('/include/city-switcher/script.js');
		$APPLICATION->SetAdditionalCSS('/include/city-switcher/style.css');

        include('template.php');
    }
}