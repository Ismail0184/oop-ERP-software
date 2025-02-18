<?php require_once 'support_file.php';
require_once 'class.numbertoword.php';

$proj_id=@$_SESSION['proj_id'];
$vtype= strtolower($_REQUEST['v_type']);
if($vtype=='receipt'){$voucher_name='RECEIPT VOUCHER';$vtypes='receipt';}
elseif($vtype=='purchase'){$voucher_name='PURCHASE VOUCHER';$vtypes='secondary_journal';}
elseif($vtype=='payment'){$voucher_name='PAYMENT VOUCHER';$vtypes='payment';}
elseif($vtype=='payment_bank'){$voucher_name='CHEQUE PAYMENT';$vtypes='secondary_payment';}
elseif($vtype=='journal_info'){$voucher_name='JOURNAL VOUCHER';$vtypes='journal_info';}
elseif($vtype=='contra'){$voucher_name='CONTRA VOUCHER';$vtype='coutra';$vtypes='contra';}
else{$vtype==$_REQUEST['v_type'];$voucher_name=$_REQUEST['v_type'];$vtypes=$_REQUEST['v_type'];}



$no=$vtype."_no";
$vdate=$vtype."_date";

$address=getSVALUE('project_info','proj_address',"where 1");
if(isset($_REQUEST['vo_no']))
{ if($vtype=='purchase'){
    $vo_no = getSVALUE('journal','jv_no','where jv_no='.$_REQUEST['vo_no'].' and tr_from = "Purchase"');
    $sql1="select jvdate,cc_code,user_id,time from journal where jv_no=$vo_no and tr_from = 'Purchase' limit 1";
}elseif ($vtype=='payment_bank')
{   $vo_no = $_REQUEST['vo_no'];
    $sql1="select paymentdate,cc_code,entry_by,time,cheque_id,maturity_date,received_from from secondary_payment where payment_no=$vo_no  limit 1";
} else {
    $vo_no = getSVALUE('journal','tr_no','where jv_no='.$_REQUEST['vo_no'].' and tr_from = "'.ucwords($vtypes).'"');
    $sql1="select jvdate,cc_code,user_id,time from journal where tr_no=$vo_no and tr_from = '".$vtypes."' limit 1";
}

    $data1=mysqli_fetch_row(mysqli_query($conn, $sql1));
    $user_name = getSVALUE('users','fname',"where user_id=".$data1[2]);
    $vo_date=find_a_field('journal','jvdate','tr_no='.$vo_no);
    $cccode=$data1[1];}
$pi=0;
$cr_amt=0;
$dr_amt=0;
if($_SESSION['usergroup']==3)
    $sql2="SELECT a.ledger_name,a.ledger_group_id,b.* FROM accounts_ledger a, journal b where
b.jv_no='".$_GET['vo_no']."' and tr_from='".$_GET['v_type']."' and a.ledger_id=b.ledger_id order by b.id";
else
    if ($vtype=='payment_bank') {
        $sql2 = "SELECT a.ledger_name,a.ledger_group_id,b.* FROM accounts_ledger a, secondary_payment b where b.payment_no='".$_GET['vo_no']."' and a.ledger_id=b.ledger_id order by b.id";
    }
    else {
        $sql2 = "SELECT a.ledger_name,a.ledger_group_id,b.* FROM accounts_ledger a, journal b where b.jv_no='".$_GET['vo_no']."' and tr_from='".$_GET['v_type']."' and a.ledger_id=b.ledger_id order by b.id";
    } ?>
<?php $voucherMaster = find_all_field('journal_voucher_master','','voucherno='.$vo_no); ?>
<?php $title=$voucher_name.'-'.$vo_no;
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$title?></title>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            max-width: 960px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Header Styles */
        .voucher-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0056b3;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .company-details {
            font-size: 14px;
        }

        .voucher-info h2 {
            margin: 0;
            font-size: 24px;
            color: #0056b3;
        }

        .voucher-info p {
            margin: 7px 0;
            font-size: 14px;
        }

        /* Table Styles */
        .voucher-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .voucher-table thead th {
            background: #0056b3;
            color: white;
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .voucher-table tbody td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .voucher-table tfoot td {
            padding: 10px;
            font-weight: bold;
            text-align: left;
            background: #f1f1f1;
        }

        /* Footer Styles */
        .voucher-footer {
            margin-top: 20px;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }

        .notes {
            margin-top: 10px;
            font-size: 12px;
            color: #666;
        }

        /* Print Styles */
        @media print {
            body {
                background-color: white;
                color: black;
            }

            .container {
                margin-top: -10px;
                border: none;
                box-shadow: none;
            }

            .voucher-header {
                border-bottom: 1px solid black;
            }

            .voucher-footer {
                page-break-before: avoid;
            }
        }

    </style>
</head>
<body>

<div class="container">
    <div class="voucher">
        <header class="voucher-header">
            <div class="company-details">
                <h1 style="text-transform: uppercase"><?=$_SESSION['company_name']?></h1>
                <p><?=$_SESSION['company_address']?></p>
                <p>Phone: +88 022-22260178 | Email: info@icpbd.com</p>
            </div>
            <div class="voucher-info">
                <h2><?=$voucher_name?></h2>
                <p><strong>Voucher Date:</strong> <?php if ($_GET['v_type']=='Loan'): ?><?=$_GET['v_date']?> <?php else: ?> <?=$vo_date?> <?php endif;?></p>
                <p><strong>Voucher No:</strong> <?php if ($_GET['v_type']=='Loan'): ?><?=$_GET['vo_no']?> <?php else: ?> <?=$vo_no?>  <?php endif;?></p>
                <p><strong>Prepared By:</strong> <?=$user_name?></p>
                <p><strong>Entry At:</strong> <?=$voucherMaster->entry_at?></p>
            </div>
        </header>

        <main class="voucher-body">
            <table class="voucher-table">
                <thead>
                <tr style="font-size: 12px">
                    <th>#</th>
                    <th>Account Name</th>
                    <th>Description</th>
                    <?php if ($vtype=='payment' || $vtype=='journal_info') { ?>
                        <th style="text-align: center; width: 10%">Cost Center</th>
                    <?php } ?>
                    <th>Debit</th>
                    <th>Credit</th>
                </tr>
                </thead>
                <tbody>

                <?
                $i=0;
                $data2=mysqli_query($conn, $sql2);
                while($info=mysqli_fetch_object($data2)){ $pi++;
                $cr_amt=$cr_amt+$info->cr_amt;
                $dr_amt=$dr_amt+$info->dr_amt;
                $bankGET = @$info->bank;
                if($bankGET==''&&$info->cheq_no!='')
                    $narration=$info->narration.':: Cheq # '.$info->cheq_no.'; C.Date= '.$info->cheq_date;
                elseif($info->cheq_no=='')
                    $narration=$info->narration;
                else
                    $narration=$info->narration.':: Cheq # '.$info->cheq_no.'; C.Date= '.$info->cheq_date.'; Bank # '.$info->bank;?>
                <tr style="font-size: 12px">
                    <td><?=$i=$i+1;?></td>
                    <td><?=$info->ledger_name?> : <?=$info->ledger_id?></td>
                    <td><?=$narration?></td>
                    <?php if ($vtype=='payment' || $vtype=='journal_info') { ?>
                        <td style="text-align: center"><?=($info->cc_code>0)? find_a_field('cost_center','center_name','id='.$info->cc_code) : 'N/A'; ?></td>
                    <?php } ?>
                    <td style="text-align: right"><?=($info->dr_amt>0)? number_format($info->dr_amt,2) : '-';?></td>
                    <td style="text-align: right"><?=($info->cr_amt >0)? number_format($info->cr_amt,2) : '-';?></td>
                </tr>
                <?php }?>
                </tbody>
                <tfoot>
                <?php if ($vtype=='payment' || $vtype=='journal_info') { ?>
                <tr style="font-size: 12px">
                    <td colspan="4"><strong>Total</strong></td>
                    <td style="text-align: right"><strong><?=($dr_amt>0)? number_format($dr_amt,2) : '-';?></strong></td>
                    <td style="text-align: right"><strong><?=($cr_amt>0)? number_format($cr_amt,2) : '-';?></strong></td>
                </tr>
                <?php } else { ?>
                <tr style="font-size: 12px">
                    <td colspan="3"><strong>Total</strong></td>
                    <td style="text-align: right"><strong><?=($dr_amt>0)? number_format($dr_amt,2) : '-';?></strong></td>
                    <td style="text-align: right"><strong><?=($cr_amt>0)? number_format($cr_amt,2) : '-';?></strong></td>
                </tr>
                <?php } ?>
                <tr style="font-size: 12px; background-color: transparent">
                    <td colspan="5" style="background-color: transparent; font-weight: normal"><strong>Amount in Word :</strong> <?=convertNumberCustom($cr_amt);?></td>
                </tr>
                </tfoot>
            </table>
        </main>

        <footer class="voucher-footer">
            <div class="signatures">
                <div>
                    <strong>Prepared By:</strong>
                    <p>___________________</p>
                </div>

                <div>
                    <strong>Received By:</strong>
                    <p>________________</p>
                </div>

                <div>
                    <strong>Checked By:</strong>
                    <p>________________</p>
                </div>

                <div>
                    <strong>Recommended By:</strong>
                    <p>________________</p>
                </div>

                <div>
                    <strong>Approved By:</strong>
                    <p>________________</p>
                </div>
            </div>
            <div class="notes">
                <strong>Notes:</strong>
                <p>All amounts are in BDT. This voucher is generated automatically by the ERP system.</p>
            </div>
        </footer>
    </div>
</div>
</body>
</html>
