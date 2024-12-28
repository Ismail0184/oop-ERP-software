<?php require_once 'support_file.php';
$ledger_name=find_a_field('accounts_ledger','ledger_name','ledger_id='.$_POST['ledger_id'])
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trial Balance</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            color: #333;
        }

        .filters {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filters label {
            font-size: 14px;
            color: #555;
        }

        .filters input[type="date"] {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .filter-btn {
            padding: 5px 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .filter-btn:hover {
            background-color: #0056b3;
        }

        /* Table Styles */
        .trial-balance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .trial-balance-table th,
        .trial-balance-table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .trial-balance-table th {
            background-color: #007bff;
            color: #fff;
        }

        .trial-balance-table tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .trial-balance-table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .totals-row {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
        }

    </style>
</head>
<body>
<div class="container">
    <header class="header">
        <h1>Trial Balance</h1>
        <div class="filters">
            <label for="start-date">Start Date:</label>
            <input type="date" id="start-date">
            <label for="end-date">End Date:</label>
            <input type="date" id="end-date">
            <button class="filter-btn">Apply Filters</button>
        </div>
    </header>
    <table class="trial-balance-table">
        <thead>
        <tr>
            <th>Account Code</th>
            <th>Account Name</th>
            <th>Debit</th>
            <th>Credit</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>1001</td>
            <td>Cash</td>
            <td>50,000</td>
            <td>0</td>
        </tr>
        <tr>
            <td>2001</td>
            <td>Accounts Payable</td>
            <td>0</td>
            <td>25,000</td>
        </tr>
        <tr class="totals-row">
            <td colspan="2"><strong>Totals</strong></td>
            <td><strong>50,000</strong></td>
            <td><strong>25,000</strong></td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
