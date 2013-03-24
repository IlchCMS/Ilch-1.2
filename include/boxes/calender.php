<?php

/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');

$tpl = new tpl('boxes/calender');

$ILCH_HEADER_ADDITIONS .=
        "\n" . '<script type="text/javascript"> ' .
        "\n" . '    $(function() { ' .
        "\n" . '        $( "#datepicker" ).datepicker({ ' .
        "\n" . '            autoSize: true, ' .
        "\n" . '            monthNames: [\'Januar\',\'Februar\',\'März\',\'April\',\'Mai\',\'Juni\',\'Juli\',\'August\',\'September\',\'Oktober\',\'November\',\'Dezember\'], ' .
        "\n" . '            monthNamesShort: [\'Jan\',\'Feb\',\'Mär\',\'Apr\',\'Mai\',\'Jun\',\'Jul\',\'Aug\',\'Sep\',\'Okt\',\'Nov\',\'Dez\'], ' .
        "\n" . '            dayNamesMin: [\'So\', \'Mo\', \'Di\', \'Mi\', \'Do\', \'Fr\', \'Sa\'], ' .
        "\n" . '            showWeek: true, ' .
        "\n" . '            changeMonth: true, ' .
        "\n" . '            changeYear: true, ' .
        "\n" . '            firstDay: 1, ' .
        "\n" . '            dateFormat: \'dd.mm.yy\', ' .
        "\n" . '            autoSize: true, ' .
        "\n" . '            buttonText: \'Choose\', ' .
        "\n" . '            yearRange: \'c-10:c+10\', ' .
        "\n" . '            onSelect: function(dateText, inst) { ' .
        "\n" . '            document.form1.submit(); ' .
        "\n" . '            var loc = \'\'; ' .
        "\n" . '            var datesplit = dateText.split(\'.\'); ' .
        "\n" . '            location.href = loc+\'?kalender-v1-m\'+datesplit[1]+\'-y\'+datesplit[2]+\'-d\'+datesplit[0]; ' .
        "\n" . '        } ' .
        "\n" . '    }); ' .
        "\n" . '}); ' .
        "\n" . '</script>' .
        "\n";

$tpl->out(0);
