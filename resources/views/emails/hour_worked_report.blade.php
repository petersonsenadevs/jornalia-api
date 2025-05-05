@extends('emails.layouts.main')

@section('title', 'Reporte de Horas Trabajadas - ' . $month . '/' . $year)

@section('content')
    <h1>Reporte de Horas Trabajadas</h1>
    
    <div class="highlight-box">
        <h3>Período: {{ $month }}/{{ $year }}</h3>
    </div>
    
    <p>Estimado usuario,</p>
    
    <p>Adjunto encontrará el reporte detallado de sus horas trabajadas correspondiente al período indicado.</p>
    
    <div class="highlight-box">
        <p><strong>El archivo PDF o Excel contiene el desglose completo de:</strong></p>
        <ul style="margin: 0; padding-left: 20px;">
            <li>Horas normales trabajadas</li>
            <li>Horas extras</li>
            <li>Horas nocturnas</li>
            <li>Horas en días festivos</li>
            <li>Cálculo de salario correspondiente</li>
        </ul>
    </div>
    
    <p>Por favor, revise el documento adjunto y no dude en contactarnos si tiene alguna pregunta sobre el reporte.</p>
    
    <p style="margin-top: 20px;">¡Gracias por usar {{ config('app.name') }}!</p>
@endsection
