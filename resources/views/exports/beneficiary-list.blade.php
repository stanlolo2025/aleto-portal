<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Beneficiary List - {{ $grant->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 0; }
        .no-print { text-align: center; padding: 15px; background: #f0f0f0; }
        .no-print button { padding: 10px 25px; font-size: 14px; cursor: pointer; background: #1a5276; color: #fff; border: none; border-radius: 5px; }

        /* Summary Page */
        .summary-page { padding: 30px; page-break-after: always; }
        .summary-page .header { text-align: center; border-bottom: 3px solid #1a5276; padding-bottom: 15px; margin-bottom: 20px; }
        .summary-page .header h1 { margin: 0; font-size: 22px; color: #1a5276; }
        .summary-page .header h2 { margin: 5px 0; font-size: 16px; color: #555; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table th, .summary-table td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; font-size: 11px; }
        .summary-table th { background: #1a5276; color: #fff; }
        .summary-table tr:nth-child(even) { background: #f9f9f9; }
        .total-row { font-weight: bold; background: #e9ecef !important; }

        /* Individual Beneficiary Page */
        .beneficiary-page { padding: 30px; page-break-after: always; min-height: 90vh; position: relative; }
        .beneficiary-page:last-child { page-break-after: auto; }
        .ben-header { text-align: center; border-bottom: 2px solid #1a5276; padding-bottom: 10px; margin-bottom: 25px; }
        .ben-header h3 { color: #1a5276; margin: 0; }
        .ben-header p { margin: 3px 0; color: #777; }
        .ben-photo { width: 120px; height: 150px; border: 3px solid #1a5276; border-radius: 6px; object-fit: cover; float: right; margin-left: 20px; }
        .ben-photo-placeholder { width: 120px; height: 150px; border: 3px solid #ccc; border-radius: 6px; float: right; margin-left: 20px; display: flex; align-items: center; justify-content: center; background: #f5f5f5; color: #999; font-size: 40px; }
        .ben-details { margin-top: 10px; }
        .ben-details table { width: 100%; border-collapse: collapse; }
        .ben-details td { padding: 8px 12px; border-bottom: 1px solid #eee; }
        .ben-details td:first-child { font-weight: bold; width: 200px; color: #333; background: #f8f9fa; }
        .ben-amount { text-align: center; margin-top: 30px; padding: 15px; background: #e8f5e9; border: 2px solid #27ae60; border-radius: 8px; }
        .ben-amount h2 { color: #27ae60; margin: 0; }
        .ben-signatures { position: absolute; bottom: 40px; left: 30px; right: 30px; }
        .ben-signatures table { width: 100%; }
        .ben-signatures td { text-align: center; padding-top: 40px; border-top: 1px solid #333; width: 30%; }
        .page-number { text-align: center; margin-top: 20px; color: #999; font-size: 10px; }

        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">🖨️ Print / Save as PDF</button>
        <span style="margin-left:15px; color:#666;">This document contains {{ $items->count() + 1 }} pages (1 summary + {{ $items->count() }} individual pages)</span>
    </div>

    <!-- Page 1: Summary Table -->
    <div class="summary-page">
        <div class="header">
            <h1>🏘️ ALETO CLAN COMMUNITY PORTAL</h1>
            <h2>GRANT BENEFICIARY LIST</h2>
            <p>Official Document for Bank Payment Processing</p>
        </div>

        <table style="width:100%; margin-bottom:20px;">
            <tr><td><strong>Grant Name:</strong> {{ $grant->name }}</td><td><strong>Grant ID:</strong> {{ $grant->grant_identifier }}</td></tr>
            <tr><td><strong>Amount per Person:</strong> ₦{{ number_format($grant->amount, 2) }}</td><td><strong>Export Date:</strong> {{ $exportDate }}</td></tr>
            <tr><td><strong>Total Beneficiaries:</strong> {{ $items->count() }}</td><td><strong>Total Amount:</strong> ₦{{ number_format($items->sum('grant_amount'), 2) }}</td></tr>
        </table>

        <table class="summary-table">
            <thead>
                <tr><th>#</th><th>Unique ID</th><th>Full Name</th><th>Age</th><th>Gender</th><th>Village</th><th>Bank Name</th><th>Account Number</th><th>Amount (₦)</th></tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                @php
                    $villager = $item->villagerRecord;
                    $bankName = $villager->proxyAccount ? $villager->proxyAccount->proxy_bank_name : ($villager->bank_name ?? 'N/A');
                    $accountNumber = $villager->getEffectiveBankAccount() ?? 'N/A';
                    $age = $villager->date_of_birth ? now()->diffInYears($villager->date_of_birth) : 'N/A';
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $villager->unique_id }}</td>
                    <td>{{ $villager->full_name }}</td>
                    <td>{{ $age }}</td>
                    <td>{{ ucfirst($villager->gender) }}</td>
                    <td>{{ $villager->village ?? 'N/A' }}</td>
                    <td>{{ $bankName }}</td>
                    <td>{{ $accountNumber }}</td>
                    <td>{{ number_format($item->grant_amount, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row"><td colspan="8" style="text-align:right;">TOTAL:</td><td>₦{{ number_format($items->sum('grant_amount'), 2) }}</td></tr>
            </tbody>
        </table>

        <div style="margin-top:30px;">
            <table style="width:100%;"><tr>
                <td style="border-top:1px solid #333; width:30%; text-align:center; padding-top:8px;">Admin Officer</td>
                <td style="width:5%;"></td>
                <td style="border-top:1px solid #333; width:30%; text-align:center; padding-top:8px;">Committee Chairman</td>
                <td style="width:5%;"></td>
                <td style="border-top:1px solid #333; width:30%; text-align:center; padding-top:8px;">Date</td>
            </tr></table>
        </div>
    </div>

    <!-- Individual Beneficiary Pages -->
    @foreach($items as $index => $item)
    @php
        $villager = $item->villagerRecord;
        $bankName = $villager->proxyAccount ? $villager->proxyAccount->proxy_bank_name : ($villager->bank_name ?? 'N/A');
        $accountNumber = $villager->getEffectiveBankAccount() ?? 'N/A';
        $proxyName = $villager->proxyAccount ? $villager->proxyAccount->representative_name : null;
    @endphp
    <div class="beneficiary-page">
        <div class="ben-header">
            <h3>ALETO CLAN COMMUNITY PORTAL — BENEFICIARY PAYMENT SLIP</h3>
            <p>Grant: {{ $grant->name }} ({{ $grant->grant_identifier }}) | Page {{ $index + 2 }} of {{ $items->count() + 1 }}</p>
        </div>

        @if($villager->passport_photo)
            <img src="{{ asset('storage/' . $villager->passport_photo) }}" class="ben-photo" alt="Photo">
        @else
            <div class="ben-photo-placeholder">👤</div>
        @endif

        <div class="ben-details">
            <table>
                <tr><td>Unique ID</td><td><strong>{{ $villager->unique_id }}</strong></td></tr>
                <tr><td>Full Name</td><td><strong style="font-size:14px;">{{ $villager->full_name }}</strong></td></tr>
                <tr><td>Date of Birth</td><td>{{ $villager->date_of_birth?->format('d/m/Y') ?? 'N/A' }}</td></tr>
                <tr><td>Gender</td><td>{{ ucfirst($villager->gender) }}</td></tr>
                <tr><td>Household ID</td><td>{{ $villager->household_id }}</td></tr>
                <tr><td>Village / Ward</td><td>{{ $villager->village ?? 'N/A' }} / {{ $villager->ward ?? 'N/A' }}</td></tr>
                <tr><td>Phone Number</td><td>{{ $villager->phone_number ?? 'N/A' }}</td></tr>
                <tr><td>NIN</td><td>{{ $villager->nin ?? 'N/A' }}</td></tr>
                <tr><td>Bank Name</td><td><strong>{{ $bankName }}</strong></td></tr>
                <tr><td>Account Number</td><td><strong>{{ $accountNumber }}</strong></td></tr>
                @if($proxyName)
                <tr><td>Proxy Representative</td><td>{{ $proxyName }} ({{ $villager->proxyAccount->relationship }})</td></tr>
                @endif
            </table>
        </div>

        <div class="ben-amount">
            <p style="margin:0; color:#333;">Amount to be Paid</p>
            <h2>₦{{ number_format($item->grant_amount, 2) }}</h2>
        </div>

        <div class="ben-signatures">
            <table><tr>
                <td>Beneficiary Signature/Thumbprint</td>
                <td>Admin Officer</td>
                <td>Date</td>
            </tr></table>
        </div>

        <div class="page-number">Page {{ $index + 2 }} of {{ $items->count() + 1 }}</div>
    </div>
    @endforeach
</body>
</html>
