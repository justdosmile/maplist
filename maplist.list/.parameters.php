<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__); 

try
{
	if (!Main\Loader::includeModule('iblock'))
		throw new Main\LoaderException(Loc::getMessage('MAPLIST_LIST_PARAMETERS_IBLOCK_MODULE_NOT_INSTALLED'));
	
	$iblockTypes = \CIBlockParameters::GetIBlockTypes(Array("-" => " "));
	
	$iblocks = array(0 => " ");

	if (isset($arCurrentValues['IBLOCK_TYPE']) && strlen($arCurrentValues['IBLOCK_TYPE']))
	{
	    $filter = array(
	        'TYPE' => $arCurrentValues['IBLOCK_TYPE'],
	        'ACTIVE' => 'Y'
	    );
	    $iterator = \CIBlock::GetList(array('SORT' => 'ASC'), $filter);
	    while ($iblock = $iterator->GetNext())
	    {
	        $iblocks[$iblock['ID']] = $iblock['NAME'];
	    }
	}
	
	$arComponentParameters = array(
		'GROUPS' => array(
		),
		'PARAMETERS' => array(
			'IBLOCK_TYPE' => Array(
				'PARENT' => 'BASE',
				'NAME' => Loc::getMessage('MAPLIST_LIST_PARAMETERS_IBLOCK_TYPE'),
				'TYPE' => 'LIST',
				'VALUES' => $iblockTypes,
				'DEFAULT' => '',
				'REFRESH' => 'Y'
			),
			'IBLOCK_ID' => array(
				'PARENT' => 'BASE',
				'NAME' => Loc::getMessage('MAPLIST_LIST_PARAMETERS_IBLOCK_ID'),
				'TYPE' => 'LIST',
				'VALUES' => $iblocks
			),
			'COUNT' =>  array(
				'PARENT' => 'BASE',
				'NAME' => Loc::getMessage('MAPLIST_LIST_PARAMETERS_COUNT'),
				'TYPE' => 'STRING',
				'DEFAULT' => '0'
			),
			'CACHE_TIME' => array(
				'DEFAULT' => 3600
			)
		)
	);
}
catch (Main\LoaderException $e)
{
	ShowError($e->getMessage());
}

