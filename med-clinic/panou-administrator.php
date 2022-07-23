<!DOCTYPE html>
<?php 
	session_start();
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == false || $_SESSION["nivel_administrare"] < 7){
			header("location: index.php");
			exit;
		}
		$idUtilizator = $_SESSION["id"];
require_once("connect.php");
require_once("autorizare.php");
include("variabilecomune.php");
global $link2;
$link2 = $link;
 if(array_key_exists('sterge', $_POST)) {
    stergeMesaj();
    	echo "
		<div class='notificare' style='overflow:auto;margin-top:5px;background-color:blue'>
 					 <p>Mesaj sters cu succes.</p>
		</div> 
	";
	    header("refresh:3;url=panou-administrator.php"); // redirect daca se ruleaza query-ul	

  }
if(isset($_POST["comanda"])){
	switch($_POST["comanda"]){
		case("adaugamedic"):
			adaugaMedic($_POST["categorieserviciu"],$_POST["numemedic"],$_POST["prenumemedic"],$_POST["CNP"],$_POST["gradmedic"]);
				break;
		case("stergemedic"):
			if(isset($_POST["concediazamedic"])){
				stergeMedic($_POST["medicid"],4);
			}
			if(isset($_POST["pensionaremedic"])){
				stergeMedic($_POST["medicid"],5);
			}
			if(isset($_POST["demisiemedic"])){
				stergeMedic($_POST["medicid"],3);
			}
			if(isset($_POST["concediumedic"])){
				stergeMedic($_POST["medicid"],2);
			}
			break;
		case("alegedataloguri"):
			$data=$_POST["filtreazadata"];
			break;
		case("anuleazaprogramare"):
			anuleazaProgramare($_POST["stergeprogramare"]);
			break;
		case("modificasetarimedic"):
			modificaSetariM($_POST["idmedicmod"], $_POST["modificanumemedic"],$_POST["modificaprenumemedic"],$_POST["modificaCNP"],$_POST["modificagradmedic"],$_POST["modificaspecializare"]);
			break;
		case("modificasetariutilizator"):
			modificaSetariUtilizator($_POST["idmodutilizator"],$_POST["modificausername"],$_POST["modificanumeutilizator"],$_POST["modificaprenumeutilizator"],$_POST["modificaemailutilizator"],$_POST["modificadatanastere"],$_POST["modificatara"],$_POST["modificaniveladministrare"]);
			break;
		case("eliminaconsultatie"):
			stergeConsultatie($_POST["categorieeliminaserviciu"]);
			break;
		case("posteazablog"):
			posteazaBlog($_POST["categorie"],$_POST["titlu"],nl2br($_POST["preview"]),$_SESSION["id"],$_POST["file"],nl2br($_POST["postare"]));
			break;
		case("adaugacategorie"):
			adaugaCategorie($_POST["numecategorie"]);
			break;
		case("eliminacategorie"):
			stergeCategorie($_POST["stergecategorie"]);
			break;
		case("modificasetari"):
			modificaSetari($_POST["youtube"],$_POST["linkedin"],$_POST["instagram"],$_POST["facebook"],$_POST["telefon"],$_POST["descriere"],$_POST["email"]);
			break;
		case("creeazacabinet"):
			adaugaCabinet($_POST["dencabinet"]);
			break;
		case("stergecabinet"):
			stergeCabinet($_POST["categorieconsultatie"]);
			break;
		case("adaugaconsultatie"):
			adaugaConsultatie($_POST["categorieserviciu"],$_POST["denumireserviciu"],$_POST["detaliiconsultatie"],$_POST["pret"]);
			break;
	}
}


 function adaugaMedic($idCategorie, $numeMedic, $prenumeMedic, $CNP,$grad){
  	$sql = "INSERT into medici (idCategorie, numeMedic,prenumeMedic,CNP,idGrad,idStatusAngajat) VALUES($idCategorie,'$numeMedic','$prenumeMedic','$CNP',$grad,1);";
  	$result = mysqli_query($GLOBALS["link2"],$sql);
    if($result){
    	afiseazaMesaj("green","Medic adaugat cu succes.");
    	adaugaInLoguri("Medic adaugat(CNP: $CNP)");
    }
    else
    	afiseazaMesaj("red","Ceva nu a functionat.");
 }

 function modificaSetariM($id,$nume,$prenume, $CNP, $idGrad,$idCategorie){
 		$sql = "UPDATE medici SET numeMedic='$nume',prenumeMedic='$prenume',CNP='$CNP',idGrad=$idGrad,idCategorie=$idCategorie WHERE id=$id;";
 		$result = mysqli_query($GLOBALS["link2"],$sql);
 		if($result){
 			  afiseazaMesaj("green","Setari medic actualizate cu succes.");
 			  adaugaInLoguri("Setari medic modificate(idMedic:$id)");
 		}
 		else{
 			 	afiseazaMesaj("red","Eroare la actualizarea setarilor.");
 		}
 }

 function modificaSetariUtilizator($id,$nume_utilizator,$nume,$prenume,$email,$data_nasterii,$tara,$nivel_administrare){
 	$sql = "UPDATE utlizatori SET nume_utilizator='$nume_utilizator',nume='$nume',prenume='$prenume',email='$email',data_nasterii='$data_nasterii',tara='$tara',nivel_administrare=$nivel_administrare WHERE id=$id;";
 	$result = mysqli_query($GLOBALS["link2"],$sql);
 	if($result){
 		afiseazaMesaj("green","Setari utilizator actualizate cu succes.");
 		adaugaInLoguri("Setari utilizator modificate(idUtilizator:$id)");
 	}
 	else{
 		afiseazaMesaj("red","Eroare la actualizarea setarilor.");
 	}
 }

 function stergeMedic($idM,$idStatus){
 	$sql = "UPDATE medici SET idStatusAngajat=$idStatus WHERE id = $idM;";
 	 $result = mysqli_query($GLOBALS["link2"],$sql);
   if($result){
    	afiseazaMesaj("green","Statusul medicului schimbata cu succes.");
    	adaugaInLoguri("Status medic schimbat(idMedic: $idM | idStare:$idStatus)");
   }
   else
    	afiseazaMesaj("red","Ceva nu a functionat.");
 }

 function anuleazaProgramare($idProgramare){
	$idSterge = $_GET["stergeprogramare"];
	$sql="UPDATE programari SET idStatus = 2 WHERE id=$idProgramare;";
	$del = mysqli_query($GLOBALS["link2"],$sql);
	if($del)
	{
		afiseazaMesaj("green","Programarea pacientului a fost anulata.");
		adaugaInLoguri("Programare anulata(idProgramare: $idSterge)");

	}
	else{
		afiseazaMesaj("red", "Programarea nu a putut fi anulata.");
	}
}

function stergeConsultatie($idConsultatie){
	$sql = "DELETE FROM servicii WHERE id=$idConsultatie;";
	$result = mysqli_query($GLOBALS["link2"],$sql);
	if($result){
		afiseazaMesaj("green","Consultatie eliminata cu succes!");
		adaugaInLoguri("Consultatie eliminata(idConsultatie=$idConsultatie)");
	}
	else{
		afiseazaMesaj("red","Eroare la eliminarea consultatiei!");
	}
}

function adaugaInLoguri($actiune){
 	$idAdministrator = $_SESSION["id"];
 	$ip = $_SERVER['REMOTE_ADDR'];
 	$sql = "INSERT INTO loguri (idAdministrator,ip,actiune) VALUES($idAdministrator,'$ip','$actiune');";
 	mysqli_query($GLOBALS["link2"],$sql);
 }

 if(isset($_GET["finalizeazaprogramare"])){
 	$idProgramare = $_GET["finalizeazaprogramare"];
 	$sql = "UPDATE programari SET idStatus = 3 WHERE id=$idProgramare;";
 	mysqli_query($link,$sql);
 }

 if(isset($_GET["stergeprogramare"])){
	$idSterge = $_GET["stergeprogramare"];
	$sql2="UPDATE programari SET idStatus = 2 WHERE id=$idSterge;";
	$del = mysqli_query($link,$sql2);
	if($del)
	{
		afiseazaMesaj("green","Programarea pacientului a fost anulata.");
		adaugaInLoguri("Programare anulata(idProgramare: $idSterge)");

	}
	else{
		afiseazaMesaj("red", "Programarea nu a putut fi anulata.");
	}
	header("Location:panou-administrator.php");
}

function posteazaBlog($idCategorie, $titlu, $preview, $idAutor,$poza, $postare){
	if(strlen($titlu) > 5 && strlen($preview) > 100 && strlen($postare) > 255){
		$sql = "INSERT INTO postari(idAutor,titlu,imagine,continut,idCateg,preview) VALUES($idAutor,'$titlu','$poza','$postare',$idCategorie,'$preview');";
		mysqli_query($GLOBALS["link2"],$sql);
		afiseazaMesaj("green","Postare realizata cu succes.");
		adaugaInLoguri("Postare creata(Titlu: $titlu)");
	}
	else{
			afiseazaMesaj("red","Postarea nu a fost creata!");
	}
}
function adaugaCategorie($numeCategorie){
	if(strlen($numeCategorie) > 3){
		$sql = "INSERT INTO categoriipostari (denumireCategorie) VALUES('$numeCategorie');";
		mysqli_query($GLOBALS["link2"],$sql);
		afiseazaMesaj("green","Ati creat o categorie noua!");
		adaugaInLoguri("Categorie adaugata(Nume categorie: $numeCategorie)");
	}
	else{
		afiseazaMesaj("red","Numele categoriei trebuie sa aibe peste 3 caractere");
	}
}

function stergeCategorie($stergeCateg){
	if(isset($stergeCateg) && isset($stergeCateg)){
		$sql = "DELETE FROM categoriipostari WHERE id=$stergeCateg;";
		mysqli_query($GLOBALS["link2"],$sql);
		afiseazaMesaj("green","Ati sters categoria.");
		adaugaInLoguri("Categorie stearsa(idCategorie: $stergeCateg)");
	}
}

function modificaSetari($youtube, $linkedin, $instagram, $facebook, $telefon, $descriere, $email){
	$sql = "UPDATE setarisite SET linkedin='$linkedin',facebook='$facebook',youtube='$youtube',instagram='$instagram',telefon='$telefon',email='$email',descriere='$descriere' WHERE id=1;";
	$modifica = mysqli_query($GLOBALS["link2"],$sql);
	if($modifica){
		afiseazaMesaj("green","Modificari efectuate cu succes");
		adaugaInLoguri("Setarile site-ului au fost modificate.");
	}
	else{
		afiseazaMesaj("red","Ceva nu a functionat.");
	}
}

function adaugaCabinet($dencabinet){
	if(isset($dencabinet)){
		$sql2 = "INSERT INTO categoriiconsultatii(denumirec) VALUES('$dencabinet');";
		$adaugac = mysqli_query($GLOBALS["link2"],$sql2);
		if($adaugac){
			afiseazaMesaj("green","Cabinet adaugat cu succes");
			adaugaInLoguri("Cabinet adaugat(Denumire: $dencabinet)");
		}
		else{
			afiseazaMesaj("red","Ceva nu a functionat.");
		}
	}
}

function stergeCabinet($categorieconsultatie){
	if(isset($categorieconsultatie)){
		$sql2 = "DELETE FROM categoriiconsultatii WHERE id=$categorieconsultatie;";
		$adaugac = mysqli_query($GLOBALS["link2"],$sql2);
		if($adaugac){
			afiseazaMesaj("green","Cabinet sters cu succes");
    	adaugaInLoguri("Cabinet sters(id Cabinet: $categorieconsultatie)");
		}
		else{
			afiseazaMesaj("red","Ceva nu a functionat.");
		}
	}
}

function adaugaConsultatie($categorie, $denumires, $detalis, $pret){
	if(strlen($denumires) > 1 && strlen($detalis) > 10){
		$sql = "INSERT INTO servicii (idCategorie,denumire,pret,detalii) VALUES($categorie,'$denumires',$pret,'$detalis');";
		$result = mysqli_query($GLOBALS["link2"],$sql);
		if($result){
			afiseazaMesaj("green","Consultatia a fost adaugata cu succes!");
    	adaugaInLoguri("Consultatie adaugata(Denumire: $denumires)");
		}
		else{
			afiseazaMesaj("red","Te rog sa oferi mai multe detalii despre consultatie.");
		}
	}
}

function afiseazaMesaj($culoare,$mesaj){
			echo "
				<div class='notificare' style='overflow:auto;margin-top:5px;background-color:$culoare'>
  				 <span class='inchide' onclick='this.parentElement.style.display='none';''>&times;</span>
 					 <p>$mesaj</p>
				</div> ";
}

function stergeMesaj(){
  $id = $_POST["sterge"];
  $sql = "DELETE FROM mesaje WHERE id=$id;";
  stergeQuery($sql);
}

function exportMesaje(){
  $handle = fopen("export.txt","w");
  fwrite($handle,"Sunt inregistrate ".sizeof($GLOBALS['numearr'])." mesaje \n--------------\n");
  $handle = fopen("export.txt","a");
  for($i = 0; $i < sizeof($GLOBALS['numearr']); $i++)
    fwrite($handle,"NUME: ".$GLOBALS['numearr'][$i]."\nEmail: ".$GLOBALS['emailarr'][$i]."\nTARA:".$GLOBALS['taraarr'][$i]."\nMESAJ: ".$GLOBALS['mesajarr'][$i]."\n\n");
}

	function stergeQuery($sql){
  	$result = mysqli_query($GLOBALS["link"],$sql);
	}

	function AfiseazaListaMedici($tipMedici){
		echo "
					<div id='scroll' style='overflow-x:auto;width:100%;'>
      <center><table id='TabelServicii' style='width:90%;color:black;'>
  			<tr>
    			<th style='background-color:red;'>Nume</th>
    			<th style='background-color:red;'>Prenume</th>
    			<th style='background-color:red;'>Cabinet</th>
    			<th style='background-color:red;'>Grad</th>			
    			<th style='background-color:red;'>Programari</th>
    			<th style='background-color:red;'>Comenzi</th>
 			  </tr>";		  
 			  	$sql = "SELECT a.id,a.numeMedic,a.prenumeMedic,b.denumirec,c.grad FROM medici a INNER JOIN categoriiconsultatii b INNER JOIN gradmedici c ON a.idGrad = c.id WHERE a.idCategorie=b.id AND idStatusAngajat=$tipMedici;";
 			  	$result = mysqli_query($GLOBALS["link2"],$sql);
 			  	WHILE($row = mysqli_fetch_array($result)){
							$nume = $row["numeMedic"];
							$prenume = $row["prenumeMedic"];
							$cabinet = $row["denumirec"];
							$grad = $row["grad"];
							$idM=$row["id"];
 			  		  echo "<tr><td>$nume</td><td>$prenume</td><td>$cabinet</td><td>$grad</td><td><a class='butonRosu' href='?idM=$idM&idStatus=1#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Active</a><a class='butonRosu' href='?idM=$idM&idStatus=2#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Anulate</a><a class='butonRosu' href='?idM=$idM&idStatus=3#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Efectuate</a></td><td><a class='butonRosu' href='?idM=$idM#popupSetariMedic' border-radius:100px;width:100px;height:50px;'>Profil medic</a></td></tr>";
 			  	}
	}
	function numarMedici($tipMedici){
		$numar;
		$sql = "SELECT a.id,a.numeMedic,a.prenumeMedic,b.denumirec,c.grad FROM medici a INNER JOIN categoriiconsultatii b INNER JOIN gradmedici c ON a.idGrad = c.id WHERE a.idCategorie=b.id AND idStatusAngajat=$tipMedici;";
 		$result = mysqli_query($GLOBALS["link2"],$sql);
 		$numar = mysqli_num_rows($result);
 		return $numar;
	}
?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Panou Administrator</title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<script src="https://kit.fontawesome.com/c684b69a55.js" crossorigin="anonymous"></script>
	<link rel="stylesheet" type="text/css" href="styles/stiluriindex.css">
	<link rel="stylesheet" type="text/css" href="styles/paginaadminstyles.css">
<style>
	.textPostare {
  padding: 12px 20px;
  box-sizing: border-box;
  border: 2px solid #ccc;
  border-radius: 4px;
  background-color: #f8f8f8;
  font-size: 16px;
  resize: none;
  background: url(img/textbox.png) center center no-repeat; 
  border: 1px solid #888; 
}
.textPostare:focus{
	background: none;
}
</style>
</head>
<body style="background-color:#f44336">
	<!--<div class="background-image"></div>-->
	<div style="padding-bottom:20px;width:96%;margin-left:2%;">
		
			<center><h1 style="text-decoration:underline; color:white;display;margin-bottom:2%;margin-top:2%"><a class="fas fa-backward" href="index.php" style="display: inline-flex;float:left;color:white;text-decoration: none;"></a>PANOUL ADMINISTRATORULUI</h1></center>
	<div class="center" style="backround-color:green;">
		<div class="meniu" style="width:100%;background-color:white;margin-top: 1%;opacity: 0.9;max-height:19%;border-radius: 50px;">
		<center><div class="imagine-cerc" stlye="padding-top:5%">
			<img src="<?php echo "afiseazapoza.php?ID=$idUtilizator"; ?>" alt="Image" style="top:20%;margin-right:3%" class="shadow">
		</div></center>

			<div class="meniu-stanga" style="width:100%;padding-bottom:2%">
			<a class="buton-deschide-tab" onclick=" schimbaTab(event, 'continutMesaje')" style="border-radius:50px">Mesaje</a>
			<a class="buton-deschide-tab" onclick=" schimbaTab(event, 'continutAbonatiNewsletter')" style="border-radius:50px">Abonati newsletter</a>
			<a class="buton-deschide-tab" onclick=" schimbaTab(event, 'continutPostareBlog')" style="border-radius:50px">Posteaza</a>
			<a class="buton-deschide-tab" onclick=" schimbaTab(event, 'continutUtilizatori')" style="border-radius:50px">Utilizatori</a>
			<a class="buton-deschide-tab" onclick=" schimbaTab(event, 'continutSetariSite')" style="border-radius:50px">Setari</a>
			<a class="buton-deschide-tab" onclick=" schimbaTab(event, 'continutMedici')" style="border-radius:50px">Medici</a>
			<a class="buton-deschide-tab" onclick=" schimbaTab(event, 'continutServicii')" style="border-radius:50px">Servicii</a>
			<?php if($_SESSION["nivel_administrare"]==9) {?> <a class="buton-deschide-tab" onclick=" schimbaTab(event, 'continutLoguri')" style="border-radius:50px">Loguri</a><?php } ?>

		</div>
		</div>
	</div><br/>
		<div class="continutPanouAdministrare" style="width:100%;height: 100%;background-color:white;border-radius:50px;">
			<!-------------------- TAB MESAJE ---------------------->
			<div class="continutTab" id="continutMesaje" style="<?php if(empty($_GET["idUt"]) && empty($_GET["idM"]) && empty($_POST["filtreazadata"])) echo "display:block;"; else echo "display:none;" ?>">
			<center><h1 style="padding-top:1%;margin-top:0px;"><i class="fas fa-inbox"></i> Mesaje primite <hr/></h1></center>
		<center>	<form style="width:100%;">
  <select name="filtru" id="filtru" style="width:80%;">
      <option value="1" <?php if(isset($_GET["filtru"])) if($_GET["filtru"]==1) echo "selected";?> >Cel mai vechi primul</option>
      <option value="2" <?php if(isset($_GET["filtru"])) if($_GET["filtru"]==2) echo "selected"; ?>>Cel mai nou primul</option>
  </select><br/>
  <input type="submit" value="Aplicati" style="width:50%;margin-top: 10px">
</form> </center>

<div id="scroll" style="overflow-x:auto;width:100%;">
     <br/> <center><table id="TabelServicii" style="width:90%;color:black;">
  <tr>
    <th style="background-color:red;">Nume</th>
    <th style="background-color:red;">Email</th>
    <th style="background-color:red;">Tara</th>
    <th style="background-color:red;">Comenzi</th>
  </tr>
    <?php
    $aplica = "";
    if(isset($_GET["filtru"])){
      $filtru = $_GET["filtru"];
      if($filtru == 1)
        $aplica = "ORDER BY data";
      else
        $aplica = "ORDER BY data desc";
    }
    $sql = "SELECT * FROM mesaje $aplica";
  $result = mysqli_query($link,$sql); 
  WHILE($row = mysqli_fetch_array($result)){
    $nume = $row["nume"];
    $id = $row["id"];
    $prenume = $row["prenume"];
    $email = $row["email"];
    $tara = $row["tara"];
    $mesaj = $row["mesaj"];
    $numearr[] = $row["nume"];
    $taraarr[] = $row["tara"];
    $mesajarr[] = $row["mesaj"];
    $emailarr[] = $row["email"];
    echo "<tr>
        <td>$nume $prenume</td>
        <td>$email</td>
        <td>$tara</td>
        <td><center><form method='POST'><a class='butonRosu' href='?idMesaj=$id#popup1' style='border-radius:100px;width:200px;height:40px;'>Vezi mesaj</a></br><button class='butonRosu' type='submit' value='$id' name='sterge' style='border-radius:100px;width:200px;height:40px;'>Sterge mesaj</button></form></td>
        </tr>";
  }
  if(array_key_exists('descarcaMesaje', $_POST)) {
    exportMesaje();
  }?>

</table></center></div>
    <form method="post">
        <center><button type="submit" name="descarcaMesaje" class="butonRosu" value="" />Export mesaje</button>
        </center>
    </form>
<div id="popup1" class="overlay" style="z-index:100;position:absolute;">
  <div class="popup">
    <?php
    if(isset($_GET["idMesaj"])){
    $id = $_GET["idMesaj"];
    $sql = "SELECT * FROM mesaje WHERE id=$id;";
    $result = mysqli_query($link,$sql);
    WHILE($row = mysqli_fetch_array($result)){
      $nume = $row["nume"];
      $prenume = $row["prenume"];
      $email = $row["email"];
      $tara = $row["tara"];
      $mesaj = $row["mesaj"];
    }
    echo "<br/>";
    echo "<i class='fas fa-user-alt' style='font-size:20px;'>  $nume $prenume</i>";
    echo "<hr/>";
    echo "<i class='fas fa-envelope-square' style='font-size:20px;'></i><strong>  Email:</strong> $email";
    echo "<hr/>";
    echo "<strong>Tara:</strong> $tara";
    echo "<hr/>";
    echo "<h2>Mesaj:</h2>";
    echo $mesaj;}
    ?>
    <a class="inchide" href="" style="z-index:100;">&times;</a></div></div></div>
		<!-------------------- TAB ABONATI NEWSLETTER ---------------------->
			<div class="continutTab" id="continutAbonatiNewsletter" style="<?php if(empty($_GET["categorie"])) echo "display:none;"; else echo "display:block;" ?>">
			<center><h1 style="padding-top:1%;margin-top:0px;"><i class="fas fa-user-friends"></i> Lista abonati newsletter<hr/></h1></center>
			<div id="scroll" style="overflow-x:auto;width:100%;">
      <center><table id="TabelServicii" style="width:90%;color:black;">
  			<tr>
    			<th style="background-color:red;">Nr. crt</th>
    			<th style="background-color:red;">Email</th>
 			  </tr>
			<?php
				$sql = "SELECT * FROM newsletter;";
				$result = mysqli_query($link,$sql);
				WHILE($row = mysqli_fetch_array($result)){
					$idAbonat = $row["id"];
					$emailAbonat = $row["email"];
					echo "<tr><td>$idAbonat</td><td>$emailAbonat</td></tr>";
				}
			?>
		</table></center></div></div>

<!---------------------- TAB POSTEAZA PE BLOG ----------------->
	<div class="continutTab" id="continutPostareBlog" style="display:none;">
		<center><h1 style="padding-top:1%;margin-top:0px;"><i class="fas fa-square"></i> Creeaza o noua postare<hr/></h1></center>
		<center>
			<form method="POST">
				<input type="hidden" name="comanda" value="posteazablog">
				<div style="margin-bottom: 3%; width:80%;">
				<label>Categorie postare</label>
				<select name="categorie">
					<?php 
						$sql = "SELECT * FROM categoriipostari";
						$result = mysqli_query($link,$sql);
						WHILE($row = mysqli_fetch_array($result)){
							$idCateg = $row["id"];
							$denumireCateg = $row["denumireCategorie"];
							echo "<option value='$idCateg'>$denumireCateg</option>";
						}
					?>
				</select></div>
				<div style="margin-bottom: 3%; width:80%;">
				<label>Titlu</label>
				<input type="text" name="titlu" required>
			</div>
			<div style="margin-bottom: 3%; width:80%;">
				<label>Previzualizare</label>
				<input type="text" name="preview" required></div>
				<div style="margin-bottom: 3%; width:80%;">
				<label>Imagine</label>
				<input type="text" name="file"></div>
				<div style="width:80%;">
				<label style="padding-top:100%">Postare completa</label>
				<i class="fas fa-question-circle " style="font-size:22px" title="In interiorul acestui textarea, poti folosi tag-uri HTML pentru stilizarea textului."></i>
				<textarea class="textPostare" name="postare" style="height:300px" cols="40" rows="5" required></textarea></div><br/>
				<input type="submit" style="margin-bottom: 3%;" name="posteaza" value="Posteaza">
			</form></center>

			<hr/>
			<h1 style="margin-top:0px;margin-bottom:0px;"> Nu gasesti categoria potrivita? Creeaza una noua! </h1>
			<hr/>
			<form method="POST">
					<input type="hidden" name="comanda" value="adaugacategorie">
					<label> Nume categorie:  </label>
					<input type="text" name="numecategorie" style="width:50%">
					<input type="submit" name="creeazacategorie" value="Creeaza categorie">
			</form>
			<hr/>
			<h1 style="margin-top:0px;margin-bottom:0px;"> Doresti sa stergi o categorie existenta? Poti face asta aici </h1>
			<hr/>
			<form method="POST">
					<input type="hidden" name="comanda" value="eliminacategorie">
					<label> Nume categorie:  </label>
					<select name="stergecategorie" style="width:50%">
						<?php
						$sql = "SELECT * FROM categoriipostari";
						$result = mysqli_query($link,$sql);
						WHILE($row = mysqli_fetch_array($result)){
								$idCategoriePostare = $row["id"];
								$denumire = $row["denumireCategorie"];
								echo "<option value='$idCategoriePostare'>$denumire</option>";
							}
						?>
					</select>
					<input type="submit" name="stergecateg" value="Sterge categorie">
			</form><br/>
	</div>
<!-------------------- TAB UTILIZATORI ---------------------->
	<div class="continutTab" id="continutUtilizatori" style="<?php if (!isset($_GET["idUt"])) echo "display:none"; else echo"display:block"; ?>">
			<center><h1 style="padding-top:1%;margin-top:0px;"><i class="fas fa-thumbs-up"></i>Utilizatori<hr/></h1></center>
			<center>
			<div id="scroll" style="overflow-x:auto;width:100%;">
      <center><table id="TabelServicii" style="width:90%;color:black;">
  			<tr>
    			<th style="background-color:red;">Poza Profil</th>
    			<th style="background-color:red;">Nume</th>
    			<th style="background-color:red;">Prenume</th>
    			<th style="background-color:red;">Email</th>
    			<th style="background-color:red;">Data Nasterii</th>
    			<th style="background-color:red;">Tara</th>    			
    			<th style="background-color:red;">Programari</th>
    			<th style="background-color:red;">Comenzi</th>

 			  </tr>
 			  <?php
 			  $countUtilizatori = 0;
 			  	$sql = "SELECT * FROM utlizatori;";
 			  	$result = mysqli_query($link,$sql);
 			  	WHILE($row = mysqli_fetch_array($result)){
 			  		$countUtilizatori++;
 			  		$nume = $row["nume"];
 			  		$prenume = $row["prenume"];
 			  		$data_nasterii = $row["data_nasterii"];
 			  		$idUt = $row["id"];
 			  		$tara = $row["tara"];
 			  		$email = $row["email"];
 			  		echo "<tr><td><img src='afiseazapoza.php?ID=$idUt' style='width:100px;height:100px'/></td><td>$nume</td><td>$prenume</td><td>$email</td><td>$data_nasterii</td><td>$tara</td><td><a class='butonRosu' href='?idUt=$idUt&idStatus=1#popupProgramari' style='border-radius:100px;width:200px;height:40px;'>Active</a><a class='butonRosu' href='?idUt=$idUt&idStatus=2#popupProgramari' style='border-radius:100px;width:200px;height:40px;'>Anulate</a><a class='butonRosu' href='?idUt=$idUt&idStatus=3#popupProgramari' style='border-radius:100px;width:200px;height:40px;'>Finalizate</a></td></td><td><a class='butonRosu' href='?idUt=$idUt#popupSetariUtilizator' style='border-radius:100px;width:200px;height:40px;'>Modifica setari</a></td></tr>";
 			  	}
 			  ?>
 			</table></center></div>

 			<hr/> <h1 style="margin-top:0px;margin-bottom:0px;"><i class="fas fa-chart-bar"></i> Statistici</h1> <hr/>
 				<?php 
 				echo "Numar total de utilizatori: ".$countUtilizatori; 

 				?>

<?php if(isset($_GET["idUt"])) { ?>
<div id="popupProgramari" class="overlay" style="width: 100%;z-index:100;position:absolute;padding:0;margin:0">
  <div class="popup">
    <?php
    $idUt2 = $_GET["idUt"];
    $statusProgramare = $_GET["idStatus"];
    switch($statusProgramare){
    	case 1 : echo "<h1 style='padding:0;margin-bottom:0;margin-top:0'>Programari active</h1>"; break;
    	case 2 : echo "<h1 style='padding:0;margin-bottom:0;margin-top:0'>Programari anulate</h1>"; break;
    	case 3 : echo "<h1 style='padding:0;margin-bottom:0;margin-top:0'>Programari finalizate</h1>"; break;
    }
    if(isset($_POST["veziToate"])){
    $sql = "SELECT a.id, a.data,a.detaliisuplimentare, b.nume,b.prenume,c.numeMedic, c.prenumeMedic, c.grad,d.denumire,d.pret,e.denumirec FROM programari a INNER JOIN utlizatori b ON a.idUtilizator = b.id  INNER JOIN medici c ON a.idMedic = c.id INNER JOIN servicii d ON a.idServiciu = d.id INNER JOIN categoriiconsultatii e ON d.idCategorie = e.id WHERE a.idUtilizator = $idUt2;";  }	
  else{
    $sql = "SELECT a.id, a.data,a.detaliisuplimentare, b.nume,b.prenume,c.numeMedic, c.prenumeMedic, d.denumire,d.pret,e.denumirec,g.grad FROM programari a INNER JOIN utlizatori b ON a.idUtilizator = b.id  INNER JOIN medici c ON a.idMedic = c.id INNER JOIN servicii d ON a.idServiciu = d.id INNER JOIN categoriiconsultatii e ON d.idCategorie = e.id INNER JOIN status f ON a.idStatus = f.id INNER JOIN gradmedici g ON c.idGrad = g.id WHERE a.idStatus = $statusProgramare AND a.idUtilizator = $idUt2 AND DATE_FORMAT(a.data,'%Y-%m-%d') >= CURDATE();";  }
    $countProg = 0;
    echo "<br/>";
    		$result = mysqli_query($link,$sql);
    		$sql2 = "SELECT nume, prenume FROM utlizatori WHERE id = $idUt2;";
    		$result2 = mysqli_query($link,$sql2);
    		$row2 = mysqli_fetch_array($result2);
    		echo "<hr style='border:2px solid black;'/><i class='fas fa-user-alt' style='font-size:20px;'> PROGRAMARI: ".$row2["nume"]." ".$row2["prenume"]."</i><hr style='border:2px solid black;'/>";

			WHILE($row = mysqli_fetch_array($result)){
				
				$countProg++;
				echo "
				<form method='POST'>
					<div class='box red' style='width:90%;height:auto'>
							<input type='hidden' name='comanda' value='anuleazaprogramare'>
      				<h2>Data: ".$row["data"]."</h2>
      				<hr/>
      				<h5>Medic: ".$row["numeMedic"]." ".$row["prenumeMedic"]." (".$row["grad"].")"."</h5>
      				<h5>Tip consultatie: ".$row["denumire"]." ( ".$row["denumirec"]." )"."</h5>
      				<h5>Pret: ".$row["pret"]." RON</h5>
      				<p>Detalii suplimentare: ".$row["detaliisuplimentare"]."</p>";
      				if($statusProgramare==1){
      				echo "<button type='submit' value='".$row["id"]."' name='stergeprogramare' class='butonRosu' style='width:auto'>Anuleaza programarea</button>";
    					}
    				echo "</div>
				";
			}
			if($countProg==0){
				echo "<center><h1>Utilizatorul nu are programari planificate</h1></center>";
			}
    ?>
    <a class="inchide" href="" style="z-index:100;">&times;</a></div></div><?php } ?>
<!------------------ POPUP SETARI UTILIZATOR ------------->
<div id="popupSetariUtilizator" class="overlay" style="width: 100%;z-index:100;position:absolute;">
  <div class="popup">
  		<?php
  			$idUtilizatorMod = $_GET["idUt"];
  			$sql = "SELECT * FROM utlizatori WHERE id = $idUtilizatorMod;";
  			$result = mysqli_query($link,$sql);
  			$row = mysqli_fetch_array($result);
  			echo "<form method='POST'><input type='hidden' name='comanda' value='modificasetariutilizator'><input type='hidden' name='idmodutilizator' value='$idUtilizatorMod'>
  			<h3>Nume utilizator</h3><input type='text' name='modificausername' value='".$row["nume_utilizator"]."' required>
  			<h3>Nume</h3><input type='text' name='modificanumeutilizator' value='".$row["nume"]."' required>
  			<h3>Prenume</h3><input type='text' name='modificaprenumeutilizator' value='".$row["prenume"]."' required> 
				<h3>Email</h3><input type='email' name='modificaemailutilizator' style='width:100%;height:45px' value='".$row["email"]."' required> 
				<h3>Tara</h3><select name='modificatara'>";
				foreach($tari as $tara) 
					 if($tara == $row["tara"])
        			echo "<option value='$tara' selected>$tara</option>";
        		else
        			echo "<option value='$tara'>$tara</option>";
				echo "</select>
				<h3>Data nasterii</h3><input type='date' name='modificadatanastere' style='width:100%;height:45px' value='".$row["data_nasterii"]."' required> 
  			<h3>Nivel administrare</h3><select name='modificaniveladministrare' required><option value='1'"; if($row["nivel_administrare"] == 1) echo "selected"; echo ">Utilizator</option><option value='8' ";if($row["nivel_administrare"] == 8) echo "selected"; echo ">Administrator</option><option value='9'"; if($row["nivel_administrare"] == 9) echo "selected"; echo ">Super administrator</option><option value='2'"; if($row["nivel_administrare"] == 2) echo "selected"; echo ">Acces restrictionat</option></select><br/><br/><input type='submit' name='salveazasetariutilizator' value='Modifica setarile'>
  			</form>";
			?> 
    <a class="inchide" href="" style="z-index:100;">&times;</a></div></div></center></div>

<!------------------ FINAL POPUP SETARI UTILIZATOR --------->

<!------------------- TAB SETARI SITE ------------------->
			<div class="continutTab" id="continutSetariSite" style="display:none;">
				<?php 
					$sql = "SELECT * FROM setarisite;";
					$result = mysqli_query($link,$sql);
					WHILE($row = mysqli_fetch_array($result)){
						$linkedin=$row["linkedin"];
						$facebook=$row["facebook"];
						$instagram=$row["instagram"];
						$youtube=$row["youtube"];
						$telefon=$row["telefon"];
						$email=$row["email"];
						$descriere=$row["descriere"];
					}

				?>
			<center><h1 style="padding-top:1%;margin-top:0px;"><i class="fas fa-cogs"></i> Setari Site<hr/></h1></center>
			<center>
				<form method="POST" style="margin-bottom:30px;font-size:20px;width:80%">
					<input type="hidden" name="comanda" value="modificasetari">
					<i class="fab fa-linkedin" style="float:left;font-size: 25px; padding-right:5px; padding-top:5px;"></i><label>LinkedIn</label>
					<input type="text" name="linkedin" value="<?php echo $linkedin; ?>">

					<i class="fab fa-facebook" style="float:left;font-size: 25px; padding-right:5px; padding-top:5px;"></i><label>Facebook</label>
					<input type="text" name="facebook" value="<?php echo $facebook; ?>">

					<i class="fab fa-instagram" style="float:left;font-size: 25px; padding-right:5px; padding-top:5px;"></i><label>Instagram</label>
					<input type="text" name="instagram" value="<?php echo $instagram; ?>">

					<i class="fab fa-youtube" style="float:left;font-size: 25px; padding-right:5px; padding-top:5px;"></i><label>Youtube</label>
					<input type="text" name="youtube" value="<?php echo $youtube; ?>">

					<i class="fas fa-phone-square-alt fa-1.9x" style="float:left;font-size: 25px; padding-right:5px; padding-top:5px;"></i><label>Numar de telefon</label>
					<input type="text" name="telefon" value="<?php echo $telefon; ?>">

					<i class="far fa-envelope" style="float:left;font-size: 25px; padding-right:5px; padding-top:5px;"></i><label>Email</label>
					<input type="email" name="email" style="width:100%;height:50px;" value="<?php echo $email; ?>"><br/><br/>

					<i class="fas fa-comments" style="float:left;font-size: 25px; padding-right:5px; padding-top:5px;"></i><label>Descriere site</label>
					<textarea type="text" name="descriere"><?php echo $descriere; ?></textarea>
					<br/><br/>
					<input type="submit" name="modificasetari" value="Salveaza modificari">
				</form><br/>
			</center>
		</div>

<!----------------- TAB MEDICI -------------------->
			<div class="continutTab" id="continutMedici" style="<?php if (!isset($_GET["idM"])) echo "display:none;"; else echo "display:block;"; ?>">
			<center><h1 style="padding-top:1%;margin-top:0px;"><i class="fas fa-user-nurse"></i> Medici<hr/></h1></center>
			<center>
				<!---------- LISTA MEDICI ------------>
			<div id="scroll" style="overflow-x:auto;width:100%;">
      <center><table id="TabelServicii" style="width:90%;color:black;">
  			<tr>
    			<th style="background-color:red;">Nume</th>
    			<th style="background-color:red;">Prenume</th>
    			<th style="background-color:red;">Cabinet</th>
    			<th style="background-color:red;">Grad</th>			
    			<th style="background-color:red;">Programari</th>
    			<th style="background-color:red;">Comenzi</th>
 			  </tr>
 			  <?php
 			  $countMedici = 0;
 			  $nrGeneralisti = $nrRezidenti = $nrSpecialisti = $nrPrimari = 0; 
 			  	$sql = "SELECT a.id,a.idGrad,a.numeMedic,a.prenumeMedic,b.denumirec,c.grad FROM medici a INNER JOIN categoriiconsultatii b INNER JOIN gradmedici c ON a.idGrad = c.id WHERE a.idCategorie=b.id AND idStatusAngajat=1;";
 			  	$result = mysqli_query($link,$sql);
 			  	WHILE($row = mysqli_fetch_array($result)){
 			  		$countMedici++;
							$nume = $row["numeMedic"];
							$prenume = $row["prenumeMedic"];
							$cabinet = $row["denumirec"];
							$grad = $row["grad"];
							$idM=$row["id"];
							switch($row["idGrad"]){
								case(1): $nrGeneralisti++; break;
								case(2): $nrRezidenti++; break;
								case(3): $nrSpecialisti++; break;
								case(4): $nrPrimari++; break;
							}
 			  			echo "<tr><td>$nume</td><td>$prenume</td><td>$cabinet</td><td>$grad</td><td><center><a class='butonRosu' href='?idM=$idM&idStatus=1#popupProgramariM'>Active</a><a class='butonRosu' href='?idM=$idM&idStatus=2#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Anulate</a><a class='butonRosu' href='?idM=$idM&idStatus=3#popupProgramariM''>Efectuate</a></td><td><a class='butonRosu' href='?idM=$idM#popupSetariMedic'>Profil medic</a></center></td></tr>";
 			  	}
 			  ?>
 			</table></center></div>
<!------- DIVERSE LISTE MEDICI ---------->
<br/>
<br/>
<button type="button" style="background-color: grey;width:90%;height:50px;margin-bottom: 3%;font-size:20px;color:white;" onclick="AfiseazaLista(1);">Mai multe liste</button>
<div id="medici" style="display:none;"> 
<button type="button" onclick="AfiseazaLista(2);"style="width:90%;height:50px;margin-bottom: 3%;font-size:20px;color:black;">Medici in concediu</button>
<div id="medici2" style="display:none;"><?php
			AfiseazaListaMedici(2);
 			  ?>
 			</table><br/></center></div>
</div>
<button type="button" onClick="AfiseazaLista(3);" style="background-color: red;width:90%;height:50px;margin-bottom: 3%;font-size:20px;color:white;">Medici concediati</button>
<div id="medici3" style="display:none;">
			<?php
			AfiseazaListaMedici(4);
 			  ?>
 			</table><br/></center></div>
</div>
<button type="button" onClick="AfiseazaLista(4);" style="font-size:20px;background-color: darkred;width:90%;height:50px;margin-bottom: 3%;color:white;">Medici demisionati</button>
<div id="medici4" style="display:none;">
				<?php
				AfiseazaListaMedici(3);
 			  ?>
 			</table><br/></center></div>
</div>
<button type="button" onClick="AfiseazaLista(5);" style="font-size:20px;background-color: green;width:90%;height:50px;margin-bottom: 3%">Medici pensionati</button>
<div id="medici5" style="display:none;">
				<?php
				AfiseazaListaMedici(5);
 			  ?>
 			</table><br/></center></div>
</div></div>
<!------ SFARSIT DIVERSE LISTE MEDICI --------->
 			<hr/> <h1 style="margin-top:0px;margin-bottom:0px;"><i class="fas fa-chart-bar"></i> Statistici</h1> <hr/>
 				<?php 
 				echo "Numar total de medici disponibili: ".$countMedici;
 				echo "<br/>Dintre care: Generalisti: ".$nrGeneralisti." | "."Rezidenti: ".$nrRezidenti." | Specialisti: ".$nrSpecialisti." | Primari: ".$nrPrimari;
 				echo "<hr/>"; 
 				echo "<br/>Numar total de medici in concediu: ".numarMedici(2);
 				echo "<br/>Numar total de medici pensionati: ".numarMedici(5);
 				echo "<br/>Numar total de medici concediati: ".numarMedici(4);
 				echo "<br/>Numar total de medici care si-au dat demisia: ".numarMedici(3);
 				?>
<!------ POPUP PROGRAMARI MEDICI ------>
<div id="popupProgramariM" class="overlay" style="width: 100%;z-index:100;position:absolute;">
  <div class="popup">
    <?php
    if(isset($_GET["idM"])){
    $idM = $_GET["idM"];
    $fill = $_GET["idStatus"];
    switch($fill){
    	case 1:
    		echo "<h1 style='padding:0;margin-bottom:0;margin-top:0'>Programari active</h1>";
    		break;
    	case 2:
    		echo "<h1 style='padding:0;margin-bottom:0;margin-top:0'>Programari anulate</h1>";
    		break;
    	case 3:
    		echo "<h1 style='padding:0;margin-bottom:0;margin-top:0'>Programari finalizate</h1>";
    		break;
    }
    if(isset($_POST["veziToate"])){
    $sql = "SELECT a.id, a.data,a.detaliisuplimentare, b.nume,b.prenume,c.numeMedic, c.prenumeMedic, c.grad,d.denumire,d.pret,e.denumirec FROM programari a INNER JOIN utlizatori b ON a.idUtilizator = b.id  INNER JOIN medici c ON a.idMedic = c.id INNER JOIN servicii d ON a.idServiciu = d.id INNER JOIN categoriiconsultatii e ON d.idCategorie = e.id WHERE a.idUtilizator = $idM;";  }	
  else{
    $sql = "SELECT a.id, a.data,a.detaliisuplimentare, b.nume,b.prenume,c.numeMedic, c.prenumeMedic, d.denumire,d.pret,e.denumirec,f.grad FROM programari a INNER JOIN utlizatori b ON a.idUtilizator = b.id  INNER JOIN medici c ON a.idMedic = c.id INNER JOIN servicii d ON a.idServiciu = d.id INNER JOIN categoriiconsultatii e ON d.idCategorie = e.id INNER JOIN gradmedici f ON c.idGrad = f.id WHERE a.idStatus=$fill AND a.idMedic = $idM AND DATE_FORMAT(a.data,'%Y-%m-%d') >= CURDATE();";  }
    $countProg = 0;
    echo "<br/>";
    		$result = mysqli_query($link,$sql);
    		$sql2 = "SELECT numeMedic, prenumeMedic FROM medici WHERE id = $idM;";
    		$result2 = mysqli_query($link,$sql2);
    		$row2 = mysqli_fetch_array($result2);
    		echo "<hr style='border:2px solid black;'/><i class='fas fa-user-alt' style='font-size:20px;'> PROGRAMARI: ".$row2["numeMedic"]." ".$row2["prenumeMedic"]."</i><hr style='border:2px solid black;'/>";

			WHILE($row = mysqli_fetch_array($result)){
				
				$countProg++;
				echo "
				<form method='POST'>
					<div class='box red' style='width:90%;height:auto'>

      				<h2>Data: ".$row["data"]."</h2>
      				<hr/>
      				<h5>Pacient: ".$row["nume"]." ".$row["prenume"]."</h5>
      				<h5>Tip consultatie: ".$row["denumire"]." ( ".$row["denumirec"]." )"."</h5>
      				<h5>Pret: ".$row["pret"]." RON</h5>
      				<p>Detalii suplimentare: ".$row["detaliisuplimentare"]."</p>";
      				if($fill==1){
      				echo "<a href='?stergeprogramare=".$row["id"]."' class='butonRosu' style='width:auto'>Anuleaza programare</a> <a href='?finalizeazaprogramare=".$row["id"]."' class='butonRosu' style='width:auto'>Finalizeaza programare</a>";
    					}
    				echo "</div>
				";
			}
			if($countProg==0){
				echo "<center><h1>Medicul nu are programari planificate</h1></center>";
			}}
    ?>
    <a class="inchide" href="" style="z-index:100;">&times;</a></div></div>
    	<!---------- FINAL POPUP PROGRAMARI MEDICI ------------>
    	<!---------- SETARI MEDICI POPUP---------------->
<div id="popupSetariMedic" class="overlay" style="width: 100%;z-index:100;position:absolute;">
  <div class="popup">

    <?php
    if(isset($_GET["idM"])){
    	    $idM = $_GET["idM"];
    	    $sql = "SELECT a.id,a.numeMedic,a.prenumeMedic,a.CNP,a.idGrad,a.idCategorie,b.grad,c.denumirec FROM medici a INNER JOIN gradmedici b ON a.idGrad = b.id INNER JOIN categoriiconsultatii c ON a.idCategorie = c.id WHERE a.id = $idM ;";
    	    $result = mysqli_query($link,$sql);
    	    WHILE($row=mysqli_fetch_array($result)){
    	    	$nume = $row["numeMedic"];
    	    	$idmedicmod = $row['id'];
    	    	$prenume = $row["prenumeMedic"];
    	    	$grad = $row["grad"];
    	    	$cnpMedic = $row["CNP"];
    	    	$denumirec = $row["denumirec"];
    	    	echo "<br/><h2>[Setari] $nume $prenume</h2><hr/> CNP: $cnpMedic <br/>Grad: $grad <br/> Specializare: $denumirec<hr/><h2>Modifica</h2><hr/>";
    	    	echo "<form method='POST'><input type='hidden' name='comanda' value='modificasetarimedic'>
    	    	<input type='hidden' name='idmedicmod' value='$idmedicmod'>
    	    	<h3>Nume</h3> <input type='text' name='modificanumemedic' value='$nume'> <h3>Prenume</h3> <input type='text' name='modificaprenumemedic' value='$prenume'> <h3> Promoveaza </h3> <select name='modificagradmedic'>";
    	    	  $sql2 = "SELECT * FROM gradmedici;";
    	    		$result2 = mysqli_query($link,$sql2);
    	    		WHILE($row2=mysqli_fetch_array($result2)){
    	    			if($row["idGrad"] == $row2[0])
    	    			echo "<option value='".$row2[0]."' selected>".$row2[1]."</option>";
    	    		else
    	    			echo "<option value='".$row2[0]."'>".$row2[1]."</option>";

    	    		}
    	    	echo "</select> <h3> Schimba specializare </h3> <select name='modificaspecializare'>"; 
    	    		$sql2 = "SELECT * FROM categoriiconsultatii;";
    	    		$result2 = mysqli_query($link,$sql2);
    	    		WHILE($row2=mysqli_fetch_array($result2)){
    	    			if($row["idCategorie"] == $row2[0])
    	    			echo "<option value='".$row2[0]."' selected>".$row2[1]."</option>";
    	    		else
    	    			echo "<option value='".$row2[0]."'>".$row2[1]."</option>";

    	    		}
    	    	echo "</select> <h3> Modifica CNP </h3> <input type='text' name='modificaCNP' value='$cnpMedic'> <input type='submit' name='modificamedic' value='Salveaza setarile'></form>";

    	    }}
    ?>
    <a class="inchide" href="" style="z-index:100;">&times;</a></div></div>

    	<!---------- FINAL SETARI MEDICI POPUP ----------->


				<!--------- FINAL LISTA MEDICI --------->


				<hr/><h1 style="padding-top:1%;margin-top:0px;margin-bottom:0px"><i class="fas fa-plus" style="color:green;"></i> Adauga medic </h1> <hr/>
					<form method="POST" style="width:80%">
						<input type="hidden" name="comanda" value="adaugamedic">
						<label> Nume </label>
						<input type="text" name="numemedic" required>
						<label> Prenume </label>
						<input type="text" name="prenumemedic" required>
						<label> CNP </label>
						<input type="text" name="CNP" maxlength="13" required>
						<label> Grad </label>
						<select name="gradmedic">
						<?php
							$sql = "SELECT * FROM gradmedici";
							$result = mysqli_query($link,$sql);
							WHILE($row = mysqli_fetch_array($result)){
								$denumireg = $row["grad"];
								$idgrad = $row["id"];
								echo "<option value='$idgrad'>$denumireg</option>";
							}
						?>
						</select><br/><br/>
						<label> Cabinet </label>
						<select name="categorieserviciu">
						<?php
							$sql = "SELECT * FROM categoriiconsultatii";
							$result = mysqli_query($link,$sql);
							WHILE($row = mysqli_fetch_array($result)){
								$denumirec = $row["denumirec"];
								$idc = $row["id"];
								echo "<option value='$idc'>$denumirec</option>";
							}
						?>
						</select>
						<br/><br/>
						<input type="submit" name="adaugamedic" value="Adauga medic">
					</form>

				<hr/><h1 style="padding-top:1%;margin-top:0px;margin-bottom:0px;"><i class="fas fa-users-cog" style="color:red;"></i> Modifica stare medic</h1><hr/>
				<form method="POST" style="width:80%">
						<input type="hidden" name="comanda" value="stergemedic">
					 <select name="medicid">
						<?php
							$sql = "SELECT a.id,a.numeMedic,a.prenumeMedic,b.denumirec,c.grad FROM medici a INNER JOIN categoriiconsultatii b INNER JOIN gradmedici c ON a.idGrad = c.id WHERE a.idCategorie=b.id AND idStatusAngajat=1;";
							$result = mysqli_query($link,$sql);
							WHILE($row = mysqli_fetch_array($result)){
								$nume = $row["numeMedic"];
								$prenume = $row["prenumeMedic"];
								$cabinet = $row["denumirec"];
								$grad = $row["grad"];
								$idm = $row["id"];
								echo "<option value='$idm'>$nume $prenume - $cabinet - ($grad)</option>";
							}
						?>
						</select>
						<input type="submit" class="acelasirand" name="concediazamedic" style="margin-top: 1%;margin-bottom:1%" value="Concediaza medic">
						<input type="submit" class="acelasirand" name="concediumedic" style="margin-top: 1%;margin-bottom:1%" value="Concediu medic">
						<input type="submit" class="acelasirand" name="demisiemedic" style="margin-top: 1%;margin-bottom:1%" value="Demisie medic">
						<input type="submit" class="acelasirand" name="pensionaremedic" style="margin-top: 1%;margin-bottom:1%" value="Pensioneaza medic">
						<input type="submit" class="acelasirand" name="stergemedic" style="margin-top: 1%;margin-bottom:1%" value="Sterge medic"></form></center></div>

<!----------------- TAB SERVICII --------------------->
			<div class="continutTab" id="continutServicii" style="display:none;">
			<center><h1 style="padding-top:1%;margin-top:0px;"><i class="fas fa-plus" style="color:green;"></i> Adauga o noua consultatie<hr/></h1></center>
			<center>
				<form method="POST" style="width:80%;">
					<input type="hidden" name="comanda" value="adaugaconsultatie">
					<label> Categorie consultatie </label>
					<select name="categorieserviciu">
						<?php
							$sql = "SELECT * FROM categoriiconsultatii";
							$result = mysqli_query($link,$sql);
							WHILE($row = mysqli_fetch_array($result)){
								$denumirec = $row["denumirec"];
								$idc = $row["id"];
								echo "<option value='$idc'>$denumirec</option>";
							}
						?>
					</select>
					<label> Denumire serviciu </label>
					<input type="text" name="denumireserviciu">

					<label> Pret </label>
					<input type="number" name="pret" min="0"> RON<br/>

					<label> Detalii serviciu </label>
					<textarea name="detaliiconsultatie"></textarea>
					<br/><br/>
					<input type="submit" name="adaugaconsultatie" value="Adauga consultatie">

				</form>
				<hr/>
				<center><h1 style="padding-top:1%;margin-top:0px;"><i class="fas fa-plus" style="color:green;"></i> Adauga cabinet nou<hr/></h1></center>
				<form method="POST" style="width:80%">
					<input type="hidden" name="comanda" value="creeazacabinet">
					<label>Denumire cabinet</label>
					<input type="text" name="dencabinet">
					<input type="submit" name="creeazacabinet" value="Creeaza cabinet">
				</form>
				<hr/>
				<center><h1 style="padding-top:1%;margin-top:0px;"><i class="fas fa-trash-alt" style="color:red;"></i> Sterge consultatie<hr/></h1></center>
				<form method="POST" style="width:80%">
					<label> Consultatie </label>
					<input type="hidden" name="comanda" value="eliminaconsultatie">
					<select name="categorieeliminaserviciu">
						<?php
							$sql = "SELECT * FROM servicii";
							$result = mysqli_query($link,$sql);
							WHILE($row = mysqli_fetch_array($result)){
								$denumire = $row["denumire"];
								$idc = $row["id"];
								echo "<option value='$idc'>$denumire</option>";
							}
						?>
					</select><br/><br/>
					<input type="submit" name="stergeconsultatie" value="Sterge consultatie">
				</form>
				<hr/>
				<center><h1 style="padding-top:1%;margin-top:0px;"><i class="fas fa-trash-alt" style="color:red;"></i> Sterge cabinet<hr/></h1></center>
				<form method="POST" style="width:80%">
					<input type="hidden" name="comanda" value="stergecabinet">
					<label> Cabinet </label>
					<select name="categorieconsultatie">
						<?php
							$sql = "SELECT * FROM categoriiconsultatii";
							$result = mysqli_query($link,$sql);
							WHILE($row = mysqli_fetch_array($result)){
								$denumirec = $row["denumirec"];
								$idc = $row["id"];
								echo "<option value='$idc'>$denumirec</option>";
							}
						?>
					</select><br/><br/>
					<input type="submit" name="stergerecabinet" value="Sterge cabinet">
				</form><br/>
			</center>
		</div>

<!----------------- TAB LOGURI --------------------->
			<div class="continutTab" id="continutLoguri" style="<?php if(isset($_POST["filtreazadata"])) echo "display:block"; else echo "display:none"; ?>">
			<center><h1 style="padding-top:1%;margin-top:0px;"><i class="fas fa-plus" style="color:green;"></i> Log-uri<hr/></h1></center>
			<center>
				<form method="POST" style="width:90%">
					<input type="hidden" name="comanda" value="alegedataloguri">
					<input type="date" name="filtreazadata" value="<?php if(isset($_POST["filtreazadata"])) echo $_POST["filtreazadata"]; else echo date('Y-m-d'); ?>" <?php echo "max='".date('Y-m-d')."';"?>>
					<input type="submit" name="alegedata" value="Alege data" style="width:100px;height:40px"><br/><br/>
					<div id="scroll" style="overflow-x:auto;width:100%;">
			<table id="TabelServicii" style="width:90%;color:black;">
  		<tr>
    		<th style="background-color:red;">Nume utilizator</th>
    		<th style="background-color:red;">Actiune</th>
    		<th style="background-color:red;">Timestamp</th>
    		<th style="background-color:red;">IP</th>
  		</tr>
  		<?php
  		if(isset($_POST["filtreazadata"])){
  			$data = $_POST["filtreazadata"];
  		}
  		else{
  			$data=date('Y-m-d');
  		}
  			$sql = "SELECT a.ip,a.actiune,a.data_ora,b.nume_utilizator FROM loguri a INNER JOIN utlizatori b ON a.idAdministrator = b.id WHERE DATE(a.data_ora) ='$data'";
  			$result = mysqli_query($link,$sql);
  			WHILE($row = mysqli_fetch_array($result)){
  				$ip = $row["ip"];
  				$actiune = $row["actiune"];
  				$numeutilizator = $row["nume_utilizator"];
  				$timestamp = $row["data_ora"];
  				echo "<tr><td>$numeutilizator</td><td>$actiune</td><td>$timestamp</td><td>$ip</td></tr>";
  			} 
  		?>
  	</table></div><br/><br/></form>
			</center>
		</div>
<!--------------- FINAL PAGINA---------------->
</div>
</div>
<script>
function schimbaTab(eveniment, numeTab) {
  var i, continutTab

  // Ascunde toate elementele care au clasa continutTab
  continutTab = document.getElementsByClassName("continutTab");
  for (i = 0; i < continutTab.length; i++) {
    continutTab[i].style.display = "none";
  }

  // Afiseaza tab-ul selectat si adauga clasa active la butonul care a activat tab-ul
  document.getElementById(numeTab).style.display = "block";
  eveniment.currentTarget.className += " active";
} 
document.getElementById("data").disabled = true; 

function AfiseazaLista(tinta){
	if(tinta==1)
		var el = document.getElementById("medici");
	else if (tinta==2)
		var el = document.getElementById("medici2");
	else if (tinta==3)
		var el = document.getElementById("medici3");
	else if (tinta==4)
		var el = document.getElementById("medici4");
	else if (tinta==5)
		var el = document.getElementById("medici5");

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
