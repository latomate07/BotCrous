@extends('layouts.main')
@section('content')

{{-- @dd($records) --}}
<div class="flex {{ $records->isEmpty() ? 'h-screen' : '' }} items-center justify-center bg-indigo-50 px-4 flex-col">
    <h1 {{ $records->isEmpty() ? 'style=position:fixed' : '' }} id="welcomeToTheWebsite">Découvrez en avant-première les derniers logements publiés sur le site du crous</h1>
    @forelse ($records as $record)
    <div class="mt-5 max-w-sm overflow-hidden rounded-xl bg-white shadow-md duration-200 hover:scale-105 hover:shadow-xl">
        <img src="{{ $record->fields->photo ?? ''}}" alt="plant" class="h-72 w-full" />
        <div class="p-5">
            <h4>{{ $record->fields->title }}</h4>
            <p class="text-medium mb-5 text-gray-700" style="font-weight: bold; text-decoration:underline">Zone : {{
                $record->fields->zone }}</p>
            <p class="text-medium mb-5 text-gray-700">
                {{
                    strlen($record->fields->infos) > 50 ?
                    substr($record->fields->infos, 0, 200)."..." :
                    $record->fields->infos
                }}
            </p>
            <a style="display:block; text-align:center" href="{{ $record->fields->bookingurl ?? '' }}"
                class="w-full rounded-md bg-indigo-600  py-2 text-indigo-100 hover:bg-indigo-500 hover:shadow-md duration-75" target="_blank">Réserver</a>
        </div>
    </div>
    @empty
    <p>Oups ! Aucun logement trouvé.</p>
    @endforelse

    <div id="chatWithRobot" style="position:fixed; left:0; top:0; margin-left:50px" class="flex h-screen items-center">
        <div class="group relative mx-auto w-96 overflow-hidden rounded-[16px] bg-gray-300 p-[1px] transition-all duration-300 ease-in-out hover:bg-gradient-to-r hover:from-indigo-500 hover:via-purple-500 hover:to-pink-500">
          <div class="group-hover:animate-spin-slow invisible absolute -top-40 -bottom-40 left-10 right-10 bg-gradient-to-r from-transparent via-white/90 to-transparent group-hover:visible"></div>
          <div class="relative rounded-[15px] bg-white p-6">
            <form id="launchBot" class="space-y-4" method="POST" action="{{ route('launch.bot') }}">
              @csrf
              <div id="stepOne">
                <img src="{{ asset('storage/svgs/ux-fast-light.svg') }}" alt=""/>
                <p class="text-lg font-semibold text-slate-800">TahirBot à votre service</p>
                <p class="font-md text-slate-500">Laissez votre mail ci-dessous, et nous nous chargerons d'envoyer automatiquement des mails de demande de réservation à chaque nouveau logement publié.</p>
                <input type="email" id="email" placeholder="E-mail" name="email" class="w-full bg-gray-100 rounded p-2 mr-4 border focus:outline-none focus:border-blue-500">
                <a style="display: block; width: 40%;" id="startBot" class="cursor-pointer btn bg-gray-200 hover:bg-gray-300 px-4 py-2 font-medium rounded mt-3">
                  Lancer le bot
                </a>
              </div>
              <div id="stepTwo" style="display: none">
                <img src="{{ asset('storage/svgs/bot-svg.svg') }}" alt="" style="height: 185px; margin: 0 auto;"/>
                <p class="text-lg font-semibold text-slate-800">Hey let's go...</p>
                <p class="font-md text-slate-500">Connectons ton compte gmail pour que je puisse réserver automatiquement un logement à ta place.</p>
                <div class="flex space-x-4">
                    <a style="display: block" id="refuseConditions" class="cursor-pointer btn bg-gray-200 hover:bg-gray-300 px-4 py-2 font-medium rounded mt-3">
                        Je refuse
                    </a>
                    <a style="display: block" id="acceptConditions" class="cursor-pointer btn bg-gray-200 hover:bg-gray-300 px-4 py-2 font-medium rounded mt-3">
                        J'accepte
                    </a>
                </div>
              </div>
              <div id="stepThree" style="display: none">
                <img src="{{ asset('storage/svgs/bot-svg.svg') }}" alt="" style="height: 185px; margin: 0 auto;"/>
                <p class="text-lg font-semibold text-slate-800">Automatisons tout ça...</p>
                <p class="font-md text-slate-500">J'aurai besoin de ton prénom afin que les mails que j'enverrai ne se retrouve pas dans les spams</p>
                <input value="{{ old('name') }}" type="text" id="name" placeholder="Prénom" name="name" class="w-full bg-gray-100 rounded p-2 mr-4 border focus:outline-none focus:border-blue-500">
                <div class="flex space-x-4">
                    <a style="display: block" id="returnToStepTwo" class="cursor-pointer btn bg-gray-200 hover:bg-gray-300 px-4 py-2 font-medium rounded mt-3">
                        Retour
                    </a>
                    <a style="display: block" id="continueToStepFourth" class="cursor-pointer btn bg-gray-200 hover:bg-gray-300 px-4 py-2 font-medium rounded mt-3">
                        Suivant
                    </a>
                </div>
              </div>
              <div id="stepFourth" style="display: none">
                <img src="{{ asset('storage/svgs/bot-svg.svg') }}" alt="" style="height: 185px; margin: 0 auto;"/>
                <p class="text-lg font-semibold text-slate-800">On y est presque...</p>
                <p class="font-md text-slate-500">Ajoute ton mot de passe pour me donner l'autorisation d'envoyer des mails à ta place</p>
                <input type="password" id="password" placeholder="Mot de passe du compte" name="password" class="w-full bg-gray-100 rounded p-2 mr-4 border focus:outline-none focus:border-blue-500">
                <div class="flex space-x-4">
                    <a style="display: block" id="returnToStepThree" class="cursor-pointer btn bg-gray-200 hover:bg-gray-300 px-4 py-2 font-medium rounded mt-3">
                        Retour
                    </a>
                    <button id="endStep" type="submit" class="cursor-pointer btn bg-gray-200 hover:bg-gray-300 px-4 py-2 font-medium rounded mt-3">
                        Terminer
                    </button>
                </div>
              </div>
              <div id="robotLoading" style="display: none">
                <img src="{{ asset('storage/svgs/bot-svg.svg') }}" alt="" style="height: 230px; margin: 0 auto;"/>
                <p id="robotStatus" class="text-lg font-semibold text-slate-800 text-center mt-5"></p>
              </div>
              <div id="robotWorking" style="display: none">
                <img src="{{ asset('storage/svgs/bot-svg.svg') }}" alt="" style="height: 230px; margin: 0 auto;"/>
                <p id="afterWorkingEnd" class="text-lg font-semibold text-slate-800 text-center mt-5"></p>
              </div>
            </form>
          </div>
        </div>
    </div>

    <div class="max-w-2xl mx-auto">

        <footer style="margin: 30px auto" class="p-4 bg-white rounded-lg shadow md:flex md:items-center md:justify-between md:p-6 dark:bg-gray-800">
            <span class="text-sm text-gray-500 sm:text-center dark:text-gray-400">© 2022 <a href="{{ url("/") }}" class="hover:underline">BotCrous</a>. Réalisé avec ❤️ par <a class="underline" href="https://www.linkedin.com/in/tahirou-magagi-b07a49245/">Tahirou</a>.
            </span>
        </footer>
    </div>
</div>

@endsection