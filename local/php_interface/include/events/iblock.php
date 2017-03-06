<?
/** @global $eventManager */

/*
 * Вызывается до изменения элемента информационного блока.
 * Может быть использовано для отмены изменения или для переопределения некоторых полей.
 * https://dev.1c-bitrix.ru/api_help/iblock/events/onbeforeiblockelementupdate.php
 */
$eventManager->addEventHandler('iblock', 'OnBeforeIBlockElementUpdate', array('DBogdanoff_Iblock', 'checkPermissionBeforeUpdate'));

/*
 * Вызывается перед удалением элемента.
 * Как правило задачи обработчика данного события - разрешить или запретить удаление.
 * https://dev.1c-bitrix.ru/api_help/iblock/events/OnBeforeIBlockElementDelete.php
 */
$eventManager->addEventHandler('iblock', 'OnBeforeIBlockElementDelete', array('DBogdanoff_Iblock', 'checkPermissionBeforeDelete'));

class DBogdanoff_Iblock
{
    /**
     * Метод позволяет изменять элемент только авторам и админам
     *
     * @param $arParams
     * @return bool
     */
    public function checkPermissionBeforeUpdate(&$arParams)
    {
        global $APPLICATION, $USER;

        $rsElement = CIBlockElement::GetByID($arParams['ID']);
        $arElement = $rsElement->Fetch();

        if ($arParams['MODIFIED_BY'] != $arElement['CREATED_BY'] && !$USER->IsAdmin()) {
            $APPLICATION->ThrowException('Вы не можете изменять элементы, созданные другими пользователями.');
            return false;
        }
    }

    /**
     * Метод позволяет удалять элемент только авторам и админам
     *
     * @param $ID
     * @return bool
     */
    public function checkPermissionBeforeDelete($ID)
    {
        global $APPLICATION, $USER;

        $rsElement = CIBlockElement::GetByID($ID);
        $arElement = $rsElement->Fetch();

        if ($USER->GetID() != $arElement["CREATED_BY"] && !$USER->IsAdmin()) {
            $APPLICATION->ThrowException('Вы не можете удалять элементы, созданные другими пользователями.');
            return false;
        }
    }
}



