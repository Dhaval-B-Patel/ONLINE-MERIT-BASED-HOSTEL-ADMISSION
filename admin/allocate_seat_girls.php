
<?php 
    session_start();
    include '../database/connect.php';
    
    $_SESSION['action']="allocate";
    $l_admin = $_SESSION['logged_admin']; 
  
    $query = mysqli_query($connection,"select * from  admin WHERE adminid='$l_admin'"); 
    $data =  mysqli_fetch_array($query,MYSQLI_ASSOC);
    $college=$data['collegename'];
    $factor = $data['factor']/100;

        $sql = mysqli_query($connection,"SELECT SUM(`girlsperroom`) as 'girlsperroom' ,SUM(`girlsrooms` * `girlsperroom`) as 'total_girls' FROM `college_hostels` WHERE `collegename` = '$college'");

      $data = mysqli_fetch_array($sql,MYSQLI_ASSOC);
      $girls_limit = $data['total_girls'];
      
      $result=mysqli_query($connection,"SELECT * FROM `determining_factors` WHERE gender='female' AND allot>0 AND college='$college'");
      $allotedseats=mysqli_num_rows($result);

      $query="SELECT AVG(`distance`) as `avgdist`, AVG(`merit`) as `avgmerit` FROM `determining_factors` WHERE gender='female' AND allot=0 AND college='$college'";
      $result = mysqli_query($connection,$query);
      $avg= mysqli_fetch_array($result,MYSQLI_ASSOC);
       $Dmean = $avg['avgdist'];
       $Mmean = $avg['avgmerit'];
       
       $limit = $girls_limit - $allotedseats;
       $query = mysqli_query($connection,"SELECT *, ('$factor')*(`distance`-'$Dmean')+ (1-'$factor')*('$Mmean'-`merit`) as `finalmerit` FROM `determining_factors` WHERE gender='female' AND allot=0 AND college='$college' ORDER BY `finalmerit` DESC LIMIT $limit;"); 
      
      $room = 1;
      while($val =  mysqli_fetch_array($query,MYSQLI_ASSOC)){
           $formid = $val['formid'];
           mysqli_query($connection,"UPDATE `determining_factors` SET `allot` = '1' WHERE `formid` = '$formid'"); 
           
           $room_allocate = 'Common'.(string)$room;
         

           $room = $room + 1;
           mysqli_query($connection,"UPDATE `determining_factors` SET `room` = '$room_allocate' WHERE `formid` = '$formid'"); 
           
           
    
      }            
    mysqli_close($connection); 
    header("location: ../admin.php");

?>