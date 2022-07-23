<!DOCTYPE html>
<?php 
	session_start();
	if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] == false || $_SESSION["nivel_administrare"] < 7){
			header("location: index.php");
			exit;
		}
		$idUtilizator = $_SESSION["id"];
require_once("connect.php");
include("variabilecomune.php");
global $link2;
$link2 = $link;
 if(array_key_exists('sterge', $_POST)) {
    stergeMesaj();
    	echo "
		<div class='alert' style='overflow:auto;margin-top:5px;background-color:blue'>
 					 <p>Mesaj sters cu succes.</p>
		</div> 
	";
	    header("refresh:3;url=admin-side.php"); // redirect daca se ruleaza query-ul	

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
	header("Location:admin-side.php");
}
if(isset($_POST["posteaza"])){
	$idCategorie = $_POST["categorie"];
	$titlu = $_POST["titlu"];
	$preview = nl2br($_POST["preview"]);
	$idAutor = $_SESSION["id"];
	$poza = $_POST["file"];
	$postare = nl2br($_POST["postare"]);
	if(strlen($titlu) > 5 && strlen($preview) > 100 && strlen($postare) > 255){
		$sql = "INSERT INTO postari(idAutor,titlu,imagine,continut,idCateg,preview) VALUES($idAutor,'$titlu','$poza','$postare',$idCategorie,'$preview');";
		mysqli_query($link,$sql);
		echo "
				<div class='alert' style='overflow:auto;margin-top:5px;background-color:green'>
  				 <span class='closebtn' onclick='this.parentElement.style.display='none';''>&times;</span>
 					 <p>Postare realizata cu succes.</p>
				</div> ";
	}
	else{
			echo "
				<div class='alert' style='overflow:auto;margin-top:5px;background-color:red'>
  				 <span class='closebtn' onclick='this.parentElement.style.display='none';''>&times;</span>
 					 <p>Titlul trebuie sa aibe peste 5 caractere, preview-ul peste 100 de caractere iar postarea peste 255 caractere.</p>
				</div> ";
	}
}
if(isset($_POST["creeazacategorie"])){
	if(strlen($_POST["numecategorie"]) > 3){
		$denCateg = $_POST["numecategorie"];
		$sql = "INSERT INTO categoriipostari (denumireCategorie) VALUES('$denCateg');";
		mysqli_query($link,$sql);
		afiseazaMesaj("green","Ati creat o categorie noua!");
		adaugaInLoguri("Categorie adaugata(Nume categorie: $denCateg)");
	}
	else{
		afiseazaMesaj("red","Numele categoriei trebuie sa aibe peste 3 caractere");
	}

}
if(isset($_POST["stergecateg"]) && isset($_POST["stergecategorie"])){
	$idCategorieStearsa = $_POST["stergecategorie"];
	$sql = "DELETE FROM categoriipostari WHERE id=$idCategorieStearsa;";
	mysqli_query($link,$sql);
	afiseazaMesaj("green","Ati sters categoria.");
	adaugaInLoguri("Categorie stearsa(idCategorie: $idCategorieStearsa)");
}

if(isset($_POST["modificasetari"])){
	$youtube = $_POST["youtube"];
	$linkedin = $_POST["linkedin"];
	$instagram = $_POST["instagram"];
	$facebook = $_POST["facebook"];
	$telefon = $_POST["telefon"];
	$descriere = $_POST["descriere"];
	$email = $_POST["email"];
	$sql = "UPDATE setarisite SET linkedin='$linkedin',facebook='$facebook',youtube='$youtube',instagram='$instagram',telefon='$telefon',email='$email',descriere='$descriere' WHERE id=1;";
	$modifica = mysqli_query($link,$sql);
	if($modifica){
		afiseazaMesaj("green","Modificari efectuate cu succes");
		adaugaInLoguri("Setarile site-ului au fost modificate.");
	}
	else{
		afiseazaMesaj("red","Ceva nu a functionat.");
	}
}

if(isset($_POST["creeazacabinet"]) && isset($_POST["dencabinet"])){
	$dencabinet2 = $_POST["dencabinet"];
	$sql2 = "INSERT INTO categoriiconsultatii(denumirec) VALUES('$dencabinet2');";
	$adaugac = mysqli_query($link,$sql2);
	if($adaugac){
		afiseazaMesaj("green","Cabinet adaugat cu succes");
		adaugaInLoguri("Cabinet adaugat(Denumire: $dencabinet2)");
	}
	else{
		afiseazaMesaj("red","Ceva nu a functionat.");
	}
}

if(isset($_POST["stergecabinet"]) && isset($_POST["categorieconsultatie"])){
	$idCabinet = $_POST["categorieconsultatie"];
	$sql2 = "DELETE FROM categoriiconsultatii WHERE id=$idCabinet;";
	$adaugac = mysqli_query($link,$sql2);
	if($adaugac){
		afiseazaMesaj("green","Cabinet sters cu succes");
    adaugaInLoguri("Cabinet sters(id Cabinet: $idCabinet)");
	}
	else{
		afiseazaMesaj("red","Ceva nu a functionat.");
	}
}

if(isset($_POST["adaugaconsultatie"])){
	if(strlen($_POST["denumireserviciu"]) > 1 && strlen($_POST["detaliiconsultatie"]) > 10){
		$categorie = $_POST["categorieserviciu"];
		$denumires = $_POST["denumireserviciu"];
		$detaliis = $_POST["detaliiconsultatie"];
		$pret = $_POST["pret"];
		$sql = "INSERT INTO servicii (idCategorie,denumire,pret,detalii) VALUES($categorie,'$denumires',$pret,'$detaliis');";
		$result = mysqli_query($link,$sql);
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
				<div class='alert' style='overflow:auto;margin-top:5px;background-color:$culoare'>
  				 <span class='closebtn' onclick='this.parentElement.style.display='none';''>&times;</span>
 					 <p>$mesaj</p>
				</div> ";
}

?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Admin CP</title>
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="stylesheet" type="text/css" href="css/style.css">
		<link rel="stylesheet" type="text/css" href="css/styles.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"/>
<script src="https://kit.fontawesome.com/c684b69a55.js" crossorigin="anonymous"></script>
<style>
	.addMore{
  border: none;
  width: 32px;
  height: 32px;
  float: right;
  transition: all ease-in-out 0.2s;
  cursor: pointer;
}
.addMore:hover{
  border: 1px solid #888;
}
	.textarea2 {
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
.textarea2:focus{
	background: none;
}
* {
  box-sizing: border-box;
}
.background-image {
  position: fixed;
  left: 0;
  right: 0;
  z-index: 1;
  display: block;
  background-image: url("https://www.teahub.io/photos/full/22-229573_red-abstract-wallpaper-4k.jpg");
  width: 100%;
  height: 100%;
  -webkit-filter: blur(5px);
  -moz-filter: blur(5px);
  -o-filter: blur(5px);
  -ms-filter: blur(5px);
  filter: blur(5px);
}
.content {
  position: absolute;
  left: 0;
  right: 0;
  z-index: 9999;
  margin-left: 20px;
  margin-right: 20px;
}
	 /* The alert message box */
.alert {
  padding: 20px;
  width: 100%;
  background-color: black; /* Red */
  color: white;
  margin-bottom: 15px;

}

/* The close button */
.closebtn {
  margin-left: 15px;
  color: white;
  font-weight: bold;
  float: right;
  font-size: 22px;
  line-height: 20px;
  cursor: pointer;
  transition: 0.3s;
}

/* When moving the mouse over the close button */
.closebtn:hover {
  color: black;
} 
a.button2{
	display:inline-block;
	padding:0.5em 3em;
	border:0.16em solid #FFFFFF;
	margin:0 0.3em 0.3em 0;
	box-sizing: border-box;
	width: 80%;
	background-color: #f44336;
	text-decoration:none;
	text-transform:uppercase;
	font-family:'Roboto',sans-serif;
	font-size: 14.3px;
	font-weight:400;
	color:#FFFFFF;
	text-align:center;
	transition: all 0.15s;
}
a.button2:hover{
	color:#DDDDDD;
	border-color:#DDDDDD;
	background-color: red;
}
a.button2:active{
	color:#BBBBBB;
	border-color:#BBBBBB;
}
@media all and (max-width:30em){
	a.button2{
	display:block;
	margin:0.4em auto;
}
} 
@media screen and (min-width: 992px) {
	a.button2{
		width: 24%;
	}
}
@media screen and (min-width: 1000px){
	.img-circle{
		margin-right: 30px;
		margin-left: 10px;
		margin-top: 3px;
	}
	.statusbar{
		display:inline-flex;
		width: 100%;
		border-radius: 50px;
	}
	.meniu-stanga{
		padding-top: 2%;
	}
	.button
}
}

/* Style the tab */
.tab {
  overflow: hidden;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
}

/* Style the buttons that are used to open the tab content */
.tab button {
  background-color: inherit;
  float: left;
  border: none;
  outline: none;
  cursor: pointer;
  padding: 14px 16px;
  transition: 0.3s;
}
/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current tablink class */
.tab button.active {
  background-color: #ccc;
}
.tabcontent {
  display: none;
  margin-top: 2%;
  padding: 6px 12px;
  border-top: none;
}

@media screen and (min-width: 600px){
	.postrow{
		width:45%;
		float:left;
	}
	.postrow1{
		width: 45%;
		float: right;
	}
}
@media screen and (min-width: 800px){
	.imagepp{
		padding-top: 15%;
		padding-left: 10%;
	}
	.acelasirand{
		width: 300px;
		max-width: 250px;
	}
}
</style>
</head>
<body style="background-color:#f44336">
	<!--<div class="background-image"></div>-->
	<div class="content" style="padding-bottom:20px;">
		
			<center><h1 style="text-decoration:underline; color:white;display"><a class="fas fa-backward" href="index.php" style="display: inline-flex;float:left;color:white;text-decoration: none;"></a>PANOUL ADMINISTRATORULUI</h1></center>
	<div class="center" style="backround-color:green;">
		<div class="statusbar" style="width:100%;background-color:white;margin-top: 1%;opacity: 0.9;max-height:19%;border-radius: 50px;">
		<center><div class="img-circle text-center mb-3 imagepp">
			<img src="<?php echo "afiseazapoza.php?ID=$idUtilizator"; ?>" alt="Image" style="top:20%;margin-right:3%" class="shadow">
		</div></center>

			<div class="meniu-stanga" style="width:100%;padding-bottom:2%">
			<a class="button2" onclick=" openCity(event, 'content2')" style="border-radius:50px">Mesaje</a>
			<a class="button2" onclick=" openCity(event, 'content3')" style="border-radius:50px">Abonati newsletter</a>
			<a class="button2" onclick=" openCity(event, 'content4')" style="border-radius:50px">Posteaza</a>
			<a class="button2" onclick=" openCity(event, 'content5')" style="border-radius:50px">Utilizatori</a>
			<a class="button2" onclick=" openCity(event, 'content6')" style="border-radius:50px">Setari</a>
			<a class="button2" onclick=" openCity(event, 'content7')" style="border-radius:50px">Medici</a>
			<a class="button2" onclick=" openCity(event, 'content8')" style="border-radius:50px">Servicii</a>
			<?php if($_SESSION["nivel_administrare"]==9) {?> <a class="button2" onclick=" openCity(event, 'content9')" style="border-radius:50px">Loguri</a><?php } ?>


		</div>
		</div>
	</div>
		<div class="content2" style="width:100%;height: 100%;background-color:white;border-radius:50px;">
			<!-------------------- CONTENT 1 ---------------------->
			<div class="tabcontent" id="content2" style="<?php if(empty($_GET["idUt"]) && empty($_GET["idM"])) echo "display:block;"; else echo "display:none;" ?>">
			<center><h1 style="padding-top:1%"><i class="fas fa-inbox"></i> Mesaje primite <hr/></h1></center>
		<center>	<form style="width:100%;">
  <select name="filtru" id="filtru" style="width:100%;">
      <option value="1" <?php if(isset($_GET["filtru"])) if($_GET["filtru"]==1) echo "selected";?> >Cel mai vechi primul</option>
      <option value="2" <?php if(isset($_GET["filtru"])) if($_GET["filtru"]==2) echo "selected"; ?>>Cel mai nou primul</option>
  </select><br/>
  <input type="submit" value="Aplicati" style="width:50%;margin-top: 10px">
</form> </center>

<div id="scroll" style="overflow-x:auto;width:100%;">
     <br/> <center><table id="customers" style="width:90%;color:black;">
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
  global $numearr;
  global $taraarr;
  global $titluarr;
  global $emailarr;
  global $mesajarr;
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
        <td><center><form method='POST'><a class='button' href='?idMesaj=$id#popup1' style='border-radius:100px;width:100px;height:50px;'>Vezi mesaj</a></br><button class='button' type='submit' value='$id' name='sterge' style='border-radius:100px;width:100px;height:50px;'>Sterge mesaj</button></form></td>
        </tr>";
  }
  if(array_key_exists('mesaje', $_POST)) {
    exportMesaje();
  }
  if(array_key_exists('mesaje2', $_POST)) {
    stergeQuery("DELTE * FROM mesaje;");
  }
// if(array_key_exists('sterge', $_POST)) {
  //  stergeMesaj();
 // }
global $link2;
$link2 = $link;
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
  ?>

</table></center></div>
    <form method="post">
        <center><button type="submit" name="mesaje" class="button" value="" />Export mesaje</button>
                <button type="submit" name="mesaje2" class="button" value="" />Sterge toate mesajele</button>
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
    <a class="close" href="" style="z-index:100;">&times;</a></div></div></div>
		<!-------------------- CONTENT 2 ---------------------->
			<div class="tabcontent" id="content3" style="<?php if(empty($_GET["categorie"])) echo "display:none;"; else echo "display:block;" ?>">
			<center><h1 style="padding-top:1%"><i class="fas fa-user-friends"></i> Lista abonati newsletter<hr/></h1></center>
			<div id="scroll" style="overflow-x:auto;width:100%;">
      <center><table id="customers" style="width:90%;color:black;">
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

<!------------------------------------ CONTENT 3 ----------------->
	<div class="tabcontent" id="content4" style="display:none;">
		<center><h1 style="padding-top:1%"><i class="fas fa-square"></i> Creeaza o noua postare<hr/></h1></center>
		<center>
			<form method="POST">
				<div class="postrow" style="margin-bottom: 3%;">
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
				<div class="postrow1" style="margin-bottom: 3%;">
				<label>Titlu</label>
				<input type="text" name="titlu" required>
			</div>
			<div class="postrow" style="margin-bottom: 3%;">
				<label>Previzualizare</label>
				<input type="text" name="preview" required></div>
				<div class="postrow1" style="margin-bottom: 3%;">
				<label>Imagine</label>
				<input type="text" name="file"></div>
				<div style="width:100%;">
				<label style="width:100%;margin-bottom:3%;white-space: pre-wrap;">Postare completa</label>
				<i class="fas fa-question-circle addMore" style="font-size:22px" title="In interiorul acestui textarea, poti folosi tag-uri HTML pentru stilizarea textului."></i>
				<textarea id="textarea" class="textarea2" name="postare" style="height:300px" cols="40" rows="5" required></textarea></div><br/>
				<input type="submit" style="margin-bottom: 3%;" name="posteaza" value="Posteaza">
			</form></center>

			<hr/>
			<h1> Nu gasesti categoria potrivita? Creeaza una noua! </h1>
			<hr/>
			<form method="POST">
					<label> Nume categorie:  </label>
					<input type="text" name="numecategorie" style="width:50%">
					<input type="submit" name="creeazacategorie" value="Creeaza categorie">
			</form>
			<hr/>
			<h1> Doresti sa stergi o categorie existenta? Poti face asta aici </h1>
			<hr/>
			<form method="POST">
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
<!-------------------- CONTENT 4 ---------------------->
			<div class="tabcontent" id="content5" style="<?php if (!isset($_GET["idUt"])) echo "display:none"; else echo"display:block"; ?>">
			<center><h1 style="padding-top:1%"><i class="fas fa-thumbs-up"></i>Utilizatori<hr/></h1></center>
			<center>
			<div id="scroll" style="overflow-x:auto;width:100%;">
      <center><table id="customers" style="width:90%;color:black;">
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
 			  		echo "<tr><td><img src='afiseazapoza.php?ID=$idUt' style='width:100px;height:100px'/></td><td>$nume</td><td>$prenume</td><td>$email</td><td>$data_nasterii</td><td>$tara</td><td><a class='button' href='?idUt=$idUt&idStatus=1#popupProgramari' border-radius:100px;width:100px;height:50px;'>Active</a><a class='button' href='?idUt=$idUt&idStatus=2#popupProgramari' border-radius:100px;width:100px;height:50px;'>Anulate</a><a class='button' href='?idUt=$idUt&idStatus=3#popupProgramari' border-radius:100px;width:100px;height:50px;'>Finalizate</a></td></td><td><a class='button' href='?idUt=$idUt#popupSetariUtilizator' border-radius:100px;width:100px;height:50px;'>Modifica setari</a></td></tr>";
 			  	}
 			  ?>
 			</table></center></div>

 			<hr/> <h1><i class="fas fa-chart-bar"></i> Statistici</h1> <hr/>
 				<?php 
 				echo "Numar total de utilizatori: ".$countUtilizatori; 

 				?>

<?php if(isset($_GET["idUt"])) { ?>
<div id="popupProgramari" class="overlay" style="width: 100%;z-index:100;position:absolute;">
  <div class="popup">
    <?php
    $idUt2 = $_GET["idUt"];
    $fill = $_GET["idStatus"];
    switch($fill){
    	case 1 : echo "<h1>Programari active</h1>"; break;
    	case 2 : echo "<h1>Programari anulate</h1>"; break;
    	case 3 : echo "<h1>Programari finalizate</h1>"; break;
    }
    if(isset($_POST["veziToate"])){
    $sql = "SELECT a.id, a.data,a.detaliisuplimentare, b.nume,b.prenume,c.numeMedic, c.prenumeMedic, c.grad,d.denumire,d.pret,e.denumirec FROM programari a INNER JOIN utlizatori b ON a.idUtilizator = b.id  INNER JOIN medici c ON a.idMedic = c.id INNER JOIN servicii d ON a.idServiciu = d.id INNER JOIN categoriiconsultatii e ON d.idCategorie = e.id WHERE a.idUtilizator = $idUt2;";  }	
  else{
    $sql = "SELECT a.id, a.data,a.detaliisuplimentare, b.nume,b.prenume,c.numeMedic, c.prenumeMedic, d.denumire,d.pret,e.denumirec,g.grad FROM programari a INNER JOIN utlizatori b ON a.idUtilizator = b.id  INNER JOIN medici c ON a.idMedic = c.id INNER JOIN servicii d ON a.idServiciu = d.id INNER JOIN categoriiconsultatii e ON d.idCategorie = e.id INNER JOIN status f ON a.idStatus = f.id INNER JOIN gradmedici g ON c.idGrad = g.id WHERE a.idStatus = $fill AND a.idUtilizator = $idUt2 AND DATE_FORMAT(a.data,'%Y-%m-%d') >= CURDATE();";  }
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
      				if($fill==1){
      				echo "<button type='submit' value='".$row["id"]."' name='stergeprogramare' class='btn btn-danger' style='width:auto'>Anuleaza programarea</button>";
    					}
    				echo "</div>
				";
			}
			if($countProg==0){
				echo "<center><h1>Utilizatorul nu are programari planificate</h1></center>";
			}
    ?>
    <a class="close" href="" style="z-index:100;">&times;</a></div></div><?php } ?>
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
  			<h3>Nivel administrare</h3><select name='modificaniveladministrare' required><option value='1'"; if($row["nivel_administrare"] == 1) echo "selected"; echo ">Utilizator</option><option value='8' ";if($row["nivel_administrare"] == 8) echo "selected"; echo ">Administrator</option><option value='9'"; if($row["nivel_administrare"] == 9) echo "selected"; echo ">Super administrator</option></select><br/><br/><input type='submit' name='salveazasetariutilizator' value='Modifica setarile'>
  			</form>";
			?> 
    <a class="close" href="" style="z-index:100;">&times;</a></div></div></center></div>

<!------------------ FINAL POPUP SETARI UTILIZATOR --------->





<!------------------- FINAL CONTENT --------------->



<!------------------- CONTENT 5 ------------------->
			<div class="tabcontent" id="content6" style="display:none;">
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
			<center><h1 style="padding-top:1%"><i class="fas fa-cogs"></i> Setari Site<hr/></h1></center>
			<center>
				<form method="POST" style="margin-bottom:30px;font-size:20px">
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
				</form>
			</center>
		</div>
<!----------------- FINAL CONTENT 5 ---------------->

<!----------------- CONTENT 6 -------------------->
			<div class="tabcontent" id="content7" style="<?php if (!isset($_GET["idM"])) echo "display:none;"; else echo "display:block;"; ?>">
			<center><h1 style="padding-top:1%"><i class="fas fa-user-nurse"></i> Medici<hr/></h1></center>
			<center>
				<!---------- LISTA MEDICI ------------>
			<div id="scroll" style="overflow-x:auto;width:100%;">
      <center><table id="customers" style="width:90%;color:black;">
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
								case(4): $nrprimari++; break;
							}
 			  			echo "<tr><td>$nume</td><td>$prenume</td><td>$cabinet</td><td>$grad</td><td><a class='button' href='?idM=$idM&idStatus=1#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Active</a><a class='button' href='?idM=$idM&idStatus=2#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Anulate</a><a class='button' href='?idM=$idM&idStatus=3#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Efectuate</a></td><td><a class='button' href='?idM=$idM#popupSetariMedic' border-radius:100px;width:100px;height:50px;'>Profil medic</a></td></tr>";
 			  	}
 			  ?>
 			</table></center></div>
<!------- DIVERSE LISTE MEDICI ---------->
<br/>
<br/>
<button type="button" class="btn btn-info" data-toggle="collapse" style="background-color: grey;width:90%;height:50px;margin-bottom: 3%;font-size:20px;" data-target="#demo">Mai multe liste</button>
<div id="demo" class="collapse">
<button type="button" class="btn btn-info" data-toggle="collapse" style="width:90%;height:50px;margin-bottom: 3%;font-size:20px;" data-target="#demo2">Medici in concediu</button>
<div id="demo2" class="collapse">
			<div id="scroll" style="overflow-x:auto;width:100%;">
      <center><table id="customers" style="width:90%;color:black;">
  			<tr>
    			<th style="background-color:red;">Nume</th>
    			<th style="background-color:red;">Prenume</th>
    			<th style="background-color:red;">Cabinet</th>
    			<th style="background-color:red;">Grad</th>			
    			<th style="background-color:red;">Programari</th>
    			<th style="background-color:red;">Comenzi</th>
 			  </tr>
 			  <?php
 			  $countMediciInConcediu = 0;
 			  	$sql = "SELECT a.id,a.numeMedic,a.prenumeMedic,b.denumirec,c.grad FROM medici a INNER JOIN categoriiconsultatii b INNER JOIN gradmedici c ON a.idGrad = c.id WHERE a.idCategorie=b.id AND idStatusAngajat=2;";
 			  	$result = mysqli_query($link,$sql);
 			  	WHILE($row = mysqli_fetch_array($result)){
 			  		$countMediciInConcediu++;
							$nume = $row["numeMedic"];
							$prenume = $row["prenumeMedic"];
							$cabinet = $row["denumirec"];
							$grad = $row["grad"];
							$idM=$row["id"];
 			  			echo "<tr><td>$nume</td><td>$prenume</td><td>$cabinet</td><td>$grad</td><td><a class='button' href='?idM=$idM&idStatus=1#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Active</a><a class='button' href='?idM=$idM&idStatus=2#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Anulate</a><a class='button' href='?idM=$idM&idStatus=3#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Efectuate</a></td><td><a class='button' href='?idM=$idM#popupSetariMedic' border-radius:100px;width:100px;height:50px;'>Profil medic</a></td></tr>";
 			  	}
 			  ?>
 			</table></center></div>
</div>
<button type="button" class="btn btn-info" data-toggle="collapse" style="background-color: red;width:90%;height:50px;margin-bottom: 3%;font-size:20px;" data-target="#demo3">Medici concediati</button>
<div id="demo3" class="collapse">
				<div id="scroll" style="overflow-x:auto;width:100%;">
      <center><table id="customers" style="width:90%;color:black;">
  			<tr>
    			<th style="background-color:red;">Nume</th>
    			<th style="background-color:red;">Prenume</th>
    			<th style="background-color:red;">Cabinet</th>
    			<th style="background-color:red;">Grad</th>			
    			<th style="background-color:red;">Programari</th>
    			<th style="background-color:red;">Comenzi</th>
 			  </tr>
 			  <?php
 			  $countMediciConcediati = 0;
 			  	$sql = "SELECT a.id,a.numeMedic,a.prenumeMedic,b.denumirec,c.grad FROM medici a INNER JOIN categoriiconsultatii b INNER JOIN gradmedici c ON a.idGrad = c.id WHERE a.idCategorie=b.id AND idStatusAngajat=4;";
 			  	$result = mysqli_query($link,$sql);
 			  	WHILE($row = mysqli_fetch_array($result)){
 			  		$countMediciConcediati++;
							$nume = $row["numeMedic"];
							$prenume = $row["prenumeMedic"];
							$cabinet = $row["denumirec"];
							$grad = $row["grad"];
							$idM=$row["id"];
 			  			echo "<tr><td>$nume</td><td>$prenume</td><td>$cabinet</td><td>$grad</td><td><a class='button' href='?idM=$idM&idStatus=1#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Active</a><a class='button' href='?idM=$idM&idStatus=2#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Anulate</a><a class='button' href='?idM=$idM&idStatus=3#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Efectuate</a></td><td><a class='button' href='?idM=$idM#popupSetariMedic' border-radius:100px;width:100px;height:50px;'>Profil medic</a></td></tr>";
 			  	}
 			  ?>
 			</table></center></div>
</div>
<button type="button" class="btn btn-info" data-toggle="collapse" style="font-size:20px;background-color: darkred;width:90%;height:50px;margin-bottom: 3%" data-target="#demo4">Medici demisionati</button>
<div id="demo4" class="collapse">
				<div id="scroll" style="overflow-x:auto;width:100%;">
      <center><table id="customers" style="width:90%;color:black;">
  			<tr>
    			<th style="background-color:red;">Nume</th>
    			<th style="background-color:red;">Prenume</th>
    			<th style="background-color:red;">Cabinet</th>
    			<th style="background-color:red;">Grad</th>			
    			<th style="background-color:red;">Programari</th>
    			<th style="background-color:red;">Comenzi</th>
 			  </tr>
 			  <?php
 			  $countMediciDemisionati = 0;
 			  	$sql = "SELECT a.id,a.numeMedic,a.prenumeMedic,b.denumirec,c.grad FROM medici a INNER JOIN categoriiconsultatii b INNER JOIN gradmedici c ON a.idGrad = c.id WHERE a.idCategorie=b.id AND idStatusAngajat=3;";
 			  	$result = mysqli_query($link,$sql);
 			  	WHILE($row = mysqli_fetch_array($result)){
 			  		$countMediciDemisionati++;
							$nume = $row["numeMedic"];

							$prenume = $row["prenumeMedic"];
							$cabinet = $row["denumirec"];
							$grad = $row["grad"];
							$idM=$row["id"];
 			  			echo "<tr><td>$nume</td><td>$prenume</td><td>$cabinet</td><td>$grad</td><td><a class='button' href='?idM=$idM&idStatus=1#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Active</a><a class='button' href='?idM=$idM&idStatus=2#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Anulate</a><a class='button' href='?idM=$idM&idStatus=3#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Efectuate</a></td><td><a class='button' href='?idM=$idM#popupSetariMedic' border-radius:100px;width:100px;height:50px;'>Profil medic</a></td></tr>";
 			  	}
 			  ?>
 			</table></center></div>
</div>
<button type="button" class="btn btn-info" data-toggle="collapse" style="font-size:20px;background-color: green;width:90%;height:50px;margin-bottom: 3%" data-target="#demo5">Medici pensionati</button>
<div id="demo5" class="collapse">
				<div id="scroll" style="overflow-x:auto;width:100%;">
      <center><table id="customers" style="width:90%;color:black;">
  			<tr>
    			<th style="background-color:red;">Nume</th>
    			<th style="background-color:red;">Prenume</th>
    			<th style="background-color:red;">Cabinet</th>
    			<th style="background-color:red;">Grad</th>			
    			<th style="background-color:red;">Programari</th>
    			<th style="background-color:red;">Comenzi</th>
 			  </tr>
 			  <?php
 			  $countMediciPensionari = 0;
 			  	$sql = "SELECT a.id,a.numeMedic,a.prenumeMedic,b.denumirec,c.grad FROM medici a INNER JOIN categoriiconsultatii b INNER JOIN gradmedici c ON a.idGrad = c.id WHERE a.idCategorie=b.id AND idStatusAngajat=4;";
 			  	$result = mysqli_query($link,$sql);
 			  	WHILE($row = mysqli_fetch_array($result)){
 			  		$countMediciPensionari++;
							$nume = $row["numeMedic"];
							$prenume = $row["prenumeMedic"];
							$cabinet = $row["denumirec"];
							$grad = $row["grad"];
							$idM=$row["id"];
 			  			echo "<tr><td>$nume</td><td>$prenume</td><td>$cabinet</td><td>$grad</td><td><a class='button' href='?idM=$idM&idStatus=1#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Active</a><a class='button' href='?idM=$idM&idStatus=2#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Anulate</a><a class='button' href='?idM=$idM&idStatus=3#popupProgramariM' border-radius:100px;width:100px;height:50px;'>Efectuate</a></td><td><a class='button' href='?idM=$idM#popupSetariMedic' border-radius:100px;width:100px;height:50px;'>Profil medic</a></td></tr>";
 			  	}
 			  ?>
 			</table></center></div>
</div></div>
<!------ SFARSIT DIVERSE LISTE MEDICI --------->
 			<hr/> <h1><i class="fas fa-chart-bar"></i> Statistici</h1> <hr/>
 				<?php 
 				echo "Numar total de medici disponibili: ".$countMedici;
 				echo "<br/>Dintre care: Generalisti: ".$nrGeneralisti." | "."Rezidenti: ".$nrRezidenti." | Specialisti: ".$nrSpecialisti." | Primari: ".$nrPrimari;
 				echo "<hr/>"; 
 				echo "<br/>Numar total de medici in concediu: ".$countMediciInConcediu;
 				echo "<br/>Numar total de medici pensionati: ".$countMediciPensionari;
 				echo "<br/>Numar total de medici concediati: ".$countMediciConcediati;
 				echo "<br/>Numar total de medici care si-au dat demisia: ".$countMediciDemisionati;
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
    		echo "<h1>Programari active</h1>";
    		break;
    	case 2:
    		echo "<h1>Programari anulate</h1>";
    		break;
    	case 3:
    		echo "<h1>Programari finalizate</h1>";
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
      				echo "<a href='?stergeprogramare=".$row["id"]."' class='btn btn-danger' style='width:auto'>Anuleaza programare</a> <a href='?finalizeazaprogramare=".$row["id"]."' class='btn btn-danger' style='width:auto'>Finalizeaza programare</a>";
    					}
    				echo "</div>
				";
			}
			if($countProg==0){
				echo "<center><h1>Medicul nu are programari planificate</h1></center>";
			}}
    ?>
    <a class="close" href="" style="z-index:100;">&times;</a></div></div>
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
    <a class="close" href="" style="z-index:100;">&times;</a></div></div>

    	<!---------- FINAL SETARI MEDICI POPUP ----------->


				<!--------- FINAL LISTA MEDICI --------->


				<hr/><h1 style="padding-top:1%"><i class="fas fa-plus" style="color:green;"></i> Adauga medic </h1> <hr/>
					<form method="POST">
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

				<hr/><h1 style="padding-top:1%"><i class="fas fa-users-cog" style="color:red;"></i> Modifica stare medic</h1><hr/>
				<form method="POST">
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

<!----------------- FINAL CONTENT 6 ------------->

<!----------------- CONTENT 7 --------------------->
			<div class="tabcontent" id="content8" style="display:none;">
			<center><h1 style="padding-top:1%"><i class="fas fa-plus" style="color:green;"></i> Adauga o noua consultatie<hr/></h1></center>
			<center>
				<form method="POST">
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
				<center><h1 style="padding-top:1%"><i class="fas fa-plus" style="color:green;"></i> Adauga cabinet nou<hr/></h1></center>
				<form method="POST">
					<label>Denumire cabinet</label>
					<input type="text" name="dencabinet">
					<input type="submit" name="creeazacabinet" value="Creeaza cabinet">
				</form>
				<hr/>
				<center><h1 style="padding-top:1%"><i class="fas fa-trash-alt" style="color:red;"></i> Sterge consultatie<hr/></h1></center>
				<form method="POST">
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
				<center><h1 style="padding-top:1%"><i class="fas fa-trash-alt" style="color:red;"></i> Sterge cabinet<hr/></h1></center>
				<form method="POST">
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
					<input type="submit" name="stergecabinet" value="Sterge cabinet">
				</form>
			</center>
		</div>




<!--------------- FINAL CONTENT 7---------------->
<!----------------- CONTENT 8 --------------------->
			<div class="tabcontent" id="content9" style="display:none;">
			<center><h1 style="padding-top:1%"><i class="fas fa-plus" style="color:green;"></i> Log-uri<hr/></h1></center>
			<center>
				<form method="POST">
					<input type="hidden" name="comanda" value="alegedataloguri">
					<input type="date" name="filtreazadata" value="<?php if(isset($_POST["filtreazadata"])) echo $_POST["filtreazadata"]; else echo date('Y-m-d'); ?>" <?php echo "max='".date('Y-m-d')."';"?>>
					<input type="submit" name="alegedata" value="Alege data" style="width:100px;height:40px"><br/><br/>
					<div id="scroll" style="overflow-x:auto;width:100%;">
			<table id="customers" style="width:90%;color:black;">
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
<!--------------- FINAL CONTENT 8----------------></div></div>
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
