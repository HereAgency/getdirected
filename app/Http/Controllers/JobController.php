<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jobs = \App\Job::where('jobs.id', '>', 0);

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

        if(Auth::user()->type == 1){
            $jobs = $jobs->join('job_staff', 'job_staff.job_id', '=', 'jobs.id')->whereHas('staffs', function($q) {
                $q->where('id_user', Auth::user()->id);
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
            'job_type'      => 'integer',
            'shift_type'      => 'integer',
            'number_utes'      => 'integer',
            'number_trafic'      => 'integer',
            'address'      => 'string',
            'location'      => 'string',
            'setup_required'      => 'string',
            'notes'      => 'string',
            'gtdc'      => 'string',
            'booking_name'      => 'string',
            'contact_number'      => 'string',
            'time_req_site'      => 'string',
            'date'      => 'date',
            'time_start'      => 'required|date',
            'status'      => 'integer',
            'tbc'      => 'boolean',
            'client'      => 'integer',
            'staffs.*'      => 'integer', // ARRAY DE STAFFS
            'permits.*'      => 'max:2048', //ARRAY DE ARCHIVOS
            'tgs.*'      => 'max:2048', //ARRAY DE ARCHIVOS
        ]);

        $args = [
            'time_start'              => $request->time_start,
        ];

        if(isset($request->job_type))
            $args['job_type'] = $request->job_type;
        if(isset($request->shift_type))
            $args['shift_type'] = $request->shift_type;
        if(isset($request->number_utes))
            $args['number_utes'] = $request->number_utes;
        if(isset($request->number_trafic))
            $args['number_trafic'] = $request->number_trafic;
        if(isset($request->address))
            $args['address'] = $request->address;
        if(isset($request->location))
            $args['location'] = $request->location;
        if(isset($request->setup_required))
            $args['setup_required'] = $request->setup_required;
        if(isset($request->notes))
            $args['notes'] = $request->notes;
        if(isset($request->gtdc))
            $args['gtdc'] = $request->gtdc;
        if(isset($request->booking_name))
            $args['booking_name'] = $request->booking_name;
        if(isset($request->contact_number))
            $args['contact_number'] = $request->contact_number;
        if(isset($request->time_req_site))
            $args['time_req_site'] = $request->time_req_site;
        if(isset($request->date))
            $args['date'] = $request->date;
        if(isset($request->status))
            $args['status'] = $request->status;
        if(isset($request->tbc))
            $args['tbc'] = $request->tbc;

        $job = new \App\Job($args);
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
        if(Auth::user()->type == 1){
            $job = \App\Job::where('jobs.id', $id);

            $job = $job->join('job_staff', 'job_staff.job_id', '=', 'jobs.id')->whereHas('staffs', function($q) {
                $q->where('id_user', Auth::user()->id);
            })->select('jobs.*', 'job_staff.confirm')->first();
        }else{
            $job = \App\Job::findOrFail($id);
        }

        $staffs = $job->staffs;
        $client = $job->client;
        $permits = $job->permits;
        $tgs = $job->tgs;

        return response()->json([
            'response' => 'success', 
            'job' => $job,
        ]);
    }

    /**
     * Genera pdf.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generate($id)
    {
        $job = \App\Job::findOrFail($id);

        $pdf = new Fpdi('L','mm',array(425,297));

        $pageCount = $pdf->setSourceFile('templates_app_risk.pdf');

        $pdf->SetFont('Arial','',12);

        $pageId = $pdf->importPage(1, PdfReader\PageBoundaries::MEDIA_BOX);

        $pdf->addPage('L');
        $pdf->useImportedPage($pageId, 0, 0);
        
        $pageId = $pdf->importPage(2, PdfReader\PageBoundaries::MEDIA_BOX);

        $pdf->addPage('L');
        $pdf->useImportedPage($pageId, 0, 0);

        $pdf->SetY(10);
        $pdf->SetX(365);
        $pdf->Write(4, $id); // ID DEL JOB

        $pdf->SetY(19);
        $pdf->SetX(281);
        $pdf->Write(4, $job->client->name);

        $pdf->SetY(19);
        $pdf->SetX(370);
        $pdf->Write(4, $job->date);

        $pdf->SetY(26.5);
        $pdf->SetX(154);
        $pdf->Write(4, $job->address);

        $pdf->SetY(26.5);
        $pdf->SetX(324);
        $pdf->Write(4, $job->location);

        // INICIO

        $pdf->SetY(52);
        $pdf->SetX(77);
        $pdf->Write(4, 'test');  // Vehicle Registration Number

        $pdf->SetY(59);
        $pdf->SetX(72);
        $pdf->Write(4, 'test');  // Vehicle Storage Location

        $pdf->SetY(65);
        $pdf->SetX(45);
        $pdf->Write(4, 'test');  // Date

        $pdf->SetY(65);
        $pdf->SetX(157);
        $pdf->Write(4, 'x');  // BP

        $pdf->SetY(65);
        $pdf->SetX(173.5);
        $pdf->Write(4, 'x');  // Caltex

        $pdf->SetY(65);
        $pdf->SetX(188.5);
        $pdf->Write(4, 'x');  // Shell

        // -----------------------

        $pdf->SetY(83.5);
        $pdf->SetX(55);
        $pdf->Write(4, 'x');  // Frames QTY

        $pdf->SetY(83.5);
        $pdf->SetX(100);
        $pdf->Write(4, 'x');  // Cones QTY

        $pdf->SetY(83.5);
        $pdf->SetX(160);
        $pdf->Write(4, 'x');  // Delineator w/base QTY

        $pdf->SetY(83.5);
        $pdf->SetX(184);
        $pdf->Write(4, 'x');  // Other

        $pdf->SetY(89.5);
        $pdf->SetX(51);
        $pdf->Write(4, 'x');  // Legs QTY

        $pdf->SetY(89.5);
        $pdf->SetX(105.5);
        $pdf->Write(4, 'x');  // Tiger Tails QTY

        $pdf->SetY(89.5);
        $pdf->SetX(151.5);
        $pdf->Write(4, 'x');  // Sand Bags QTY

        $pdf->SetY(89.5);
        $pdf->SetX(182);
        $pdf->Write(4, 'x');  // QTY

        // -----------------------

        $pdf->SetY(103);
        $pdf->SetX(49);
        $pdf->Write(4, 'x');  // Hard Hat 

        $pdf->SetX(65);
        $pdf->Write(4, 'x');  // Radio 

        $pdf->SetX(89);
        $pdf->Write(4, 'x');  // Safety Boots 

        $pdf->SetX(107);
        $pdf->Write(4, 'x');  // TC Shirt 

        $pdf->SetX(130);
        $pdf->Write(4, 'x');  // Long Pants 

        $pdf->SetX(151.5);
        $pdf->Write(4, 'x');  // Stop Baton 

        $pdf->SetX(166.5);
        $pdf->Write(4, 'x');  // Radio 

        $pdf->SetX(198.5);
        $pdf->Write(4, 'x');  // Wet Weather Gear

        $pdf->SetY(110);
        $pdf->SetX(58);
        $pdf->Write(4, 'x');  // Safety Glasses 

        $pdf->SetX(81);
        $pdf->Write(4, 'x');  // Night Wand 

        $pdf->SetX(103);
        $pdf->Write(4, 'x');  // White Card 

        $pdf->SetX(143.5);
        $pdf->Write(4, 'x');  // Traffic Controller License 

        $pdf->SetX(171);
        $pdf->Write(4, 'x');  // All TC checked

        // -----------------------

        $daily_vehicle_check = array(
            array(false,'test'), // 'headlights'
            array(false,'test'), // 'indicators_front'
            array(false,'test'), // 'indicators_back'
            array(false,'test'), // 'reverse_lights'
            array(false,'test'), // 'brake_lights'
            array(false,'test'), // 'reverse_beeper'
            array(false,'test'), // 'tyres_front'
            array(false,'test'), // 'tyres_rear'
            array(false,'test'), // 'tyre_spare'
            array(false,'test'), // 'jack'
            array(false,'test'), // 'oil'
            array(false,'test'), // 'water'
            array(false,'test'), // 'windscreen_wipers'
            array(false,'test'), // 'brakes'
            array(false,'test'), // 'fire_extinguisher'
            array(false,'test'), // 'first_aid_kit'
            array(false,'test'), // 'odometer_reading'
            array(false,''), // 'windscreen'
        );
        $initial_y = 122;

        foreach ($daily_vehicle_check as $key => $value) {

            $pdf->SetY($initial_y+(5.48*($key+($key==17?1.3:0))));
            if($value[0])
                $pdf->SetX(110.5);    
            else
                $pdf->SetX(123.5);
            $pdf->Write(4, 'x');  // FAIL
            $pdf->SetX(133);
            $pdf->Write(4, $value[1]);  // REASONS

            if ($key == 15) {
                $pdf->SetY(216);
                $pdf->SetX(107);
                $pdf->Write(4, '05');  
                $pdf->SetX(115);
                $pdf->Write(4, '05');  
                $pdf->SetX(124);
                $pdf->Write(4, '20');  // Next Service Due 
            }

        }
        
        $pageId = $pdf->importPage(3, PdfReader\PageBoundaries::MEDIA_BOX);

        $pdf->addPage('L');
        $pdf->useImportedPage($pageId, 0, 0);

        $pdf->SetY(10);
        $pdf->SetX(365);
        $pdf->Write(4, $id); // ID DEL JOB

        $pdf->SetY(19);
        $pdf->SetX(281);
        $pdf->Write(4, $job->client->name);

        $pdf->SetY(19);
        $pdf->SetX(370);
        $pdf->Write(4, $job->date);

        $pdf->SetY(26.5);
        $pdf->SetX(154);
        $pdf->Write(4, $job->address);

        $pdf->SetY(26.5);
        $pdf->SetX(324);
        $pdf->Write(4, $job->location);
        
        $pageId = $pdf->importPage(4, PdfReader\PageBoundaries::MEDIA_BOX);

        $pdf->addPage('L');
        $pdf->useImportedPage($pageId, 0, 0);

        $pdf->Output();

        return response()->json(['message' => 'Archivo generado existosamente!'], 201);
    }

    /**
     * Staff confirma job.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function confirm($id)
    {
        if(Auth::user()->type == 1){
            $job = \App\Job::findOrFail($id);

            $staff = \App\Staff::where('id_user', Auth::user()->id)->first();

            $job->staffs()->updateExistingPivot($staff->id, array('confirm' => 1), false);

            return response()->json(['message' => 'Job Confirmado!'], 201);
        }else{
           return response()->json(['message' => 'Accion unica para staffs'], 500);
        }
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
            'job_type'      => 'integer',
            'shift_type'      => 'integer',
            'number_utes'      => 'integer',
            'number_trafic'      => 'integer',
            'address'      => 'string',
            'location'      => 'string',
            'setup_required'      => 'string',
            'notes'      => 'string',
            'gtdc'      => 'string',
            'booking_name'      => 'string',
            'contact_number'      => 'string',
            'time_req_site'      => 'string',
            'date'      => 'date',
            'time_start'      => 'date',
            'status'      => 'integer',
            'tbc'      => 'boolean',
            'client'      => 'integer',
            'del_staffs.*'      => 'integer', // ARRAY DE STAFFS ELIMINADOS
            'del_per.*'      => 'integer', // ARRAY DE PERMITS ELIMINADOS
            'del_tgs.*'      => 'integer', // ARRAY DE TGS ELIMINADOS
            'staffs.*'      => 'integer', // ARRAY DE STAFFS
            'permits.*'      => 'max:2048', //ARRAY DE ARCHIVOS
            'tgs.*'      => 'max:2048', //ARRAY DE ARCHIVOS
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

        if(isset($request->staffs)){
            foreach ($request->staffs as $value) {
                $staff = \App\Staff::findOrFail($value);
                $job->staffs()->save($staff);
            }
        }

        if(isset($request->del_staffs)){
            foreach ($request->del_staffs as $id_staff) {
                $job->staffs()->detach($id_staff);
            }
        }

        if(isset($request->del_per)){
            foreach ($request->del_per as $id_per) {
                $job->permits()->detach($id_per);
            }
        }

        if(isset($request->del_tgs)){
            foreach ($request->del_tgs as $id_tgs) {
                $job->tgs()->detach($id_tgs);
            }
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
            'status'      => 'integer',
            'tbc'      => 'boolean'
        ]);
        
        $job = \App\Job::findOrFail($id);

        if(isset($request->status))
            $job->status = $request->status;
        if(isset($request->tbc))
            $job->tbc = $request->tbc;

        $job->save();

        return response()->json(['message' => 'Estado cambiado existosamente!'], 201);
    }

    /**
     * Asignar o eliminar staffs
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function asignar_staff(Request $request, $id)
    {
        $request->validate([
            'del_staffs.*'      => 'integer', // ARRAY DE STAFFS ELIMINADOS
            'staffs.*'      => 'integer', // ARRAY DE STAFFS
        ]);
        
        $job = \App\Job::findOrFail($id);

        if(isset($request->staffs)){
            foreach ($request->staffs as $id_staff) {
                $staff = \App\Staff::findOrFail($id_staff);
                $job->staffs()->save($staff);
            }
        }

        if(isset($request->del_staffs)){
            foreach ($request->del_staffs as $id_staff) {
                $job->staffs()->detach($id_staff);
            }
        }

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
        $job = \App\Job::findOrFail($id);
        $job->delete();

        return response()->json(['message' => 'Job borrado existosamente!'], 201);
    }
}
