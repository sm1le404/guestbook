<?php

namespace Saa\Guestbook;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class MessagesTable extends DataManager
{
	public static function getFilePath()
	{
		return __FILE__;
	}

	public static function getTableName()
	{
		return 'saa_guestbook_messages';
	}

	public static function getMap()
	{
		return array(
			'ID'       => array(
				'data_type'    => 'integer',
				'primary'      => true,
				'autocomplete' => true,
				'title'        => Loc::getMessage('STATISTICS_ENTITY_ID_FIELD'),
			),
			'USER_ID'   => array(
				'data_type' => 'integer',
				'title'     => Loc::getMessage('STATISTICS_ENTITY_URL_ID_FIELD'),
                'required'  => false
			),
			'DATETIME' => array(
				'data_type' => 'datetime',
				'required'  => true,
				'title'     => Loc::getMessage('STATISTICS_ENTITY_DATETIME_FIELD'),
			),
			'TEXT'      => array(
				'data_type'  => 'string',
				'title'      => Loc::getMessage('STATISTICS_ENTITY_URL_FIELD'),
                'required'  => true
			),
            'FILE'   => array(
                'data_type' => 'integer',
                'title'     => Loc::getMessage('STATISTICS_ENTITY_URL_ID_FIELD'),
                'required'  => false
            ),
		);
	}
}