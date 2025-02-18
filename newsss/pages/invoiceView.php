<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Invoice</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="invoice-container">
    <!-- Invoice Header -->
    <header class="invoice-header">
        <div class="company-info">
            <img src="logo.png" alt="Company Logo" class="logo">
            <div class="company-details">
                <h1>XYZ Solutions</h1>
                <p>123 Business Street, City, Country</p>
                <p>Email: contact@xyzsolutions.com</p>
                <p>Phone: (123) 456-7890</p>
            </div>
        </div>
        <div class="invoice-details">
            <h2>Invoice</h2>
            <p><strong>Invoice #:</strong> 00123</p>
            <p><strong>Date:</strong> 2024-12-11</p>
        </div>
    </header>

    <!-- Customer and Payment Info -->
    <section class="customer-info">
        <div class="customer-details">
            <h3>Bill To:</h3>
            <p><strong>John Doe</strong></p>
            <p>456 Customer Street, City, Country</p>
            <p>Email: john.doe@example.com</p>
            <p>Phone: (987) 654-3210</p>
        </div>
        <div class="payment-details">
            <h3>Payment Terms:</h3>
            <p><strong>Due Date:</strong> 2024-12-25</p>
            <p><strong>Payment Method:</strong> Bank Transfer</p>
        </div>
    </section>

    <!-- Invoice Table -->
    <section class="invoice-table-container">
        <table class="invoice-table">
            <thead>
            <tr>
                <th>Item Description</th>
                <th>Unit Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Product A</td>
                <td>$50.00</td>
                <td>2</td>
                <td>$100.00</td>
            </tr>
            <tr>
                <td>Product B</td>
                <td>$30.00</td>
                <td>3</td>
                <td>$90.00</td>
            </tr>
            <tr>
                <td>Service C</td>
                <td>$20.00</td>
                <td>1</td>
                <td>$20.00</td>
            </tr>
            </tbody>
        </table>
    </section>

    <!-- Invoice Totals -->
    <section class="invoice-totals">
        <div class="totals-left">
            <p><strong>Notes:</strong> Thank you for your business!</p>
        </div>
        <div class="totals-right">
            <p><strong>Subtotal:</strong> $210.00</p>
            <p><strong>Tax (10%):</strong> $21.00</p>
            <p><strong>Total Amount:</strong> $231.00</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="invoice-footer">
        <p>&copy; 2024 XYZ Solutions. All rights reserved.</p>
    </footer>
</div>
</body>
</html>
