<?php
if (!CMS\Me::id())
	throw new Error403();
CMS\Me::logout();
throw new Redirect('/');
