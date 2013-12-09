<?php
    class login 
    {
        public function login()
        {
            
        }
    }
?>
    <form action="trocheinaczej.php" method="post">

            Login: <input type="text" name="login" size="3" maxlength="20" />
            Pass: <input type="text" name="password" size="3" maxlength="20" /> 
               <input type="submit" value="Zaloguj" />
    </form>
<?php
   $login = CMS::vars('GET', array('login', 'password'));
   
// yhmm tu wywołaś logn() ? 