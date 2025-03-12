<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    Seleccione el grupo:
        <select name="selectGrupo">
            <option value='1000'>1A Matericas</option>
            <option value='1001'>1B Matericas</option>
            <option value='1002'>2C Fisica</option>
        </select>
    Lista de Asistencia:
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Nombre</th>
                <th>Asistio</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($grupo->alumnos as $key => $alumno)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$alumno->apellidos}} {{$alumno->nombres}}</td>
                <td><input type="checkbox" name='alumno1' checked></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
