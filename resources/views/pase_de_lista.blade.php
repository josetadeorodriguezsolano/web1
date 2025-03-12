<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script src="js/jquery-3.7.1.js"></script>
    <script>
        $.fn.hasAttr = function(name) {
            return this.attr(name) !== undefined;
        };

        $(document).ready(function(){
            $("input[type='checkbox']").click(function() {
                let x = $(this).hasAttr('checked');
                let id = $(this).attr('alumno_id');
                if (x){
                    $.get("pase_de_lista/inasistencia/vino/"+id, function(data, status){
                      alert("Data: " + data + "\nStatus: " + status);
                    });
                }
                else {
                    $.get("pase_de_lista/inasistencia/falto/"+id, function(data, status){
                      alert("Data: " + data + "\nStatus: " + status);
                    });
                }
            });
        });
    </script>
</head>
<body>
    Seleccione el grupo:
        <select name="selectGrupo">
            @foreach ($gruposImpartidos as $gpo)
                <option value='{{$gpo->id}}'>{{$gpo->materia->grado}}{{$gpo->letra}} {{$gpo->materia->nombre}}</option>
            @endforeach
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
                <td><input type="checkbox" name='alumno1' alumno_id='{{$alumno->id}}'
                @if($alumno->falto(date('Y-m-d'))!=null)
                    checked
                @endif
                ></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
