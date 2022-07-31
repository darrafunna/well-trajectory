
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
        <h1>Well Trajectory - Build Hold Drop</h1>
      </div>
      <div class="tipe">
        <a href="buildHold.php">Build Hold</a>
        <a href="buildHoldDrop.php">Build Hold Drop</a>
        <a href="horizontal.php">Horizontal</a>
      </div>
      <div class="gambar">
        <img src="buildHoldDrop.png" alt="" />
      </div>
      <div class="inputArea">
        <form action="<?= $_SERVER['PHP_SELF']?>" method="post">
          <label for="kop">Kick Of Point (V1):</label><br />
          <input type="number" step="any" id="kop" name="kop" /> ft<br />
          <label for="target">End of Drop (Target):</label><br />
          <input type="number" step="any" id="target" name="target" /> ft<br />
          <label for="n">Northing:</label><br />
          <input type="number" step="any" id="n" name="n" /> ft<br />
          <label for="e">Easting:</label><br />
          <input type="number" step="any" id="e" name="e" /> ft<br />
          <label for="bur">Build Up Rate (BUR):</label><br />
          <input type="number" step="any" id="bur" name="bur" /> deg/100ft<br />
          <label for="dor">Drop Off Rate (DOR):</label><br />
          <input type="number" step="any" id="dor" name="dor" /> deg/100ft<br /><br />
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
      $dor = $_POST["dor"];
      $r1 = (180*100)/($bur*PI());
      $r2 = (180*100)/($dor*PI());
      $d4 = sqrt((($n * $n)+($e * $e)));

      //Kasus perhitungan yang digunakan disini R1+R2<D4
     
      if ($r1+$r2 <= $d4) {
        $lineX = $d4-($r1+$r2);
        $sudutB = rad2deg(tan(($lineX/($target-$kop))));
        $lineOF = ($target-$kop)/(cos(deg2rad($sudutB)));
        $lineOG = sqrt(($lineOF*$lineOF)-(($r1+$r2)*($r1+$r2)));
        $sudutFOG = rad2deg(asin(($r1+$r2)/$lineOF));
        $maxInclination = $sudutB+$sudutFOG;
        $md_eob = $kop +(($maxInclination*100)/$bur);
        $v2 = $kop +($r1*(sin(deg2rad($maxInclination))));
        $v3 = $v2+($lineOG*cos(deg2rad($maxInclination)));
        $v4 = $target;
        $d1 = $r1*(1-cos(deg2rad($maxInclination)));
        $d2 = $d4-($r2*(1-cos(deg2rad($maxInclination))));
        $d4 = $d2+($r2*(1-cos(deg2rad($maxInclination))));
        $md_sod = $md_eob + $lineOG;
        $md_eod = $md_sod+(($maxInclination*100)/$dor);

      } else {
        $lineX = rad2deg((atan(($target-$kop)/(($r1+$r2)-$d4)))-(acos(($r1+$r2)/($target-$kop)*sin(atan(($target-$kop)/(($r1+$r2)-$d4))))));
        $sudutB = " ";
        $lineOF = " ";
        $lineOG = " ";
        $sudutFOG = " ";
        $maxInclination = " ";
        $md_eob = $kop +(($lineX*100)/$bur);
        $v2 = $kop +($r1*(sin(deg2rad($lineX))));
        $v3 = $target-($r2*(1-sin(deg2rad($lineX))));
        $v4 = $target;
        $d1 = $r1*(1-cos(deg2rad($lineX)));
        $d2 = $d4-($r2*(1-cos(deg2rad($lineX))));
        $d4 = ($r2*(1-cos(deg2rad($lineX))))+$d2;
        $md_sod = $md_eob + (($d2-$d1)/(sin(deg2rad($lineX))));
        $md_eod = $md_sod+((PI()/(180))*$r2*$lineX);
         
      }

      
    }?>
      
      <div class="endOfBuild">
        <br />
        <?php if(!empty($_POST)){ ?>
          End Of Build (EOB)
          <table class="tableEOB">
          <tr>
            <td>MD EOB</td>
            <td>
              <?php 
              if(!empty($_POST)){
              echo $md_eob;
              }
               ?>
              </td>
          </tr>
          <tr>
            <td>VD (V2)</td>
            <td><?php 
            if(!empty($_POST)){
                  echo $v2;
            }
               ?></td>
          </tr>
          <tr>
            <td>Displacement (D1)</td>
            <td><?php 
            if(!empty($_POST)){
              echo $d1;
            }
               ?></td>
          </tr>
        </table>
        <br />

        Start of Drop (SOD)
          <table class="tableEOB">
          <tr>
            <td>MD SOD</td>
            <td>
              <?php 
              if(!empty($_POST)){
              echo $md_sod;
              }
               ?>
              </td>
          </tr>
          <tr>
            <td>VD (V3)</td>
            <td><?php 
            if(!empty($_POST)){
                  echo $v3;
            }
               ?></td>
          </tr>
          <tr>
            <td>Displacement (D2)</td>
            <td><?php 
            if(!empty($_POST)){
              echo $d2;
            }
               ?></td>
          </tr>
        </table>
        <br>

        End Of Drop (EOD)
          <table class="tableEOB">
          <tr>
            <td>MD EOD</td>
            <td>
              <?php 
              if(!empty($_POST)){
              echo $md_eod;
              }
               ?>
              </td>
          </tr>
          <tr>
            <td>VD (V4)</td>
            <td><?php 
            if(!empty($_POST)){
                  echo $v4;
            }
               ?></td>
          </tr>
          <tr>
            <td>Displacement (D4)</td>
            <td><?php 
            if(!empty($_POST)){
              echo $d4;
            }
               ?></td>
          </tr>
        </table>

        <?php }else{ ?>
          End Of Build (EOB)
          <table class="tableEOB">
          <tr>
            <td>MD EOB</td>
            <td>0</td> 
          </tr> ft <br>
          <tr>
            <td>VD (V2)</td>
            <td>0</td>
          </tr>
          <tr>
            <td>Displacement (D1)</td>
            <td>0</td>
          </tr> 
        </table>
        <br />

        Start of Drop (SOD)
          <table class="tableEOB">
          <tr>
            <td>MD SOD</td>
            <td>0</td>
          </tr>
          <tr>
            <td>VD (V3)</td>
            <td>0</td>
          </tr>
          <tr>
            <td>Displacement (D2)</td>
            <td>0</td>
          </tr>
        </table>
        <br />

        End Of Drop (EOD)
          <table class="tableEOB">
          <tr>
            <td>MD EOD</td>
            <td>0</td>
          </tr>
          <tr>
            <td>VD (V4)</td>
            <td>0</td>
          </tr>
          <tr>
            <td>Displacement (D4)</td>
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
            <th>MD (ft)</th>
            <th>Inclination (deg)</th>
            <th>TVD (ft)</th>
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

                <tr>
                  <td><?php 
                    echo $kop;
                   ?></td>
                   <td><?php 
                    echo 0;
                   ?></td>
                   <td><?php 
                    echo $kop;
                   ?></td>
                   <td><?php 
                    echo 0;
                   ?></td>
                   <td><?php 
                   echo "KOP";
                  ?></td>
                </tr>

              <?php
              $md = $kop + 100;
              $inclination = $bur;
              while ($md <= $md_eob){?>
                <tr>
                  <td><?php 
                    echo $md;
                   ?></td>
                   <td><?php 
                    echo $inclination;
                   ?></td>
                   <td><?php 
                    echo ($r1*(sin(deg2rad($inclination))))+$kop;
                   ?></td>
                   <td><?php 
                    echo $r1*(1-cos(deg2rad($inclination)));
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
              $incli_hold = (($md_eob-$md)*($bur/100)) + $inclination;
              $tvd_eob = ($r1*(sin(deg2rad($incli_hold))))+$kop;
              $totDept_eob = $r1*(1-cos(deg2rad($incli_hold)));
              ?>

              <tr>
                  <td><?php 
                    echo $md_eob;
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
              while ($md <= $md_sod){?>
                <tr>
                  <td><?php 
                    echo $md;
                   ?></td>
                   <td><?php 
                    echo $incli_hold;
                   ?></td>
                   <td><?php 
                    echo (cos(deg2rad($incli_hold))*($md-$md_eob))+$tvd_eob;
                   ?></td>
                   <td><?php 
                    echo $totDept_eob+(sin(deg2rad($incli_hold))*($md-$md_eob));
                   ?></td>
                   <td><?php
                    echo "Hold";
                  ?></td>
                </tr>
                <?php
                $md = $md + 100;
                ?>
              <?php } ?>

              <?php 
              $totDept_sod = $d4-($r2*(1-cos(deg2rad($maxInclination))));
              ?>

              <tr>
                  <td><?php 
                    echo $md_sod;
                   ?></td>
                   <td><?php 
                    echo $incli_hold;
                   ?></td>
                   <td><?php 
                    echo (cos(deg2rad($incli_hold))*($md-$md_eob))+$tvd_eob;
                   ?></td>
                   <td><?php 
                    echo $totDept_sod;
                   ?></td>
                   <td><?php
                    echo "Start Of Drop";
                  ?></td>
                </tr>

                <?php
                $incli_drop = $incli_hold-(($md-$md_sod)*($dor/100));
               while ($md <= $md_eod){?>
                <tr>
                  <td><?php 
                    echo $md;
                   ?></td>
                   <td><?php 
                    echo $incli_drop;
                   ?></td>
                   <td><?php 
                    echo $v3+($r2*(sin(deg2rad($maxInclination))-sin(deg2rad($incli_drop))));
                   ?></td>
                   <td><?php 
                    echo ($totDept_sod)+($r2*(cos(deg2rad($incli_drop))-(cos(deg2rad($maxInclination)))));
                   ?></td>
                   <td><?php
                    echo "Drop";
                  ?></td>
                </tr>
                <?php
                $md = $md + 100;
                $incli_drop = $incli_drop-(($md-($md-100))*($dor/100));
                ?>
              <?php } ?>

              <tr>
                  <td><?php 
                    echo $md_eod;
                   ?></td>
                   <td><?php 
                    echo $incli_drop;
                   ?></td>
                   <td><?php 
                    echo $v4;
                   ?></td>
                   <td><?php 
                    echo $d4;
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
