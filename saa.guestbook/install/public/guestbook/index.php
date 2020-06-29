<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Гостевая книга");
?>
<?$APPLICATION->IncludeComponent(
    "saa:guestbook",
    "",
    [],
    false,
    ['HIDE_ICONS' => 'Y']
);?>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>