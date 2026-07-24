<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt #{{ $fee->receipt_no ?? $fee->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            font-size: 14px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 40px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            vertical-align: top;
        }
        /* Header */
        .header td {
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            color: #0d6efd;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0 0;
            color: #555;
            font-size: 13px;
        }
        .header-right {
            text-align: right;
        }
        .header-right h3 {
            margin: 0;
            color: #333;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        /* Details section */
        .details-table {
            margin-top: 30px;
            margin-bottom: 40px;
        }
        .details-table h4 {
            margin: 0 0 8px;
            font-size: 12px;
            color: #888;
            text-transform: uppercase;
        }
        .details-table p {
            margin: 3px 0;
            font-size: 15px;
        }
        /* Items Table */
        table.items {
            margin-bottom: 40px;
        }
        table.items th {
            background-color: #f8f9fa;
            color: #555;
            text-align: left;
            padding: 12px 15px;
            border-bottom: 2px solid #ddd;
            text-transform: uppercase;
            font-size: 12px;
        }
        table.items td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        table.items th.right, table.items td.right {
            text-align: right;
        }
        /* Totals */
        .totals-wrapper {
            width: 100%;
        }
        .totals-table {
            width: 50%;
            float: right;
        }
        .totals-table td {
            padding: 8px 15px;
            text-align: right;
            font-size: 15px;
        }
        .totals-table tr.grand-total td {
            font-size: 18px;
            font-weight: bold;
            color: #0d6efd;
            border-top: 2px solid #ddd;
            padding-top: 15px;
        }
        .totals-table tr.balance td {
            font-size: 16px;
            font-weight: bold;
            color: #dc3545;
            padding-top: 15px;
        }
        /* Footer */
        .footer {
            margin-top: 60px;
            text-align: center;
            color: #999;
            font-size: 12px;
            border-top: 1px solid #eee;
            padding-top: 20px;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        
        <!-- Header -->
        <table class="header">
            <tr>
                <td>
                    <h2>Student System</h2>
                    <p>123 Education Lane<br>Nairobi, Kenya<br>info@studentsystem.com</p>
                </td>
                <td class="header-right">
                    <h3>Official Receipt</h3>
                    <p>
                        <strong>Receipt #:</strong> {{ $fee->receipt_no ?? $fee->id }}<br>
                        <strong>Date Paid:</strong> {{ \Carbon\Carbon::parse($fee->payment_date)->format('F d, Y') }}
                    </p>
                </td>
            </tr>
        </table>

        <!-- Details -->
        <table class="details-table">
            <tr>
                <td>
                    <h4>Billed To</h4>
                    <p><strong>{{ $fee->student->name ?? 'N/A' }}</strong></p>
                    <p>{{ $fee->student->course ?? 'N/A' }}</p>
                    <p>{{ $fee->student->email ?? 'N/A' }}</p>
                </td>
                <td style="text-align: right;">
                    <h4>Payment Info</h4>
                    <p><strong>Method:</strong> {{ $fee->payment_method }}</p>
                    <p><strong>Status:</strong> <span style="color: #198754;">Successful</span></p>
                </td>
            </tr>
        </table>

        <!-- Line Items -->
        <table class="items">
            <tr>
                <th>Description</th>
                <th class="right">Term</th>
                <th class="right">Amount Paid</th>
            </tr>
            <tr>
                <td>Fee Payment - {{ $fee->student->course ?? 'Course' }}</td>
                <td class="right">{{ $fee->term ?? '—' }}</td>
                <td class="right">KES {{ number_format($fee->amount, 2) }}</td>
            </tr>
        </table>

        <!-- Totals -->
        <div class="totals-wrapper">
            <table class="totals-table">
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td>KES {{ number_format($fee->amount, 2) }}</td>
                </tr>
                <tr class="grand-total">
                    <td>Total Paid:</td>
                    <td>KES {{ number_format($fee->amount, 2) }}</td>
                </tr>
                <tr class="balance">
                    <td>Current Balance Remaining:</td>
                    <td>
                        @if($fee->student)
                            KES {{ number_format(max(0, $fee->student->balance), 2) }}
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Thank you for your payment.<br>
            This is a computer-generated receipt and requires no physical signature.
        </div>
    </div>
</body>
</html>
