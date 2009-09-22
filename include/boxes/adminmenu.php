<?php
// Copyright by Manuel
// Support www.ilch.de
defined ('main') or die ('no direct access');
if (is_coadmin()) { ?><script language="JavaScript" type="text/javascript">
<!--

  function createNewUser() {
    var Fenster = window.open ('admin.php?user-createNewUser', 'createNewUser', 'status=yes,scrollbars=yes,height=200,width=350,left=300,top=50');
    Fenster.focus();
  }
//-->
</script><?php
}

?>
<SCRIPT LANGUAGE="JavaScript"><!--
var myMenu =
[
    [null, 'Seite', './', null, null],
<?php
if (is_coadmin()) { ?>

    [null, 'Admin', 'admin.php?admin', null, null,
      <?php if (is_admin()) { ?>
      ['<img src="include/images/icons/admin/konfiguration.png" width="16" height="16">', 'Konfiguration', 'admin.php?allg', null, null],
      <?php }
      if ($allgAr['mail_smtp']) { ?>
      ['<img src="include/images/icons/admin/smtpconf.png" width="16" height="16">', 'SMPT Konfiguration', 'admin.php?smtpconf', null, null],
      <?php } ?>
      ['<img src="include/images/icons/admin/navigation.png" width="16" height="16">', 'Navigation', 'admin.php?menu', null, null],
      <?php if (is_admin()) { ?>
      ['<img src="include/images/icons/admin/backup.png" width="16" height="16">', 'Backup', 'admin.php?backup', null, null],
      <?php } ?>
      ['<img src="include/images/icons/admin/ranks.png" width="16" height="16">', 'Ranks', 'admin.php?range', null, null],
      ['<img src="include/images/icons/admin/smilies.png" width="16" height="16">', 'Smilies', 'admin.php?smilies', null, null],
      ['<img src="include/images/icons/admin/newsletter.png" width="16" height="16">', 'Newsletter', 'admin.php?newsletter', null, null],
      ['<img src="include/images/icons/admin/version_check.png" width="16" height="16">', 'Versions Kontrolle', 'admin.php?admin-versionsKontrolle', null, null],
      ['<img src="include/images/icons/admin/version_check.png" width="16" height="16">', 'Server Konfiguration', 'admin.php?checkconf', null, null],
      ['<img src="include/images/icons/admin/stats_site.png" width="16" height="16">', 'Statistik', null, null, null,
        ['<img src="include/images/icons/admin/stats_visitor.png" width="16" height="16">', 'Besucher', 'admin.php?admin-besucherStatistik', null, null],
        ['<img src="include/images/icons/admin/stats_site.png" width="16" height="16">', 'Seite', 'admin.php?admin-siteStatistik', null, null],
        ['<img src="include/images/icons/admin/stats_online.png" width="16" height="16">', 'Online', 'admin.php?admin-userOnline', null, null],
      ],
    ],

    [null, 'User', 'admin.php?user', null, null,
      ['<img src="include/images/icons/admin/user.png" width="16" height="16">', 'Verwalten', 'admin.php?user', null, null],
      <?php if (is_admin()) { ?>
      ['<img src="include/images/icons/admin/user_rights.png" width="16" height="16">', 'Grundrechte', 'admin.php?grundrechte', null, null],
      <?php } ?>
      ['<img src="include/images/icons/admin/user_profile_fields.png" width="16" height="16">', 'Profilefelder', 'admin.php?profilefields', null, null],
      ['<img src="include/images/icons/admin/user_add.png" width="16" height="16">', 'neuen User', 'javascript: createNewUser();',null ,null],
    ],

    [null, 'Eigene Box/Page', 'admin.php?selfbp', null, null],

    [null, 'Clanbox', null, null, null,
      ['<img src="include/images/icons/admin/wars_next.png" width="16" height="16">', 'Nextwars', 'admin.php?wars-next', null, null],
      ['<img src="include/images/icons/admin/wars_last.png" width="16" height="16">', 'Lastwars', 'admin.php?wars-last', null, null],
      ['<img src="include/images/icons/admin/teams.png" width="16" height="16">', 'Teams', 'admin.php?groups', null, null],
      ['<img src="include/images/icons/admin/awards.png" width="16" height="16">', 'Awards', 'admin.php?awards', null, null],
      ['<img src="include/images/icons/admin/kasse.png" width="16" height="16">', 'Kasse', 'admin.php?kasse', null, null],
      ['<img src="include/images/icons/admin/rules.png" width="16" height="16">', 'Rules', 'admin.php?rules', null, null],
      ['<img src="include/images/icons/admin/history.png" width="16" height="16">', 'History', 'admin.php?history', null, null],
      ['<img src="include/images/icons/admin/training_times.png" width="16" height="16">', 'Trainzeiten', 'admin.php?trains', null, null],
    ],

    [null, 'Content', null, null, null,
      ['<img src="include/images/icons/admin/news.png" width="16" height="16">', 'News', 'admin.php?news', null, null],
      ['<img src="include/images/icons/admin/forum.png" width="16" height="16">', 'Forum', 'admin.php?forum', null, null],
      ['<img src="include/images/icons/admin/downloads.png" width="16" height="16">', 'Downloads', 'admin.php?archiv-downloads', null, null],
      ['<img src="include/images/icons/admin/links.png" width="16" height="16">', 'Links', 'admin.php?archiv-links', null, null],
      ['<img src="include/images/icons/admin/gallery.png" width="16" height="16">', 'Gallery', 'admin.php?gallery', null, null],
      ['<img src="include/images/icons/admin/guestbook.png" width="16" height="16">', 'Gbook', 'admin.php?gbook', null, null],
      ['<img src="include/images/icons/admin/vote.png" width="16" height="16">', 'Umfrage', 'admin.php?vote', null, null],
      ['<img src="include/images/icons/admin/calendar.png" width="16" height="16">', 'Kalender', 'admin.php?kalender', null, null],
      ['<img src="include/images/icons/admin/contact.png" width="16" height="16">', 'Kontakt', 'admin.php?contact', null, null],
      ['<img src="include/images/icons/admin/imprint.png" width="16" height="16">', 'Impressum', 'admin.php?impressum', null, null],
      [null, 'Boxen', null, null, null,
        ['<img src="include/images/icons/admin/partners.png" width="16" height="16">', 'Partner', 'admin.php?archiv-partners', null, null],
        ['<img src="include/images/icons/admin/picofx.png" width="16" height="16">', 'Pic of X', 'admin.php?picofx', null, null],
      ],
    ],

    [null, 'Module', null, null, null,
		<?php
    $erg = db_query("SELECT `url`, `name` FROM `prefix_modules` WHERE `ashow` = 1");
    while ($row = db_fetch_assoc($erg)) {
        echo '[null, \'' . $row['name'] . '\', \'admin.php?' . $row['url'] . '\', null, null],' . "\n";
    }

    ?>
    ]

	<?php
} elseif (count($_SESSION['authmod']) > 0) {
    echo "[null, 'Module', null, null, null,";
    $q = "SELECT DISTINCT `url`, `name`
	FROM `prefix_modulerights` `a`
	LEFT JOIN `prefix_modules` `b` ON `b`.`id` = `a`.`mid`
	WHERE `b`.`gshow` = 1 AND `uid` = " . $_SESSION['authid'];
    $erg = db_query($q);
    while ($row = db_fetch_assoc($erg)) {
        echo '[null, \'' . $row['name'] . '\', \'admin.php?' . $row['url'] . '\', null, null],' . "\n";
    }
    echo "],";
}

?>
];
--></SCRIPT>