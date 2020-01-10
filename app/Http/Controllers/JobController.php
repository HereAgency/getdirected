<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jobs = App\Job::all()->paginate(15);

        return response()->json(['response' => 'success', 'jobs' => $jobs]);
    }

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
            'job_type'      => 'required|integer',
            'shift_type'      => 'required|integer',
            'number_utes'      => 'required|integer',
            'number_trafic'      => 'required|integer',
            'address'      => 'required|string',
            'location'      => 'required|string',
            'setup_required'      => 'required|string',
            'notes'      => 'required|string',
            'date'      => 'required|date',
            'time_start'      => 'required|date',
            'status'      => 'required|integer',
            'tbc'      => 'required|boolean',
            'client'      => 'required|integer',
            'staffs.*'      => 'required|integer', // ARRAY DE STAFFS
            'permits.*'      => 'required|max:2048', //ARRAY DE ARCHIVOS
            'tgs.*'      => 'required|max:2048', //ARRAY DE ARCHIVOS
        ]);

        $job = new Job([
            'job_type'              => $request->job_type,
            'shift_type'              => $request->shift_type,
            'number_utes'              => $request->number_utes,
            'number_trafic'              => $request->number_trafic,
            'address'              => $request->address,
            'location'              => $request->location,
            'setup_required'              => $request->setup_required,
            'notes'              => $request->notes,
            'date'              => $request->date,
            'time_start'              => $request->time_start,
            'status'              => $request->status,
            'tbc'              => $request->tbc,
        ]);
        $job->save();
        
        return response()->json(['message' => 'Job creado existosamente!'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $job = App\Job::findOrFail($id);

        return response()->json(['response' => 'success', 'job' => $job]);
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
            'job_type'      => 'required|integer',
            'shift_type'      => 'required|integer',
            'number_utes'      => 'required|integer',
            'number_trafic'      => 'required|integer',
            'address'      => 'required|string',
            'location'      => 'required|string',
            'setup_required'      => 'required|string',
            'notes'      => 'required|string',
            'date'      => 'required|date',
            'time_start'      => 'required|date',
            'status'      => 'required|integer',
            'tbc'      => 'required|boolean',
            'client'      => 'required|integer',
            'staffs.*'      => 'required|integer', // ARRAY DE STAFFS
            'permits.*'      => 'required|max:2048', //ARRAY DE ARCHIVOS
            'tgs.*'      => 'required|max:2048', //ARRAY DE ARCHIVOS
        ]);
        
        $job = App\Job::findOrFail($id);

        if(isset($request->job_type))
            $job->job_type = $request->job_type;
        if(isset($request->shift_type))
            $job->shift_type = $request->shift_type;
        if(isset($request->number_utes))
            $job->number_utes = $request->number_utes;
        if(isset($request->number_trafic))
            $job->number_trafic = $request->number_trafic;
        if(isset($request->address))
            $job->address = $request->address;
        if(isset($request->location))
            $job->location = $request->location;
        if(isset($request->setup_required))
            $job->setup_required = $request->setup_required;
        if(isset($request->notes))
            $job->notes = $request->notes;
        if(isset($request->date))
            $job->date = $request->date;
        if(isset($request->time_start))
            $job->time_start = $request->time_start;
        if(isset($request->status))
            $job->status = $request->status;
        if(isset($request->tbc))
            $job->tbc = $request->tbc;

        $job->save();

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

        return response()->json(['message' => 'Job editado existosamente!'], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $job = App\Flight::find($id);
        $job->delete();

        return response()->json(['message' => 'Job borrado existosamente!'], 201);
    }
}
