<?
/** @global $eventManager */

/*
 * Вызывается перед отправкой письма о новом заказе
 * Может быть использовано для модификации данных, изменения идентификатора типа почтового события или отмены отправки письма.
 * https://dev.1c-bitrix.ru/api_help/sale/events/events_send_post.php
 */
$eventManager->addEventHandler('sale', 'OnOrderNewSendEmail', Array('DBogdanoff_Sale', 'editOrderTemplate'));

class DBogdanoff_Sale
{
    /**
     * Метод добавляет в поля шаблона доп. данные
     *
     * @param $ORDER_ID
     * @param $eventName
     * @param $arFields
     * @return bool
     */
    function editOrderTemplate($ORDER_ID, &$eventName, &$arFields)
    {
        if (!CModule::IncludeModule('sale'))
            return false;

        // Заказ
        $arOrder = CSaleOrder::GetByID($ORDER_ID);

        // Св-ва заказа
        $dbResProps = CSaleOrderPropsValue::GetOrderProps($ORDER_ID);
        while ($arOrderProps = $dbResProps->Fetch())
            $arProps[$arOrderProps['CODE']] = $arOrderProps['VALUE'];

        // Цена без учёта доставки
        $arFields['PRICE_EXCLUDE_DELIVERY'] = CurrencyFormat($arOrder['PRICE'] - $arOrder['PRICE_DELIVERY'], 'RUB');
        $arFields['DELIVERY_PRICE'] = CurrencyFormat($arFields['DELIVERY_PRICE'], 'RUB');

        // Название платёжной системы
        $arPayItem = CSalePaySystem::GetByID($arOrder['PAY_SYSTEM_ID']);
        $arFields['NAME_PAY'] = ($arPayItem['NAME']) ?: 'Другая система';

        // Способ доставки
        $arDeliveryName = explode(':', $arOrder['DELIVERY_ID']);
        $dbHandler = CSaleDeliveryHandler::GetList(array(), array('SID' => $arDeliveryName[0]));
        if ($row = $dbHandler->Fetch())
        {
            $arFields['DELIVERY_NAME'] = $row['NAME'];
            foreach($row['PROFILES'] as $PID => $PRODILE)
            {
                if ($PID == $arDeliveryName[1])
                    $arFields['DELIVERY_NAME'] .= ' '. $PRODILE['TITLE'];

            }
        }

        return true;
    }
}