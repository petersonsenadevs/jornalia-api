<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hour Worked Report</title>
    <style>
        @page {
            size: A4;
            margin: 2cm;
        }
        body {
            font-family: Arial, sans-serif;
            color: #777;
            margin: 0;
            padding: 0;
            background: white;
        }
        .page {
            width: 21cm;
            min-height: 29.7cm;
            padding: 0;
            margin: 0 auto;
            background: white;
        }
        .page-break {
            page-break-after: always;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
            padding-top: 20px;
        }
        .logo {
            width: 250px;
            height: auto;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            color: #008080;
            text-align: center;
            margin-bottom: 10px;
        }
        .subtitle {
            font-size: 16px;
            color: #000;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: auto;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        thead {
            display: table-header-group;
        }
        tfoot {
            display: table-footer-group;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-size: 14px;
        }
        th {
            background-color: #008080;
            color: white;
            font-weight: bold;
        }
        .data-cell {
            background-color: #e0f2f1;
        }
        .total-row {
            font-weight: bold;
            background-color: #b2dfdb;
        }
        .footer {
            font-size: 12px;
            color: #777;
            text-align: right;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            position: relative;
            bottom: 0;
        }
        .no-data {
            color: #999;
            font-style: italic;
        }
        .summary-section {
            page-break-inside: avoid;
        }
        .confirmation-message {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #008080;
            border-radius: 0 5px 5px 0;
        }
        .confirmation-actions {
            margin-top: 15px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 8px 20px;
            margin: 0 10px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .btn-primary {
            background-color: #008080;
            color: white;
            border: 1px solid #006666;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border: 1px solid #5a6268;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Logo -->
        <div class="logo-container">
            <img src="{{ $logoPath }}" alt="Logo" class="logo">
        </div>

        <!-- Título -->
        <h1 class="title">Reporte de Horas Trabajadas</h1>
        <p class="subtitle">Empleado: {{ $employee->name }}</p>
        <p class="subtitle">Período: {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</p>

        <!-- Tabla de datos -->
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Horas Normales</th>
                    <th>Horas Extras</th>
                    <th>Horas Nocturnas</th>
                    <th>Horas Festivas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hourWorkedData as $data)
                    <tr>
                        <td class="data-cell">{{ \Carbon\Carbon::parse($data['date'])->format('d/m/Y') }}</td>
                        <td class="data-cell">{{ $data['normal_hours'] ? $data['normal_hours']['hours'] . 'h ' . $data['normal_hours']['minutes'] . 'm' : '-' }}</td>
                        <td class="data-cell">{{ $data['overtime_hours'] ? $data['overtime_hours']['hours'] . 'h ' . $data['overtime_hours']['minutes'] . 'm' : '-' }}</td>
                        <td class="data-cell">{{ $data['night_hours'] ? $data['night_hours']['hours'] . 'h ' . $data['night_hours']['minutes'] . 'm' : '-' }}</td>
                        <td class="data-cell">{{ $data['holiday_hours'] ? $data['holiday_hours']['hours'] . 'h ' . $data['holiday_hours']['minutes'] . 'm' : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="no-data">No hay datos registrados para este período</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Resumen (siempre al final, evitando cortes) -->
        <div class="summary-section">
            <table>
                <tbody>
                    <tr class="total-row">
                        <td colspan="5" style="text-align: left;">Resumen de Horas y Salarios:</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="data-cell" style="text-align: left;">Total de Horas normales: {{ $totalNormalHours['hours'] }}h {{ $totalNormalHours['minutes'] }}m</td>
                        <td colspan="2" class="data-cell" style="text-align: left;">Precio/hora: {{ number_format($employee->normal_hourly_rate, 2) }}€</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="data-cell" style="text-align: left;">Total de Horas extras: {{ $totalOvertimeHours['hours'] }}h {{ $totalOvertimeHours['minutes'] }}m</td>
                        <td colspan="2" class="data-cell" style="text-align: left;">Precio/hora: {{ number_format($employee->overtime_hourly_rate, 2) }}€</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="data-cell" style="text-align: left;">Total de Horas festivas: {{ $totalHolidayHours['hours'] }}h {{ $totalHolidayHours['minutes'] }}m</td>
                        <td colspan="2" class="data-cell" style="text-align: left;">Precio/hora: {{ number_format($employee->holiday_hourly_rate, 2) }}€</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="data-cell" style="text-align: left;">Total de Horas nocturnas: {{ $totalNightHours['hours'] }}h {{ $totalNightHours['minutes'] }}m</td>
                        <td colspan="2" class="data-cell" style="text-align: left;">Precio/hora: {{ number_format($employee->night_hourly_rate, 2) }}€</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="data-cell" style="text-align: left;"><strong>Total de Horas trabajadas: {{ $totalHours['hours'] }}h {{ $totalHours['minutes'] }}m</strong></td>
                        <td colspan="2" class="data-cell" style="text-align: left;"><strong>Salario Total: {{ number_format($salary['total_gross_salary'], 2) }}€</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

 

        <section>
            <p style="text-align: center; font-size: 14px; color: #777; margin-top: 20px;">
                Este es un reporte meramente informativo, no tiene ningún valor legal.
            </p>
        </section>

        <!-- Pie de página -->
        <div class="footer">
            Generado el: {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
</body>
</html>
