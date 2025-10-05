<?php


namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return response()->json(Customer::with('pointSale')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string|unique:customers',
            'point_sale_id' => 'required|exists:point_sales,id',
            'activity' => 'nullable|string',
            'localisation' => 'nullable|string',
        ]);

        $customer = Customer::create($validated);

        return response()->json($customer, 201);
    }

    public function show(Customer $customer)
    {
        return response()->json($customer->load('purchases'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'phone' => 'sometimes|string|unique:customers,phone,' . $customer->id,
            'point_sale_id' => 'sometimes|exists:point_sales,id',
            'activity' => 'nullable|string',
            'localisation' => 'nullable|string',
        ]);

        $customer->update($validated);

        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
