<!DOCTYPE html>
<?php 
	session_start();
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == false){
			header("location: index.php");
			exit;
		}
		$idUtilizator = $_SESSION["id"];
require_once("connect.php"); 
require_once("autorizare.php");
if(isset($_GET["programare"]) && $_GET["programare"]==1){
	$idServiciu = $_GET["consultatie"];
	$idMedic = $_GET["medic"];
	$data = $_GET["data"];
	$detalii = $_GET["detalii"];
	$idUtilizator = $_SESSION["id"];
	$sql = "INSERT INTO programari(idServiciu,idMedic,data,idUtilizator,detaliiSuplimentare,idStatus) VALUES('$idServiciu','$idMedic','$data','$idUtilizator','$detalii',1);";
	mysqli_query($link, $sql);
	if($link->affected_rows > 0){
	echo "
		<div class='alert' style='overflow:auto;margin-top:5px;background-color:green'>
 					 <p>Programare creata cu succes.</p>
		</div> 
	";
	header( "refresh:3;url=members-side.php" );
	}
}
if(isset($_GET["sterge"])){
	$idSterge = $_GET["sterge"];
	$sql2="UPDATE programari SET idStatus=2 WHERE id=$idSterge;";
	$del = mysqli_query($link,$sql2);
	if($del)
{
	echo "
		<div class='alert' style='overflow:auto;margin-top:5px;background-color:blue'>
 					 <p>Programare anulata cu succes.</p>
		</div> 
	";
    header("refresh:3;url=members-side.php"); // redirect daca se ruleaza query-ul	
}
else
{
    echo "Eroare"; // afiseaza eroare daca nu se ruleaza query
}

}

if(isset($_POST["schimbaparola"]) && $_POST["schimbaparola"]==1){
	$parolacurenta = $_POST["parolacurenta"];
	$parolacurenta2 = ltrim($_POST["parolanoua"]);
	$parolanoua = $_POST["parolanoua"];
	$parolanouac = $_POST["parolanouac"];
	$idUtilizator = $_SESSION["id"];
	if(strlen($parolanoua) > 6){
	if($parolanouac == $parolanoua){
		$sql = "SELECT parola FROM utlizatori WHERE id = $idUtilizator;";
		$result = mysqli_query($link,$sql);
		WHILE($row = mysqli_fetch_array($result))
		{
			$parolac = $row["parola"];
		}
		if(password_verify($parolacurenta,$parolac)){
			$parolahash = password_hash($parolacurenta2, PASSWORD_DEFAULT);
			$sql = "UPDATE utlizatori SET parola = '$parolahash' WHERE id=$idUtilizator;";
			mysqli_query($link,$sql);
				echo "
				<div class='alert' style='overflow:auto;margin-top:5px;background-color:green'>
  				 <span class='closebtn' onclick='this.parentElement.style.display='none';''>&times;</span>
 					 <p>Ati schimbat parola cu succes.</p>
				</div> ";
				 echo $parolacurenta2;
				 echo $parolahash;
				 header("refresh:3;url=logout.php");
		}
		else{
				echo "
				<div class='alert' style='overflow:auto;margin-top:5px;background-color:red'>
  				 <span class='closebtn' onclick='this.parentElement.style.display='none';''>&times;</span>
 					 <p>Parola introdusa nu este corecta.</p>
				</div> ";
				echo $parolacurenta;
		}
	}
	else{
				echo "
				<div class='alert' style='overflow:auto;margin-top:5px;background-color:red'>
  				 <span class='closebtn' onclick='this.parentElement.style.display='none';''>&times;</span>
 					 <p>Parolele nu coincid.</p>
				</div> ";
	}

}
	else{
				echo "
				<div class='alert' style='overflow:auto;margin-top:5px;background-color:red'>
  				 <span class='closebtn' onclick='this.parentElement.style.display='none';''>&times;</span>
 					 <p>Parola trebuie sa aibe peste 6 caractere.</p>
				</div> ";
	}
}
if(isset($_POST["updateSetari"]) && $_POST["updateSetari"]==1){
	$nume = $_POST["nume"];
	$prenume = $_POST["prenume"];
	$email = $_POST["email"];
	$tara = $_POST["tara"];
	$idUtilizator = $_SESSION["id"];

	if(strlen($nume) > 2 && strlen($prenume) > 2){
		if(file_exists($_FILES['pozaprofil']['tmp_name']) || is_uploaded_file($_FILES['pozaprofil']['tmp_name'])){
			$poza=fopen($_FILES["pozaprofil"]["tmp_name"],"rb");
			$continutpoza=addslashes(fread($poza,$_FILES["pozaprofil"]["size"]));
			$tip_poza=$_FILES["pozaprofil"]["type"];
			$sql = "UPDATE utlizatori SET nume='$nume',prenume='$prenume',email='$email',tara='$tara',poza_profil='$continutpoza',tip_poza = '$tip_poza' WHERE id=$idUtilizator;";
		
		}
		else{
			$sql = "UPDATE utlizatori SET nume='$nume',prenume='$prenume',email='$email',tara='$tara' WHERE id=$idUtilizator;";
		}
		$_SESSION["nume"] = $nume;
		$_SESSION["prenume"] = $prenume;
		$_SESSION["tara"] = $tara;
		$_SESSION["email"] = $email;
		$_SESSION["profilepic"] = "afiseazapoza.php?ID=$idUtilizator";
		mysqli_query($link,$sql);
		echo "
			<div class='alert' style='overflow:auto;margin-top:5px;background-color:green'>
  				 <span class='closebtn' onclick='this.parentElement.style.display='none';''>&times;</span>
 					 <p>Modificari efectuate cu succes.</p>
			</div> ";
			header("refresh:3;url=members-side.php");
	}

}
?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>User CP</title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="stylesheet" type="text/css" href="styles/paginamembrustyles.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/styles.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"/>
	<script src="https://kit.fontawesome.com/c684b69a55.js" crossorigin="anonymous"></script>
</head>
<body style="background-color:#f44336">
	<!--<div class="background-image"></div>-->
	<div class="content" style="padding-bottom:20px;">
		
			<center><h1 style="text-decoration:underline; color:white;display"><a class="fas fa-backward" href="index.php" style="display: inline-flex;float:left;color:white;text-decoration: none;"></a>PANOUL UTILIZATORULUI</h1></center>
			<?php
			$sql = "SELECT * FROM programari WHERE idUtilizator = ".$_SESSION["id"]." AND data = '".date('Y-m-d')."' AND idStatus = 1;";
			$result = mysqli_query($link,$sql);
			$count = 0;
			WHILE($row = mysqli_fetch_array($result)){
				$count++;
			}
			if($count != 0){ ?>
			<div class="alert" style="overflow:auto;margin-top:5px;background-color:black;">
  				<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
 					 <p>Aveti o programare astazi.</p>
		</div> 
	<?php }	?>
	<div class="center" style="backround-color:green;">
		<div class="statusbar" style="width:100%;background-color:white;margin-top: 1%;opacity: 0.9;max-height:19%;border-radius: 50px;">
		<center><div class="img-circle text-center mb-3">
			<img src="<?php echo "afiseazapoza.php?ID=$idUtilizator"; ?>" alt="Image" style="top:20%;margin-right:3%" class="shadow">
		</div></center>

			<div class="meniu-stanga" style="width:100%;padding-bottom:2%">
			<a class="button2" onclick=" openCity(event, 'content2')" style="border-radius:50px">Setari Cont</a>
			<a class="button2" onclick=" openCity(event, 'content3')" style="border-radius:50px">Programeaza-te</a>
			<a class="button2" onclick=" openCity(event, 'content4')" style="border-radius:50px">Programari</a>
			<a class="button2" onclick=" openCity(event, 'content5')" style="border-radius:50px">Postari apreciate</a>
		</div>
		</div>
	</div>
		<div class="content2" style="width:100%;height: 100%;background-color:white;border-radius:50px;">
			<!-------------------- CONTENT 1 ---------------------->
			<div class="tabcontent" id="content2" style="<?php if(empty($_GET["categorie"])) echo "display:block;"; else echo "display:none;" ?>">
			<center><h1 style="padding-top:1%"><i class="fas fa-cog"></i> Setări<hr/></h1></center>
			<center>
			<form method="POST" enctype="multipart/form-data">
				<div class="row" style="width:80%;">
				<div class="col-md-6">
					<div class="form-group">
						<label>Nume</label>
						<input type="text" name="nume" class="form-control" value="<?php echo $_SESSION['nume']?>" required>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Prenume</label>
						<input type="text" name="prenume" class="form-control" value="<?php echo $_SESSION['prenume']?>" required>
					</div>
				</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Email</label>
							<input type="email" name="email" class="form-control" value="<?php echo $_SESSION['email']?>" required>
						</div>
					</div>
					<div class="col-md-6">
						<label>Țara</label>
						<div class="form-group">
        					  <select id="country" class="form-control"  name="tara">
        					  	<?php
        					  		$countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");

        					  		foreach($countries as $country)
        					  			if($country == $_SESSION["tara"])
        					  			echo "<option value='$country' selected>$country</option>";
        					  		else
        					  			echo "<option value='$country'>$country</option>";

        					  	?>
       						 </select>
						</div>
					</div>
						<div class="col-md-6">
							<div class="form-group">
							 	<label>Data Nașterii</label>
								<input type="date" id="data" name="data" class="form-control" value="<?php echo $_SESSION['data_nasterii']?>" disable>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							  	<label>Poză de profil</label>
							  	<input type="file" name="pozaprofil" class="form-control" value="<?php echo $_SESSION['pozaprofil'];?>" accept="image/png, image/jpeg" />
							</div>
						</div>
					</div><button class="btn btn-dark" type="submit" name="updateSetari" value="1" style="height: 50px;width:80%;margin-bottom:3%">Salveaza modificarile</button><br/><hr/>
</form>
<form method="POST">
					<h2><i class="fas fa-key"></i> Schimba parola</h2>
					<hr/>
					<div class="row" style="width:80%;">
									<div class="col-md-6">
					<div class="form-group">
						<label>Parola curenta</label>
						<input type="password" name="parolacurenta" class="form-control" value="">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Parola noua</label>
						<input type="password" name="parolanoua" class="form-control" value="" required>
					</div>
				</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Confirma parola</label>
							<input type="password" name="parolanouac" class="form-control" value="" required>
						</div>
					</div></div><hr/>
					<button class="btn btn-dark" name="schimbaparola" value="1" type="submit" style="height: 50px;width:80%;margin-bottom:3%">Schimba parola</button><br/>
		</form></div>
		<!-------------------- CONTENT 2 ---------------------->
			<div class="tabcontent" id="content3" style="<?php if(empty($_GET["categorie"])) echo "display:none;"; else echo "display:block;" ?>">
			<center><h1 style="padding-top:1%"><i class="fas fa-clock"></i> Programeaza-te<hr/></h1></center>
			<center>

			<form type="GET">
				<div class="row" style="width:80%;">

						<?php   
							if(empty($_GET["categorie"])){
								echo "<center><div class='col-md-6' style='width:100%'>";
								$sql = "SELECT * FROM categoriiconsultatii;";
								$result=mysqli_query($link,$sql);
								echo "<label>Cabinet medical</label>
								<div class='form-group'>
								<select id='country' class='form-control' name='categorie'>
								";
								WHILE($row=mysqli_fetch_array($result)){
									echo "<option value='".$row["id"]."'>".$row["denumirec"]."</option>";
								}
								echo "</select><button class='btn btn-dark' type='submit' style='height: 50px;width:80%;margin-bottom:3%'>Creeaza programare</button></div>";
								echo "</center>";
							}
						?>
					<?php if(!empty($_GET["categorie"])){ ?>
					<?php
						$idCateg = $_GET["categorie"];
						$sql1 = "SELECT * FROM servicii WHERE idCategorie=$idCateg;";
						$result1 = mysqli_query($link,$sql1);
						echo "<div class='col-md-6'>
							<label>Tip consultatie</label>
							<div class='form-group'>
							<select id='country' class='form-control' name='consultatie'>";
						WHILE($row=mysqli_fetch_array($result1)){
							echo "<option value='".$row["id"]."'>".$row["denumire"]." - PRET(".$row["pret"]." RON)"."</option>";
						}
					echo "</select></div></div>";
						$sql1 = "SELECT * FROM medici WHERE idCategorie=$idCateg AND idStatusAngajat=1;";
						$result1 = mysqli_query($link,$sql1);
						echo "<div class='col-md-6'>
							<label>Medic</label>
							<div class='form-group'>
							<select id='country' class='form-control' name='medic'>";
						WHILE($row=mysqli_fetch_array($result1)){
							echo "<option value='".$row["id"]."'>".$row["numeMedic"]." ".$row["prenumeMedic"]."</option>";
						}
					echo "</select></div></div>";
						echo "
						<div class='col-md-6'>
							<div class='form-group'>
							 	<label>Data Consultatiei</label>
								<input type='date' name='data' value='".date("Y-m-d")."' min=".date("Y-m-d")." class='form-control'>
							</div>
						</div>
						<div class='col-md-6'>
							<div class='form-group'>
							  	<label>Detalii suplimentare</label>
							  	<input type='text' name='detalii' class='form-control' value='Scrieti diverse detalii..'>
							</div>
							
						</div>
						<button class='btn btn-dark' type='submit' name='programare' value='1' style='height: 50px;width:80%;margin-bottom:3%'>Creeaza programare</button><br/>
						<a class='btn btn-dark' href='members-side.php'>Renunta</a>
						";?><?php } ?>
							</div>			<br/>
<hr/>
					
</form></div>
<!------------------------------------ CONTENT 3 ----------------->
	<div class="tabcontent" id="content4" style="display:none;">
		<center><h1 style="padding-top:1%"><i class="fas fa-calendar"></i> Programari<hr/></h1></center>
		<center>
			  <button type="button" class="btn btn-info" data-toggle="collapse" style="width: 100%;height:50px;margin-bottom: 3%" data-target="#demo">Programari active</button>
  <div id="demo" class="collapse">
			<form type="POST">
		<?php
			$countProg = 0;
			$id = $_SESSION["id"];
			$sql="SELECT a.id, a.data,a.detaliisuplimentare, c.numeMedic, c.prenumeMedic,d.denumire,d.pret,e.denumirec,f.grad FROM programari a INNER JOIN utlizatori b ON a.idUtilizator = b.id  INNER JOIN medici c ON a.idMedic = c.id INNER JOIN servicii d ON a.idServiciu = d.id INNER JOIN categoriiconsultatii e ON d.idCategorie = e.id INNER JOIN gradmedici f ON c.idGrad = f.id WHERE a.idUtilizator = $id AND a.idStatus = 1;";
			$result = mysqli_query($link,$sql);
			WHILE($row = mysqli_fetch_array($result)){
				$countProg++;
				echo "
					<div class='box red' style='width:90%;height:auto'>
      				<h2>Data: ".$row["data"]."</h2>
      				<hr/>
      				<h5>Medic: ".$row["numeMedic"]." ".$row["prenumeMedic"]." (MEDIC ".$row["grad"].")"."</h5>
      				<h5>Tip consultatie: ".$row["denumire"]." ( ".$row["denumirec"]." )"."</h5>
      				<h5>Pret: ".$row["pret"]." RON</h5>
      				<p>Detalii suplimentare: ".$row["detaliisuplimentare"]."</p>
      				<button type='submit' value='".$row["id"]."' name='sterge' class='btn btn-danger' style='width:auto'>Anuleaza</button>
    				</div>
				";
			}
			if($countProg==0){
				echo "<center><h1>Nu aveti programari planificate</h1></center>";
			}
		?>
</form></div>
	<button type="button" class="btn btn-info" data-toggle="collapse" style="width: 100%;height:50px;background-color: red;margin-bottom: 3%" data-target="#demo2">Programari anulate</button>
  <div id="demo2" class="collapse">
		<?php
			$countProgAnulate = 0;
			$id = $_SESSION["id"];
			$sql="SELECT a.id, a.data,a.detaliisuplimentare, c.numeMedic, c.prenumeMedic,d.denumire,d.pret,e.denumirec,f.grad FROM programari a INNER JOIN utlizatori b ON a.idUtilizator = b.id  INNER JOIN medici c ON a.idMedic = c.id INNER JOIN servicii d ON a.idServiciu = d.id INNER JOIN categoriiconsultatii e ON d.idCategorie = e.id INNER JOIN gradmedici f ON c.idGrad = f.id WHERE a.idUtilizator = $id AND a.idStatus = 2;";
			$result = mysqli_query($link,$sql);
			WHILE($row = mysqli_fetch_array($result)){
				$countProgAnulate++;
				echo "
					<div class='box red' style='width:90%;height:auto'>
      				<h2>Data: ".$row["data"]."</h2>
      				<hr/>
      				<h5>Medic: ".$row["numeMedic"]." ".$row["prenumeMedic"]." (MEDIC ".$row["grad"].")"."</h5>
      				<h5>Tip consultatie: ".$row["denumire"]." ( ".$row["denumirec"]." )"."</h5>
      				<h5>Pret: ".$row["pret"]." RON</h5>
      				<p>Detalii suplimentare: ".$row["detaliisuplimentare"]."</p>
    				</div>
				";
			}
			if($countProgAnulate==0){
				echo "<center><h1>Nu aveti programari anulate</h1></center>";
			}
		?>
</div>
			  <button type="button" class="btn btn-info" data-toggle="collapse" style="width: 100%;height:50px;margin-bottom: 3%;background-color:green" data-target="#demo3">Programari efectuate</button>
  <div id="demo3" class="collapse">
		<?php
			$countProgEfectuate = 0;
			$id = $_SESSION["id"];
			$sql="SELECT a.id, a.data,a.detaliisuplimentare, c.numeMedic, c.prenumeMedic,d.denumire,d.pret,e.denumirec,f.grad FROM programari a INNER JOIN utlizatori b ON a.idUtilizator = b.id  INNER JOIN medici c ON a.idMedic = c.id INNER JOIN servicii d ON a.idServiciu = d.id INNER JOIN categoriiconsultatii e ON d.idCategorie = e.id INNER JOIN gradmedici f ON c.idGrad = f.id  WHERE a.idUtilizator = $id AND a.idStatus = 3;";
			$result = mysqli_query($link,$sql);
			WHILE($row = mysqli_fetch_array($result)){
				$countProgEfectuate++;
				echo "
					<div class='box red' style='width:90%;height:auto'>
      				<h2>Data: ".$row["data"]."</h2>
      				<hr/>
      				<h5>Medic: ".$row["numeMedic"]." ".$row["prenumeMedic"]." (MEDIC ".$row["grad"].")"."</h5>
      				<h5>Tip consultatie: ".$row["denumire"]." ( ".$row["denumirec"]." )"."</h5>
      				<h5>Pret: ".$row["pret"]." RON</h5>
      				<p>Detalii suplimentare: ".$row["detaliisuplimentare"]."</p>
    				</div>
				";
			}
			if($countProgEfectuate==0){
				echo "<center><h1>Nu aveti programari din trecut.</h1></center>";
			}
		?></div>
</div>
<!-------------------- CONTENT 4 ---------------------->
			<div class="tabcontent" id="content5" style="display:none;">
			<center><h1 style="padding-top:1%"><i class="fas fa-thumbs-up"></i> Postari apreciate de catre tine<hr/></h1></center>
			<center>
				<?php
					$id = $_SESSION["id"];
	$sql = "SELECT a.id, a.idAutor, a.titlu, a.imagine, a.preview, a.data,a.idCateg, b.nume, b.prenume, b.poza_profil,c.denumireCategorie FROM postari a INNER JOIN utlizatori b ON a.idAutor = b.id INNER JOIN categoriipostari c ON a.idCateg = c.id INNER JOIN aprecieripostari d ON d.idUtilizator = $id AND d.idPostare = a.id";
	$result = mysqli_query($link,$sql);
	$count = 0;
	WHILE($row = mysqli_fetch_array($result))
	{
		$count++;
    $id = $row["id"];
		$titlu = $row["titlu"];
		$imagine = $row["imagine"];
		$preview = $row["preview"];
		$idAutor = $row["idAutor"];
		$autor = $row["nume"]." ".$row["prenume"];
		$data = $row["data"];
		$pozaautor = $row["poza_profil"];
		$denumire = $row["denumireCategorie"];
		echo "
  			<div class='card' style='display:inline-block;height:650px'>
    			<div class='card-header'>
      				<img src='$imagine' alt='rover' />
    			</div>
    		<div class='card-body'>
      			<span class='tag tag-teal'>$denumire</span>
      		<h3>
        		$titlu
      		</h3>
          <hr style='color:black;border: 1px solid black; width:100%'/>
      		<p>
        		$preview 
            <br/>
        		<a class='button' href='blogpost.php?id=$id'>Citeste mai mult</a><br/>
      		</p>
          <hr style='color:black;border: 1px solid black; width:100%'/>
          Autor:
      <div class='user'>
        <img src='afiseazapoza.php?ID=$idAutor' alt='user' />
        <div class='user-info'>
          <h5>$data</h5>
          <small>$autor</small>
        </div>
      </div>
    </div></div>";
	}
?>
<!------------------- FINAL CONTENT --------------->
				</div></center>
		</div>
</div>


<script>
function openCity(evt, cityName) {
  // Declare all variables
  var i, tabcontent, tablinks;

  // Get all elements with class="tabcontent" and hide them
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Get all elements with class="tablinks" and remove the class "active"
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Show the current tab, and add an "active" class to the button that opened the tab
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
} 
document.getElementById("data").disabled = true; 
</script>


</body>
</html>
