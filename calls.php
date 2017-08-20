<?php 
session_start();
//calls.php 
require_once("functions.php");
//connect to database 
$connection = mysqli_connect("localhost","root","","sacco");
	If(!$connection)
		{  echo mysql_error(); 
      exit();
    }


        $act = $_REQUEST['action'];
      
	?>

<html>
    <head>
      <title>SACCO SYSTEM</title>
      <meta charset="utf-8">
      <style type="text/css">
        *{
          margin:0px;
          padding:0px;
        }
        body{
          margin:20px;
          background-color: rgb(7,169,191);
          text-align:center;
        }
        a{
          text-decoration: none;
          font:20px bold arial;
          color:red;
        }
        table{
          width: 80%;
        }
        h1,h2{
          padding: 20px;
        }
        #bigwrapper{
          width:1050px;
         margin:20px auto;
        }
        ul li{
          list-style: none;
          display: inline-block;
          padding: 5px;
        }
        .forms,.login{
          margin:5px;
          padding:8px;
          border-radius: 8px;
        }
        .reg,.idea,.log{
          font:25px bold arial;
        }
        #register{
          padding-right: 10px;
        }
        .ben{
          color:green;
        }
        #nav ul li a{
			background-color:#fff;
			color:#000;
			}
		#nav ul li a:hover{
			background-color:#1e2250;
			color:#9598a2;
			}
       
      </style>
    </head>
<body>
<h1>FAMILY SACCO MANAGEMENT SYSTEM</h1>
  <div id="bigwrapper">
    <table border=0 >
 
        <tr>
           <td> 
              <?php if(!isset($_SESSION['username'])){?><h3><a href="?action=login">Login</a></h3> <?php } else { ?>  <h3><a href="?action=logout">Logout</a></h3> <br>  
              <h1>Menu</h1> <br> 
              <div id ="nav">
              <ul>
              <li><a href="?action=reg" text-decoration="none">Registration</a></li>
              <li><a href="?action=contribution"> contributions</a></li>
              <li><a href="?action=loans"> loans</a></li>
              <li><a href="?action=idea">ideas</a></li>
              <li><a href="?action=ben">benefits</a></li>
              <li><a href="?action=reports">sacco reports</a></li>
              <li><a href="?action=payment">payments</a></li<?php  } ?><br> 
              </ul>
              </div>
              </td>



    </tr>
    <!--  all your content including forms and tables of results will be displayed here. So put your ifs/ switch here  -->

    <?php 

    switch($act){
    case "login":  //imagine that you already put ?action=login somewhere eg. In your menu under a href or as your action in the form
         showlogin();  //call the function, which is in functions.php

    break;
    case "authenticate"://repeat as above for all menu links 
      return loguser();//note that i have to write the arguements hia inside this fuction

    break;

    case "logout":
    logout_user();

    break;

    case "reg":
    register();

    break;


    case "saveinfo":
     return registration();

    break;
    case "contribution":
    approve_cont();

    break;

    case "new_cont":
    $reciept_no= $_REQUEST['reciept_no'];
    $name =  $_REQUEST['name'];
    $amount = $_REQUEST['amount'];
    $contribution_date = $_REQUEST['contribution_date'];
    $member_id = $_REQUEST['member_id'];
     return approved_cont($reciept_no,$name,$amount,$contribution_date,$member_id);
    break;

    case "loans":
      approve_loan();
    break;

    case "loan_req":
    $id = $_REQUEST['id'];
    $amt =  $_REQUEST['amount'];
    $borrowing_date = $_REQUEST['borrowing_date'];
    $paying_date = $_REQUEST['paying_date'];
    $install = $_REQUEST['installment'];
    return approved_loan($amt,$borrowing_date,$paying_date,$id,$install);
    break;

    case "loan_deny":
    $id = $_REQUEST['id'];
    $amt =  $_REQUEST['amount'];
    $borrowing_date = $_REQUEST['borrowing_date'];
    $paying_date = $_REQUEST['paying_date'];
     return denied_loan($amt,$borrowing_date,$paying_date,$id);
    break;


    case "reports":
    member_reports();

    break;
    case "idea":
    investment_idea();

    break;

    case "businessidea":
    $b_name = $_REQUEST['b_name'];
    $initial_cap = $_REQUEST['initial_cap'];
    $desc = $_REQUEST['description'];
    $mem_id = $_REQUEST['mem_id'];
    return approved_idea($b_name,$initial_cap,$desc,$mem_id );

    break;

    case "updateidea":
    return updated_idea();

    break;

    case "ben":
    return benefits();

    break;

    case "ben_distribution":
    return distribute_benefits();

    break;

    case "payment":
    return  make_payment();

    break;

    case "new_payment":
    $repay_amount= $_REQUEST['repay_amount'];
    $repay_date =  $_REQUEST['repay_date'];
    $loan_no = $_REQUEST['loan_no'];
    $member_id = $_REQUEST['member_id'];
    return payments($repay_amount, $repay_date,$loan_no ,$member_id);

    break;


    default : break;

    }
    ?>


    </table>
    </div>
</body>
</html>
