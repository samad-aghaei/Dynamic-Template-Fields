<?php
//header("Content-Type: application/json;charset=utf-8");

require_once 'lib/router.php';
require_once 'lib/db.php';

$tpl = json_decode( shell_exec('node assets/script.js assets/template.html 2>&1'), true);

function process_post_data( $tpl, &$data, $DB )
{
	if( !empty( $data ) )
	{
		$data = array_intersect_key($data, $tpl);
		unset($data['users']['ID']);
		unset($data['users']['register_date']);

		$users = null;
		$settings = null;

		foreach( $data as $key => $value )
		{
			if( is_array( $value ) )
			{
				$users = [ $key, implode(',', array_map(function($str){return sprintf("`%s`", $str);}, array_keys($value))), implode(',', array_map(function($str){return sprintf("'%s'", $str);}, array_values($value))) ];
				unset($data[$key]);
			}
		}

		$settings = [ 'settings', implode(',', array_map(function($str){return sprintf("`%s`", $str);}, array_keys($data))), implode(',', array_map(function($str){return sprintf("'%s'", $str);}, array_values($data))) ];

		$DB->query( "INSERT INTO ". $settings[0] ." ( ". $settings[1] ." ) VALUES ( ". $settings[2] ." )" );
		$DB->query( "INSERT INTO ". $users[0] ." ( ". $users[1] ." ) VALUES ( ". $users[2] ." )" );

		$data = 'Thank you!';
	}
	else
	{
		$data = 'Error: Empty Data';
	}

	return $data;
}

if( $matches && isset( $matches['action']) && $matches['action'] === 'admin' )
{
	if ( $_SERVER['REQUEST_METHOD'] === 'POST')
	{
		$isRestful = false;

		if( getallheaders()['Content-Type'] === 'application/json' )
		{
			$data = json_decode( file_get_contents('php://input'), true );
			$isRestful = true;
		}
		else
		{
			$data = $_POST;
		}
		
		$data = process_post_data( $tpl, $data, $DB, 'insert' );

		if( $isRestful )
		{
			echo json_encode([
				'message' => $data
			]);
		}
		else
		{
			echo $data;
		}
	}
	else
	{
		echo '<form action="/dynamic-fields/admin" method="post">';

		foreach( $tpl as $key => $element )
		{
			if( is_array($element) )
			{
				foreach( $element as $k => $el )
				{
					echo ucfirst($k) . ': ' . '<input type="text" name="users[' . $k . ']" /><br>';
				}
			}
			else
			{
				echo ucfirst($key) . ': ' . '<input type="text" name="' . $key . '" /><br>';
			}
		}

		echo '<input type="submit" /></form>';		
	}
} 
else
{
	$users = null;
	$settings = null;

	foreach( $tpl as $key => $value )
	{
		if( is_array( $value ) )
		{
			$users = $DB->query( "SELECT ". implode(',', array_keys($value)) ." From ". $key ."" )->fetch_all(MYSQLI_ASSOC);
			unset($tpl[$key]);
		}
	}

	$settings = $DB->query( "SELECT ". implode(',', array_keys($tpl)) ." From settings" )->fetch_array(MYSQLI_ASSOC);


	if ( $_SERVER['REQUEST_METHOD'] === 'GET' && isset( getallheaders()['json'] ) )
	{
		$settings['users'] = $users;

		ob_end_clean();
		header("Content-Type: application/json;charset=utf-8");

		die(
			json_encode( $settings ) . PHP_EOL
		);
	}

	$users    = json_encode($users);
	$settings = json_encode($settings);

	echo shell_exec("node assets/script.js assets/template.html '{\"users\":{$users}}' '{$settings}'");
}








