<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hour Worked Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #777;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
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
        .footer {
            font-size: 12px;
            color: #777;
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <!-- Logo -->
    <div class="logo-container">
        <img src="{{ $logoPath }}" alt="Logo" class="logo">
    </div>

    <!-- Título -->
    <h1 class="title">Reporte de Horas Trabajadas</h1>
    <p class="subtitle">Empleado: {{ $employee->name }}</p>
    <p class="subtitle">Mes: {{ $month }}, Año: {{ $year }}</p>

    <!-- Tabla de datos -->
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Horas Normales</th>
                <th>Horas Extras</th>
                <th>Horas Festivas</th>
                
            </tr>
        </thead>
        <tbody>
            @foreach($hourWorkedData as $data)
                <tr>
                    <td class="data-cell">{{ $data['date'] }}</td>
                    <td class="data-cell">{{ $data['normal_hours'] }}</td>
                    <td class="data-cell">{{ $data['overtime_hours'] }}</td>
                    <td class="data-cell">{{ $data['holiday_hours'] }}</td>
                </tr>
            @endforeach
            <tr>
                <td class="data-cell" colspan="4">Total de Horas normales trabajadas: {{ $totalNormalHours }}</td>
            </tr>
            <tr>
                <td class="data-cell" colspan="4">Total de Horas extras trabajadas: {{ $totalOvertimeHours }}</td>
            </tr>
            <tr>
                <td class="data-cell" colspan="4">Total de Horas festivas trabajadas: {{ $totalHolidayHours }}</td>
            </tr>
            <tr></tr>
                <td class="data-cell" colspan="4">Total de Horas trabajadas: {{ $totalHours }}</td>
            </tr>
        </tbody>
    </table>
    
    <section>
        <p style="text-align: center; font-size: 14px; color: #777;">Este es un reporte meramente informativo, no tiene ningún valor legal.</p>
    </section>

    <!-- Pie de página -->
    <div class="footer">
        Generado el: {{ now()->format('Y-m-d H:i:s') }}
    </div>
</body>
</html>
