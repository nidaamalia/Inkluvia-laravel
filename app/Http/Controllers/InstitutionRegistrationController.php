<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstitutionRegistrationRequest;
use App\Mail\InstitutionRegisteredAdmin;
use App\Mail\InstitutionRegisteredUser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;

class InstitutionRegistrationController extends Controller
{
    public function store(InstitutionRegistrationRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Mail::to('libaihua369@gmail.com')->send(new InstitutionRegisteredAdmin($data));
        Mail::to($data['email'])->send(new InstitutionRegisteredUser($data));

        return back()->with('status', 'Terima kasih! Pendaftaran lembaga Anda telah dikirim. Cek email untuk konfirmasi.');
    }
}


