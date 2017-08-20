<?php 

function register(){
     $connection = mysqli_connect("localhost","root","","sacco");

      if(!$connection){
        die("connection failed". mysqli_connect_error());
}
//Calculate the initial contribution
      $query = "SELECT MAX(total_contribution) AS max_contribution FROM members ";
      $result = $connection->query($query); 
     $maximum = mysqli_fetch_assoc($result);
      echo "The maximum contribution is ".$maximum['max_contribution']."<br>";
      $initial_pay = 0.75*$maximum['max_contribution'];
      echo "Initial payment price is ".$initial_pay."<br><br>";
?>
   <form method="post" action="calls.php?action=saveinfo">

       <table>
       <h2>NEW MEMBER REGISTRATION</h2>
           <tr>
		<td class="reg">member name:</td>
                     <td><input type="text" name="name" placeholder="member name" class="forms"></td>
           </tr>
           <tr>
		<td class="reg">username:</td>
                     <td><input type="text" name="username" placeholder="username" class="forms"></td>
           </tr>
                 <tr>
    <td class="reg">password:</td>
                     <td><input type="password" name="pwd" placeholder="password" class="forms"></td>
           </tr>
                 <tr>
    <td class="reg">contact:</td>
                     <td><input type="text" name="contact" placeholder="contact" class="forms"></td>
           </tr>
                 <tr>
    <td class="reg">Email address:</td>
                     <td><input type="email" name="mail" placeholder="email address" class="forms"></td>
           </tr>
                 <tr>
    <td class="reg">Joining date:</td>
                     <td><input type="date" name="Joining_date" placeholder="date of joining" class="forms"></td>
           </tr>
             <tr>
    <td class="reg">Initial contribution:</td>
                     <td><input type="number" name="intial_cont" value=<?php echo $initial_pay?> class="forms"></td>
           </tr>
          <tr>
		<td> </td>
                     <td><input type="submit" value="register" class="forms" id="register"></td>
           </tr>


      </table>

   </form>
<?php 
}
function registration(){

$name = $_POST['name'];
$username = $_POST['username'];
$pwd = $_POST['pwd'];
$contact = $_POST['contact'];
$mail = $_POST['mail'];
$Joining_date = $_POST['Joining_date'];
$initial_contribution=$_POST['intial_cont'];


//connect to a server and access a database
$connection = mysqli_connect('localhost','root','','sacco');

if(!$connection){
  die("connection failed". mysqli_connect_error());
}

//query the database
if(strlen($pwd)>9){
$query1 = "insert into members(name,username,password,contact,email,joining_date,total_contribution) values('$name','$username','$pwd','$contact','$mail','$Joining_date','$initial_contribution')";

$result = $connection->query($query1);

if (!$result) {
  echo mysqli_error(); exit();
 }

//close the connection
?>
<meta http-equiv="refresh" content="0.00001;calls.php?action=reg">
<?php
}else{
  echo "password is too short";
}
mysqli_close($connection);

}


function showlogin(){
?>
   <form method="post" action="calls.php?action=authenticate">
       <table>
           <tr>
		<td class="log">User name:</td>
                     <td><input type="text" name="username" class="login" /></td>
           </tr>
           <tr>
		<td class="log">Password:</td>
                     <td><input type="password" name="pass" class="login"></td>
           </tr>
<tr>
		<td> </td>
                     <td><input type="submit" value="submit" class="login"></td>
           </tr>


      </table>

   </form>
<?php 

}
function logout_user(){ 
session_start();
session_destroy();

header("Location: calls.php");

}


function loguser(){ 
 $connection = mysqli_connect('localhost','root','','sacco');

  $username= $_POST['username'];
  $password =$_POST['pass'];

$query1 = "select * from system_admin where username='$username' and password ='$password'";
$result = $connection->query($query1);


if (!$row = $result->fetch_assoc()){
  echo "you have either entered the wrong password and username";
  }else{
       $_SESSION['username']=$username;
    echo "Welcome ".$username." please wait .......";
    ?>
      <meta http-equiv="refresh" content="3;calls.php?action=contribution" >
    <?php
  }
} 


function approve_cont(){
		$filename = 'sacco.txt';
		$handle = fopen($filename,'r');

?>
  <table border="1">
    <tr>    
      <th>Reciept number</th>
      <th>member name</th>
      <th>Contribution amount</th>
      <th>contribution date</th>
      <th>member_id</th>
      <th>CONTRIBUTION APPROVAL</th>
    </tr>
  
<?php
echo "<h1>"."CONTRIBUTIONS TO APPROVE"."</h1>";
while(!feof($handle)){
   $contribution_line =fgets($handle);
   $contribution_array =explode(' ', $contribution_line);
   if($contribution_array[0] == 'contribution'){
   
    unset($contribution_array[0]); 
  
    if(isset($contribution_array[1])&&isset($contribution_array[2])&&isset($contribution_array[3])&&isset($contribution_array[4])&&isset($contribution_array[5])){
      echo "<tr>";
      echo "<td>".$contribution_array[1]."</td>";
      echo "<td>".$contribution_array[2]."</td>";
      echo "<td>".$contribution_array[3]."</td>";
      echo "<td>".$contribution_array[4]."</td>";
      echo "<td>".$contribution_array[5]."</td>";
      ?>
         <td> <a href="calls.php?action=new_cont&reciept_no=<?php echo $contribution_array[1]; ?>&name=<?php echo $contribution_array[2];?>&amount=<?php echo $contribution_array[3];?>&contribution_date=<?php echo $contribution_array[4];?>&member_id=<?php echo $contribution_array[5];?>">Approve</a> 
         <a href="calls.php?action=new_cont&reciept_no=<?php echo $contribution_array[1]; ?>&name=<?php echo $contribution_array[2];?>&amount=<?php echo $contribution_array[3];?>&contribution_date=<?php echo $contribution_array[4];?>&member_id=<?php echo $contribution_array[5];?>">Deny</a> 
          </td>
         </tr>
      <?php
      
      
      }
    }
}
fclose($handle);

?>
</table>

<?php
}


function approved_cont($rn,$name,$camount,$cdate,$mem_id){
  $connection = mysqli_connect("localhost","root","","sacco");

if(!$connection){
  die("connection failed". mysqli_connect_error()); exit();
}

$query1 = $connection->query("INSERT INTO contributions set reciept_no='$rn',name='$name',amount='$camount',contribution_date='$cdate',member_id='$mem_id'");
// I nned to find a way of deleting a row after it has been added to the database
//$result = $connection->query($query1);


//get the intial contribution
    $query1 = "SELECT total_contribution FROM members WHERE member_id='$mem_id'";
    $result1 = $connection->query($query1);
    $row=mysqli_fetch_assoc($result1);
    $mem_initial = $row['total_contribution'];
    $member_total =$mem_initial +$camount;


   $r =$connection->query("UPDATE members SET total_contribution='$member_total' WHERE member_id='$mem_id'");
      

?>
 <meta http-equiv="refresh" content="0.00001;calls.php?action=contribution" >
 <?php 
}


function approve_loan(){
 
$filename = 'sacco.txt';
$handle = fopen($filename,'r');

?>
  <table border="1">
    <tr>    
      <th>loan_amount</th>
      <th>date_of_borrowing</th>
      <th>date of paying</th>
      <th>member_id</th>
      <th>month_installment</th>
      <th>LOAN APPROVAL</th>
    </tr>
  
<?php
echo "<h1>"."LOANS TO APPROVE"."</h1>";
while(!feof($handle)){
   $loan_line =fgets($handle);
   $loan_array =explode(' ', $loan_line);
    if($loan_array[0] == 'loan_request'){
    //Name for function to approve contribution
    unset($loan_array[0]); 
     $loan_array[5] = (($loan_array[1]*0.03)+$loan_array[1])/12;
  
    if(isset($loan_array[1])&&isset($loan_array[2])&&isset($loan_array[3])&&isset($loan_array[4])&&isset($loan_array[5])){
      echo "<tr>";
      echo "<td>".$loan_array[1]."</td>";
      echo "<td>".$loan_array[2]."</td>";
      echo "<td>".$loan_array[3]."</td>";
      echo "<td>".$loan_array[4]."</td>";
      echo "<td>".$loan_array[5]."</td>";
      ?>

         <td> <a href="calls.php?action=loan_req&id=<?php echo $loan_array[4]; ?>&amount=<?php echo $loan_array[1];?>&borrowing_date=<?php echo $loan_array[2];?>&paying_date=<?php echo $loan_array[3];?>&installment=<?php echo $loan_array[5];?>">Approve</a>  
         <a href="calls.php?action=loan_deny&id=<?php echo $loan_array[4]; ?>&amount=<?php echo $loan_array[1];?>&borrowing_date=<?php echo $loan_array[2];?>&paying_date=<?php echo $loan_array[3];?>&installment=<?php echo $loan_array[5];?>">Deny</a> 
         </td>
         </tr>

      <?php
      
      
      }
    }
}
fclose($handle);

?>

</table>

<?php
}


function approved_loan($amt,$bd,$pd,$id,$month_install){
	$connection = mysqli_connect("localhost","root","","sacco");

if(!$connection){
  die("connection failed". mysqli_connect_error()); exit();
}

    $query2 = "SELECT  sum(total_contribution) AS mem_total,member_id FROM members WHERE member_id='$id'";
    $result2 = $connection->query($query2);
    $mem_total =mysqli_fetch_assoc($result2);
    $member_total=$mem_total['mem_total'];

    if($amt < (0.5*$member_total)){
     
          $query1 = $connection->query("INSERT INTO loan set loan_amount='$amt',date_of_borrowing='$bd',date_of_paying='$pd',loan_status='approved',member_id='$id'");

          if (!$query1){
            echo mysql_error(); exit();
          }
          //set the monthly installment for a member
          $loan_bal =(0.03*$amt)+$amt;
          $query1 = $connection->query("INSERT INTO repayment_details set monthly_installment='$month_install',loan_balance='$loan_bal',member_id='$id'");

          if (!$query1) {
            echo mysql_error(); exit();
          }


?>
 <meta http-equiv="refresh" content="0.1;calls.php?action=loans" >

 <?php
 }
 
else{
    echo "The member is not eligible for this loan, the requested amount is greater than 1/2 thier contributions";
  }

}

// this function will deny a loan and set the status to be pending
function denied_loan($amt,$bd,$pd,$id){
  $connection = mysqli_connect("localhost","root","","sacco");

if(!$connection){
  die("connection failed". mysqli_connect_error()); exit();
}

$query1 = $connection->query("INSERT INTO loan set loan_amount='$amt',date_of_borrowing='$bd',date_of_paying='$pd',loan_status='rejected',member_id='$id'");

//$result = $connection->query($query1);


?>
 <meta http-equiv="refresh" content="0.1;calls.php?action=loans" >
 <?php
}



// the function for investment ideas
function investment_idea(){

 
$filename = 'sacco.txt';
$handle = fopen($filename,'r');

?>
  <table border="1">
    <tr>    
      <th>Business_name</th>
      <th>initial capital</th>
      <th>simple description</th>
      <th>member_id</th>
      <th>IDEA APPROVAL</th>
    </tr>
  
<?php
echo "<h1>"."IDEAS TO APPROVE"."</h1>";
while(!feof($handle)){
   $idea_line =fgets($handle);
   $idea_array =explode(' ', $idea_line);
      if($idea_array[0] == 'idea'){
    unset($idea_array[0]); 
 
    if(isset($idea_array[1])&&isset($idea_array[2])&&isset($idea_array[3])&&isset($idea_array[4])){
      echo "<tr>";
      echo "<td>".$idea_array[1]."</td>";
      echo "<td>".$idea_array[2]."</td>";
      echo "<td>".$idea_array[3]."</td>";
      echo "<td>".$idea_array[4]."</td>";
      ?>
         <td> <a href="calls.php?action=businessidea&b_name=<?php echo $idea_array[1];?>&initial_cap=<?php echo $idea_array[2];?>&description=<?php echo $idea_array[3];?>&mem_id=<?php echo $idea_array[4];?>">Approve</a>  </td>
         </tr>
      <?php
      
      
    }
  }
}

fclose($handle);

?>
</table>
<form method="POST" action="calls.php?action=updateidea"  >

       <table>
       <h2>UPDATE IDEA DETAILS</h2>
           <tr>
    <td class="idea">Idea number:</td>
                     <td><input type="text" name="ideano" placeholder="Idea number" class="forms"></td>
           </tr>
           <tr>
    <td class="idea">Business name:</td>
                     <td><input type="text" name="ideaname" placeholder="Business name" class="forms"></td>
           </tr>
                  <tr>
    <td class="idea">Business name:</td>
                     <td><input type="date" name="date_of_investment" placeholder="date of investment"  class="forms"></td>
           </tr>
                 <tr>
    <td class="idea">Profits:</td>
                     <td><input type="number" name="profits" placeholder="Profits" class="forms"></td>
           </tr>
                 <tr>
    <td class="idea">losses:</td>
                     <td><input type="number" name="losses" placeholder="losses" class="forms"></td>
           </tr>

          <tr>
    <td class="idea"> </td>
                     <td><input type="submit" value="Update idea" class="forms"></td>
           </tr>


      </table>

   </form>
<?php

}
 
function approved_idea($bname,$capital,$descrption,$mem_id){
  $connection = mysqli_connect("localhost","root","","sacco");

if(!$connection){
  die("connection failed". mysqli_connect_error()); exit();
}
    $query2 = "SELECT  sum(total_contribution) AS total_contribution,member_id FROM members";
    $result2 = $connection->query($query2);
    $mem_total =mysqli_fetch_assoc($result2);
    $total_contribution=$mem_total['total_contribution'];
if($capital<=$total_contribution){

$query1 = $connection->query("INSERT INTO investments SET business_name='$bname',initial_capital='$capital',description='$descrption',member_id='$mem_id'");

//$result = $connection->query($query1);

?>
 <meta http-equiv="refresh" content="0.000001;calls.php?action=idea" >
 <?php
 }
else{
  echo "The initial capital is greater than half the total contributions";
}
}



function updated_idea(){
  //recieve information
$ideano = $_POST['ideano'];
$D_O_I = $_POST['date_of_investment'];
$profits= $_POST['profits'];
$losses = $_POST['losses'];

  $connection = mysqli_connect("localhost","root","","sacco");

if(!$connection){
  die("connection failed". mysqli_connect_error()); exit();
}
//updates on the investment idea
    $query1 = "SELECT profits,losses FROM investments WHERE idea_no='$ideano'";
    $result1 = $connection->query($query1);
    $row=mysqli_fetch_assoc($result1);
    $previous_profit = $row['profits'];
    $previous_losses= $row['losses'];
    $new_profit =$previous_profit + $profits;
    $new_losses =$previous_losses+$losses; 

    $query1 = $connection->query("UPDATE investments SET date_of_investment='$D_O_I',profits='$new_profit',losses='$new_losses'  WHERE idea_no='$ideano'");


if (!$query1) {
  echo "Either the business name or idea number is wrong";
}else


?>
<meta http-equiv="refresh" content="0.1;calls.php?action=idea" >

 <?php
}
 
function benefits(){
  ?>

<tr><th><h4>SUBMIT IDEA NUMBER TO DISTRIBUTE BENEFITS</a></h4><th></tr>
<tr><th><form method="POST" action="calls.php?action=ben_distribution"><input type="text" name="ideano" placeholder="Idea number" class="forms">
<input type="submit" value="Distribute benefits" class="forms"></form></th></tr>

<?php

}

function distribute_benefits(){

$connection = mysqli_connect("localhost","root","","sacco");


if(!$connection){
  die("connection failed". mysqli_connect_error());
}

$ideano = $_POST['ideano'];

  //Calculating the sum of profits
    $query = "SELECT business_name,date_of_investment,sum(profits) AS total_profits FROM investments WHERE idea_no='$ideano'";
    $result = $connection->query($query);
    $row = mysqli_fetch_assoc($result);
    $business_name=$row['business_name'];
    $investment_date=$row['date_of_investment'];
    echo "TOTAL PROFITS: ".$row['total_profits']."<br>";

    //Profits to share
    $sharing = 0.65*$row['total_profits'];
    $add_savings=0.3*$row['total_profits'];
    $add_ben =0.05*$row['total_profits'];
    echo "ADDITIONAL SAVINGS: ".$add_savings."<br>";
    echo "PROFITS TO SHARE: ".$sharing."<br>";

    //Calculating total contributions
    $query = "SELECT sum(total_contribution) AS total_contributions FROM members ";
    $result = $connection->query($query); 
    $total = mysqli_fetch_assoc($result);
    $total_savings =$add_savings+$total['total_contributions'];
    echo "TOTAL SAVINGS: ".$total_savings."<br><br>";
    echo "TOTAL CONTRIBUTIONS: ".$total['total_contributions']."<br><br>";

    $query1 = "SELECT MAX(total_contribution),member_id fROM members";
    $result1 = $connection->query($query1); 
    $top_member = mysqli_fetch_assoc($result1);
    echo "Additional benefits to highest contributor: ".$add_ben."<br>";

    $query2 = "SELECT total_contribution,member_id FROM members WHERE Joining_date < '$investment_date'";
    $result2 = $connection->query($query2);
  
while ($row=mysqli_fetch_assoc($result2)){
  $mid = $row['member_id'];
  $mem_total = $row['total_contribution']; 
  $share = ($mem_total/$total['total_contributions'])*100;
  $benefit = ($share/100) * $sharing;


 
   $r = $connection->query("insert into benefits set benefits_amount='$benefit',member_id='$mid',  business_name='$business_name'");

}
//calculation of the additional benefits from highest contributor
    $query = "SELECT MAX(benefits_amount) FROM benefits WHERE business_name='$business_name'";
    $result = $connection->query($query); 
    $max_ben = mysqli_fetch_assoc($result);
    $max =$max_ben['MAX(benefits_amount)'];
    $total_ben=$max +$add_ben;

        $rr = $connection->query("UPDATE benefits SET benefits_amount='$total_ben' WHERE benefits_amount='$max'");
      if($rr){
        echo "Benefits are successfully distributed";
      }


$result =$connection->query("select benefits.benefits_id,benefits.benefits_amount,benefits.member_id,benefits.business_name,members.name from benefits,members WHERE benefits.member_id =members.member_id");
?>
 <table border="1" width="80%">
  <h2>DISTRIBUTION OF BENEFITS</h2>
    <tr>
    <th>benefits_id</th>
    <th>benefits_amount</th>
    <th>member_id</th>
    <th>member name</th>
    <th>business_name</th>
  </tr>

<?php 
  
while ($row=mysqli_fetch_assoc($result)){
  echo "<tr>";
  echo "<td>".$row['benefits_id']."</td>";
  echo "<td>".$row['benefits_amount']."</td>";
  echo "<td>".$row['member_id']."</td>";
  echo "<td>".$row['name']."</td>";
  echo "<td>".$row['business_name']."</td>";
  echo "</tr>";
}
  

?>

  </table>
<?php
}

function make_payment(){
$filename = 'sacco.txt';
$handle = fopen($filename,'r');



?>
  <table border="1">
    <tr>    
      <th>Repayment amount</th>
      <th>Repayment date</th>
      <th>loan number</th>
      <th>member id</th>
      <th>PAYMENT</th>
    </tr>
  
<?php
echo "<h1>"."LOAN REPAYMENTS"."</h1>";
while(!feof($handle)){
   $payment_line =fgets($handle);
   $payment_array =explode(' ', $payment_line);
   if($payment_array[0] == 'loan_repayment'){
   
    unset($payment_array[0]); 
  
    if(isset($payment_array[1])&&isset($payment_array[2])&&isset($payment_array[3])&&isset($payment_array[4])){
      echo "<tr>";
      echo "<td>".$payment_array[1]."</td>";
      echo "<td>".$payment_array[2]."</td>";
      echo "<td>".$payment_array[3]."</td>";
      echo "<td>".$payment_array[4]."</td>";
      ?>
         <td> <a href="calls.php?action=new_payment&repay_amount=<?php echo $payment_array[1]; ?>&repay_date=<?php echo $payment_array[2];?>&loan_no=<?php echo $payment_array[3];?>&member_id=<?php echo $payment_array[4];?>">make_payment</a> 
          </td>
         </tr>
      <?php
      
      
      }
    }
}
fclose($handle);

?>
</table>

<?php
}


function payments($repay_amount, $repay_date,$loan_no ,$member_id){
  $connection = mysqli_connect("localhost","root","","sacco");

if(!$connection){
  die("connection failed". mysqli_connect_error()); exit();
}
    $query = "SELECT loan_balance FROM repayment_details WHERE member_id='$member_id'";
    $result = $connection->query($query); 
    $bal = mysqlI_fetch_assoc($result);
    $new_bal=$bal['loan_balance']-$repay_amount;

    $query1 = $connection->query("UPDATE repayment_details set repayment_amount='$repay_amount',loan_balance='$new_bal',repayment_date='$repay_date',loan_no='$loan_no' WHERE member_id='$member_id'");


?>
 <meta http-equiv="refresh" content="0.0001;calls.php?action=payment">
 <?php
}




function member_reports(){
//connect to a server and access a database
$connect = mysqli_connect('localhost','root','','sacco');


// To display back report from the members
$query1 ="select * from members";
$result=$connect->query($query1);

// To display back report from the contributions
$query2 ="select * from loan where loan_status='approved'";
$result2=$connect->query($query2);

$query3 ="select * from contributions";
$result3=$connect->query($query3);

$query4 ="select * from repayment_details WHERE loan_balance>0 and repayment_amount>0";
$result4=$connect->query($query4);

$query5 ="select benefits.benefits_id,benefits.benefits_amount,benefits.member_id,benefits.business_name,members.name from benefits,members WHERE benefits.member_id =members.member_id";
$result5=$connect->query($query5);

	$query = "SELECT business_name,MIN(profits) AS worst_idea FROM investments";
    $result = $connect->query($query);
    $row = mysqli_fetch_assoc($result);
    $worst_idea=$row['business_name'];
    
    $query2 = "SELECT business_name,MAX(profits) AS best_idea FROM investments";
    $result2 = $connect->query($query2);
    $row = mysqli_fetch_assoc($result2);
    $best_idea=$row['business_name'];
    

    
    $query = "SELECT count(loan_no) AS loan_number FROM loan";
    $result = $connect->query($query);
    $row = mysqli_fetch_assoc($result);
    $loan_number=$row['loan_number'];
    
    $query = "SELECT count(member_id) AS total_members FROM members";
    $result = $connect->query($query);
    $row = mysqli_fetch_assoc($result);
    $total_members=$row['total_members'];
    
    $query = "SELECT count(reciept_no) AS cont_number FROM contributions";
    $result = $connect->query($query);
    $row = mysqli_fetch_assoc($result);
    $cont_number=$row['cont_number'];
    
    $query = "SELECT count(idea_no) AS idea_number FROM investments";
    $result = $connect->query($query);
    $row = mysqli_fetch_assoc($result);
    $idea_number=$row['idea_number'];


    $query = "SELECT business_name,date_of_investment,sum(profits) AS total_profits FROM investments";
    $result = $connect->query($query);
    $row = mysqli_fetch_assoc($result);
    $total_profits=$row['total_profits'];
    $add_savings=0.3*$row['total_profits'];
    
    $query = "SELECT sum(loan_balance) AS loan_balance FROM repayment_details ";
    $result = $connect->query($query); 
    $total = mysqli_fetch_assoc($result);
    $loan_balance =$total['loan_balance'];

    $query = "SELECT sum(total_contribution) AS total_contributions FROM members ";
    $result = $connect->query($query); 
    $total = mysqli_fetch_assoc($result);
    $total_savings =$add_savings+$total['total_contributions'];
    
    


?>

<table border="1">
	<tr colspan="2"><th>General report</th><tr>
	<tr colspan="2"><th>Numbers</th><th>Value</th><tr>
	<tr><td>Number of members </td><td><?php echo $total_members; ?> </td></tr>
	<tr><td>Number of contributions </td><td><?php echo $cont_number; ?>  </td></tr>
	<tr><td>Number of loans </td><td><?php echo $loan_number; ?>  </td></tr>
	<tr><td>Number of investments </td><td><?php echo $idea_number; ?>  </td></tr>
	<tr><th>Totals</th><th>Value</th></tr>
	<tr><td>Total profits </td><td><?php echo $total_profits;?> </td></tr>
	<tr><td>Total contributions </td><td><?php echo $total['total_contributions'];?> </td></tr>
	<tr><td>Total savings </td><td><?php echo  $total_savings;?> </td></tr>
	<tr><td>Total money in loans </td><td><?php echo  $loan_balance;?></td></tr>
	<tr><th>ideas</th><th>Value</th></tr>
	<tr><td>Best idea</td><td><?php echo $best_idea;?> </td></tr>
	<tr><td>Worst idea </td><td> <?php echo $worst_idea;?></td></tr>
	
	
	
</table>

<!--this html is to display data from the sacco members back to the administrator-->

  <table border="1" width="80%">
  <h2>members table</h2>
    <tr>
    <th>member_id</th>
    <th>name</th>
    <th>username</th>
    <th>password</th>
    <th>contact</th>
    <th>email</th>
    <th>joining_date</th>
  </tr>

<?php 
  
while ($row=mysqli_fetch_assoc($result)) {
  echo "<tr>";
  echo "<td>".$row['member_id']."</td>";
  echo "<td>".$row['name']."</td>";
  echo "<td>".$row['username']."</td>";
  echo "<td>".$row['password']."</td>";
  echo "<td>".$row['contact']."</td>";
  echo "<td>".$row['email']."</td>";
  echo "<td>".$row['Joining_date']."</td>";
  echo "</tr>";
}
  

?>

  </table>


<!--this html is to display data from the loan back to the administrator-->
    <h2>List of loans  approved</h2>
    <table border="1" width="80%">
    <tr>
    <th>loan_no</th>
    <th>loan_amount</th>
    <th>date_of_borrowing</th>
    <th>date of paying</th>
    <th>loan_status</th>
    <th>member_id</th>
  </tr>

<?php 
  
while ($row=mysqli_fetch_assoc($result2)) {
  echo "<tr>";
  echo "<td>".$row['loan_no']."</td>";
  echo "<td>".$row['loan_amount']."</td>";
  echo "<td>".$row['date_of_borrowing']."</td>";
  echo "<td>".$row['date_of_paying']."</td>";
  echo "<td>".$row['loan_status']."</td>";
  echo "<td>".$row['member_id']."</td>";
  echo "</tr>";
}
  

?>

  </table>

<!--this html is to display data from the contribution back to the administrator-->
<h2>Contributions table</h2>
<table border="1" width="80%">
    <tr>
    <th>reciept_no</th>
    <th>name</th>
    <th>amount</th>
    <th>contribution_date</th>
    <th>member_id</th>
  </tr>

<?php 
  
while ($row=mysqli_fetch_assoc($result3)) {
  echo "<tr>";
  echo "<td>".$row['reciept_no']."</td>";
  echo "<td>".$row['name']."</td>";
  echo "<td>".$row['amount']."</td>";
  echo "<td>".$row['contribution_date']."</td>";
  echo "<td>".$row['member_id']."</td>";
  echo "</tr>";
}
  

?>

  </table>
  <h2>Distribution of benefits from different investment ideas</h2>
  <table border="1" width="80%" >
      <tr>
    <th>benefits_id</th>
    <th>benefits_amount</th>
    <th>member_id</th>
    <th>member name</th>
    <th>business_name</th>
   </tr>

<?php 
  
while ($row=mysqli_fetch_assoc($result5)) {
  echo "<tr>";
  echo "<td>".$row['benefits_id']."</td>";
  echo "<td>".$row['benefits_amount']."</td>";
  echo "<td>".$row['member_id']."</td>";
  echo "<td>".$row['name']."</td>";
  echo "<td>".$row['business_name']."</td>";
  echo "</tr>";
}
?>  

  </table>
  <h2>Members still paying their loans</h2>
  <table border="1" width="80%" >
      <tr>
    <th>repayment_id</th>
    <th>repayment_amount</th>
    <th>loan_balance</th>
    <th>repayment_date</th>
    <th>monthly_installment</th>
    <th>loan_no</th>
    <th>member_id</th>
   </tr>

<?php 
  
while ($row=mysqli_fetch_assoc($result4)) {
  echo "<tr>";
  echo "<td>".$row['repayment_id']."</td>";
  echo "<td>".$row['repayment_amount']."</td>";
  echo "<td>".$row['loan_balance']."</td>";
  echo "<td>".$row['repayment_date']."</td>";
  echo "<td>".$row['monthly_installment']."</td>";
  echo "<td>".$row['loan_no']."</td>";
  echo "<td>".$row['member_id']."</td>";
  echo "</tr>";
}
?> 
<?php
}
?>

</table>
</body>
</html>
