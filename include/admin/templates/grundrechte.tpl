<form action="admin.php?grundrechte" method="post">
<table cellspacing="1" cellpadding="5" broder="0" class="border">
    <tr><td colspan="2" class="Cdark">
            <input class="sub" type="submit" value="&Auml;nderungen speichern" name="subchange" />
    </td><td class="Cmite">Auf <img src="include/images/icons/edit.png" border="0" title="Modulrechte &auml;ndern" alt="&auml;ndern" /> klicken die einem Grundrecht zugeordneten Modulrechte zu sehen bzw zu &auml;ndern.</td>
    </tr>
    {foreach $grundrechte as $row}
        <tr class="{cycle values="Cmite,Cnorm"}">
          <td><input name="gr{$row.id}" value="{$row.name}" /></td>
          <td><a class="CMR" grid="{$row.id}" href="javascript:void();"><img src="include/images/icons/edit.png" border="0" title="Modulrechte &auml;ndern" alt="&auml;ndern" /></a></td>
          <td>{$descs[$row.id]}</td>
        </tr>
    {/foreach}
    <tr><td colspan="3" class="Cdark"><input class="sub" type="submit" value="&Auml;nderungen speichern" name="subchange" /></td></tr>
</table></form>
<script type="text/javascript">
$(document).ready(function() {
    $('a.CMR').click(function() {
        var grid = $(this).attr('grid');
        ic.modalDialog({
            url: 'admin.php?grundrechte-cmr' + grid,
            title: 'Modulrechte ändern',
            width: 450,
            height: 500
        });
    });
});
</script>