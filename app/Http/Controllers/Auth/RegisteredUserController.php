<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'rg' => ['required', 'regex:/^[A-Z]{2}\d{8}$/'],
                'cpf' => [
                    'required',
                    Rule::unique(table: 'users'),
                    'cpf'
                ],
                'birthdate' => [
                    'required',
                    'date',
                    'before_or_equal:' . now()->subYears(18)->format('Y-m-d')
                ],
                'zip_code' => [
                    'required',
                    'regex:/^\d{5}-\d{3}$|^\d{8}$/',
                ],
                'address' => [
                    'required',
                    'min:3',
                    'max:255',
                ],
                'neighborhood' => [
                    'required',
                    'min:3',
                    'max:255',
                ],
                'number' => [
                    'required',
                    'numeric'
                ],
                'complement' => [
                    'nullable',
                    'min:2',
                    'max:255',
                ],
                'city' => [
                    'required',
                    'min:3',
                    'max:255',
                ],
                'state' => [
                    'required',
                    'min:2',
                    'max:255',
                ],
                'country' => [
                    'required',
                    'min:3',
                    'max:255',
                ],
                'status' => [
                    'required',
                    Rule::in(['active', 'inactive', 'pending'])
                ],
                'terms' => [
                    'accepted'
                ],
            ],
            [
                'birthdate.before_or_equal' => 'Você precisa ter pelo menos 18 anos para se registrar.',
            ]
        )
        ;


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rg' => $request->rg,
            'cpf' => $request->cpf,
            'birthdate' => $request->birthdate,
            'zip_code' => $request->zip_code,
            'address' => $request->address,
            'neighborhood' => $request->neighborhood,
            'number' => $request->number,
            'complement' => $request->complement,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'status' => $request->status,
            'term' => true
        ]);

        event(new Registered($user));

        Auth::login($user);

        return to_route('dashboard');
    }
}
