<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = \App\Client::where('id', '>', 0)->paginate(15);

        return response()->json(['response' => 'success', 'Clients' => $clients]);    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string',
            'address'      => 'required|string',
            'mobile'      => 'required|string',
            'email'      => 'required|string',
            'phone'      => 'required|string',
            'since_date'      => 'required|date',
            'contact_name'      => 'required|string',
            'status'      => 'required|integer',
        ]);

        $client = new Job([
            'name'  => $request->name,
            'address'  => $request->address,
            'mobile'  => $request->mobile,
            'email'  => $request->email,
            'phone'  => $request->phone,
            'since_date'  => $request->since_date,
            'contact_name'  => $request->contact_name,
            'status'  => $request->status,
        ]);
        $client->save();
        
        return response()->json(['message' => 'Client creado existosamente!'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $client = \App\Client::findOrFail($id);

        return response()->json(['response' => 'success', 'job' => $client]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'      => 'string',
            'address'      => 'string',
            'mobile'      => 'string',
            'email'      => 'string',
            'phone'      => 'string',
            'since_date'      => 'date',
            'contact_name'      => 'string',
            'status'      => 'integer',
        ]);
        
        $client = \App\Client::findOrFail($id);

        if(isset($request->name))
            $client->name = $request->name;
        if(isset($request->address))
            $client->address = $request->address;
        if(isset($request->mobile))
            $client->mobile = $request->mobile;
        if(isset($request->email))
            $client->email = $request->email;
        if(isset($request->phone))
            $client->phone = $request->phone;
        if(isset($request->since_date))
            $client->since_date = $request->since_date;
        if(isset($request->contact_name))
            $client->contact_name = $request->contact_name;
        if(isset($request->status))
            $client->status = $request->status;

        $client->save();

        return response()->json(['message' => 'Client editado existosamente!'], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client = \App\Client::find($id);
        $client->delete();

        return response()->json(['message' => 'Client borrado existosamente!'], 201);
    }
}
