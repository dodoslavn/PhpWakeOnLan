<?

include('../config.php');

if (!empty($_GET['wol']))
	{
	$row = 1;
	$data = '';
	if (($handle = fopen($list_csv, "r")) !== FALSE) {
	    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
	        $num = count($data);
	        $row++;
		if ( $_GET['wol'] == $data[1] )
			{ $name = $data[1]; $mac = $data[0]; }
        	}
	    fclose($handle);
	    }
	if (!empty($name))
	    {
            exec($binary." ".$mac, $output, $retval);
            echo $output[0];
            echo "<br><br>";
	    }
        else { echo "WoL device was not foun in list!<br>\n"; }
	echo "<br>\n";
	}

echo "
<html>
<head>
	<title>WakeOnLan</title>
<head>
<body>
";

$row = 1;
if (($handle = fopen($list_csv, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        $num = count($data);
        $row++;
	echo $data[1]." (".$data[0].") - <a href='?wol=".$data[1]."'>WoL</a><br>\n";
        #for ($c=0; $c < $num; $c++) {
        #    echo $data[$c] . "<br />\n";
        #    }
        }
    fclose($handle);
    }

echo "</body></html>"
?>
