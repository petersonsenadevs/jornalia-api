<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        /* Estilos mínimos para Excel */
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th {
            background-color: #D9D9D9;
            font-weight: bold;
            text-align: center;
        }
        td {
            text-align: center;
        }
        .totals {
            font-weight: bold;
            background-color: #F2F2F2;
        }
        .salary-info {
            background-color: #E6E6E6;
        }
    </style>
</head>
<body>
    <!-- Cabecera de identificación -->
    <table>
        <tr>
            <td colspan="5" style="text-align: left; font-weight: bold;">Empleado: {{ $employee->name }}</td>
        </tr>
        <tr>
            <td colspan="5" style="text-align: left; font-weight: bold;">Período: {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}</td>
        </tr>
        <tr><td colspan="5"></td></tr>
    </table>

    <!-- Datos principales -->
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
                    <td>{{ \Carbon\Carbon::parse($data['date'])->format('d/m/Y') }}</td>
                    <td>{{ $data['normal_hours'] ? $data['normal_hours']['hours'] . ':' . str_pad($data['normal_hours']['minutes'], 2, '0', STR_PAD_LEFT) : '-' }}</td>
                    <td>{{ $data['overtime_hours'] ? $data['overtime_hours']['hours'] . ':' . str_pad($data['overtime_hours']['minutes'], 2, '0', STR_PAD_LEFT) : '-' }}</td>
                    <td>{{ $data['night_hours'] ? $data['night_hours']['hours'] . ':' . str_pad($data['night_hours']['minutes'], 2, '0', STR_PAD_LEFT) : '-' }}</td>
                    <td>{{ $data['holiday_hours'] ? $data['holiday_hours']['hours'] . ':' . str_pad($data['holiday_hours']['minutes'], 2, '0', STR_PAD_LEFT) : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Sin registros</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Línea en blanco -->
    <table>
        <tr><td colspan="5"></td></tr>
    </table>

    <!-- Resumen de horas -->
    <table>
        <tr class="totals">
            <td colspan="5" style="text-align: left;">RESUMEN DE HORAS Y SALARIOS</td>
        </tr>
        <tr class="salary-info">
            <td colspan="3" style="text-align: left;">Total Horas normales: {{ $totalNormalHours['hours'] }}:{{ str_pad($totalNormalHours['minutes'], 2, '0', STR_PAD_LEFT) }}</td>
            <td colspan="2" style="text-align: left;">Tarifa: {{ number_format($employee->normal_hourly_rate, 2) }}€/h</td>
        </tr>
        <tr class="salary-info">
            <td colspan="3" style="text-align: left;">Total Horas extras: {{ $totalOvertimeHours['hours'] }}:{{ str_pad($totalOvertimeHours['minutes'], 2, '0', STR_PAD_LEFT) }}</td>
            <td colspan="2" style="text-align: left;">Tarifa: {{ number_format($employee->overtime_hourly_rate, 2) }}€/h</td>
        </tr>
        <tr class="salary-info">
            <td colspan="3" style="text-align: left;">Total Horas festivas: {{ $totalHolidayHours['hours'] }}:{{ str_pad($totalHolidayHours['minutes'], 2, '0', STR_PAD_LEFT) }}</td>
            <td colspan="2" style="text-align: left;">Tarifa: {{ number_format($employee->holiday_hourly_rate, 2) }}€/h</td>
        </tr>
        <tr class="salary-info">
            <td colspan="3" style="text-align: left;">Total Horas nocturnas: {{ $totalNightHours['hours'] }}:{{ str_pad($totalNightHours['minutes'], 2, '0', STR_PAD_LEFT) }}</td>
            <td colspan="2" style="text-align: left;">Tarifa: {{ number_format($employee->night_hourly_rate, 2) }}€/h</td>
        </tr>
        <tr class="totals">
            <td colspan="3" style="text-align: left;">TOTAL HORAS: {{ $totalHours['hours'] }}:{{ str_pad($totalHours['minutes'], 2, '0', STR_PAD_LEFT) }}</td>
            <td colspan="2" style="text-align: left;">SALARIO TOTAL: {{ number_format($salary['total_gross_salary'], 2) }}€</td>
        </tr>
    </table>

    <!-- Nota al pie -->
    <table>
        <tr><td colspan="5"></td></tr>
        <tr>
            <td colspan="5" style="text-align: right; font-size: 11px;">Generado: {{ now()->format('d/m/Y H:i:s') }}</td>
        </tr>
    </table>
</body>
</html>