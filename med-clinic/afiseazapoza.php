<?php
  if(isset($_GET["ID"])){

      $db=mysqli_connect("localhost","root","root","proiectweb");
      $q=mysqli_query($db,"select poza_profil,tip_poza from utlizatori where id=".$_GET["ID"]);
      while($poza=mysqli_fetch_assoc($q)){
        header("Content-type:".$poza["tip_poza"]);
        echo $poza["poza_profil"];
      }
  }

?>
