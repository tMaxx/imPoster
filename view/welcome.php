Be welcomed, my dear user!
<br><br>
Elem dump below:
<?
$q = DB("Elem")->select('*')->where('content LIKE "%a%"'); // TODO
pre_dump(/*$q, */$q->rows(), $q);
// pre_dump($q, );pre_dump($q->obj('Elem'));