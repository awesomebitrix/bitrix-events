<?
/** @global $eventManager */

/*
 * Вызывается при выводе в административном интерфейсе формы редактирования.
 * Событие позволяет изменить или добавить собственные вкладки формы редактирования.
 * https://dev.1c-bitrix.ru/api_help/main/events/onadmintabcontrolbegin.php
 */
$eventManager->addEventHandler('main', 'OnAdminTabControlBegin', array('DBogdanoff_Main', 'removeAdvTab'));

/*
 * Вызывается при выводе в административном разделе панели кнопок.
 * Событие позволяет модифицировать или добавить собственные кнопки на панель.
 * https://dev.1c-bitrix.ru/api_help/main/events/onadmincontextmenushow.php
 */
$eventManager->addEventHandler('main', 'OnAdminContextMenuShow', array('DBogdanoff_Main', 'addButton'));

class DBogdanoff_Main
{
    /**
     * Удаляет вкладку "Реклама" при редактировании элемента инфоблока
     *
     * @param $form
     */
    function removeAdvTab(&$form) {
        if($GLOBALS["APPLICATION"]->GetCurPage() == "/bitrix/admin/iblock_element_edit.php")
        {
            $k = false;
            foreach($form->tabs as $key => $tab)
                if ($tab['TAB'] == 'Реклама')
                    $k = $key;

            if ($k !== false)
                unset($form->tabs[ $k ]);
        }
    }


    /**
     * Добавляет кнопку в блок действий при редактировании элемента инфоблока
     *
     * @param $items
     */
    function addButton(&$items)
    {
        if($GLOBALS["APPLICATION"]->GetCurPage(true) == "/bitrix/admin/iblock_element_edit.php" && $_REQUEST['IBLOCK_ID'] == IBLOCK_ID_PHOTO) {
            $ar_new[0] = $items[0];
            $ar_new[1] = array(
                "TEXT"  => "Скачать описание",
                "ICON"  => "docx",
                "TITLE" => "Страница настроек модулей",
                "LINK"  => "settings.php?lang=".LANGUAGE_ID
            );

            foreach($items as $k => $item) {
                if ($k < 1) continue;
                $ar_new[] = $item;
            }

            $items = $ar_new;
        }
    }
}