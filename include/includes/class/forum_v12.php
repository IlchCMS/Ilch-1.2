<?php

/**
 * @name    IlchBB Forum
 * @version 3.1
 * @author  Florian Koerner
 * @link    http://www.koerner-ws.de/
 * @license GNU General Public License
 */

class ilchBB
{
    var $newTopics = array();

    function ilchBB()
    {
        //

        // Neue Beitraege abfragen
        $this->getNewTopics();
    }

    /**
     * Fragt alle neuen Topics ab
     *
     * @return bolean
     */
    function getNewTopics()
    {
        // Abbruch, wenn nicht eingeloggt
        if (!loggedin()) return FALSE;

        // Topics aus der User-Datenbank
        $query = db_query('SELECT `ilchbb_lastquery`,`ilchbb_newtopics` FROM `prefix_user` WHERE `id` = '.$_SESSION['authid']);
        $result = db_fetch_assoc($query);

        // Zeit zwischenspeichern
        $lastQuery = $result['ilchbb_lastquery'];

        // Ergebnisse umwandeln
        $array = unserialize($result['ilchbb_newtopics']);

        // Topics aus Foren-Datenbank
        $query = 'SELECT `a`.`id` AS `tid`, `b`.`id` AS `fid`
                    FROM `prefix_topics` AS `a`
                      LEFT JOIN `prefix_forums` AS `b` ON `b`.`id` = `a`.`fid`
                      LEFT JOIN `prefix_posts` AS `c` ON `c`.`id` = `a`.`last_post_id`
                      LEFT JOIN `prefix_groupusers` AS `vg` ON `vg`.`uid` = '.$_SESSION['authid'].' AND `vg`.`gid` = `b`.`view`
                      LEFT JOIN `prefix_groupusers` AS `rg` ON `rg`.`uid` = '.$_SESSION['authid'].' AND `rg`.`gid` = `b`.`reply`
                      LEFT JOIN `prefix_groupusers` AS `sg` ON `sg`.`uid` = '.$_SESSION['authid'].' AND `sg`.`gid` = `b`.`start`
                    WHERE (('.$_SESSION['authright'].' <= `b`.`view` AND `b`.`view` < 1)
                      OR ('.$_SESSION['authright'].' <= `b`.`reply` AND `b`.`reply` < 1)
                      OR ('.$_SESSION['authright'].' <= `b`.`start` AND `b`.`start` < 1)
                      OR `vg`.`fid` IS NOT NULL
                      OR `rg`.`fid` IS NOT NULL
                      OR `sg`.`fid` IS NOT NULL
                      OR -9 >= '.$_SESSION['authright'].')
                      AND `c`.`time` >= '.$lastQuery.'
                      AND `c`.`erstid` != '.$_SESSION['authid'];
        $query = db_query($query);

        // Gefundene Eintraege in Array speichern
        while ($result = db_fetch_assoc($query))
        {
            if (!isset($array[$result['fid']][$result['tid']]))
                $array[$result['fid']][$result['tid']] = $lastQuery;
        }

        // Array in Class speichern
        if (is_array($array)) {
            $this->newTopics = $array;
        }        

        // Array fuer Datenbank vorbereiten
        $array = escape(serialize($array),'string');

        // Array in Datanbank speichern
        db_query('UPDATE `prefix_user` SET `ilchbb_lastquery` = '.time().', `ilchbb_newtopics` = "'.$array.'" WHERE `id` = '.$_SESSION['authid']);

        // Erfolg
        return TRUE;
    }

    /**
     * Prueft, ob neue Beitraege vorhanden sind
     *
     * @param integer $fid Forum-ID
     * @param integer $tid Topic-ID
     * @return bolean
     */
    function checkNewTopics($fid, $tid = 0)
    {
        // Gelesen, wenn nicht eingeloggt
        if (!loggedin()) return FALSE;

        // Pruefen, ob neue Beitraege
        if ((isset($this->newTopics[$fid]) AND $tid == 0)
                OR (isset($this->newTopics[$fid][$tid])))
        {
            return TRUE;
        }

        // Keine neuen Beitraege
        return FALSE;
    }

    /**
     * Fragt den neusten Post, mitsamt Seite ab
     *
     * @param integer $fid Foren-ID
     * @param integer $tid Topic-ID
     * @param integer $Fpanz Posts pro Seite
     * @return array
     */
    function newestPostPage($fid, $tid, $Fpanz)
    {
        // Standard-Array
        $array = array(1,'');

        // Abbruch, wenn nicht eingeloggt
        if (!loggedin()) return $array;

        // Pruefen, ob Datensatz (noch) vorhanden
        if (isset($this->newTopics[$fid][$tid]))
        {
            $time = $this->newTopics[$fid][$tid];

            $posts = @db_result(db_query('SELECT count(`id`) FROM `prefix_posts` WHERE `tid` = '.$tid.' AND `time` <= '.$time),0);
            $fpost = @db_result(db_query('SELECT `id` FROM `prefix_posts` WHERE `tid` = '.$tid.' AND `time` <= '.$time.' ORDER BY `id` DESC LIMIT 1'),0);

            if ($posts == 0) $posts = 1;
            
            $page = ceil ( ($posts)  / $Fpanz );
            $array = array($page, $fpost);
        }

        // Ergebnisse als Array zurueckgeben
        return $array;
    }

    /**
     * Prüft, ob ein Beitrag neu ist
     *
     * @param integer $fid  Foren-ID
     * @param integer $tid  Topic-ID
     * @param integer $time Timestamp des Beitrages
     * @return bolean
     */
    function checkPostTime($fid, $tid, $time)
    {
        // Kein neuer Beitrag, wenn nicht eingeloggt, kein Datensatz oder Erstellzeit frueher
        if (!loggedin() OR !isset($this->newTopics[$fid][$tid]) OR $this->newTopics[$fid][$tid] > $time)
            return FALSE;

        // Neuer Beitrag
        return TRUE;
    }

    /**
     * Loescht Datensaetze aus dem NewTopics-Array
     *
     * @param integer $fid Forum-ID
     * @param integer $tid Topic-ID
     * @return void
     */
    function deleteNewTopics($fid = 0, $tid = 0)
    {
        // Abbruch, wenn nicht eingeloggt
        if (!loggedin()) return;

        // Entsprechende Eintraege loeschen
        if ($fid == 0 AND $tid == 0)
        {
            $this->newTopics = array();
        }
        else if ($tid == 0)
        {
            // Abbruch, wenn kein Datensatz
            if (!isset($this->newTopics[$fid]))
                    return;

            // Datensatz entfernen
            unset($this->newTopics[$fid]);
        }
        else
        {
            // Abbruch, wenn kein Datensatz
            if (!isset($this->newTopics[$fid][$tid]))
                    return;

            // Datensatz entfernen
            unset($this->newTopics[$fid][$tid]);

            // Auch Foren-ID leeren, wenn keine weiteren neuen Beitraege
            if (count($this->newTopics[$fid]) == 0)
                    $this->deleteNewTopics($fid);
        }

        // Array fuer Datenbank vorbereiten
        $array = escape(serialize($this->newTopics),'string');

        // Array in Datanbank speichern
        db_query('UPDATE `prefix_user` SET `ilchbb_newtopics` = "'.$array.'" WHERE `id` = '.$_SESSION['authid']);
    }

    /**
     * Gibt einen String mit Thead-IDs zurueck
     *
     * @return string
     */
    function showNewTopics()
    {
        // Leeren Cache-String schreiben
        $cache = '0';

        // Abbruch, wenn nicht eingeloggt
        if (!loggedin()) return $cache;

        // Cache-String mit Topic-IDs fuellen
        foreach ($this->newTopics AS $topic)
        {
            foreach ($topic AS $tid => $time)
            {
                $cache .= ', '.$tid;
            }
        }

        // Cache-String zurueckgeben
        return $cache;
    }
}

?>