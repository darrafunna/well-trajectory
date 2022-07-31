
<!-- Ini adalah halaman index yang defaultnya merupakan Well Trajectory Build Hold -->

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Well Trajectory</title>
    <link rel="stylesheet" href="style.css" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <div class="container">
      <div class="namaWeb">
        <h1>Well Trajectory - Build Hold</h1>
      </div>
      <div class="tipe">
        <a href="buildHold.php">Build Hold</a>
        <a href="buildHoldDrop.php">Build Hold Drop</a>
        <a href="horizontal.php">Horizontal</a>
      </div>
      <div class="gambar">
        <img src="buildHold.png" alt="" />
      </div>
      <div class="inputArea">
        <form action="<?= $_SERVER['PHP_SELF']?>" method="post">
          <label for="kop">Kick Of Point (V1):</label><br />
          <input type="number" step="any" id="kop" name="kop" /> ft<br />
          <label for="target">Target (V3):</label><br />
          <input type="number" step="any" id="target" name="target" /> ft<br />
          <label for="n">Northing:</label><br />
          <input type="number" step="any" id="n" name="n" /> ft<br />
          <label for="e">Easting:</label><br />
          <input type="number" step="any" id="e" name="e" /> ft<br />
          <label for="bur">Build Up Rate (BUR):</label><br />
          <input type="number" step="any" id="bur" name="bur" /> deg/100ft<br /><br />
          <input type="submit" value="Calculate" />
        </form>
      </div>

      <?php 

    if(!empty($_POST)){

      $kop = $_POST["kop"];
      $target = $_POST["target"];
      $n = $_POST["n"];
      $e = $_POST["e"];
      $bur = $_POST["bur"];
      $r = (180*100)/($bur*PI());
      $d2 = sqrt((($n * $n)+($e * $e)));

      if ($d2 > $r) {
        $lineDC = $d2 - $r;
      } else {
        $lineDC = $r - $d2;
      }

      if ($d2 > $r) {
        $lineDO = $target - $kop;
      } else {
        $lineDO = $kop - $target;
      }

      $sudutDOC = rad2deg(atan($lineDC/$lineDO));
      $lineOC = $lineDO/(cos(deg2rad($sudutDOC)));
      $sudutBOC = rad2deg(acos($r/$lineOC));
      
      if ($d2 > $r) {
        $sudutBOD = $sudutBOC - $sudutDOC;
      } else {
        $sudutBOD = $sudutBOC + $sudutDOC;
      }

      if ($d2 > $r) {
        $maximum_angle_of_well = 90 - $sudutBOD;
      } else {
        $maximum_angle_of_well = 90 + $sudutBOD;
      }

      if ($d2 > $r) {
        $lineBC = sqrt(($lineOC*$lineOC)-($r*$r));
      } else {
        $lineBC = sqrt(($lineOC*$lineOC)+($r*$r));
      }

      if ($d2 > $r) {
        $lineEC = $lineBC*(sin(deg2rad($maximum_angle_of_well)));
      } else {
        $lineEC = $lineBC*(sin(deg2rad($maximum_angle_of_well)));
      }

      $eob_md = $kop+(($maximum_angle_of_well*100)/$bur);
      $eob_vd = $kop+($r*sin(deg2rad($maximum_angle_of_well)));
      $eob_displacement = $r*(1-cos(deg2rad($maximum_angle_of_well)));

      $target_md = $eob_md + $lineBC;
      $target_displacement = $lineEC + $eob_displacement;

    }

      ?>
      
      <div class="endOfBuild">
        <br />
        <?php if(!empty($_POST)){ ?>
          End Of Build (EOB)
          <table class="tableEOB">
          <tr>
            <td>MD</td>
            <td>
              <?php 
              if(!empty($_POST)){
              echo $eob_md;
              }
               ?>
              </td>
          </tr>
          <tr>
            <td>VD</td>
            <td><?php 
            if(!empty($_POST)){
                  echo $eob_vd;
            }
               ?></td>
          </tr>
          <tr>
            <td>Displacement</td>
            <td><?php 
            if(!empty($_POST)){
              echo $eob_displacement;
            }
               ?></td>
          </tr>
        </table>
        <br />

        Target
        <table class="tableEOB">
          <tr>
            <td>MD</td>
            <td><?php 
            if(!empty($_POST)){
              echo $target_md;
            }
               ?></td>
          </tr>
          <tr>
            <td>Displacement</td>
            <td><?php 
            if(!empty($_POST)){
              echo $target_displacement;
            }
               ?></td>
          </tr>
        </table>

        <?php }else{ ?>
          End Of Build (EOB)
          <table class="tableEOB">
          <tr>
            <td>MD</td>
            <td>0</td>
          </tr>
          <tr>
            <td>VD</td>
            <td>0</td>
          </tr>
          <tr>
            <td>Displacement</td>
            <td>0</td>
          </tr>
        </table>
        <br />

        Target
        <table class="tableEOB">
          <tr>
            <td>MD</td>
            <td>0</td>
          </tr>
          <tr>
            <td>Displacement</td>
            <td>0</td>
          </tr>
        </table>
        <?php } ?>
      </div>

      <div class="tabelKedalaman">
        <br />
        Tabel Kedalaman
        <table>
          <tr>
            <th>MD (ft) </th>
            <th>Inclination (deg)</th>
            <th>TVD (ft) </th>
            <th>Total Departure (ft)</th>
            <th>Status</th>
          </tr>
          <tr>
            <td>0</td>
            <td></td>
            <td>0</td>
            <td>0</td>
            <td>Vertical</td>
          </tr>
          <?php 
          //Ini perulangan buat tabel kedalamannya
          if(!empty($_POST)){?>
              <?php
              for ($i = 100; $i <= $kop; $i+=100){?>
                <tr>
                  <td><?php 
                    echo $i;
                   ?></td>
                   <td><?php 
                    echo 0;
                   ?></td>
                   <td><?php 
                    echo $i;
                   ?></td>
                   <td><?php 
                    echo 0;
                   ?></td>
                   <td><?php
                   if($i<$kop){
                    echo "Vertical";
                  }else{
                    echo "KOP"; 
                  }?></td>
                </tr>
              <?php } ?>

              <?php
              $md = $kop + 100;
              $inclination = $bur;
              while ($md <= $eob_md){?>
                <tr>
                  <td><?php 
                    echo $md;
                   ?></td>
                   <td><?php 
                    echo $inclination;
                   ?></td>
                   <td><?php 
                    echo $kop+($r*sin(deg2rad($inclination)));
                   ?></td>
                   <td><?php 
                    echo ($r)*(1-cos(deg2rad($inclination)));
                   ?></td>
                   <td><?php
                    echo "Build";
                  ?></td>
                </tr>
                <?php
                $inclination = $inclination + $bur;
                $md = $md + 100;
                ?>
              <?php } ?>

              <?php 
              $incli_hold = $inclination + (($eob_md-$md)*($bur/100));
              $tvd_eob = $kop+($r*sin(deg2rad($incli_hold)));
              $totDept_eob = ($r)*(1-cos(deg2rad($incli_hold)));
              ?>

              <tr>
                  <td><?php 
                    echo $eob_md;
                   ?></td>
                   <td><?php 
                    echo $incli_hold;
                   ?></td>
                   <td><?php 
                    echo $tvd_eob;
                   ?></td>
                   <td><?php 
                    echo $totDept_eob;
                   ?></td>
                   <td><?php
                    echo "End Of Build";
                  ?></td>
                </tr>

              <?php
              while ($md <= $target_md){?>
                <tr>
                  <td><?php 
                    echo $md;
                   ?></td>
                   <td><?php 
                    echo $incli_hold;
                   ?></td>
                   <td><?php 
                    echo (cos(deg2rad($incli_hold))*($md-$eob_md))+$tvd_eob;
                   ?></td>
                   <td><?php 
                    echo $totDept_eob+(sin(deg2rad($incli_hold))*($md-$eob_md));
                   ?></td>
                   <td><?php
                    echo "Hold";
                  ?></td>
                </tr>
                <?php
                $md = $md + 100;
                ?>
              <?php } ?>

              <tr>
                  <td><?php 
                    echo $target_md;
                   ?></td>
                   <td><?php 
                    echo $incli_hold;
                   ?></td>
                   <td><?php 
                    echo $target;
                   ?></td>
                   <td><?php 
                    echo $target_displacement;
                   ?></td>
                   <td><?php
                    echo "Target";
                  ?></td>
                </tr>

            <?php } ?>
        </table>
      </div>
      <br />
      <div class="grafikArea">
        <br />
        Grafik <br />
        <img src="grafikWell.PNG" alt="" class="gambarGrafik" /><br />
        gambar diatas hanya untuk contoh saja
      </div>
    </div>
  </body>
</html>
