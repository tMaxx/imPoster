<?php $this->guard_user();
CMS\Me::logout();
throw new Redirect('/');
