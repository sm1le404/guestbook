<?php

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Application;
use \Bitrix\Main\Context;
use \Bitrix\Main\Loader;
use \Bitrix\Main\ModuleManager;
use \Saa\Guestbook\MessagesTable;

Loc::loadMessages(__FILE__);

if (class_exists('saa_guestbook'))
{
	return;
}

class saa_guestbook extends CModule
{
	public $MODULE_ID = 'saa.guestbook';
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;
	public $PARTNER_NAME;
	public $PARTNER_URI;
	public $MODULE_GROUP_RIGHTS = 'N';

	public function __construct()
	{
		$arModuleVersion = array();

		include __DIR__ . '/version.php';

		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME = Loc::getMessage('GUESTBOOK_MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::getMessage('GUESTBOOK_MODULE_DESCRIPTION');
		$this->PARTNER_NAME = Loc::getMessage('GUESTBOOK_PARTNER_NAME');
		$this->PARTNER_URI = Loc::getMessage('GUESTBOOK_PARTNER_URI');
	}

    public function InstallFiles(array $arParams = [])
    {
        CopyDirFiles(__DIR__ . '/components/', $_SERVER['DOCUMENT_ROOT'] . '/local/components/', true, true);
        CopyDirFiles(__DIR__ . '/public/', $_SERVER['DOCUMENT_ROOT'] . '/', true, true);
        return true;
    }

    public function UnInstallFiles(array $arParams = [])
    {
        DeleteDirFiles(__DIR__ . '/components/', $_SERVER['DOCUMENT_ROOT'] . '/local/components/');
        DeleteDirFiles(__DIR__ . '/public/', $_SERVER['DOCUMENT_ROOT'] . '/');
        return true;
    }

	public function DoInstall()
	{
		global $APPLICATION;
		$this->InstallDB();
        $this->InstallFiles();
		$APPLICATION->IncludeAdminFile(Loc::getMessage('GUESTBOOK_INSTALL_TITLE'), __DIR__ . '/step1.php');
	}

	public function InstallDB()
	{
        ModuleManager::registerModule($this->MODULE_ID);
        Loader::includeModule($this->MODULE_ID);
        if (!Application::getConnection()->isTableExists(MessagesTable::getTableName()))
        {
            MessagesTable::getEntity()->createDbTable();
        }
		return true;
	}

	public function DoUninstall()
	{
		global $APPLICATION;
		$this->UnInstallDB(['savedata' => Context::getCurrent()->getRequest()->get('savedata')]);
        $this->UnInstallFiles(['savedata' => Context::getCurrent()->getRequest()->get('savedata')]);
		$APPLICATION->IncludeAdminFile(Loc::getMessage('GUESTBOOK_UNINSTALL_TITLE'), __DIR__ . '/unstep1.php');
	}

	public function UnInstallDB(array $arParams = array())
	{
        Loader::includeModule($this->MODULE_ID);
        if (Application::getConnection()->isTableExists(MessagesTable::getTableName()))
        {
            Application::getConnection()->dropTable(MessagesTable::getTableName());
        }
        ModuleManager::unRegisterModule($this->MODULE_ID);
		return true;
	}
}