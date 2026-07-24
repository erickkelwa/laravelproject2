<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Fee;
use App\Models\MpesaTransaction;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MpesaController extends Controller
{
    private $consumerKey;
    private $consumerSecret;
    private $shortcode;
    private $passkey;
    private $env;

    public function __construct()
    {
        $this->consumerKey = env('MPESA_CONSUMER_KEY', 'ukLTXwKT0MuAVpnsUqwVVlzeS7Tf9AD1JSTUjmBUfOGQg3fc');
        $this->consumerSecret = env('MPESA_CONSUMER_SECRET', 'rUtZUafz9gV6kJ2LqTwUs1oLXsoOH8sPJRDasBPEp7r0cSpJ7L9GA8flq9OnmX4Z');
        $this->shortcode = env('MPESA_SHORTCODE', '174379'); // sandbox shortcode
        $this->passkey = env('MPESA_PASSKEY', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919');
        $this->env = env('MPESA_ENV', 'sandbox'); // 'live' or 'sandbox'
    }

    public function index(Request $request)
    {
        $query = MpesaTransaction::with('student')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('receipt_number', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhereDate('created_at', $search);
        }

        $transactions = $query->paginate(15)->withQueryString();

        return view('mpesa.index', compact('transactions'));
    }

    public function generateAccessToken()
    {
        $url = $this->env == 'live' 
            ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
            : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);

        try {
            // withoutVerifying() prevents local SSL certificate errors on Windows (cURL error 60)
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Basic ' . $credentials
            ])->get($url);
            
            if (!$response->successful()) {
                Log::error('Safaricom Auth Failed: ' . $response->body());
                return ['error' => 'Safaricom returned: ' . $response->body()];
            }

            return $response->json()['access_token'] ?? null;
            
        } catch (\Exception $e) {
            Log::error('Mpesa Auth Exception: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function stkPush(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric',
            'student_id' => 'required|exists:students,id',
            'term' => 'nullable|string'
        ]);

        $phone = $this->formatPhone($request->phone);
        $amount = $request->amount;
        $studentId = $request->student_id;
        $term = $request->term;
        $student = \App\Models\Student::findOrFail($studentId);
        
        $accessToken = $this->generateAccessToken();

        if (is_array($accessToken) && isset($accessToken['error'])) {
            return back()->with('error', 'Token Error: ' . $accessToken['error']);
        }

        if (!$accessToken) {
            return back()->with('error', 'Failed to get access token from Safaricom.');
        }

        // withoutVerifying() prevents local SSL certificate errors on Windows
        $url = $this->env == 'live'
            ? 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
            : 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        // Generate callback URL using APP_URL to ensure it's the public HTTPS URL (not localhost)
        $callbackUrl = env('MPESA_CALLBACK_URL', rtrim(env('APP_URL'), '/') . '/mpesa/callback');

        $response = Http::withoutVerifying()->withToken($accessToken)->post($url, [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => $callbackUrl,
            'AccountReference' => 'Student ' . $studentId,
            'TransactionDesc' => 'Fee Payment'
        ]);

        $result = $response->json();

        if (isset($result['ResponseCode']) && $result['ResponseCode'] == '0') {
            // Save pending transaction
            MpesaTransaction::create([
                'student_id' => $studentId,
                'checkout_request_id' => $result['CheckoutRequestID'],
                'merchant_request_id' => $result['MerchantRequestID'],
                'amount' => $amount,
                'phone_number' => $phone,
                'status' => 'pending',
                'term' => $term
            ]);

            \Illuminate\Support\Facades\Notification::send(
                \App\Models\User::all(), 
                new \App\Notifications\MpesaPaymentPending($amount, $phone)
            );

            return back()->with('success', 'STK Push sent successfully. Please check your phone to enter M-Pesa PIN.')
                         ->with('mpesa_checkout_id', $result['CheckoutRequestID'])
                         ->with('mpesa_student_name', $student->name);
        }

        return back()->with('error', 'Failed to send STK Push: ' . ($result['errorMessage'] ?? 'Unknown error'));
    }

    public function callback(Request $request)
    {
        Log::info('M-Pesa Callback:', $request->all());

        $callbackData = $request->input('Body.stkCallback');

        if (!$callbackData) {
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid data']);
        }

        $checkoutRequestId = $callbackData['CheckoutRequestID'];
        $resultCode = $callbackData['ResultCode'];
        $resultDesc = $callbackData['ResultDesc'];

        $transaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)->first();

        if ($transaction) {
            if ($resultCode == 0) {
                // Payment successful
                $items = $callbackData['CallbackMetadata']['Item'];
                $amount = 0;
                $mpesaReceiptNumber = '';
                $transactionDate = now();

                foreach ($items as $item) {
                    if ($item['Name'] == 'Amount') {
                        $amount = $item['Value'];
                    } elseif ($item['Name'] == 'MpesaReceiptNumber') {
                        $mpesaReceiptNumber = $item['Value'];
                    } elseif ($item['Name'] == 'TransactionDate') {
                        $transactionDate = Carbon::createFromFormat('YmdHis', $item['Value']);
                    }
                }

                // Update transaction
                $transaction->update([
                    'status' => 'success',
                    'receipt_number' => $mpesaReceiptNumber,
                    'transaction_date' => $transactionDate,
                    'result_desc' => $resultDesc
                ]);

                // Create Fee record
                $fee = Fee::create([
                    'student_id' => $transaction->student_id,
                    'amount' => $amount,
                    'payment_method' => 'MPESA',
                    'term' => $transaction->term,
                    'receipt_no' => $mpesaReceiptNumber,
                    'payment_date' => $transactionDate->format('Y-m-d')
                ]);

                $studentName = $fee->student ? $fee->student->name : 'Unknown Student';
                \Illuminate\Support\Facades\Notification::send(
                    \App\Models\User::all(),
                    new \App\Notifications\MpesaPaymentReceived($amount, $studentName, $transactionDate->format('h:i A, M d'))
                );

                // Send email receipt
                if ($fee->student && $fee->student->email) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($fee->student->email)
                            ->send(new \App\Mail\PaymentReceived($fee));
                    } catch (\Exception $e) {
                        Log::error('Failed to send email receipt: ' . $e->getMessage());
                    }
                }
            } else {
                // Payment failed or cancelled
                $transaction->update([
                    'status' => 'failed',
                    'result_desc' => $resultDesc
                ]);

                $studentName = $transaction->student ? $transaction->student->name : 'Unknown Student';
                \Illuminate\Support\Facades\Notification::send(
                    \App\Models\User::all(),
                    new \App\Notifications\MpesaPaymentFailed($transaction->amount, $studentName, $resultDesc)
                );
            }
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    public function checkStatus($checkoutRequestId)
    {
        $transaction = MpesaTransaction::where('checkout_request_id', $checkoutRequestId)->first();

        if (!$transaction) {
            return response()->json(['status' => 'not_found']);
        }

        $fee_id = null;
        if ($transaction->status === 'success' && $transaction->receipt_number) {
            $fee = \App\Models\Fee::where('receipt_no', $transaction->receipt_number)->first();
            $fee_id = $fee ? $fee->id : null;
        }

        return response()->json([
            'status' => $transaction->status,
            'result_desc' => $transaction->result_desc,
            'fee_id' => $fee_id
        ]);
    }

    private function formatPhone($phone)
    {
        // format 07xx to 2547xx
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 1) == '0') {
            return '254' . substr($phone, 1);
        }
        if (substr($phone, 0, 1) == '+') {
            return substr($phone, 1);
        }
        if (strlen($phone) == 9) {
            return '254' . $phone;
        }
        return $phone;
    }
}
