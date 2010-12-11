<form action="admin.php?{$site}-cmr-{$id}" method="post">
<table class="border" cellspacing="1" cellpadding="2" width="350">
<tr class="Chead"><td colspan="2"><b>Modulerechte f&uuml;r {$name} &auml;ndern</b></td></tr>
{if isset($info)}
<tr class="Chead"><td colspan="2">{$info}</td></tr>
{/if}
{foreach $data as $row}
    <tr class="{cycle values="Cmite,Cnorm"}">
        <td>{$row.name}</td>
        <td><input type="checkbox" name="mid[]" value="{$row.id}" {if $row.hasright == 1}checked="checked"{/if} />
        {if isset($row.rightfrom) and ($row.rightfrom) and $row.hasright == 1 and $row.rightfrom == 1}
        <span title="Modulrecht durch Grundrecht"> [G]</span>
        {/if}
        </td>
    </tr>
{/foreach}
<tr class="Cdark"><td colspan="2"><input type="submit" value="&Auml;nderungen speichern" name="subCMR" />
<input type="button" onclick="parent.ic.modalDialogClose();" value="Schlie&szlig;en" />
</td></tr>
</table></form>