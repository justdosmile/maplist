<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc as Loc;

class MapListComponent extends CBitrixComponent
{

	protected $cacheKeys = array();
	protected $cacheAddon = array();
    protected $tagCache;
	

	public function onIncludeComponentLang()
	{
		$this->includeComponentLang(basename(__FILE__));
		Loc::loadMessages(__FILE__);
	}
	
    /**
     * Готовим входные параметры
     * @param array $arParams
     * @return array
     */
    public function onPrepareComponentParams($params)
    {
        $result = array(
            'IBLOCK_TYPE' => trim($params['IBLOCK_TYPE']),
            'IBLOCK_ID' => intval($params['IBLOCK_ID']),
            'COUNT' => intval($params['COUNT']),
			'API_KEY' => intval($params['API_KEY']),
            'CACHE_TIME' => intval($params['CACHE_TIME']) > 0 ? intval($params['CACHE_TIME']) : 3600,
			'FILTER' => is_array($params['FILTER']) && sizeof($params['FILTER']) ? $params['FILTER'] : array(),
            'CACHE_TAG_OFF' => $params['CACHE_TAG_OFF'] == 'Y'
        );
        return $result;
    }
	
	/**
	 * Определяем пользоваться кешем или нет
	 * @return bool
	 */
	protected function readDataFromCache()
	{
		global $USER;
		if ($this->arParams['CACHE_TYPE'] == 'N')
			return false;

		if (is_array($this->cacheAddon))
			$this->cacheAddon[] = $USER->GetUserGroupArray();
		else
			$this->cacheAddon = array($USER->GetUserGroupArray());

		return !($this->startResultCache(false, $this->cacheAddon, md5(serialize($this->arParams))));
	}

	/**
	 * Кешируем ключи arResult
	 */
	protected function putDataToCache()
	{
		if (is_array($this->cacheKeys) && sizeof($this->cacheKeys) > 0)
		{
			$this->SetResultCacheKeys($this->cacheKeys);
		}
	}

	/**
	 * Прерываем кэширование
	 */
	protected function abortDataCache()
	{
		$this->AbortResultCache();
	}

    /**
     * Конец кэширования
     * @return bool
     */
    protected function endCache()
    {
        if ($this->arParams['CACHE_TYPE'] == 'N')
            return false;

        $this->endResultCache();
    }
	
	/**
	 * Проверяем подключение iblock 
	 * Иначе кидаем ошибку
	 */
	protected function checkModules()
	{
		if (!Main\Loader::includeModule('iblock'))
			throw new Main\LoaderException(Loc::getMessage('MAPLIST_EXCEPTION_IBLOCK_MODULE_NOT_INSTALLED'));
	}
	
	/**
	 * Проверяем обязательные параметры
	 * Иначе кидаем ошибку
	 */
	protected function checkParams()
	{
		if ($this->arParams['IBLOCK_ID'] <= 0)
			throw new Main\ArgumentNullException('IBLOCK_ID');

		if ($this->arParams['API_KEY'] == "")
			throw new Main\LoaderException(Loc::getMessage('MAPLIST_EXCEPTION_API_KEY_EMPTY'));
	}

	/**
	 * Вытаскиваем объекты
	 */
	protected function getResult()
	{
		$filter = array(
			'IBLOCK_TYPE' => $this->arParams['IBLOCK_TYPE'],
			'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
			'ACTIVE' => 'Y'
		);

		$select = array(
			'ID',
			'NAME',
			'PROPERTY_COORD',
			'DETAIL_PAGE_URL',
		);
		$iterator = \CIBlockElement::GetList(Array("SORT"=>"ASC"), $filter, false, false, $select);

		$items = array();

		while ($element = $iterator->GetNext())
		{
			$items[] = array(
				'ID' => $element['ID'],
				'NAME' => $element['NAME'],
				'COORDINATES' => $element['PROPERTY_COORD_VALUE'],
				'DETAIL_URL' => $element['DETAIL_PAGE_URL']
			);
		}

		$this->arResult['ITEMS']['CNT'] = count($items);
		$this->arResult['ITEMS']['JSON'] = json_encode($items);
	}
	
	/**
	 * Подключаем скрипт карт Яндекс.Карты и script.js
	 */
	protected function componentProlog()
	{
		global $APPLICATION;
		$APPLICATION->AddHeadScript('http://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey='.$this->arParams['API_KEY']);
	}
	
	/**
	 * Устанавливаем тегированный кэш
	 */
	protected function componentEpilog()
	{
		if ($this->arResult['IBLOCK_ID'] && $this->arParams['CACHE_TAG_OFF'])
            \CIBlock::enableTagCache($this->arResult['IBLOCK_ID']);
	}
	
	/**
	 * Логика компонента
	 */
	public function executeComponent()
	{
		try
		{
			$this->componentProlog();

			$this->checkModules();
			$this->checkParams();

			if (!$this->readDataFromCache())
			{
				$this->getResult();
				$this->putDataToCache();
				$this->includeComponentTemplate();
			}

			$this->componentEpilog();
		}
		catch (Exception $e)
		{
			$this->abortDataCache();
			ShowError($e->getMessage());
		}
	}
}

