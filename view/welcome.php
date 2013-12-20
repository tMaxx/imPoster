Be welcomed, my dear user!
<br><br>
Elem dump below:
<?
$q = DB("Elem");
pre_dump($q, DB("SELECT * FROM List"));
// pre_dump($q, new DB("Elem"));pre_dump($q->obj('Elem'));