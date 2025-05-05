@extends('emails.layouts.main')

@section('title', 'Bienvenido a ' . config('app.name'))

@section('content')
    <h1>¡Bienvenido a {{ config('app.name') }}!</h1>
    
    <p>¡Hola {{ $name }}!</p>
    
    <div class="highlight-box">
        <p>Tu cuenta ha sido creada exitosamente con el correo: <strong>{{ $email }}</strong></p>
    </div>

    <div class="highlight-box">
        <p>Gracias por registrarte en {{ config('app.name') }}.</p>
    </div>
    
    <p>Con {{ config('app.name') }} podrás:</p>
    <ul>
        <li>Registrar tus horas de trabajo de forma sencilla</li>
        <li>Gestionar diferentes tipos de jornadas laborales</li>
        <li>Acceder a reportes detallados de tus horas</li>
        <li>Ver tus salarios calculados automáticamente</li>
        <li>Exportar informes en PDF y CSV</li>
    </ul>
    
    <p>Ya puedes comenzar a usar nuestra plataforma para gestionar tus horas de trabajo de manera eficiente.</p>
    
    <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos.</p>
@endsection