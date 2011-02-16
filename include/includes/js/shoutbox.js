function del() 
{
  if (anz = prompt(unescape("Wie viele Eintr%E4ge sollen erhalten bleiben%3F\n(Es werden die zuletzt geschriebenen erhalten)", "0")))
  {
    if (anz >= 0) 
	  { window.location.href = "index.php?shoutbox-delall-"+anz; } 
	  else alert(unescape("Du musst eine Zahl gr%F6ßer gleich 0 eingeben"));
  }
}