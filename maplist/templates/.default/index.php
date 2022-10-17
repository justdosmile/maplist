<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?$APPLICATION->IncludeComponent(
    "jds:maplist.list",
    "",
    Array(
        "CACHE_TIME" => $arParams['CACHE_TIME'],
        "CACHE_TYPE" => $arParams['CACHE_TYPE'],
        "COUNT" => $arParams['COUNT'],
        "IBLOCK_CODE" => $arParams['IBLOCK_CODE'],
        "IBLOCK_ID" => $arParams['IBLOCK_ID'],
        "API_KEY" => $arParams['API_KEY']
    ),
    $component
);?>
