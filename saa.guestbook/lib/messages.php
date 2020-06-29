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
				'title'     => 'ID',
			),
			'USER_ID'   => array(
				'data_type' => 'integer',
                'required'  => false,
                'title'     => 'USER_ID',
			),
			'DATETIME' => array(
				'data_type' => 'datetime',
				'required'  => true,
				'title'     => 'DATETIME',
			),
			'TEXT'      => array(
				'data_type'  => 'string',
				'title'      => 'TEXT',
                'required'  => true
			),
            'FILE'   => array(
                'data_type' => 'integer',
                'title'     => 'FILE',
                'required'  => false
            ),
		);
	}
}