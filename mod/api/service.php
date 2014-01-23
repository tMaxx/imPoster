<? CMS::setContentType('json');
$response = array();
$cmd = CMS\Vars::post('command');

if ($cmd) {
	$response = CMS\Vars::post();

} else
	$response = [
		'status' => false,
		'error_msg' => 'No command provided'
	];


$flags = JSON_FORCE_OBJECT | JSON_BIGINT_AS_STRING;
if (CMS\Vars::get(array('pp')))
	$flags |= JSON_PRETTY_PRINT;
echo json_encode($response, $flags);
