<?php
require_once 'support_file.php';
$title='Pending Requests';

$now=time();
$unique='id';
$unique_field='fname';
$table="dealer_credit_limit_record";
$page="acc_dealer_credit_limit.php";
$crud      =new crud($table);
$$unique = @$_GET[$unique];

$res="Select 
dr.dealer_code,
concat(d.dealer_code,' : ' ,d.account_code) as account_code,
d.dealer_name_e as dealer_name,
d.dealer_type,
dr.requested_limit,
IF (dr.limit_duration = 'Longtime', 'Unlimited', 'Once Only') as credit_limit_duration,
dr.remarks,
concat(u.fname, '<br> at: ',dr.entry_at) as reuqested_by,
dr.status as status

from 
    
dealer_info d ,
accounts_ledger a,
dealer_credit_limit_request dr,
users u

 where 
 d.account_code=a.ledger_id and
 d.canceled in ('Yes') and 
 d.dealer_code = dr.dealer_code and
 dr.entry_by = u.user_id and 
 dr.status in ('PENDINGS')
 group by d.account_code
 order by d.account_code";
?>

<?php require_once 'header_content.php'; ?>
    <script type="text/javascript">
        function DoNavPOPUP(lk)
        {myWindow = window.open("<?=$page?>?id="+lk, "myWindow", "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no,directories=0,toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=600,height=600,left = 450,top = -1");}
    </script>
<?php require_once 'body_content.php'; ?>





<?php if(!isset($_GET[$unique])){ ?>
    <?=$crud->report_templates_with_status($res,$title,'12');?>
<?php } ?>
<?=$html->footer_content();?>