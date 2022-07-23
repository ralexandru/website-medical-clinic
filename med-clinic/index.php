<!DOCTYPE html>
<?php 
  require_once("connect.php"); 
  include("variabilecomune.php");
  session_start();
  $filtru="";
  global $link2;
  $link2 = $link;
  $mesajNetrimis = 0;
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
  if(isset($_POST["comanda"])){
    switch($_POST["comanda"]){
      case("filtreazaservicii"):
          if($_POST["filtruservicii"] == 0)
            $filtru = "";
          else
            $filtru = "WHERE idCategorie=".$_POST["filtruservicii"].";";
          break;
      case("adaugaInNewsletter"):
          AdaugaInNewsletter($_POST["emailNewsletter"]);
          break;
      case("trimiteMesaj"):
          TrimiteMesaj($_POST["prenume"],$_POST["nume"],$_POST["email"],$_POST["tara"],$_POST["mesaj"]);
          break;
      case("login"):
          Conectare($_POST["numeUtilizator"],$_POST["parola"]);
          break;
      case("inregistrare"):
          Inregistrare($_POST["utilizator"],$_POST["parola"],$_POST["confirmaparola"],$_POST["nume"],$_POST["prenume"],$_POST["tara"],$_POST["data_nasterii"],$_POST["email"],0);
          break;
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

  function TrimiteMesaj($prenume, $nume, $email, $tara, $mesaj){
     $sqlMesaj = "INSERT INTO mesaje(prenume, nume, email, tara, mesaj) VALUES('$prenume','$nume','$email','$tara','$mesaj');";
     $result = mysqli_query($GLOBALS["link2"], $sqlMesaj);
     if($result){
        $mesajNetrimis = 1;
      }
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
        
        // Pregatesc un query de insert
        $sql = "INSERT INTO utlizatori(nume_utilizator,parola,nume,prenume,data_nasterii,tara,nivel_administrare,email) VALUES (?, ?,'$nume','$prenume','$data_nasterii','$tara',$niveladministrare,'$email')";         
        if($stmt = mysqli_prepare($GLOBALS["link2"], $sql)){
            mysqli_stmt_bind_param($stmt, "ss", $numeUtilizator, $hash_parola);
            $hash_parola = password_hash($parola, PASSWORD_DEFAULT); // Creaza hash-ul parolei
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
    <title>Raducu Alexandru-Florian(Programare WEB Avansata - Proiect)</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://kit.fontawesome.com/c684b69a55.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="styles/stiluriindex.css">
<style>
</style>
</head>
<body>
    <div id="continut-pagina">
        <div id="continut">
    <!--- BARA MENIU --->
    <div class="bara-meniu" style="position:fixed;width:100%;z-index:2;">
	   <i class="fas fa-hand-holding-medical fa-2x" style="padding-right:20px;margin-top:0px;">MedClinic</i>
        <i class="fas fa-phone-square-alt fa-1.9x" style="margin-top:0px;font-size: 15px; position:absolute;"> <?php echo " ".$telefon; ?></i>
        <i class="far fa-envelope" style="margin-top: 20px;font-size: 15px;"><?php echo " ".$email; ?></i>
	    <a href="<?php echo $instagram; ?>" class="fab fa-instagram fa-1.9x" style="float:right;font-size: 25px; padding-right:5px; padding-top:5px;text-decoration:none;color:white;"></a>
	    <a href="<?php echo $facebook; ?>" class="fab fa-facebook-square" style="float:right;font-size: 25px; padding-right:5px; padding-top:5px;text-decoration:none;color:white;"></a>
	    <a href="<?php echo $linkedin; ?>" class="fab fa-linkedin" style="float:right;font-size: 25px; padding-right:5px; padding-top:5px;text-decoration:none;color:white;"></a>
	    <a href="<?php echo $youtube; ?>" class="fab fa-youtube" style="float:right;font-size: 25px; padding-right:5px; padding-top:5px;text-decoration:none;color:white;"></a>
    </div>

    <!--- PAGINA --->
    <div style="width:100%; position:fixed;margin-top:80px;z-index:4;">

    <!--- Dropdown-ul pentru inregistrare/conectare sau panou utilizator/administrator --->
    <div class="buton-utilizator" style="float:right;margin-top:0px;padding-top:-20px;">
        <div class="buton-meniu">
        <i class="fas fa-user" style="border-radius: 50px;padding-left: 10px;padding-right: 10px; padding-top: 5px; padding-bottom: 5px;display: inline-flex;font-size:40px;color:white;border: 2px solid #960202;background-color: #f44336;margin-top:40px;"><i class="fas fa-arrow-down" style="font-size:15px;display: inline-flex;margin-top:25%;"></i><?php  if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){ $username = $_SESSION["nume_utilizator"]; echo "<span style='font-size: 14px;margin-top:14%'>   $username</span>";} ?></i>
            <div class="buton-utilizator-continut" style="border-radius:50px;background-color:transparent;">
            <?php 
                if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
                    echo '<center><a class="buton-login" href="panou-membru.php" data-target="#login" style="color:white;">Panou utilizator</a>';
                    if($_SESSION["nivel_administrare"]>=8)
                        echo '<center><a class="buton-login" href="panou-administrator.php" style="color:white;">Panou administrator</a>';
                    echo '<center><a class="buton-login" href="logout.php" style="color:white;">Logout</a>';
                }
                else{
                    echo '<center><a class="buton-login" href="#" id="declanseaza-modal-logare" style="color:white;">Login</a>
                    <a class="buton-login" href="#" id="declanseaza-modal-inregistrare" style="color:white;">Creeaza cont</a></center> ';}
            ?>
            </div>
        </div>
    </div>
</div>

<!--- Slideshow-ul cu imagini --->
<div class="imagini-slide" style="padding-top:5.1%;">
    <div class="imaginiSlide efectImagine">
        <img src="<?php echo 'img/slide1.png';?>" style="width:100%;height:300px">
    </div>

    <div class="imaginiSlide efectImagine">
        <img src="img/slide2.png" style="width:100%;height:300px">
    </div>

    <div class="imaginiSlide efectImagine">
        <img src="img/slide1.png" style="width:100%;height:300px">
    </div>
</div>

<br>
<br/><center>

<!--- NUMAR DE CAZURI/FA O PROGRAMARE --->
<div style="padding-top:10%;"></div>
    <div style="display:inline-flex">
	   <div><i class="fas fa-virus" style="font-size:60px;display: inline-flex;padding-top:15px;color:#f44336;padding-bottom:14px;"></i><br/><a class="butonRosu" href="#popupcovid" style="display: inline-flex;"> Informatii Covid19</a></div>
		  <div class="vl"></div>

	   <div><i class="fas fa-clock" style="font-size:60px;display: inline-flex;padding-top:15px;color:#f44336;padding-bottom:14px;"></i><br/><a class="butonRosu" style="display: inline-flex;"
        <?php
            if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
                echo "href='panou-membru.php'";
            }
            else
                echo 'data-target="#login" id="declanseaza-modal-logare2" data-toggle="Continut-Modal-Logare"';
        ?>>Fa o programare</a></div>
    </div>
<hr/>

<!--- DESPRE NOI --->

    <h3 id="despre">Despre noi</h3>
    <hr/>
    <?php echo $descriere; ?>
    <hr/>

<!--- SERVICII --->

    <h3 id="servicii">Servicii</h3>
    <hr/>
    <form action="index.php" method="POST" style="margin-left:30px;">
        <label>Aplica un filtru: </label>
        <input type="hidden" name="comanda" value="filtreazaservicii">
        <select name="filtruservicii" style="width:20%;margin-left:30px;">
        <option value="0" >TOATE CONSULTATIILE</option>";
        <?php
            //Incarca cabinetele medicale in select
            $sql = "SELECT * FROM categoriiconsultatii;";
            $result=mysqli_query($link,$sql);
            WHILE($row=mysqli_fetch_array($result)){
            if(isset($_POST["filtruservicii"]) && $_POST["filtruservicii"] == $row["id"])
                echo "<option value='".$row["id"]."' selected>".$row["denumirec"]."</option>";
            else
                echo "<option value='".$row["id"]."'>".$row["denumirec"]."</option>";
            }       
        ?>
        </select>
        <input type="submit" style="width:20%;margin-left:30px;" name="aplicafiltru" value="Aplica filtru">
    </form>

    <table id="TabelServicii" style="width:80%">
        <tr>
            <th><center>Serviciu</center></th>
            <th><center>Preț</center></th>
            <th><center>Detalii</center></th>
        </tr>
        <?php
        //Incarca servciile medicale in tabel
           $sql = "SELECT * FROM servicii $filtru;";
	       $result = mysqli_query($link,$sql); 
	       WHILE($row = mysqli_fetch_array($result)){
		      $denumire = $row["denumire"];
		      $id = $row["id"];
		      $detalii = $row["detalii"];
		      $pret = $row["pret"];
		      echo "<tr>
				<td> $denumire</td>
				<td>$pret</td>
				<td><center><a class='butonRosu' href='?id=$id#popupServicii' border-radius:100px;width:100px;height:50px;'>Detalii</button></center></td>
				</tr>";
	       }
	   ?>
    </table>
    <!--- Popup detalii serviciu --->

    <div id="popupServicii" class="overlay" style="z-index:100;position:absolute;">
	   <div class="popup">
		  <?php
		      $id = $_GET["id"];
		      $sql2 = "SELECT denumire,detalii FROM servicii WHERE id = $id;";
		      $result = mysqli_query($link,$sql2);
		      WHILE($row = mysqli_fetch_array($result)){
			     $detalii2 = $row["detalii"];
			     $titlu = $row["denumire"];
		      }
		      echo "<h2>$titlu</h2>";
		      echo "<hr/>"
		    ?>
		  <a class="inchide" href="#" style="z-index:100;">&times;</a>
		  <div class="content">
            <?php
				echo $detalii2;
			?>
		  </div>
	   </div>
    </div>

    <!--- Popup mesaj trimis --->

    <div id="popuptrimis" class="overlay" style="z-index:100;position:absolute;">
	   <div class="popup">
		  <?php 
		      if($mesajNetrimis==0)
		  	     echo "<h2>Mesaj trimis</h2>";
		      else
			     echo "<h2>Mesaj netrimis</h2>";
          ?>
		  <a class="inchide" href="#" style="z-index:100;">&times;</a>
		  <div class="content">
		  <?php
		      if($mesajNetrimis==0)
			     echo "O sa primiti un raspuns in curand!";
		      else
			     echo "Va rugam sa completati toate campurile!";?>			
		  </div>
	   </div>
    </div>

    <!--- SECTIUNE CONTACT --->

    <hr/>
        <h3 id="contact">Contacteaza-ne</h3>
    <hr/>

    <center>
        <div class="continut" style="width:80%;background-color: #f44336; border-radius:10px;">
            <div style="text-align:center">
        </div>
        <div>
            <div class="coloana">
                <img src="img/map.png" style="width:100%;border-radius:50px;">
            </div>
        <div class="coloana">
            <form name="trimiteMesaj" action="#popuptrimis" method="POST">
                <input type="hidden" name="comanda" value="trimiteMesaj">
                <label for="fname" style="color:white;">Prenume:</label>
                <input type="text" style="border-radius: 100px;" name="prenume" placeholder="Prenumele dvs.." required>
                <label for="lname" style="color:white;">Nume:</label>
                <input type="text" style="border-radius: 100px;" name="nume" placeholder="Numele dvs.." required>
                <label for="lname" style="color:white;">Email:</label><br/>
                <input type="email" style="border-radius: 100px;width:100%;height:50px;" name="email" placeholder="Emailul dvs.." required><br/>
                <label for="country" style="color:white;">Tara:</label>
                <select id="country" style="border-radius: 100px;" name="tara" required>
                    <?php
                    //Incarca tarile in select din vectorul care se afla in fisierul(variabilecomune.php)
                        foreach($tari as $tara) 
                            echo "<option value='$tara'>$tara</option>"; 
                    ?>
                </select>
                <label for="mesajComplet" style="color:white;">Mesaj:</label>
                <textarea id="mesajComplet" name="mesaj" placeholder="Scrieti mesajul dorit.." style="height:170px" required></textarea>
                <input type="submit" value="Submit">
            </form>
        </div>
    </div>
</div>
</center>

    <div style="margin-bottom:0px;padding-bottom: 0px;bottom:0px;">
        <section style="height:80px;" style="margin-bottom:0px;padding-bottom: 0px;bottom:0px;"></section>
	    <div style="text-align:center;">
	   </div>
        <hr/>
        <h3 id="blog">Ultimele 3 postari de pe blog</h3>
        <hr/>
		<div class='continut' style='width:80%;border-radius: 10px;'>
            <?php
	           $sql = "SELECT a.id, a.idAutor,a.titlu, a.imagine, a.preview, a.data,a.idCateg, b.nume, b.prenume, b.poza_profil,c.denumireCategorie FROM postari a INNER JOIN utlizatori b ON a.idAutor = b.id INNER JOIN categoriipostari c ON a.idCateg = c.id ORDER by a.data DESC;";
	           $result = mysqli_query($link,$sql);
	           $count = 0;
	           WHILE($row = mysqli_fetch_array($result))
	           {
                    if($count==3)
                        break;
		            $count++;
                    $id = $row["id"];
		            $titlu = $row["titlu"];
                    $idAutor = $row["idAutor"];
		            $imagine = $row["imagine"];
		            $preview = $row["preview"];
		            $autor = $row["nume"]." ".$row["prenume"];
		            $data = $row["data"];
		            $pozaautor = $row["poza_profil"];
		            $denumire = $row["denumireCategorie"];
		            echo "
  			           <div class='postare'>
    			             <div class='postare-titlu'>
      				         <img src='$imagine' alt='rover' />
    			       </div>
    		           <div class='postare-previzualizare'>
      			           <span class='categorie categorie-culoare'>$denumire</span>
      		               <h3>$titlu</h3>
                           <hr style='color:black;border: 1px solid black; width:100%'/>
      		               <p>$preview<br/><a class='butonRosu' href='postare-blog.php?id=$id'>Citeste mai mult</a><br/></p>
                           <hr style='color:black;border: 1px solid black; width:100%'/>
                           Autor:
                           <div class='utilizator'>
                               <img src='afiseazapoza.php?ID=$idAutor' alt='Autor postare' />
                               <div class='utilizator-info'>
                                   <h5>$data</h5>
                                   <small>$autor</small>
                               </div>
                           </div>
                       </div>
                   </div>";
               }
        ?>
    </div>
    <br/>

    <!--- Formularul de login --->
<div id="Continut-Modal-Logare" class="continut-modal">
  <div class="continut-modal-formular" style="width:40%">
                     <h4 style="color:white;">Login</h4>
                     <form method="POST">
                        <input type="hidden" name="comanda" value="login">
                        <p style="float:left;color:white;">Nume de utilizator:</p>
                        <input type="text" name="numeUtilizator" class="numeUtilizator" placeholder="Username" style="color:white;"/>
                        <p style="float:left;color:white;">Parola:</p>
                        <input type="password" name="parola" class="parola" placeholder="parola" style="width: 100%;height: 40px;color:white;"/>
                        <input class="btn login" type="submit" value="Login" />
                    </form>
        </div>  
    </div>
<div id="Continut-Modal-Inregistrare" class="continut-modal">
  <div class="continut-modal-formular" style="width:50%">
        <h4 style="color:white;margin:0;">Creeaza un cont!</h4>
        <form method="POST">
          <input type="hidden" name="comanda" value="inregistrare">
          <p style="float:left;color:white;">Nume de utilizator:</p>
          <input type="text" name="utilizator" class="numeUtilizator form-control" placeholder="Nume utilizator" style="color:white;" required>
          <p style="float:left;color:white;">Nume de familie:</p>
          <input type="text" name="nume" class="numeUtilizator form-control" placeholder="Nume de familie" style="color:white;" required>
          <p style="float:left;color:white;">Prenume:</p>
          <input type="text" name="prenume" class="numeUtilizator form-control" placeholder="Prenume" style="color:white;" required>
          <p style="float:left;color:white;">Email:</p>
          <input type="email" name="email" class="numeUtilizator form-control" placeholder="Prenume" style="width: 100%;height: 40px;color:white;" required>
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
          <input type="password" name="parola" class="parola form-control" placeholder="parola" style="width:100%;height:40px;color:white;" required>
          <p style="float:left;color:white;">Confirma parola:</p>
          <input type="password" name="confirmaparola" class="parola form-control" placeholder="parola" style="width:100%;height:40px;color:white;">
          <input class="btn login" type="submit" value="Creeaza cont"/>
        </form> 
</div></div></div>

    <!----------- Notes eroare inregistrare ------------>

<div id="popupNotes" class="overlay" style="z-index:100;position:absolute;">
  <div class="popup">
    <?php
       echo $username_err."<br/>".$password_err."<br/>".$confirm_password_err;
    ?>
    <a class="inchide" href="#" style="z-index:100;">&times;</a>
    <div class="content">
    </div>
  </div>
</div>


<!-------------- CAZURI COVID POPUP ---------->
<?php $cazuriCovid = file_get_contents('https://www.worldometers.info/coronavirus/country/romania/'); ?>
<div id="popupcovid" class="overlay" style="z-index:100;position:absolute;">
  <div class="popup">
    <a class="inchide" href="" style="z-index:100;color:red;font-size:30px;float:right;text-decoration: none;font-weight:bolder;">&times;</a>
    <div class="content">
<?php
        echo "<h2><i class='fas fa-chart-line'></i> Cazuri totale de covid:</h2>";
        preg_match('#aaa">(.*) </span>#', $cazuriCovid, $potrivire);
        echo "<span style='font-size:20px;font-weight:3'>".$potrivire[1]."</span>";
        echo "<br/><br/><h2><i class='fas fa-ghost'></i> Persoane decedate:</h2>";
        preg_match('#<span>(.*)</span>#', $cazuriCovid, $potrivire);
        echo "<span style='font-size:20px;font-weight:3'>".$potrivire[0]."</span>";
        echo "<br/><br/><h2><i class='fas fa-briefcase-medical'></i> Persoane vindecate:</h2>";
        preg_match('#8ACA2B ">\n<span>(.*)</span>#', $cazuriCovid, $potrivire);
        echo "<span style='font-size:20px;font-weight:3'>".explode('">',$potrivire[0])[1]."</span>";
        echo "<hr/><h2><i class='fas fa-chart-bar'></i> Situatie astazi(".date('d-m-Y').")<hr style='margin-bottom:0%;'/>";
        echo "<h2><i class='fas fa-chart-line'></i> Numar cazuri:</h2>";
        preg_match('#<strong>(.*) and#', $cazuriCovid, $potrivire);
        echo "<span style='font-size:20px;font-weight:3'>".explode(" ",$potrivire[0])[0]."</span></strong>";
        echo "<h2><i class='fas fa-ghost'></i> Numar decese:</h2>";
        preg_match('#and <strong>(.*) new deaths#', $cazuriCovid, $potrivire);
        echo "<span style='font-size:20px;font-weight:3'>".explode(" ",$potrivire[0])[1]."</span>";
      ?>
    </div>
  </div>
</div>
<!------------- FOOTER PAGINA -------------------->
 </div>
    <div class="footer-jos" id="footer" style="margin-top:100%">
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

// JavaScript pentru slideshow-ul de imagini
var IndexImagine = 0;
AfiseazaImagini();

function AfiseazaImagini() {
  var i;
  var imagini = document.getElementsByClassName("imaginiSlide");
  for (i = 0; i < imagini.length; i++) {
    imagini[i].style.display = "none";  
  }
  IndexImagine++;
  if (IndexImagine > imagini.length) {IndexImagine = 1}    
  imagini[IndexImagine-1].style.display = "block";  
  setTimeout(AfiseazaImagini, 3000); // Schimba imaginea la fiecare 3 secunde
}

// JavaScript pentru ferestrele modale

    // Gaseste divurile apartinand acestor clase
var modalInregistrare = document.getElementById("Continut-Modal-Inregistrare");
var modalLogare = document.getElementById("Continut-Modal-Logare");

    // Butoanele care declanseaza div-urile de mai sus
var btn = document.getElementById("declanseaza-modal-inregistrare");
var btnLogare = document.getElementById("declanseaza-modal-logare");
var btnLogare2 = document.getElementById("declanseaza-modal-logare2");
    // Se schimba display-ul cand sunt apasate butoanele
btn.onclick = function() {
  modalInregistrare.style.display = "block";
}
btnLogare.onclick = function() {
  modalLogare.style.display="block";
}
btnLogare2.onclick = function() {
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
</script>
</body>
</html>
