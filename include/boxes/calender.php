<?php
	if (AJAXCALL) {
		$widgetCalling='kalender';
		include 'include/contents/kalender.php';
		$list = '<ul class="kalenderwidget">';
		$lastEl = false;
		$anzahl = count($kalenderwidget)-1;
		$i = 0;
		foreach ($kalenderwidget as $datum => $value) {

			if ($i == $anzahl) {
				$lastEl = 'class="last"';
			}
			$list .= '<li ' . $lastEl . '>' . $datum;
			foreach ($value as $value2) {
				$list .= '<br/><a href="index.php?kalender-v0-e' . $value2['id'] .  '" title="Zum Eintrag">' . $value2['title'] . '</a>';
			}
			$list .= '</li>';
			$i++;
		}
		$list .= '</ul>';
		echo json_encode( array('content'=> $list ) );
		exit;
	}
?>
<script>
$(function() {
	$.ajax({
	  url: "index.php",
	  data: "calender&boxreload=true&ajax=true",
	  dataType: "json",
	  success: function(response){
		$('#kalenderwidget').html(response.content);
	  },
	  error: function(response){
		$('#kalenderwidget').html('<?php echo $lang['error_occured'] ?>');
	  },
	});
});
</script>
<noscript>
Bitte JavaScript aktivieren
</noscript>
<div id="kalenderwidget">
	<p style="text-align:center;">
		<img src="include/images/ajax-loader.gif" alt="Content wird geladen"/>
	</p>
</div>

<p></p>
