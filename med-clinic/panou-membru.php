<!DOCTYPE html>
<?php 
	session_start();
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == false){
			header("location: index.php");
			exit;
		}
		$idUtilizator = $_SESSION["id"];
require_once("connect.php"); 
  global $link2;
  $link2 = $link;
require_once("autorizare.php");
include("variabilecomune.php");
if(isset($_POST["comanda"])){
	switch($_POST["comanda"]){
	case("creeazaProgramare"):
		CreeazaProgramare($_POST["consultatie"],$_POST["medic"],$_POST["data"],$_POST["detalii"],$_SESSION["id"]);
		break;
	case("anuleazaProgramare"):
		AnuleazaProgramare($_POST["anuleaza"]);
		break;
	case("schimbaParola"):
		SchimbaParola($_POST["parolacurenta"],$_POST["parolanoua"],$_POST["parolanouac"],$_SESSION["id"]);
		break;
	case("actualizeazaSetari"):
		ActualizeazaSetari($_POST["nume"],$_POST["prenume"],$_POST["email"],$_POST["tara"],$_SESSION["id"]);
		break;
	}
}
function CreeazaProgramare($consultatie,$medic,$data,$detalii,$idUtilizator){
	$sql = "INSERT INTO programari(idServiciu,idMedic,data,idUtilizator,detaliiSuplimentare,idStatus) VALUES('$consultatie','$medic','$data','$idUtilizator','$detalii',1);";
	$rezultat = mysqli_query($GLOBALS["link2"], $sql);
	if($rezultat){
		notificare("green","Programare efectuata cu succes!");
		header( "refresh:3;url=panou-membru.php" );
	}
}
function AnuleazaProgramare($idProgramare){
	$sql="UPDATE programari SET idStatus=2 WHERE id=$idProgramare;";
	$anulare = mysqli_query($GLOBALS["link2"],$sql);
	if($anulare){
		notificare("green","Programare anulata cu succes!");
		header("refresh:3;url=panou-membru.php");
	}
}
function notificare($culoare,$mesaj){
	echo "
		<div class='notificare' style='overflow:auto;margin-top:5px;background-color:$culoare'>
 					 <p>$mesaj</p>
		</div> 
	";
}
function SchimbaParola($parolaCurenta,$parolaNoua,$confirmaParolaNoua,$idUtilizator){
	echo "<h1>$parolaCurenta / $parolaNoua</h1>";
	if(strlen($parolaNoua) > 6){
	if($confirmaParolaNoua == $parolaNoua){
		$sql = "SELECT parola FROM utlizatori WHERE id = $idUtilizator;";
		$result = mysqli_query($GLOBALS["link2"],$sql);
		$row = mysqli_fetch_array($result);
		$parolaCurentaBuna = $row["parola"];
		if(password_verify($parolaCurenta,$parolaCurentaBuna)){
			$parolahash = password_hash(ltrim($parolaNoua), PASSWORD_DEFAULT);
			$sql = "UPDATE utlizatori SET parola = '$parolahash' WHERE id=$idUtilizator;";
			mysqli_query($GLOBALS["link2"],$sql);
				notificare("green","Parola a fost schimbata cu succes!");
				 header("refresh:3;url=logout.php");
		}
		else{
				notificare("red","Parola introdusa nu este corecta!");
		}
	}
	else{
				notificare("red","Parolele nu coincid!");
	}

}
	else{
				notificare("red","Parola trebuia sa aibe peste 6 caractere!");
	}
}

function ActualizeazaSetari($nume,$prenume,$email,$tara,$idUtilizator){
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
		$actualizare = mysqli_query($GLOBALS["link2"],$sql);
		if($actualizare)
			notificare("green","Setari actualizate cu succes!");
	}
}

function AfiseazaProgramari($tipProgramare){
	$filtru = "";
	$mesaj_zero_programari = "";
	switch($tipProgramare){
		case("activa"):
			$filtru=1;
			$mesaj_zero_programari="Nu aveti programari planificate!";
			break;
		case("anulata"):
			$filtru=2;
			$mesaj_zero_programari="Nu aveti programari anulate!";
			break;
		case("finalizata"):
			$filtru=3;
			$mesaj_zero_programari="Nu aveti programari finalizate!";
			break;
	}
			$countProg = 0;
			$id = $_SESSION["id"];
			$sql="SELECT a.id, a.data,a.detaliisuplimentare, c.numeMedic, c.prenumeMedic,d.denumire,d.pret,e.denumirec,f.grad FROM programari a INNER JOIN utlizatori b ON a.idUtilizator = b.id  INNER JOIN medici c ON a.idMedic = c.id INNER JOIN servicii d ON a.idServiciu = d.id INNER JOIN categoriiconsultatii e ON d.idCategorie = e.id INNER JOIN gradmedici f ON c.idGrad = f.id WHERE a.idUtilizator = $id AND a.idStatus = $filtru;";
			$result = mysqli_query($GLOBALS["link2"],$sql);
			WHILE($row = mysqli_fetch_array($result)){
				$countProg++; ?> <?php
				echo "
					<div style='width:90%;height:auto;border-radius:100px;background-color:white;border:3px dotted black'>
      				<h2>Data: ".$row["data"]."</h2>
      				<hr style='border: 1px solid black;'/>
      				<h5>Medic: ".$row["numeMedic"]." ".$row["prenumeMedic"]." (MEDIC ".$row["grad"].")"."</h5>
      				<h5>Tip consultatie: ".$row["denumire"]." ( ".$row["denumirec"]." )"."</h5>
      				<h5>Pret: ".$row["pret"]." RON</h5>
      				<p>Detalii suplimentare: ".$row["detaliisuplimentare"]."</p>
      				<input type='hidden' name='anuleaza' value='".$row["id"]."'>";
      				if($tipProgramare=="activa")
      				 echo "<input type='submit' class='butonRosu' value='Anuleaza programarea' name='butonAnuleaza' style='width:auto'><br/>";
    				echo "</div><br/>
				";
			}
			if($countProg==0){
				echo "<center><h1>$mesaj_zero_programari</h1></center>";
			}
}
?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Panoul pacientului</title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="stylesheet" type="text/css" href="styles/stiluriindex.css">
	<link rel="stylesheet" type="text/css" href="styles/paginaadminstyles.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	<script src="https://kit.fontawesome.com/c684b69a55.js" crossorigin="anonymous"></script>
	<style>
	</style>
</head>
<body style="background-color:#f44336">
	<div class="continut" style="width:96%;margin-left:2%;">
		
			<center style="width:100%"><h1 style="text-decoration:underline; color:white;display"><a class="fas fa-backward" href="index.php" style="display: inline-flex;float:left;color:white;text-decoration: none;"></a>PANOUL PACIENTULUI</h1></center>
			<?php
			$sql = "SELECT * FROM programari WHERE idUtilizator = ".$_SESSION["id"]." AND data = '".date('Y-m-d')."' AND idStatus = 1;";
			$result = mysqli_query($link,$sql);
			if(mysqli_num_rows($result) != 0){ 
				notificare("black","Aveti o programare pe data de astazi!");
 }	?>
	<div style="width: 100%;backround-color:green;">
		<div class="meniu" style="width:100%;background-color:white;margin-top: 1%;opacity: 0.9;max-height:100%;border-radius: 50px;">
		<center><div class="imagine-cerc">
			<img src="<?php echo "afiseazapoza.php?ID=$idUtilizator"; ?>" alt="Image" style="top:20%;margin-right:3%" class="shadow">
		</div></center>

			<div class="meniu-stanga" style="width:100%;padding-bottom:2%">
			<a class="buton-deschide-tab" onclick=" deschideTab(event, 'cotinutSetariCont')" style="border-radius:50px">Setari Cont</a>
			<a class="buton-deschide-tab" onclick=" deschideTab(event, 'continutCreeazaProgramare')" style="border-radius:50px">Programeaza-te</a>
			<a class="buton-deschide-tab" onclick=" deschideTab(event, 'continutProgramari')" style="border-radius:50px">Programari</a>
			<a class="buton-deschide-tab" onclick=" deschideTab(event, 'continutPostariApreciate')" style="border-radius:50px">Postari apreciate</a>
		</div>
		</div><br/><br/>
	</div>
		<div class="continut1" style="width:100%;height: 100%;background-color:white;border-radius:50px;">
			<!-------------------- Setari cont ---------------------->
			<div class="continutTab" id="cotinutSetariCont" style="<?php if(empty($_GET["categorie"])) echo "display:block;"; else echo "display:none;" ?>">
			<center><h1 style="padding-top:1%"><i class="fas fa-cog"></i> Setări<hr/></h1></center>
			<center>
			<form method="POST" enctype="multipart/form-data">
				<input type="hidden" name="comanda" value="actualizeazaSetari">
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
        					  <select class="form-control"  name="tara">
        					  	<?php

        					  		foreach($tari as $tara) 
        					  			if($tara == $_SESSION["tara"])
        					  			echo "<option value='$tara' selected>$tara</option>";
        					  		else
        					  			echo "<option value='$tara'>$tara</option>";

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
					</div><input class="butonRosu" type="submit" name="updateSetari" value="Modifica setari" style="height: 50px;width:80%;margin-bottom:3%"><br/><hr/>
</form>
<form method="POST">
	<input type="hidden" name="comanda" value="schimbaParola">
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
					<input type="submit" class="butonRosu" name="schimbaparola" value="Schimba Parola" style="height: 50px;width:80%;margin-bottom:3%"><br/>
		</form></div>
		<!-------------------- Creeaza programare ---------------------->
			<div class="continutTab" id="continutCreeazaProgramare" style="<?php if(empty($_GET["categorie"])) echo "display:none;"; else echo "display:block;" ?>">
			<center><h1 style="padding-top:1%"><i class="fas fa-clock"></i> Programeaza-te<hr/></h1></center>
			<center>

			<form method="GET">
				<div style="width:80%;">

						<?php   
							if(empty($_GET["categorie"])){
								echo "<center><div class='col-md-6' style='width:100%'>";
								$sql = "SELECT * FROM categoriiconsultatii;";
								$result=mysqli_query($link,$sql);
								echo "<label>Cabinet medical</label>
								<div class='form-group'>
								<select class='form-control' name='categorie'>
								";
								WHILE($row=mysqli_fetch_array($result)){
									echo "<option value='".$row["id"]."'>".$row["denumirec"]."</option>";
								}
								echo "</select><br/><button class='butonRosu' type='submit' style='height: 50px;width:80%;margin-bottom:3%'>Creeaza programare</button></div>";
								echo "</center>";
							}
						?></form>
					<?php if(!empty($_GET["categorie"])){ ?> <form method="POST"> <?php
						echo "<input type='hidden' name='comanda' value='creeazaProgramare'>";
						$idCateg = $_GET["categorie"];
						$sql1 = "SELECT * FROM servicii WHERE idCategorie=$idCateg;";
						$result1 = mysqli_query($link,$sql1);
						echo "<div class='col-md-6'>
							<label>Tip consultatie</label>
							<div class='form-group'>
							<select class='form-control' name='consultatie'>";
						WHILE($row=mysqli_fetch_array($result1)){
							echo "<option value='".$row["id"]."'>".$row["denumire"]." - PRET(".$row["pret"]." RON)"."</option>";
						}
					echo "</select></div></div>";
						$sql1 = "SELECT * FROM medici WHERE idCategorie=$idCateg AND idStatusAngajat=1;";
						$result1 = mysqli_query($link,$sql1);
						echo "<div class='col-md-6'>
							<label>Medic</label>
							<div class='form-group'>
							<select class='form-control' name='medic'>";
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
						<input type='submit' name='CreareProgramare' value='Creeaza programare'><br/>
						<a href='panou-membru.php'>Renunta</a>
						</form>";?><?php } ?>
							</div>			<br/>
<hr/>
					
</form></div>
<!------------------- Lista programari ----------------->
	<div class="continutTab" id="continutProgramari" style="display:none;">
		<center><h1 style="padding-top:1%"><i class="fas fa-calendar"></i> Programari<hr/></h1></center>
		<center>
		<button onclick="afiseazaProgramari(1);" class="butonRosu" data-toggle="collapse" style="width:90%;height:50px;margin-bottom: 3%;font-size:20px;">Programari active</button>
  <div id="programariActive" class="collapse" style="display:none;"><form method="POST"> <input type="hidden" name="comanda" value="anuleazaProgramare">
		<?php
			AfiseazaProgramari("activa");
		?>
</form></div>
		<button onclick="afiseazaProgramari(2);" class="butonRosu" data-toggle="collapse" style="width:90%;height:50px;margin-bottom: 3%;font-size:20px;background-color:red;">Programari anulate</button>
  <div id="programariAnulate" class="collapse" style="display:none;">
		<?php
			AfiseazaProgramari("anulata");
		?>
</div>
		<button onclick="afiseazaProgramari(3);" class="butonRosu" data-toggle="collapse" style="width:90%;height:50px;margin-bottom: 3%;font-size:20px;background-color:black;">Programari efectuate</button>
  <div id="programariFinalizate" class="collapse" style="display:none;">
		<?php
			AfiseazaProgramari("finalizata");
		?></div>
</div>
<!-------------------- Postari apreciate ---------------------->
			<div class="continutTab" id="continutPostariApreciate" style="display:none;">
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
  			<div class='postare' style='display:inline-block;height:650px'>
    			<div class='postare-titlu'>
      				<img src='$imagine' alt='rover' />
    			</div>
    		<div class='postare-previzualizare'>
      			<span class='categorie categorie-culoare'>$denumire</span>
      		<h3>
        		$titlu
      		</h3>
          <hr style='color:black;border: 1px solid black; width:100%'/>
      		<p>
        		$preview 
            <br/>
        		<a class='butonRosu' href='blogpost.php?id=$id'>Citeste mai mult</a><br/>
      		</p>
          <hr style='color:black;border: 1px solid black; width:100%'/>
          Autor:
      <div class='utilizator'>
        <img src='afiseazapoza.php?ID=$idAutor' alt='user' />
        <div class='utilizator-info'>
          <h5>$data</h5>
          <small>$autor</small>
        </div>
      </div>
    </div></div>";
	}
?>
<!------------------- Sfarsit --------------->
				</div></center>
		</div>
</div>


<script>
function deschideTab(eveniment, tabDeschis) {
  var i, continutTab;

  // Ascunde toate elementele ce au clasa continutTab
  continutTab = document.getElementsByClassName("continutTab");
  for (i = 0; i < continutTab.length; i++) {
    continutTab[i].style.display = "none";
  }

  // Afiseaza tab-ul curent si modifica clasa butonului care a deschis tab-ul
  document.getElementById(tabDeschis).style.display = "block";
  eveniment.currentTarget.className += " active";
} 
document.getElementById("data").disabled = true; 

function afiseazaProgramari(tinta){
	if(tinta==1)
		var el = document.getElementById("programariActive");
	else if (tinta==2)
		var el = document.getElementById("programariAnulate");
	else
		var el = document.getElementById("programariFinalizate");
  if(el.style.display === "none"){
  	el.style.display="block";
  }
  else{
  	el.style.display="none";
  }
}
</script>


</body>
</html>
