<?php
if (!CMS\Me::id())
	throw new Error403();
