<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class MapListComplexComponent extends CBitrixComponent
{
	protected $defaultUrlTemplates404 = array();
	protected $componentVariables = array();
	protected $page = '';

	/**
	 * Определяем шаблоны компонента для ЧПУ
	 */
	protected function setSefDefaultParams()
	{
		$this->defaultUrlTemplates404 = array(
		    'index' => 'index.php',
		    'detail' => 'detail/#ELEMENT_ID#/'
		);
		$this->componentVariables = array('ELEMENT_ID');
	}
	
	/**
	 * Получаем объекты, определяем url
	 */
	protected function getResult()
	{
		$urlTemplates = array();
		if ($this->arParams['SEF_MODE'] == 'Y')
		{
			$variables = array();
			$urlTemplates = \CComponentEngine::MakeComponentUrlTemplates(
				$this->defaultUrlTemplates404,
				$this->arParams['SEF_URL_TEMPLATES']
			);
			$variableAliases = \CComponentEngine::MakeComponentVariableAliases(
				$this->defaultUrlTemplates404,
				$this->arParams['VARIABLE_ALIASES']
			);

			$engine = new CComponentEngine($this);
			if (CModule::IncludeModule('iblock'))
			{
				$engine->addGreedyPart("#SECTION_CODE_PATH#");
				$engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
			}
			$this->page = $engine->guessComponentPath(
				$this->arParams['SEF_FOLDER'],
				$urlTemplates,
				$variables
			);
		
		    if (strlen($this->page) <= 0)
		        $this->page = 'index';
		
		    \CComponentEngine::InitComponentVariables(
		    	$this->page,
		    	$this->componentVariables, $variableAliases,
		    	$variables
			);
		}
		else
		{
		    $this->page = 'index';
		}
		
		$this->arResult = array(
		   'FOLDER' => $this->arParams['SEF_FOLDER'],
		   'URL_TEMPLATES' => $urlTemplates,
		   'VARIABLES' => $variables,
		   'ALIASES' => $variableAliases
		);
	}

	/**
	 * Логика компонента
	 */
	public function executeComponent()
	{
		try
		{
			$this->setSefDefaultParams();
			$this->getResult();
			$this->includeComponentTemplate($this->page);
		}
		catch (Exception $e)
		{
			ShowError($e->getMessage());
		}
	}
}

