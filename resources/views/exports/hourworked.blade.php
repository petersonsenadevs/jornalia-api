<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Hour Worked Report</title>
</head>
<body>

     

    <!-- Agregar un texto -->
    <h1>Exportación de Horas Trabajadas del Empleado {{ $employee->name }} mes: {{ $month }}, año: {{ $year }}</h1>
   

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
                    <td>{{ $data['date'] }}</td>
                    <td>{{ $data['normal_hours'] }}</td>
                    <td>{{ $data['overtime_hours'] }}</td>
                    <td>{{ $data['holiday_hours'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>