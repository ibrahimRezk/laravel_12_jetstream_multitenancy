<?php
// new
namespace App\Exceptions;

use Exception;

class PayPalException extends Exception
{
    protected $paypalError;

    public function __construct($message = '', $paypalError = null, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->paypalError = $paypalError;
    }

    public function getPayPalError()
    {
        return $this->paypalError;
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'PayPal payment error',
                'message' => $this->getMessage(),
                'details' => $this->paypalError
            ], 422);
        }

        return redirect()->back()
            ->with('error', 'Payment error: ' . $this->getMessage())
            ->withInput();
    }
}