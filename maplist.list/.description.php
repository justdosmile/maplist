<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => Loc::getMessage('MAPLIST_LIST_DESCRIPTION_TITLE'),
	"DESCRIPTION" => Loc::getMessage('MAPLIST_LIST_DESCRIPTION'),
	"ICON" => '/images/icon.gif',
	"SORT" => 20,
	"PATH" => array(
		"ID" => 'jds',
		"NAME" => Loc::getMessage('MAPLIST_LIST_DESCRIPTION_GROUP'),
		"SORT" => 10,
		"CHILD" => array(
			"ID" => 'standard',
			"NAME" => Loc::getMessage('MAPLIST_LIST_DESCRIPTION_DIR'),
			"SORT" => 10
		)
	),
);

?>