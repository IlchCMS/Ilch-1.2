<script type="text/javascript">
ic.loadFile({
    file: 'include/includes/js/shoutbox.js',
    type: 'js',
    onload: function() {
        $(ic.shoutboxArchiveOnload);
    }
});
</script>
<form id="shb_form" action="index.php?shoutbox" method="post">
{$antihack}
<table width="100%" align="center" class="border" cellpadding="2" cellspacing="1" border="0">
<tr class="Chead"><td><b>Shoutbox {$lang['archiv']}</b></td></tr>
{foreach $data as $key=>$row}
    <tr class="{cycle values="Cmite,Cnorm"}" id="shb_tr_{$key}"><td>
    <b>{$row['nickname']}:</b> {if $row['time'] != 0}<span class="smalfont">{$row['time']}</span>{/if}
    {if $siteadmin}<input type="checkbox" name="chk[]" value="{$key}" title="Mit Doppelklick löschen" />{/if}
    <br />{$row['textarea']}</td></tr>
{/foreach}
{if $multipages != ''}
    <tr class="Cdark"><td align="center" id="shb_multipages">{$multipages}</td></tr>
{/if}
{if $siteadmin}
    <tr class="Cdark"><td><button id="shb_delall">{$lang['clearshoutbox']}</button> <button id="shb_delsel">markierte l&ouml;schen</button></td></tr>
{/if}
</table></form>