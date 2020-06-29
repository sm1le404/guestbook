<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Error;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Loader;
use \Saa\Guestbook\MessagesTable;

/**
 * Class GuestBookComponent
 */
class GuestBookComponent extends CBitrixComponent implements Controllerable, Errorable
{
    /**
     * @var ErrorCollection
     */
    private $errorCollection;

    /** @inheritdoc */
    public function __construct($component = null)
    {
        parent::__construct($component);
        $this->errorCollection = new ErrorCollection;
    }

    public function configureActions()
    {
        return array(
            'fastAjax' => [
                'prefilters' => [
                    new ActionFilter\Csrf(),
                    new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_POST])
                ]
            ]
        );
    }

    public function fastAjaxAction()
    {
        try {
            global $USER;
            $params = \Bitrix\Main\Context::getCurrent()->getRequest()->toArray();
            if (count($params) && Loader::includeModule('saa.guestbook'))
            {
                if (strlen($params['text']) > 5)
                {
                    $file = 0;
                    if ($_FILES['file']['size'] > 0)
                    {
                        $file = \CFile::SaveFile($_FILES['file'], 'messages');
                    }

                    $data = [
                        'TEXT' => htmlspecialchars(trim($params['text'])),
                        'FILE' => (int) $file,
                        'USER_ID' => $USER->GetID() ? $USER->GetID() : 0,
                        'DATETIME' => new \Bitrix\Main\Type\DateTime()
                    ];

                    $result = MessagesTable::add($data);

                    if (!$result->isSuccess())
                    {
                        $this->addErrors([new Error('Произошла ошбика при добавлении сообщения, повторите попытку позже')]);
                    }
                }
                else
                {
                    $this->addErrors([new Error('Ваше сообщение короче 5 символов')]);
                }
            }
            else
            {
                $this->addErrors([new Error('Проверьте заполненность полей')]);
            }
        }catch (\Exception $e)
		{
            $this->addErrors([new Error($e->getMessage())]);
        }
    }

    /**
     * Start Component
     */
    public function executeComponent()
    {
        try
        {
            if (Loader::includeModule('saa.guestbook'))
            {
                global $USER;
                $this->arResult['AUTHORIZED'] = $USER->IsAuthorized();

                //размер страниц опционально вынести в параметры
                $nav = new \Bitrix\Main\UI\PageNavigation('page');
                $nav->allowAllRecords(true)->setPageSize(2)->initFromUri();

                $fileList = [];
                $userList = [];
                /** @var Bitrix\Main\ORM\Query\Result $iterator */
                $iterator = MessagesTable::getList([
                   'limit' => $nav->getLimit(),
                   'offset' => $nav->getOffset(),
                   'order' => ['ID' => 'desc']
                ]);
                while ($elem = $iterator->fetch())
                {
                    $this->arResult['ITEMS'][] = $elem;
                    if ($elem['FILE'])
                    {
                        $fileList[$elem['FILE']] = $elem['FILE'];
                    }

                    if ($elem['USER_ID'])
                    {
                        $userList[$elem['USER_ID']] = $elem['USER_ID'];
                    }
                }

                if (count($userList))
                {
                    $iterator = \Bitrix\Main\UserTable::getList([
                        'select' => ['ID', 'FULL_NAME'],
                        'filter' => ['ID' => $userList],
                        'runtime' => [
                            new \Bitrix\Main\Entity\ExpressionField('FULL_NAME', 'CONCAT_WS(\' \',NAME,LAST_NAME)')
                        ]
                    ]);
                    while ($user = $iterator->fetch())
                    {
                       $this->arResult['USER_LIST'][$user['ID']] = $user['FULL_NAME'];
                    }
                }

                if (count($fileList))
                {
                    $iterator = \Bitrix\Main\FileTable::query()
                        ->setSelect(['ID', 'SUBDIR', 'FILE_NAME'])
                        ->whereIn('ID', $fileList)
                        ->exec();

                    foreach ($iterator as $file)
                    {
                        $this->arResult['FILE_LIST'][$file['ID']] = '/upload/'.$file['SUBDIR'].'/'.$file['FILE_NAME'];
                    }
                }

                $nav->setRecordCount(MessagesTable::getList(['select' => ['ID']])->getSelectedRowsCount());
                $this->arResult['NAV_OBJECT'] = $nav;

            }
            $this->includeComponentTemplate();
            return true;
        }
        catch (Exception $e)
        {
            ShowError($e->getMessage());
        }
    }


    /**
     * Getting array of errors.
     * @return Error[]
     */
    final public function getErrors()
    {
        return $this->errorCollection->toArray();
    }

    /**
     * @return ErrorCollection
     */
    final public function getErrorsCollection()
    {
        return $this->errorCollection;
    }

    /**
     * @param string $code
     *
     * @return \Bitrix\Main\Error|null
     */
    final public function getErrorByCode($code)
    {
        return $this->errorCollection->getErrorByCode($code);
    }

    /**
     * Adds list of errors to error collection.
     *
     * @param Error[] $errors Errors.
     *
     * @return $this
     */
    protected function addErrors(array $errors)
    {
        $this->errorCollection->add($errors);

        return $this;
    }
}
