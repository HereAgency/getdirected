<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $staffs = App\Staff::all()->paginate(15);

        return response()->json(['response' => 'success', 'staffs' => $staffs]);    }

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
            'relationship'      => 'required|integer',
            'name'      => 'required|string',
            'address'       => 'required|string',
            'mobile'        => 'required|string',
            'email'     => 'required|string',
            'vehicle_registration'      => 'required|string',
            'contact'       => 'required|string',
            'phone'     => 'required|string',
            'start_date'        => 'required|date',
            'vehicle'       => 'required|boolean',
            'tcas.*'      => 'required|max:2048', //ARRAY DE ARCHIVOS
            'tfns.*'      => 'required|max:2048', //ARRAY DE ARCHIVOS
        ]);

        $staff = new Job([
            'relationship'  => $request->relationship,
            'name'  => $request->name,
            'address'  => $request->address,
            'mobile'  => $request->mobile,
            'email'  => $request->email,
            'vehicle_registration'  => $request->vehicle_registration,
            'contact'  => $request->contact,
            'phone'  => $request->phone,
            'start_date'  => $request->start_date,
            'vehicle'  => $request->vehicle,
        ]);
        $staff->save();

        if($request->hasFile('tcas')){ 
            
            $files = $request->file('tcas'); 

            foreach ($files as $file) {

                $fileName = str_random(20).'.'.$file->getClientOriginalExtension();

                $path = str_replace('\\', '/', base_path() . '/public/tcas/');

                $file->move($path, $fileName);

                $archivo = new Archivo();

                // $archivo->path = $path.$fileName;
                $archivo->path = '/tcas/'.$fileName;

                $archivo->save();

                $staff->tcas()->save($archivo); // OR ASOCIATE

            }

        }

        if($request->hasFile('tfns')){ 
            
            $files = $request->file('tfns'); 

            foreach ($files as $file) {

                $fileName = str_random(20).'.'.$file->getClientOriginalExtension();

                $path = str_replace('\\', '/', base_path() . '/public/tfns/');

                $file->move($path, $fileName);

                $archivo = new Archivo();

                // $archivo->path = $path.$fileName;
                $archivo->path = '/tfns/'.$fileName;

                $archivo->save();

                $staff->tfns()->save($archivo); // OR ASOCIATE

            }

        }

        $staff->save();
        
        return response()->json(['message' => 'Staff creado existosamente!'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $staff = App\Staff::findOrFail($id);

        return response()->json(['response' => 'success', 'job' => $staff]);
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
            'relationship'      => 'required|integer',
            'name'      => 'required|string',
            'address'       => 'required|string',
            'mobile'        => 'required|string',
            'email'     => 'required|string',
            'vehicle_registration'      => 'required|string',
            'contact'       => 'required|string',
            'phone'     => 'required|string',
            'start_date'        => 'required|date',
            'vehicle'       => 'required|boolean',
            'tcas.*'      => 'required|max:2048', //ARRAY DE ARCHIVOS
            'tfns.*'      => 'required|max:2048', //ARRAY DE ARCHIVOS
        ]);
        
        $staff = App\Staff::findOrFail($id);

        if(isset($request->job_type))
            $staff->job_type = $request->job_type;

        $staff->save();

        /*if($request->hasFile('documento')){
            if (isset($user->documento->path)){
                unlink(base_path().'/public'.$user->documento->path);
                $user->documento()->delete();
            }
            
            $image = $request->file('documento'); 

            $imageName = $user->id.'.'.$image->getClientOriginalExtension();

            $path = str_replace('\\', '/', base_path() . '/public/documents/');

            $image->move($path, $imageName);

            $archivo = new Archivo();

            // $archivo->path = $path.$imageName;
            $archivo->path = '/documents/'.$imageName;

            $archivo->save();

            $user->documento()->associate($archivo);
        }*/

        return response()->json(['message' => 'Staff editado existosamente!'], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $staff = App\Staff::find($id);
        $staff->delete();

        return response()->json(['message' => 'Staff borrado existosamente!'], 201);
    }
}
