<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalesTransaction;
use App\Models\Customer;
use App\Models\User;
use App\Models\Product;
use App\Models\TransactionDetail;
use Carbon\Carbon;

class SalesController extends Controller
{
    public function index()
    {
        $sales = SalesTransaction::with('customer', 'user', 'details.product')->get();
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::all();
        $users = User::all();

        $products = Product::where(function($query) {
            $query->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', Carbon::today());
        })->where('Quantity_in_Stock', '>', 0)->get();

        return view('sales.create', compact('customers', 'products', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'Customer_ID'              => 'required|exists:customers,Customer_ID',
            'User_ID'                  => 'required|exists:users,User_ID',
            'payment_method'           => 'required|in:Cash,GCash',
            'products.*.Product_ID'    => 'required|exists:products,Product_ID',
            'products.*.Quantity'      => 'required|numeric|min:1',
            'products.*.Kilo'          => 'required|numeric|min:0.1',
            'products.*.Price'         => 'required|numeric|min:0',
        ]);

        // Check for expired, out-of-stock, and stock limits
        foreach ($request->products as $item) {
            $product = Product::find($item['Product_ID']);
            if (!$product) {
                return back()->withInput()->withErrors('Invalid product selected.');
            }
            if (($product->expiry_date && $product->expiry_date < Carbon::today()) || $product->Quantity_in_Stock <= 0) {
                return back()->withInput()->withErrors('One of the selected products is expired or out of stock.');
            }
            if ($item['Quantity'] > $product->Quantity_in_Stock) {
                return back()->withInput()->withErrors([
                    'products' => "Cannot sell {$item['Quantity']} of {$product->Product_Name}. Only {$product->Quantity_in_Stock} available."
                ]);
            }
        }

        // Calculate total based on Kilo × Price
        $totalAmount = 0;
        foreach ($request->products as $product) {
            $totalAmount += $product['Kilo'] * $product['Price'];
        }

        // Create the sale
        $sale = SalesTransaction::create([
            'Customer_ID'    => $request->Customer_ID,
            'User_ID'        => $request->User_ID,
            'payment_method' => $request->payment_method,
            'total_amount'   => $totalAmount,
        ]);

        // Create transaction details and deduct stock
        foreach ($request->products as $productData) {
            TransactionDetail::create([
                'transaction_ID' => $sale->transaction_ID,
                'Product_ID'     => $productData['Product_ID'],
                'Quantity'       => $productData['Quantity'],
                'Kilo'           => $productData['Kilo'],
                'Price'          => $productData['Price'],
            ]);

            $product = Product::find($productData['Product_ID']);
            $product->Quantity_in_Stock -= $productData['Quantity'];
            $product->save();
        }

        return redirect()->route('sales.index')->with('success', 'Sale created successfully.');
    }

    public function edit(SalesTransaction $sale)
    {
        $customers = Customer::all();
        $users = User::all();

        $products = Product::where(function($query) {
            $query->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', Carbon::today());
        })->where('Quantity_in_Stock', '>', 0)->get();

        $sale->load('details.product');
        return view('sales.edit', compact('sale', 'customers', 'products', 'users'));
    }

    public function update(Request $request, SalesTransaction $sale)
    {
        $request->validate([
            'Customer_ID'              => 'required|exists:customers,Customer_ID',
            'User_ID'                  => 'required|exists:users,User_ID',
            'payment_method'           => 'required|in:Cash,GCash',
            'products.*.Product_ID'    => 'required|exists:products,Product_ID',
            'products.*.Quantity'      => 'required|numeric|min:1',
            'products.*.Kilo'          => 'required|numeric|min:0.1',
            'products.*.Price'         => 'required|numeric|min:0',
        ]);

        // Restore old quantities back to stock
        foreach ($sale->details as $oldDetail) {
            $product = Product::find($oldDetail->Product_ID);
            $product->Quantity_in_Stock += $oldDetail->Quantity;
            $product->save();
        }

        // Check stock limits with new quantities
        foreach ($request->products as $item) {
            $product = Product::find($item['Product_ID']);
            if (!$product) {
                return back()->withInput()->withErrors('Invalid product selected.');
            }
            if (($product->expiry_date && $product->expiry_date < Carbon::today()) || $product->Quantity_in_Stock <= 0) {
                return back()->withInput()->withErrors('One of the selected products is expired or out of stock.');
            }
            if ($item['Quantity'] > $product->Quantity_in_Stock) {
                return back()->withInput()->withErrors([
                    'products' => "Cannot sell {$item['Quantity']} of {$product->Product_Name}. Only {$product->Quantity_in_Stock} available."
                ]);
            }
        }

        // Calculate total based on Kilo × Price
        $totalAmount = 0;
        foreach ($request->products as $product) {
            $totalAmount += $product['Kilo'] * $product['Price'];
        }

        // Update the sale
        $sale->update([
            'Customer_ID'    => $request->Customer_ID,
            'User_ID'        => $request->User_ID,
            'payment_method' => $request->payment_method,
            'total_amount'   => $totalAmount,
        ]);

        // Delete old details and create new ones
        $sale->details()->delete();

        foreach ($request->products as $productData) {
            TransactionDetail::create([
                'transaction_ID' => $sale->transaction_ID,
                'Product_ID'     => $productData['Product_ID'],
                'Quantity'       => $productData['Quantity'],
                'Kilo'           => $productData['Kilo'],
                'Price'          => $productData['Price'],
            ]);

            $product = Product::find($productData['Product_ID']);
            $product->Quantity_in_Stock -= $productData['Quantity'];
            $product->save();
        }

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
    }

    public function destroy(SalesTransaction $sale)
    {
        foreach ($sale->details as $detail) {
            $product = Product::find($detail->Product_ID);
            $product->Quantity_in_Stock += $detail->Quantity;
            $product->save();
        }

        $sale->details()->delete();
        $sale->delete();

        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully.');
    }
}