<?php require_once 'support_file.php';
$ledger_name=find_a_field('accounts_ledger','ledger_name','ledger_id='.$_POST['ledger_id']);
$companyid=@$_SESSION['companyid'];
$sectionid = @$_SESSION['sectionid'];
if($sectionid=='400000'){
    $sec_com_connection=' and 1';
} else {
    $sec_com_connection=" and j.company_id='".$_SESSION['companyid']."' and j.section_id in ('400000','".$_SESSION['sectionid']."')";
}
$date_checking = find_all_field('dev_software_data_locked','','status="LOCKED" and section_id="'.$_SESSION['sectionid'].'" and company_id="'.$_SESSION['companyid'].'"');
if($date_checking>0) {
    $lockedStartInterval = @$date_checking->start_date;
    $lockedEndInterval = @$date_checking->end_date;
} else
{
    $lockedStartInterval = '';
    $lockedEndInterval = '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$ledger_name?> :: Transaction Statement</title>
    <style>

        /* styles.css */

        /* General styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }

        .statement-container {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
        }

        header {
            margin-bottom: 30px;
        }

        .bank-info {
            text-align: center;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .bank-info h1 {
            margin: 0;
            color: #0056b3;
            font-size: 24px;
        }

        .bank-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }

        .account-info h2 {
            margin: 0;
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }

        .account-info p {
            margin: 5px 0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 14px;
        }

        table thead {
            background: #0056b3;
            color: white;
        }

        table th, table td {
            text-align: left;
            padding: 10px;
            border: 1px solid #ddd;
        }

        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        table tbody tr:hover {
            background: #f1f1f1;
        }

        footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }

        /* Print-specific styles */
        /* Print-specific styles */

        @media print {

            a {
                text-decoration: none; /* Remove underline */
                color: inherit;       /* Match text color with surrounding content */
            }

            body {
                background-color: white;
                color: black;
            }
            .statement-container {
                width: 100%;
                margin: 0;
                padding: 0;
                border: none;
                box-shadow: none;
            }
            table {
                width: 100%;
                font-size: 12px;
            }
            header, footer {
                text-align: center;
            }
            footer {
                margin-top: 10px;
            }
            @page {
                size: auto; /* Let the browser handle page size */
                margin: 0.5in; /* Minimum safe margin for most printers */
            }
        }
    </style>
</head>



<body>
<div class="statement-container">
    <header>
        <div class="bank-info">
            <h1 style="text-transform: uppercase"><?=$_SESSION['company_name']?></h1>
            <p><?=$_SESSION['company_address']?></p>
            <p>Phone: +88 022-22260178 | Email: info@icpbd.com</p>
        </div>
        <div class="account-info">
            <h2>Transaction Statement</h2>
            <?php if ($_REQUEST['ledger_id']>0){ ?>
            <p><strong>Account Name:</strong> <?=$ledger_name?></p>
            <p><strong>Account Number:</strong> <?=$_POST['ledger_id']?></p>
            <?php } else { ?>
                <p><strong>Account Name:</strong> All Transactions</p>
            <?php } ?>
            <?php if ($_REQUEST['cc_code']>0){ ?>
                <p><strong>Cost Center:</strong> <?=find_a_field('cost_center','center_name','id='.$_REQUEST['cc_code'])?></p>
            <?php } ?>
            <p><strong>Statement Period:</strong> <?=date("d-M-Y", strtotime($_POST['f_date']));?> to <?=date("d-M-Y", strtotime($_POST['t_date']));?></p>
        </div>
    </header>
    <main>
        <table style="width: 100%">
            <thead>
            <tr style="font-size: 14px">
                <th style="text-align: center">#</th>
                <th style="text-align: center">Date</th>
                <th style="text-align: center">Ref. No</th>
                <th style="text-align: center">Description</th>
                <th style="text-align: center">Cost Center</th>
                <th style="text-align: center">Source</th>
                <th style="text-align: center">Debit</th>
                <th style="text-align: center">Credit</th>
                <th style="text-align: center; width: 10%">Balance</th>
            </tr>
            </thead>
            <tbody>
            <?php

            if($_POST['cc_code']!='')
            {
                $ccCodeConn = " AND a.cc_code = '".$_POST['cc_code']."'";
            } else {
                $ccCodeConn = "";
            }
            if($_POST['tr_from']!='')
            {
                $trFromConn = " AND a.tr_from = '".$_POST['tr_from']."'";
            } else {
                $trFromConn = "";
            }

            $total_sql = "select sum(a.dr_amt),sum(a.cr_amt) from journal a,accounts_ledger b where a.visible_status=1 and a.ledger_id=b.ledger_id and a.jvdate between '".$_POST['f_date']."' AND '".$_POST['t_date']."' and a.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and a.ledger_id like '".$_POST['ledger_id']."'".$ccCodeConn.$trFromConn."";
            $total=mysqli_fetch_array(mysqli_query($conn, $total_sql));

            $c="select sum(a.dr_amt)-sum(a.cr_amt) from
            journal a,
            accounts_ledger b
            where a.visible_status=1 and a.ledger_id=b.ledger_id and a.jvdate<'".$_POST['f_date']."' and a.ledger_id like '".$_POST['ledger_id']."'".$ccCodeConn.$trFromConn."";
            $p="select
a.jvdate,
b.ledger_name,
a.dr_amt,
a.cr_amt,
a.tr_from,
a.narration,
a.jv_no,
a.tr_no,
a.jv_no,
a.cheq_no,
a.cheq_date,
a.user_id,
a.PBI_ID,
a.cc_code,
a.ledger_id as lid ,
u.fname as approvedby,
c.center_name
from
journal a,
accounts_ledger b,
users u,
cost_center c
where
a.visible_status=1 and
a.cc_code=c.id and
a.ledger_id=b.ledger_id and
a.jvdate between '".$_POST['f_date']."' AND '".$_POST['t_date']."' and
a.jvdate NOT BETWEEN '".$lockedStartInterval."' and '".$lockedEndInterval."' and 
a.ledger_id like '".$_POST['ledger_id']."' and 
a.user_id=u.user_id".$ccCodeConn.$trFromConn."
order by a.jvdate,a.id";
            if($total[0]>$total[1])
            {
                $t_type="(Dr)";
                $t_total=$total[0]-$total[1];
            }	else	{
                $t_type="(Cr)";
                $t_total=$total[1]-$total[0];	}
            /* ===== Opening Balance =======*/

            $psql=mysqli_query($conn, $c);
            $pl = mysqli_fetch_array($psql);
            $blance=$pl[0];
            ?>
            <tr style="font-size: 11px">
                <td>0</td>
                <td style="text-align: center"><?=$_POST['f_date'];?></td>
                <td colspan="6">Opening Balance</td>
                <td style="text-align: right"><?php if($blance>0) echo '(Dr)'.number_format($blance,2); elseif($blance<0) echo '(Cr) '.number_format(((-1)*$blance),0,'.','');else echo "0.00"; ?></td>
            </tr>

            <?php
            $sql=mysqli_query($conn, $p);
            $i= 0;
            while($data=mysqli_fetch_row($sql)){?>
                <tr style="font-size: 11px">
                    <td><?=$i=$i+1;?></td>
                    <td><?=$data[0]?></td>
                    <td><?php
                        if($data[4]=='Receipt'||$data[4]=='Payment'||$data[4]=='Journal_info'||$data[4]=='Contra')
                        {
                            $link="acc_voucher_print.php?v_type=".$data[4]."&v_date=".$data[0]."&view=1&vo_no=".$data[8];
                            echo "<a href='$link' target='_blank'>".$data[7]."</a>";
                        }else {
                            $link="acc_voucher_print.php?v_type=".$data[4]."&v_date=".$data[0]."&view=1&vo_no=".$data[8];
                            echo "<a href='$link' target='_blank'>".$data[6]."</a>";}?>
                    </td>
                    <td><?=$data[5];?></td>
                    <td style="text-align: center"><?=($data[13]>0)? $data[16] : 'N/A'; ?></td>
                    <td style="text-align: center"><?=$data[4];?></td>
                    <td style="text-align: right"><?=($data[2]>0)? number_format($data[2]) : '-';?></td>
                    <td style="text-align: right"><?=($data[3]>0)? number_format($data[3]) : '-';?></td>
                    <td style="text-align: right"><?php $blance = $blance+($data[2]-$data[3]);
                        if($blance>0) echo '(Dr) '.number_format($blance,2,'.',',');
                        elseif($blance<0) echo '(Cr) '.number_format(((-1)*$blance),2,'.',',');else echo "0.00"; ?></td>
                </tr>
            <?php }?>

            <tr style="font-size: 11px">
                <th colspan="6" style="text-align: right">Total</th>
                <th style="text-align: right"><?=number_format($total[0],2);?></th>
                <th style="text-align: right"><?=number_format($total[1],2);?></th>
                <th style="text-align: right">
                    <?php if($blance>0) echo '(Dr) '.number_format($blance,2,'.',',');
                    elseif($blance<0) echo '(Cr) '.number_format(((-1)*$blance),2,'.',',');else echo "0.00"; ?>
                </th>
            </tr>
            </tbody>
        </table>
    </main>
    <footer>
        <p><em>Report Generated By: <?=$_SESSION['username']?>, <?=$_SESSION['designation']?>, Time: <?php $dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
                echo $now=$dateTime->format("d/m/Y  h:i:s A");?></em></p>
    </footer>
</div>
</body>
</html>
