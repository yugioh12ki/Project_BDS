<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\detail_transaction;
use App\Models\Commission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Process transaction payment via VNPAY for 4-step transaction creation
     */
    public function processTransactionPayment(Request $request)
    {
        try {
            $request->validate([
                'transaction_id' => 'required|string',
                'payment_type' => 'required|in:transaction,commission,customer_payment',
                'amount' => 'required|numeric|min:1',
                'payment_description' => 'nullable|string'
            ]);

            $transactionId = $request->transaction_id;
            $paymentType = $request->payment_type;
            $amount = $request->amount;
            $description = $request->payment_description ?? 'Thanh toán giao dịch bất động sản';

            // Verify transaction exists and user has permission
            $transaction = Transaction::with(['trans_property', 'trans_cus', 'trans_agent', 'trans_owner'])
                ->where('TransactionID', $transactionId)
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy giao dịch'
                ], 404);
            }

            // Check user permission based on payment type
            $user = Auth::user();
            if (!$this->hasPaymentPermission($user, $transaction, $paymentType)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền thực hiện thanh toán này'
                ], 403);
            }

            // Create VNPAY payment URL
            $vnpayUrl = $this->createVNPAYPaymentUrl([
                'transaction_id' => $transactionId,
                'payment_type' => $paymentType,
                'amount' => $amount,
                'description' => $description,
                'user_id' => $user->UserID,
                'order_info' => $this->generateOrderInfo($transaction, $paymentType)
            ]);

            return response()->json([
                'success' => true,
                'payment_url' => $vnpayUrl,
                'message' => 'Đang chuyển hướng đến VNPAY...'
            ]);

        } catch (\Exception $e) {
            Log::error('Transaction payment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xử lý thanh toán: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle VNPAY return callback
     */
    public function vnpayReturn(Request $request)
    {
        try {
            $vnp_SecureHash = $request->vnp_SecureHash;
            $inputData = array();

            foreach ($request->all() as $key => $value) {
                if (substr($key, 0, 4) == "vnp_") {
                    $inputData[$key] = $value;
                }
            }

            unset($inputData['vnp_SecureHash']);
            ksort($inputData);

            $vnp_HashSecret = config('vnpay.hash_secret', 'BC0OVP01E49X7O56QN2A0METEOO8GRES');
            $hashData = "";
            $i = 0;

            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }

            $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

            if ($secureHash == $vnp_SecureHash) {
                if ($request->vnp_ResponseCode == '00') {
                    // Payment successful
                    $this->handleSuccessfulPayment($request);
                    return $this->redirectToSuccessPage($request);
                } else {
                    // Payment failed
                    return $this->redirectToErrorPage($request, 'Thanh toán không thành công');
                }
            } else {
                return $this->redirectToErrorPage($request, 'Chữ ký không hợp lệ');
            }
        } catch (\Exception $e) {
            Log::error('VNPAY return error: ' . $e->getMessage());
            return $this->redirectToErrorPage($request, 'Lỗi xử lý kết quả thanh toán');
        }
    }

    /**
     * Handle VNPAY IPN callback
     */
    public function vnpayIPN(Request $request)
    {
        try {
            $vnp_HashSecret = config('vnpay.hash_secret', 'BC0OVP01E49X7O56QN2A0METEOO8GRES');
            $inputData = array();

            foreach ($request->all() as $key => $value) {
                if (substr($key, 0, 4) == "vnp_") {
                    $inputData[$key] = $value;
                }
            }

            $vnp_SecureHash = $inputData['vnp_SecureHash'];
            unset($inputData['vnp_SecureHash']);
            ksort($inputData);

            $hashData = "";
            $i = 0;
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }

            $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

            if ($secureHash == $vnp_SecureHash) {
                if ($request->vnp_ResponseCode == '00') {
                    $this->handleSuccessfulPayment($request);
                    echo json_encode(['RspCode' => '00', 'Message' => 'Confirm Success']);
                } else {
                    echo json_encode(['RspCode' => '01', 'Message' => 'Transaction Failed']);
                }
            } else {
                echo json_encode(['RspCode' => '97', 'Message' => 'Invalid Signature']);
            }
        } catch (\Exception $e) {
            Log::error('VNPAY IPN error: ' . $e->getMessage());
            echo json_encode(['RspCode' => '99', 'Message' => 'Unknown Error']);
        }
    }

    /**
     * Create VNPAY payment URL
     */
    private function createVNPAYPaymentUrl($paymentData)
    {
        $vnp_Url = config('vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $vnp_TmnCode = config('vnpay.tmn_code', 'F0DR9Y2U');
        $vnp_HashSecret = config('vnpay.hash_secret', 'BC0OVP01E49X7O56QN2A0METEOO8GRES');

        // Generate unique transaction reference
        $vnp_TxnRef = $paymentData['transaction_id'] . '_' . $paymentData['payment_type'] . '_' . time();

        // Determine return URL based on payment type
        $returnUrl = $this->getReturnUrl($paymentData['payment_type']);

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $paymentData['amount'] * 100, // VNPay expects amount in smallest unit
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => request()->ip(),
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => $paymentData['order_info'],
            "vnp_OrderType" => "billpayment",
            "vnp_ReturnUrl" => $returnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

        return $vnp_Url;
    }

    /**
     * Handle successful payment
     */
    private function handleSuccessfulPayment($request)
    {
        DB::beginTransaction();
        try {
            // Parse transaction reference to get payment details
            $txnRef = $request->vnp_TxnRef;
            $parts = explode('_', $txnRef);
            $transactionId = $parts[0];
            $paymentType = $parts[1];

            $transaction = Transaction::find($transactionId);
            if (!$transaction) {
                throw new \Exception('Transaction not found');
            }

            switch ($paymentType) {
                case 'transaction':
                    $this->updateTransactionPayment($transaction, $request);
                    break;

                case 'commission':
                    $this->updateCommissionPayment($transaction, $request);
                    break;

                case 'customer_payment':
                    $this->updateCustomerPayment($transaction, $request);
                    break;
            }

            DB::commit();
            Log::info('Payment successful for transaction: ' . $transactionId . ', type: ' . $paymentType);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update transaction payment status
     */
    private function updateTransactionPayment($transaction, $request)
    {
        // Update transaction status
        $transaction->TranStatus = 'Paid';
        $transaction->save();

        // Update detail transaction payment status
        detail_transaction::where('TransactionID', $transaction->TransactionID)
            ->where('DTran_Status', 'Chờ đợi')
            ->update([
                'DTran_Status' => 'Đã thanh toán',
                'PaymentType' => 'VNPAY'
            ]);
    }

    /**
     * Update commission payment status
     */
    private function updateCommissionPayment($transaction, $request)
    {
        Commission::where('TransactionID', $transaction->TransactionID)
            ->where('StatusCommission', 'Pending')
            ->update([
                'StatusCommission' => 'Success',
                'PaidDate' => now()
            ]);
    }

    /**
     * Update customer payment status
     */
    private function updateCustomerPayment($transaction, $request)
    {
        // Find the specific detail transaction for customer payment
        $detailTransaction = detail_transaction::where('TransactionID', $transaction->TransactionID)
            ->where('DTran_Status', 'Chờ đợi')
            ->orderBy('Num_Pay', 'asc')
            ->first();

        if ($detailTransaction) {
            $detailTransaction->DTran_Status = 'Đã thanh toán';
            $detailTransaction->PaymentType = 'VNPAY';
            $detailTransaction->save();

            // Check if all payments are completed
            $remainingPayments = detail_transaction::where('TransactionID', $transaction->TransactionID)
                ->where('DTran_Status', 'Chờ đợi')
                ->count();

            if ($remainingPayments == 0) {
                $transaction->TranStatus = 'Paid';
                $transaction->save();
            }
        }
    }

    /**
     * Check if user has permission for payment type
     */
    private function hasPaymentPermission($user, $transaction, $paymentType)
    {
        switch ($paymentType) {
            case 'transaction':
                return $user->Role === 'Agent' && $transaction->AgentID === $user->UserID;

            case 'commission':
                return $user->Role === 'Owner' && $transaction->OwnerID === $user->UserID;

            case 'customer_payment':
                return $user->Role === 'Customer' && $transaction->CusID === $user->UserID;

            default:
                return false;
        }
    }

    /**
     * Generate order info for payment
     */
    private function generateOrderInfo($transaction, $paymentType)
    {
        $propertyTitle = $transaction->trans_property->Title ?? 'Bất động sản';

        switch ($paymentType) {
            case 'transaction':
                return "Thanh toán giao dịch {$transaction->TransactionID} - {$propertyTitle}";

            case 'commission':
                return "Thanh toán hoa hồng giao dịch {$transaction->TransactionID} - {$propertyTitle}";

            case 'customer_payment':
                return "Thanh toán khách hàng giao dịch {$transaction->TransactionID} - {$propertyTitle}";

            default:
                return "Thanh toán giao dịch {$transaction->TransactionID}";
        }
    }

    /**
     * Get return URL based on payment type
     */
    private function getReturnUrl($paymentType)
    {
        switch ($paymentType) {
            case 'transaction':
                return route('payment.vnpay.return.transaction');

            case 'commission':
                return route('payment.vnpay.return.commission');

            case 'customer_payment':
                return route('payment.vnpay.return.customer');

            default:
                return route('payment.vnpay.return');
        }
    }

    /**
     * Redirect to success page based on user role
     */
    private function redirectToSuccessPage($request)
    {
        $user = Auth::user();
        $message = 'Thanh toán thành công!';

        switch ($user->Role) {
            case 'Agent':
                return redirect()->route('agent.transactions')->with('success', $message);

            case 'Owner':
                return redirect()->route('owner.transactions.index')->with('success', $message);

            case 'Customer':
                return redirect()->route('customer.transaction.history')->with('success', $message);

            default:
                return redirect('/')->with('success', $message);
        }
    }

    /**
     * Redirect to error page
     */
    private function redirectToErrorPage($request, $errorMessage)
    {
        $user = Auth::user();

        switch ($user->Role ?? 'guest') {
            case 'Agent':
                return redirect()->route('agent.transactions')->with('error', $errorMessage);

            case 'Owner':
                return redirect()->route('owner.transactions.index')->with('error', $errorMessage);

            case 'Customer':
                return redirect()->route('customer.transaction.history')->with('error', $errorMessage);

            default:
                return redirect('/')->with('error', $errorMessage);
        }
    }

    // Legacy method for backward compatibility
    public function paymentVNPAY(Request $request)
    {
        return $this->processTransactionPayment($request);
    }
}
