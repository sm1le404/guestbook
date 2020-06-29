<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
$this->addExternalCss("/bitrix/css/main/bootstrap.css");?>
<div class="container">
    <?if (count($arResult['ITEMS']) && is_array($arResult['ITEMS'])):?>
        <?foreach ($arResult['ITEMS'] as $item):?>
        <div class="row">
            <div class="col-12">
                <div class="alert alert-primary" role="alert">
                    <?=$item['DATETIME']?><br/>
                    <?=$arResult['USER_LIST'][$item['USER_ID']] ? $arResult['USER_LIST'][$item['USER_ID']] : 'Анонимный пользователь'?>:
                    <p> <?=$item['TEXT']?></p>
                    <?if ($arResult['FILE_LIST'][$item['FILE']]):?>
                        <p><a href="<?=$arResult['FILE_LIST'][$item['FILE']]?>" target="_blank">Скачать файл</a></p>
                    <?endif?>
                </div>
            </div>
        </div>
        <?endforeach;?>
    <?else:?>
        В данной книге пока нет сообщений, будьте первым!
    <?endif?>
</div>

<?
$APPLICATION->IncludeComponent(
    "bitrix:main.pagenavigation",
    "",
    array(
        "NAV_OBJECT" => $arResult['NAV_OBJECT'],
        "SEF_MODE" => "N",
    ),
    false
);
?>

<div class="container">
    <div class="row">
        <div class="col-6">
            <form method="post" action="" id="messageForm">
                <?=bitrix_sessid_post()?>
                <fieldset class="form-group">
                    <label for="text">Текст сообщения</label>
                    <input type="text" id="text" class="form-control" name="text" minlength="5">
                </fieldset>
                <?if ($arResult['AUTHORIZED']):?>
                    <fieldset class="form-group">
                        <label for="file">Файл</label>
                        <input type="file" id="file" class="form-control-file" name="file">
                    </fieldset>
                <?endif?>
                <button type="submit" class="btn btn-primary">Отправить</button>
            </form>
        </div>
        <div class="col-6"></div>
    </div>
</div>