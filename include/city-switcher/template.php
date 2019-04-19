<?php
/**
 * Created by PhpStorm.
 * User: Pashted
 * Date: 28.03.2018
 * Time: 14:31
 *
 * @var $this Switcher
 */
?>

<div class="pull-left small city-switcher" title="Выбрать другой город">
    <div>Выбранный город:</div>
    <? // город, связанный с текущим поддоменом ?>
    <span class="cityname"><?= $this->cities_list[$this->current_city_id]['name'] ?></span>
</div>


<div class="modal" id="switch-city" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <i data-dismiss="modal" class="jqmClose top-close fa fa-close"></i>
                <div class="h3 modal-title">Выберите город</div>
            </div>

            <div class="modal-body">
                <?
                foreach ($this->cities_list as $n => $city) {

                    if ($n == $this->current_city_id) { ?>
                        <span><?= $city['name'] ?> <i class="fa fa-check text-muted"></i></span>
                    <? } else { ?>
                        <a href="http://<?= $city['domain'] . $_SERVER['REQUEST_URI'] ?>"><?= $city['name'] ?></a>
                    <? } ?>
                <? } ?>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
            </div>

        </div>
    </div>
</div>

<div class="modal" id="ask-city" role="dialog" data-ask="<?= $this->check_user() === 0 && strlen($this->user_city_url) > 0 ?>">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-body">
                <div class="h3">Ваш город</div>
                <div class="h2 user-city" data-href="<?= $this->user_city_url ?>"><?= $this->user_city ?>?</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-lg">Да</button>
                <button type="button" class="btn btn-default btn-lg white">Нет (выбрать другой город)</button>
            </div>

        </div>
    </div>
</div>
