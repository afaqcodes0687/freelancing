<?php

namespace App\Http\Controllers\Api\Freelancer;

use App\Helper\LogActivity;
use App\Http\Controllers\Controller;
use App\Models\Order;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /**
     * Generate invoice for a completed order
     */
    public function generate_invoice($order_id)
    {
        try {
            // Debug: Check if user is authenticated
            if (!auth('sanctum')->user()) {
                return response()->json(['msg' => 'User not authenticated'], 401);
            }
            
            // Set the web guard user for view composers
            if (auth('sanctum')->user() && !auth('web')->user()) {
                auth('web')->setUser(auth('sanctum')->user());
            }
            
            $order = Order::with(['user', 'freelancer'])
                ->where('id', $order_id)
                ->where('freelancer_id', auth('sanctum')->user()->id)
                ->where('payment_status', 'complete') // Check for complete payment instead of just status 3
                ->first();

            if (!$order) {
                return response()->json([
                    'msg' => __('Order not found or not completed'),
                    'status' => 'error'
                ], 404);
            }

            // Security management
            // if (moduleExists('SecurityManage')) {
            //     LogActivity::addToLog('Invoice generate', 'Freelancer');
            // }

            // Debug: Check if view exists
            if (!view()->exists('frontend.user.freelancer.order.order-invoice')) {
                return response()->json([
                    'msg' => 'Invoice view not found: frontend.user.freelancer.order.order-invoice',
                    'status' => 'error'
                ], 500);
            }
            
            // Debug: Check storage directory
            $storagePath = storage_path('app/public/invoices');
            if (!is_dir($storagePath)) {
                mkdir($storagePath, 0755, true);
            }
            
            // Ensure the public/storage link exists
            if (!is_link(public_path('storage'))) {
                // Try to create the storage link
                try {
                    \Illuminate\Support\Facades\Artisan::call('storage:link');
                } catch (\Exception $e) {
                    // If we can't create the link, save directly to public folder
                    $storagePath = public_path('invoices');
                    if (!is_dir($storagePath)) {
                        mkdir($storagePath, 0755, true);
                    }
                }
            }

            // Generate PDF
            $pdf = PDF::loadView('frontend.user.freelancer.order.order-invoice', compact('order'));
            
            // Save PDF to storage
            $fileName = 'invoice_' . $order->id . '_' . time() . '.pdf';
            $filePath = 'invoices/' . $fileName;
            
            // Store PDF
            if (is_link(public_path('storage'))) {
                Storage::put($filePath, $pdf->output());
                $fileUrl = Storage::url($filePath);
            } else {
                // Save directly to public/invoices folder
                $fullPath = $storagePath . '/' . $fileName;
                file_put_contents($fullPath, $pdf->output());
                $fileUrl = url('invoices/' . $fileName);
            }

            return response()->json([
                'msg' => __('Invoice generated successfully'),
                'status' => 'success',
                'data' => [
                    'order_id' => $order->id,
                    'invoice_url' => $fileUrl,
                    'invoice_name' => $fileName,
                    'order_details' => [
                        'id' => $order->id,
                        'price' => $order->price,
                        'payable_amount' => $order->payable_amount,
                        'payment_status' => $order->payment_status,
                        'status' => $order->status,
                        'created_at' => $order->created_at,
                        'client' => [
                            'name' => $order->user->first_name . ' ' . $order->user->last_name,
                            'email' => $order->user->email,
                        ],
                        'freelancer' => [
                            'name' => $order->freelancer->first_name . ' ' . $order->freelancer->last_name,
                            'email' => $order->freelancer->email,
                        ]
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'msg' => __('Failed to generate invoice: ') . $e->getMessage() . ' Line: ' . $e->getLine() . ' File: ' . $e->getFile(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Get list of orders eligible for invoice generation
     */
    public function eligible_orders()
    {
        try {
            // Debug: Check if user is authenticated
            if (!auth('sanctum')->user()) {
                return response()->json(['msg' => 'User not authenticated'], 401);
            }
            
            // Set the web guard user for view composers
            if (auth('sanctum')->user() && !auth('web')->user()) {
                auth('web')->setUser(auth('sanctum')->user());
            }
            
            $orders = Order::with(['user'])
                ->where('freelancer_id', auth('sanctum')->user()->id)
                ->where('payment_status', 'complete') // Check for complete payment instead of just status 3
                ->select(['id', 'user_id', 'price', 'payable_amount', 'payment_status', 'status', 'created_at'])
                ->latest()
                ->paginate(10);

            return response()->json([
                'msg' => __('Eligible orders retrieved successfully'),
                'status' => 'success',
                'data' => $orders
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'msg' => __('Failed to retrieve orders: ') . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Download invoice PDF directly
     */
    public function download_invoice($order_id)
    {
        try {
            // Debug: Check if user is authenticated
            if (!auth('sanctum')->user()) {
                return response()->json(['msg' => 'User not authenticated'], 401);
            }
            
            // Set the web guard user for view composers
            if (auth('sanctum')->user() && !auth('web')->user()) {
                auth('web')->setUser(auth('sanctum')->user());
            }
            
            $order = Order::where('id', $order_id)
                ->where('freelancer_id', auth('sanctum')->user()->id)
                ->where('payment_status', 'complete')
                ->first();

            if (!$order) {
                return response()->json([
                    'msg' => __('Order not found or not completed'),
                    'status' => 'error'
                ], 404);
            }

            // Generate PDF
            $pdf = PDF::loadView('frontend.user.freelancer.order.order-invoice', compact('order'));
            
            $fileName = 'invoice_' . $order->id . '_' . time() . '.pdf';

            return response()->streamDownload(
                function () use ($pdf) {
                    echo $pdf->output();
                },
                $fileName,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
                ]
            );

        } catch (\Exception $e) {
            return response()->json([
                'msg' => __('Failed to download invoice: ') . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * Get invoice information without generating PDF
     */
    public function invoice_info($order_id)
    {
        try {
            // Debug: Check if user is authenticated
            if (!auth('sanctum')->user()) {
                return response()->json(['msg' => 'User not authenticated'], 401);
            }
            
            // Set the web guard user for view composers
            if (auth('sanctum')->user() && !auth('web')->user()) {
                auth('web')->setUser(auth('sanctum')->user());
            }
            
            $order = Order::with(['user', 'freelancer'])
                ->where('id', $order_id)
                ->where('freelancer_id', auth('sanctum')->user()->id)
                ->where('payment_status', 'complete')
                ->first();

            if (!$order) {
                return response()->json([
                    'msg' => __('Order not found or not completed'),
                    'status' => 'error'
                ], 404);
            }

            return response()->json([
                'msg' => __('Invoice information retrieved successfully'),
                'status' => 'success',
                'data' => [
                    'order_id' => $order->id,
                    'invoice_number' => 'INV-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                    'order_details' => [
                        'id' => $order->id,
                        'price' => $order->price,
                        'commission_charge' => $order->commission_charge,
                        'commission_amount' => $order->commission_amount,
                        'transaction_charge' => $order->transaction_charge,
                        'transaction_amount' => $order->transaction_amount,
                        'payable_amount' => $order->payable_amount,
                        'payment_status' => $order->payment_status,
                        'payment_gateway' => $order->payment_gateway,
                        'status' => $order->status,
                        'created_at' => $order->created_at,
                        'description' => $order->description,
                    ],
                    'client_info' => [
                        'name' => $order->user->first_name . ' ' . $order->user->last_name,
                        'email' => $order->user->email,
                    ],
                    'freelancer_info' => [
                        'name' => $order->freelancer->first_name . ' ' . $order->freelancer->last_name,
                        'email' => $order->freelancer->email,
                    ],
                    'invoice_date' => now()->format('Y-m-d'),
                    'due_date' => now()->addDays(30)->format('Y-m-d'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'msg' => __('Failed to retrieve invoice information: ') . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
}
