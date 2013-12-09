<?php
$vars = CMS::vars('GET', array('login', 'password'));
if (User::login($vars)) {
	//hallelujah!
	//TODO
	new DB('INSERT INTO UserSessions VALUES (...)')->param($vars)->exec();
	//generalnie redirect, rozwiązanie z dupy
	throw new ErrorHTTP('Redirect', 300);
} else {
	//chyba śnisz, walnij jakimś błędem
}

?>
    <form action="trocheinaczej.php" method="post">

            Login: <input type="text" name="login" size="3" maxlength="20" />
            Pass: <input type="text" name="password" size="3" maxlength="20" /> 
               <input type="submit" value="Zaloguj" />
    </form>
<?php
