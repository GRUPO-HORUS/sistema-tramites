@extends('layouts.app')

@section('title', 'Home')

@section('content')
	@if (Auth::guest() || !Auth::user()->registrado)
		<h1 class="title">Bienvenido</h1>
		<hr>
		<br>

		<div class="row">
			<div class="col-sm-12">
				<section id="simple-login">
				<div class="section-header">
					En este sitio puede encontrar y realizar trámites proveídos por la ANTSV. Antes de realizar un trámite, usted debe autenticarse en el sistema. Para iniciar un trámite, primero debe autenticarse.
				</div>
				<br>
				<div class="row">
							<div class="col-md-4 item">

								<div class="card text-center">
									<div class="card-body">
										<div class="media">
												<i class="icon-archivo"></i>
											<div class="media-body">
												<p class="card-text">
													Ingresar con Identidad Electrónica
												</p>
												<p>Acceda aquí para ver los trámites y posee Identidad Electrónica. Si tiene cédula de identidad y no posee identidad electrónica, <a href="https://www.paraguay.gov.py/crear-cuenta">acceda aquí</a>.</p>
											</div>
										</div>
									</div>
									
										<a href="/login/claveunica"
										   class="card-footer claveunica" >
													<i class="material-icons">person</i> Autenticarse
											<span class="float-right">&#8594;</span>
										</a>
						&nbsp;
								</div>

							</div>
							
							<div class="col-md-4 item">

								<div class="card text-center">
									<div class="card-body">
										<div class="media">
												<i class="icon-archivo"></i>
											<div class="media-body">
												<p class="card-text">
													Ingreso Extranjeros
												</p>
												<p>Utilice esta acción para acceder al sistema en el caso de que usted es extranjero o no posee cédula de identidad. Si no posee usuario, <a href="/tramites/iniciar/3">acceda aquí</a>.</p>
											</div>
										</div>
									</div>
									
										<a href="/login"
										   class="card-footer" >
													<i class="material-icons">person</i> Autenticarse
											<span class="float-right">&#8594;</span>
										</a>
						&nbsp;
								</div>

							</div>

							<div class="col-md-4 item">

								<div class="card text-center">
									<div class="card-body">
										<div class="media">
												<i class="icon-archivo"></i>
											<div class="media-body">
												<p class="card-text">
													Ingreso de Funcionarios y Municipalidades
												</p>
												<p>Acceso a los trámites ofrecidos a municipios de uso exclusivo por parte de las municipalidades. Si no posee usuario comuniquese con ANTSV</p>
											</div>
										</div>
									</div>
									
										<a href="/login"
										   class="card-footer" >
													<i class="material-icons">person</i> Autenticarse
											<span class="float-right">&#8594;</span>
										</a>
						&nbsp;
								</div>

							</div>
				</div>
			</section>
			</div>
		</div>
		
	@endif
    <h1 class="title">Listado de trámites disponibles</h1>
    {{--<div class="date"><i class="material-icons red">date_range</i></div>--}}
    <hr>
	@if (Auth::guest() || !Auth::user()->registrado)
		Para iniciar los trámites, debe de autenticarse antes.
		<br>
	@endif
    <br>

    <div class="row">
        <div class="col-sm-12">
            @include('home.tramites', ['login' => false])
        </div>
    </div>
@endsection
