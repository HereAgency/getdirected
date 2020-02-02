<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jobs = \App\Job::where('id', '>', 0);

        if(isset($_GET['start']) && !isset($_GET['end'])){
            $from = date($_GET['start']);
            $jobs = $jobs->where('date', $from);
        }elseif(isset($_GET['start']) && isset($_GET['end'])){
            $from = date($_GET['start']);
            $to = date($_GET['end']);
            $jobs = $jobs->whereBetween('date', [$from, $to]);
        }

        if(isset($_GET['status'])){
            $jobs = $jobs->where('status',$_GET['status']);
        }

        if(Auth::user()->type == 1){
            $jobs = $jobs->whereHas('staffs', function($q) {
                $q->where('id_user', Auth::user()->id);
            });
        }

        if(isset($_GET['staff'])){
            $jobs = $jobs->whereHas('staffs', function($q) {
                $q->where('staff_id', $_GET['staff']);
            });
        }

        $jobs = $jobs->paginate(15);

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

        $job = new \App\Job([
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

        if(isset($request->staffs)){
            foreach ($request->staffs as $value) {
                $staff = \App\Staff::findOrFail($value);
                $job->staffs()->save($staff);
            }
        }
        
        if(isset($request->client)){
            $client = \App\Client::findOrFail($request->client);
            $job->client()->associate($client);
        }

        if($request->hasFile('permits')){ 
            
            $files = $request->file('permits'); 

            foreach ($files as $file) {

                $fileName = str_random(20).'.'.$file->getClientOriginalExtension();

                $path = str_replace('\\', '/', base_path() . '/public/permits/');

                $file->move($path, $fileName);

                $archivo = new Archivo();

                // $archivo->path = $path.$fileName;
                $archivo->path = '/permits/'.$fileName;

                $archivo->save();

                $job->permits()->save($archivo); // OR ASOCIATE

            }

        }

        if($request->hasFile('tgs')){ 
            
            $files = $request->file('tgs'); 

            foreach ($files as $file) {

                $fileName = str_random(20).'.'.$file->getClientOriginalExtension();

                $path = str_replace('\\', '/', base_path() . '/public/tgs/');

                $file->move($path, $fileName);

                $archivo = new Archivo();

                // $archivo->path = $path.$fileName;
                $archivo->path = '/tgs/'.$fileName;

                $archivo->save();

                $job->tgs()->save($archivo); // OR ASOCIATE

            }

        }

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
        $job = \App\Job::findOrFail($id);

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
            'del_per.*'      => 'required|integer', // ARRAY DE PERMITS ELIMINADOS
            'del_tgs.*'      => 'required|integer', // ARRAY DE TGS ELIMINADOS
            'staffs.*'      => 'required|integer', // ARRAY DE STAFFS
            'permits.*'      => 'required|max:2048', //ARRAY DE ARCHIVOS
            'tgs.*'      => 'required|max:2048', //ARRAY DE ARCHIVOS
        ]);
        
        $job = \App\Job::findOrFail($id);

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

        // ADAPTAR ESTA PARTE A UPDATE

        foreach ($request->staffs as $value) { // DIFERENCIA ENTRE ACTUAL Y NUEVO
            $pedido->staffs()->attach($value);
        }

        if($request->hasFile('permits')){ 
            
            $files = $request->file('permits'); 

            foreach ($files as $file) {

                $fileName = str_random(20).'.'.$file->getClientOriginalExtension();

                $path = str_replace('\\', '/', base_path() . '/public/permits/');

                $file->move($path, $fileName);

                $archivo = new Archivo();

                // $archivo->path = $path.$fileName;
                $archivo->path = '/permits/'.$fileName;

                $archivo->save();

                $job->permits()->save($archivo); // OR ASOCIATE

            }

        }

        if($request->hasFile('tgs')){ 
            
            $files = $request->file('tgs'); 

            foreach ($files as $file) {

                $fileName = str_random(20).'.'.$file->getClientOriginalExtension();

                $path = str_replace('\\', '/', base_path() . '/public/tgs/');

                $file->move($path, $fileName);

                $archivo = new Archivo();

                // $archivo->path = $path.$fileName;
                $archivo->path = '/tgs/'.$fileName;

                $archivo->save();

                $job->tgs()->save($archivo); // OR ASOCIATE

            }

        }

        $job->save();

        return response()->json(['message' => 'Job editado existosamente!'], 201);
    }

    /**
     * Cambiar solo el estado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function estado(Request $request, $id)
    {
        $request->validate([
            'status'      => 'required|integer'
        ]);
        
        $job = \App\Job::findOrFail($id);

        if(isset($request->status))
            $job->status = $request->status;

        $job->save();

        return response()->json(['message' => 'Estado cambiado existosamente!'], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $job = \App\Job::find($id);
        $job->delete();

        return response()->json(['message' => 'Job borrado existosamente!'], 201);
    }
}
