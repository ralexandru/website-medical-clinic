<!DOCTYPE html>
<?php require_once("connect.php"); 
session_start();
global $link2;
$link2 = $link;
  $sql = "SELECT * FROM setarisite;";
  $result = mysqli_query($link,$sql);
  $row = mysqli_fetch_array($result);
  $linkedin=$row["linkedin"];
  $facebook=$row["facebook"];
  $instagram=$row["instagram"];
  $youtube=$row["youtube"];
  $telefon=$row["telefon"];
  $email=$row["email"];
  $descriere=$row["descriere"];
// COMENZI

if(isset($_POST["comanda"])){
  switch($_POST["comanda"]){
    case("comenteaza"):
      adaugaComentariu($_POST["idPostare"],$_SESSION["id"],$_POST["comentariu"]);
        break;
    case("stergecomentariu"):
      stergeComentariu($_POST["comentariuid"]);
      break;
    case("stergePostarea"):
      stergePostare($_GET["id"]);
      break;
    case("aprecieaza"):
      aprecieazaPostare($_GET["id"],$_SESSION["id"]);
      break;
    case("adaugaInNewsletter"):
      AdaugaInNewsletter($_POST["emailNewsletter"]);
      break;
    case("login"):
      Conectare($_POST["numeUtilizator"],$_POST["parola"]);
      break;
    case("inregistrare"):
      Inregistrare($_POST["utilizator"],$_POST["parola"],$_POST["confirmaparola"],$_POST["nume"],$_POST["prenume"],$_POST["tara"],$_POST["data_nasterii"],$_POST["email"],0);
      break;
  }
}

function adaugaComentariu($id_postare,$id_autor_comentariu,$comentariu){
  $sql = "INSERT INTO comentariipostari (id_postare,id_autor_comentariu,comentariu) VALUES($id_postare,$id_autor_comentariu,'$comentariu');";
  $result = mysqli_query($GLOBALS["link2"],$sql);
  if($result){
    //fa ceva
  }
  else{
    //fa altceva
  }
}
function aprecieazaPostare($idPostare,$idUtilizator){
    $sql = "INSERT INTO aprecieripostari(idPostare,idUtilizator) VALUES($idPostare,$idUtilizator);";
    mysqli_query($GLOBALS["link2"],$sql);

}
function stergeComentariu($idComentariu){
  if($_SESSION["nivel_administrare"]>7){
    $sql = "UPDATE comentariipostari SET stareComentariu = 1 WHERE idComentariu=$idComentariu;";
    $result = mysqli_query($GLOBALS["link2"],$sql);
    if($result){

    }
    else{

    }
  }
}
  function AdaugaInNewsletter($email){
    $sql="select * from newsletter where (email='$email');";
    $res=mysqli_query($GLOBALS["link2"],$sql);
    if (mysqli_num_rows($res) > 0) {
      $notificareEmail = "V-ati abonat deja la newsletter-ul nostru!";
    }
    else{
      $sql2 = "INSERT INTO newsletter(email) VALUES('$email');";
      mysqli_query($GLOBALS["link2"], $sql2);
      $notificareEmail = "V-ati abonat cu succes!";
    }
    echo "<h1>".$notificareEmail."</h1>";
  }
function stergePostare($idPostare){
  $sql = "DELETE FROM postari WHERE id=$idPostare";
  mysqli_query($GLOBALS["link2"],$sql);
}
  function Conectare($numeUtilizator,$parola){
    $eroare_login = "";
    $utilizator = trim($numeUtilizator);
    $parolaUtilizator = trim($parola);
        $sql = "SELECT id, nume_utilizator, parola, nume, prenume, poza_profil, nivel_administrare, data_nasterii, tara, email FROM utlizatori WHERE nume_utilizator = ?";
        
        if($stmt = mysqli_prepare($GLOBALS["link2"], $sql)){
            // Setez variabilele pentru query-ul de tip select prezentat mai sus.
            mysqli_stmt_bind_param($stmt, "s", $verifica_numeUtilizator);
            $verifica_numeUtilizator = $numeUtilizator;
            
            // Execut query-ul
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                // Verific daca exista numele de utilizator in baza de date
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Preiau datele aferente utilizatorului din baza de date
                    mysqli_stmt_bind_result($stmt, $id, $utilizator, $hash_parola, $nume, $prenume, $poza_profil, $nivel_administrare, $data_nasterii, $tara,$email);
                    if(mysqli_stmt_fetch($stmt)){
                        // Verific daca hash-ul parolei stocate in baza de date corespunde cu hash-ul generat la conectare. Nu am folosit MD5 deoarece am dorit o protectie suplimentara impotrive rainbow table-urilor
                        if(password_verify($parola, $hash_parola)){
                            // Parola este corecta deci datele sunt stocate in sesiunea utilizatorului
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["nume"] = $nume;
                            $_SESSION["prenume"] = $prenume;
                            $_SESSION["profilepic"] = $poza_profil;
                            $_SESSION["nivel_administrare"] = $nivel_administrare;  
                            $_SESSION["data_nasterii"] = $data_nasterii;
                            $_SESSION["tara"] = $tara; 
                            $_SESSION["email"] = $email; 
                            $_SESSION["passCript"] = $hash_parola;
                            $_SESSION["keyadmin"] = session_id();
                            $_SESSION["nume_utilizator"] = $utilizator;                       
                        } else $eroare_login = "Verificati detaliile de conectare inca o data.";                        
                    }
                } else $eroare_login = "Verificati detaliile de conectare inca o data.";
            } else $eroare_login="Exista o problema de conexiune.";
            echo "<h1>$eroare_login</h1>";
            header( "refresh:2;url=index.php" );
            mysqli_stmt_close($stmt);
        }
    }

    function Inregistrare($numeUtilizator,$parola,$confirma_parola,$nume,$prenume,$tara,$data_nasterii,$email,$niveladministrare){
        $eroare_inregistrare = "";
        $dateValide = 1;
        // Verific daca numele de utilizator este formatat corect
        if(!preg_match('/^[a-zA-Z0-9_]+$/', trim($numeUtilizator))){
            $eroare_inregistrare = "Numele de utilizator poate contine doar litere, cifre si underscore.";
        } else{
            $sql = "SELECT id FROM utlizatori WHERE nume_utilizator = ?";        
            if($stmt = mysqli_prepare($GLOBALS["link2"], $sql)){
                mysqli_stmt_bind_param($stmt, "s", $utilizator);
                $utilizator = trim($numeUtilizator);
                if(mysqli_stmt_execute($stmt)){
                    mysqli_stmt_store_result($stmt);
                    if(mysqli_stmt_num_rows($stmt) == 1){
                        $dateValide = 0;
                        $eroare_inregistrare = $eroare_inregistrare."<br/>Exista deja un cont cu acest nume de utilizator.";
                    } else{
                        $numeUtilizator = trim($numeUtilizator);
                    }
                } else{
                    echo "Ceva nu a functionat cum trebuie!";
                }
            mysqli_stmt_close($stmt);
        }
    }
    
    // Verifica parola
    if(empty(trim($parola))){
        $dateValide = 0;
        $eroare_inregistrare = $eroare_inregistrare."<br/>Va rugam sa introduceti o parola.";     
    } elseif(strlen(trim($parola)) < 6){
        $dateValide = 0;
        $eroare_inregistrare = $eroare_inregistrare."<br/>Parolele trebuie sa aibe peste 6 caractere.";
    } else{
        $parola = trim($parola);
    }
    
    // Verifica confirma parola
    if(empty(trim($confirma_parola))){
        $dateValide=0;
        $eroare_inregistrare = $eroare_inregistrare."<br/>Parolele nu coincid.";     
    } else{
        $confirma_parola = trim($confirma_parola);
        if($dateValide == 1 && $parola != $confirma_parola){
            $dateValide=0;
            $eroare_inregistrare = $eroare_inregistrare."<br/>Parolele nu coincid.";
        }
    }
    
    // Daca nu sunt erori pana acum, $dateValide = 1, deci vom insera noul utilizator in baza de date.
    if($dateValide == 1){
        
        // Prepare an insert statement
        $sql = "INSERT INTO utlizatori(nume_utilizator,parola,nume,prenume,data_nasterii,tara,nivel_administrare,email) VALUES (?, ?,'$nume','$prenume','$data_nasterii','$tara',$niveladministrare,'$email')";         
        if($stmt = mysqli_prepare($GLOBALS["link2"], $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $numeUtilizator, $hash_parola);
            $hash_parola = password_hash($parola, PASSWORD_DEFAULT); // Creates a password hash
            if(mysqli_stmt_execute($stmt)){
            } else{
                echo "Ceva nu a functionat cum trebuie!";
            }
            mysqli_stmt_close($stmt);
        }
    }
    else{ 
        echo "<h1>$eroare_inregistrare</h1>";
        header( "refresh:2;url=index.php" ); 
    }   
}    
?>
<html>
<head>
<title>Clinica Medicala</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="styles/stiluriindex.css">
<script src="https://kit.fontawesome.com/c684b69a55.js" crossorigin="anonymous"></script>
</head>
<body style="background-image: linear-gradient(#f44336, #7a0000);">
    <div id="continut-pagina">
        <div id="continut">
    <!--- BARA MENIU --->
    <div class="bara-meniu" style="position:fixed;width:100%;z-index:2;">
     <i class="fas fa-hand-holding-medical fa-2x" style="padding-right:20px;margin-top:0px;"><a href="index.php" style="text-decoration:none;color:white;">MedClinic</a></i>
        <i class="fas fa-phone-square-alt fa-1.9x" style="margin-top:0px;font-size: 15px; position:absolute;"> <?php echo " ".$telefon; ?></i>
        <i class="far fa-envelope" style="margin-top: 20px;font-size: 15px;"><?php echo " ".$email; ?></i>
      <a href="<?php echo $instagram; ?>" class="fab fa-instagram fa-1.9x" style="float:right;font-size: 25px; padding-right:5px; padding-top:5px;text-decoration:none;color:white;"></a>
      <a href="<?php echo $facebook; ?>" class="fab fa-facebook-square" style="float:right;font-size: 25px; padding-right:5px; padding-top:5px;text-decoration:none;color:white;"></a>
      <a href="<?php echo $linkedin; ?>" class="fab fa-linkedin" style="float:right;font-size: 25px; padding-right:5px; padding-top:5px;text-decoration:none;color:white;"></a>
      <a href="<?php echo $youtube; ?>" class="fab fa-youtube" style="float:right;font-size: 25px; padding-right:5px; padding-top:5px;text-decoration:none;color:white;"></a>
    </div>

<div>
    <!--- Dropdown-ul pentru inregistrare/conectare sau panou utilizator/administrator --->
    <div class="buton-utilizator" style="float:right;margin-top:0px;padding-top:-20px;">
        <div class="buton-meniu">
      <i class="fas fa-user" style="border-radius: 50px;padding-left: 10px;padding-right: 10px; padding-top: 5px; padding-bottom: 5px;display: inline-flex;font-size:40px;color:white;border: 2px solid #960202;background-color: #f44336;margin-top:40px;"><i class="fas fa-arrow-down" style="font-size:15px;display: inline-flex;margin-top:25%;"></i><?php  if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){ $username = $_SESSION["nume_utilizator"]; echo "<span style='font-size: 14px;margin-top:14%'>   $username</span>";} ?></i>
            <div class="buton-utilizator-continut" style="border-radius:50px;background-color:transparent;">
            <?php 
                if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
                    echo '<center><a class="buton-login" href="members-side.php" data-target="#login" style="color:white;">User CP</a>';
                    if($_SESSION["nivel_administrare"]>=8)
                        echo '<center><a class="buton-login" href="admin-side.php" style="color:white;">Admin CP</a>';
                    echo '<center><a class="buton-login" href="logout.php" style="color:white;">Logout</a>';
                }
                else{
                    echo '<center><a class="buton-login" id="declanseaza-modal-logare" style="color:white;">Login</a>
                    <a class="buton-login"id="declanseaza-modal-inregistrare" style="color:white;">Creeaza cont</a></center> ';}
            ?>
            </div>
        </div>
    </div>
</div>
<div class="postareBlog" style="height:80%;">
<?php
if(isset($_GET["id"])){
  $id = $_GET["id"];
  echo "salut <br/>";
    $sql = "SELECT a.id, a.idAutor, a.titlu, a.imagine, a.continut, a.data,a.idCateg,b.nume_utilizator, b.nume, b.prenume, b.poza_profil,c.denumireCategorie FROM postari a INNER JOIN utlizatori b ON a.idAutor = b.id INNER JOIN categoriipostari c ON a.idCateg = c.id WHERE a.id = $id;";
    $result = mysqli_query($link,$sql);
    $row = mysqli_fetch_array($result);
    $titlu = $row["titlu"];
    $idAutor = $row["idAutor"];
    $continut = $row["continut"];
    $data = $row["data"];
    $nume = $row["nume"];
    $prenume = $row["prenume"];
    $poza_profil = $row["poza_profil"];
    $nume_utilizator = $row["nume_utilizator"];
    $denumire_categorie = $row["denumireCategorie"];
    $imagine = $row["imagine"];

    if(isset($titlu)){
      echo " <h3 style='margin-top:7%;margin-left:40%'> Va dorim lectura placuta! </h3>
      <div class='content' style='width:94%;margin-left:3%;background-color:white;opacity:0.8;margin-top:0%;border-radius:60px;margin-top:3%'>
      <img src='$imagine'/ style='width:100%;height:300px;'>
      <center><h3 style='font-weight:bold;font-size:40px'> $titlu </h3></center>
      <p style='margin-left:1%'>Data postarii: $data</p>
      <p style='margin-left:1%'>Categoria: $denumire_categorie</p>
      <hr style='border:1px solid black'/>
      <div style='background-color:white;margin-left:1%;width:98%;text-align:justify;text-justify: inter-word;border-radius:50px'><span style='font-size:20px;'>$continut</div>
      <br/></div><br/>
      <div class='detaliiautor' style='margin-left:3%;width:94%;background-color:grey;border-radius:60px;display:inline-flex;padding-bottom:0%'>
        <img src='afiseazapoza.php?ID=$idAutor' style='width:200px;height:200px;border-radius:60px;'/>
        <div>
        <span style='color:white;font-size:20px;'> Autor: $nume $prenume</span><br/>
        <span style='color:white;font-size:20px;'> Nume utilizator: $nume_utilizator</span><br/>
        <span style='color:white;font-size:20px;'> Data postarii: $data</span>
</div>
      </div>
      ";
    }
    else{
      echo "<div style='padding-top:15%;padding-left:27%'><span style='font-size:50px;padding-top:500px;'> Eroare 404: Postarea nu exista! </span></div>";
    }
  }
?>
<?php if(isset($titlu)){ ?>
</div>
<?php
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
  $idUtilizator = $_SESSION["id"];
  $sql = "SELECT * FROM aprecieripostari WHERE idPostare = $id AND idUtilizator = $idUtilizator;";
}
  $result = mysqli_query($link,$sql);
  $num_rows = mysqli_num_rows($result);
  $sql = "SELECT * FROM aprecieripostari WHERE idPostare = $id";
  $result = mysqli_query($link,$sql);
  $aprecieriPostare = mysqli_num_rows($result);
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
if($_SESSION["nivel_administrare"] == 9)
  echo "<form method='POST'><input type='hidden' name='comanda' value='stergePostarea'><button class='butonRosu' name='sterge' style='float:right;margin-right:5%' value='$id'>Sterge postarea</button></form>";
}
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
  if($num_rows == 0)
    echo "<form method='POST'><input type='hidden' name='comanda' value='aprecieaza'><input type='hidden' name='idPostare' value='$id'><input type='submit' class='butonRosu' style='float:left;margin-left:4%' value='Aprecieaza'></form>";
  else
    echo "<a class='butonRosu' href='?id=$id&aprecieaza=1' style='float:left;margin-left:4%'>Aceasta postare v-a ajutat!</a>";
}
echo "<br/> <br/> <br/><span style='color:white;margin-left:4%'>Postarea a fost apreciata de $aprecieriPostare persoane!</span>";
if(isset($_GET["sterge"]) && $_GET["sterge"]==1){
  $sql = "DELETE FROM postari WHERE id=$id;";
}

      if(isset($_SESSION["loggedin"])){
        echo "<hr/><br/>
            <form method='POST' style='width:80%;margin-left:10%'>
            <input type='hidden' name='idPostare' value='".$_GET["id"]."'>
            <input type='hidden' name='comanda' value='comenteaza'>
            <label style='color:white'><i class='fas fa-comments'></i> Lasa un comentariu</label>
            <textarea name='comentariu' required></textarea>
            <input type='submit' name='comenteaza' value='Posteaza comentariul'>
            </form>
        ";
      }
?>

<button type="button" class="btn btn-info" data-toggle="collapse" style="width: 80%;margin-left: 10%;margin-top: 2%;height:50px;margin-bottom: 3%" data-target="#demo" onclick="afiseazaComentarii();">Vezi comentariile +</button>
  <div id="demo" class="collapse" style="display:none">
      <div class="zonacomentariu" style="width:100%;margin-left:0%">
        <h2 style="margin-top:0%;color:white;">Toate comentariile:</h2>
        <hr/>
          <?php
            $sql = "SELECT a.idComentariu,a.comentariu,a.data,b.id,b.nume_utilizator,b.nume,b.prenume FROM comentariipostari a INNER JOIN utlizatori b ON a.id_autor_comentariu = b.id WHERE id_postare=".$_GET["id"]." AND stareComentariu!='1';";
            $result = mysqli_query($link,$sql);
            WHILE($row=mysqli_fetch_array($result)){
              $id_autor_comentariu = $row["id"];
              $idComentariu = $row["idComentariu"];
              $comentariu=$row["comentariu"];
              $data = $row["data"];
              $nume_utilizator = $row["nume_utilizator"];
              $nume =$row["nume"];
              $prenume = $row["prenume"];
              echo "<div class='comentzone' style='border:1px solid red;height:150px;overflow-y: scroll;background-color:white'>";
              echo "<img src='afiseazapoza.php?ID=$id_autor_comentariu' style='width:150px;height:150px;margin-top:0px;padding-top:0%'/>
              <div style='display:inline-block'><span style=''><strong>Nume: $nume <br/> Prenume: $prenume <br/>Data: $data <br/>Nume utilizator: $nume_utilizator</strong><span></div>";

              
              echo "<hr style='border:1px solid black;margin-top:0%;'/><div class='comentariu' style='width:100%;float:right;margin-left:0%;background-color:white;height:auto;min-height:150px;'>
               <span style=''>$comentariu</span><br/>
              <br/>"; 
                             if(isset($_SESSION["nivel_administrare"])){
                if($_SESSION["nivel_administrare"] >= 8){
                  echo "<form method='POST' style='width:100%;display:inline-block;'>
                    <input type='hidden' name='comanda' value='stergecomentariu'>
                    <input type='hidden' name='comentariuid' value='$idComentariu'>
                    <input type='submit' name='stergec' value='Sterge Comentariu'>                
                </form>";}
              }
               echo "</div></div><br/><br/>
               ";

            }
          ?>
      </div>
  </div>
<?php }?>
    <!--- Formularul de login --->
<div id="Continut-Modal-Logare" class="continut-modal">
  <div class="continut-modal-formular" style="width:40%">
                     <h4 style="color:white;">Login</h4>
                     <form method="POST">
                        <input type="hidden" name="comanda" value="login">
                        <p style="float:left;color:white;">Nume de utilizator:</p>
                        <input type="text" name="numeUtilizator" class="numeUtilizator form-control" placeholder="Username" style="color:white;"/>
                        <p style="float:left;color:white;">Parola:</p>
                        <input type="password" name="parola" class="parola form-control" placeholder="parola" style="width: 100%;height: 40px;color:white;"/>
                        <input class="btn login" type="submit" value="Login" />
                    </form>
        </div>  
    </div>
<div id="Continut-Modal-Inregistrare" class="continut-modal">
  <div class="continut-modal-formular" style="width:50%">
        <h4 style="color:white;margin:0;">Creeaza un cont!</h4>
          <?php echo $username_err."<br/>".$password_err."<br/>".$confirm_password_err; ?>
        <form method="POST">
          <input type="hidden" name="comanda" value="inregistrare">
          <p style="float:left;color:white;">Nume de utilizator:</p>
          <input type="text" name="utilizator" class="numeUtilizator form-control" placeholder="Nume utilizator" style="color:white;"/>
          <p style="float:left;color:white;">Nume de familie:</p>
          <input type="text" name="nume" class="numeUtilizator form-control" placeholder="Nume de familie" style="color:white;"/>
          <p style="float:left;color:white;">Prenume:</p>
          <input type="text" name="prenume" class="numeUtilizator form-control" placeholder="Prenume" style="color:white;"/>
          <p style="float:left;color:white;">Email:</p>
          <input type="email" name="email" class="numeUtilizator form-control" placeholder="Prenume" style="width: 100%;height: 40px;color:white;"/>
          <p style="float:left;color:white;">Data nasterii:</p><br/><br/>
          <input type="date" style="width:100%;height:40px" name="data_nasterii">
          <p style="float:left;color:white;">Tara:</p>
          <select name="tara">
             <?php
            foreach($tari as $tara)
                   echo "<option value='$tara'>$tara</option>";                  
            ?>
          </select>
          <p style="float:left;color:white;">Parola:</p>
          <input type="password" name="parola" class="parola form-control" placeholder="parola" style="width:100%;height:40px;color:white;"/>
          <p style="float:left;color:white;">Confirma parola:</p>
          <input type="password" name="confirmaparola" class="parola form-control" placeholder="parola" style="width:100%;height:40px;color:white;"/>
          <input class="btn login" type="submit" value="Creeaza cont"/>
        </form> 
</div></div>
    <!----------- Notes eroare inregistrare ------------>

<div id="popupNotes" class="overlay" style="z-index:100;position:absolute;">
  <div class="popup">
    <?php
       echo $username_err."<br/>".$password_err."<br/>".$confirm_password_err;
    ?>
    <a class="close" href="#" style="z-index:100;">&times;</a>
    <div class="content">
    </div>
  </div>
</div>


<!------------- FOOTER PAGINA -------------------->
 </div>
    <div class="footer-jos" id="footer" style="margin-top:100%; background-color:#c81313q">
        <div class="footer-stanga">
            <center><h3><i class="fas fa-hand-holding-medical fa-2x" style="padding-right:20px;margin-top:0px; color:white;font-size:40px">MedClinic</i></h3></center>
             <center><hr style="color: white; width:70%"/>
           <p style="color:white;margin-left:5%">Site realizat de către RĂDUCU Alexandru-Florian pentru proiectul din cadrul disciplinei Programare WEB Avansată.</p>
                <p style="color:white;">© 2021, Toate drepturile rezervate.</p></center>
        </div>
        <div class="linie-verticala" style="left: 35%;"></div>
        <div class="footer-mijloc">
            <center><h3><i class="fas fa-list fa-2x" style="padding-right:20px;margin-top:0px; color:white;font-size:30px"> Meniu</i></h3>
            <hr style="color: white; width:50%"/>
            
                <a href="#" style="color:white; font-size:20px;text-decoration: none">Despre noi</a><br/>
                <a href="#" style="color:white; font-size:20px;text-decoration: none">Servicii</a><br/>
                <a href="#" style="color:white; font-size:20px;text-decoration: none">Trimite-ne un mesaj</a><br/>
                <a href="#" style="color:white; font-size:20px;text-decoration: none">Blog</a>
            </center>
        </div>
        <div class="linie-verticala" style="left: 65%;"></div>
        <div class="footer-dreapta">
            <center><h3><i class="far fa-envelope fa-2x" style="padding-right:20px;margin-top:0px; color:white;font-size:25px"> Abonează-te la newsletter</i></h3>
            <hr style="color: white; width:90%"/>
                <p style="color:white">Nu rata nimic nou, abonează-te la newsletter-ul nostru:</p>
                <form method="POST">
                    <input type="hidden" name="comanda" value="adaugaInNewsletter">
                    <input type="email" name="emailNewsletter" style="border-radius:50px;width:300px; height:35px"><br/>
                    <input type="submit" name="butonAbonare" value="Aboneaza-te" style="background-color:#960f00; text-decoration:none;border:0px;width:125px;height: 30px;font-size:15px;margin: 0;padding: 0;color:white;margin-top:2%;border-radius:50px">
                </form>
            </center>
        </div>
    </div>   
</div>
<script>

// JavaScript pentru ferestrele modale

    // Gaseste divurile apartinand acestor clase
var modalInregistrare = document.getElementById("Continut-Modal-Inregistrare");
var modalLogare = document.getElementById("Continut-Modal-Logare");

    // Butoanele care declanseaza div-urile de mai sus
var btn = document.getElementById("declanseaza-modal-inregistrare");
var btnLogare = document.getElementById("declanseaza-modal-logare");

    // Se schimba display-ul cand sunt apasate butoanele
btn.onclick = function() {
  modalInregistrare.style.display = "block";
}
btnLogare.onclick = function() {
  modalLogare.style.display="block";
}

    // Cand se apasa oriunde in afara ferestrei modale, aceasta se inchide
window.onclick = function(event) {
  if (event.target == modalInregistrare) {
    modalInregistrare.style.display = "none";
  }
  if (event.target == modalLogare){
    modalLogare.style.display = "none";
  }
}

function afiseazaComentarii(){
    var el = document.getElementById("demo");

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