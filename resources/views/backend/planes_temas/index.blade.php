@extends('backend.layouts.master')

@section('page-header')
    <h3>
       Relación de Temas asociados con los Planes de Estudios</br>
       de la <b>{{$escuela->nombre}}</b><br>
       <b style="font-size:13px">({{$universidad->nombre}})</b>
     </h3>
@endsection



@section('renderjs')
  <style>
    .filterable {
    margin-top: 15px;
    }
    .filterable .panel-heading .pull-right {
      margin-top: -20px;
    }
    .filterable .filters input[disabled] {
      background-color: transparent;
      border: none;
      cursor: auto;
      box-shadow: none;
      padding: 0;
      height: auto;
    }
    .filterable .filters input[disabled]::-webkit-input-placeholder {
      color: #333;
    }
    .filterable .filters input[disabled]::-moz-placeholder {
      color: #333;
    }
    .filterable .filters input[disabled]:-ms-input-placeholder {
      color: #333;
    }

    .center {
    margin-top:50px;   
    }

    .modal-header {
    padding-bottom: 5px;
    }

    .modal-footer {
        padding: 0;
    }
    
    .modal-footer .btn-group button {
    height:40px;
    border-top-left-radius : 0;
    border-top-right-radius : 0;
    border: none;
    border-right: 1px solid #ddd;
    }
    
    .modal-footer .btn-group:last-child > button {
    border-right: 0;
    }
  </style>
  <script>
      $(document).ready(function(){
    $('.filterable .btn-filter').click(function(){
        var $panel = $(this).parents('.filterable'),
        $filters = $panel.find('.filters input'),
        $tbody = $panel.find('.table tbody');
        if ($filters.prop('disabled') == true) {
            $filters.prop('disabled', false);
            $filters.first().focus();
        } else {
            $filters.val('').prop('disabled', true);
            $tbody.find('.no-result').remove();
            $tbody.find('tr').show();
        }
    });

    $('.filterable .filters input').keyup(function(e){
        /* Ignore tab key */
        var code = e.keyCode || e.which;
        if (code == '9') return;
        /* Useful DOM data and selectors */
        var $input = $(this),
        inputContent = $input.val().toLowerCase(),
        $panel = $input.parents('.filterable'),
        column = $panel.find('.filters th').index($input.parents('th')),
        $table = $panel.find('.table'),
        $rows = $table.find('tbody tr');
        /* Dirtiest filter function ever ;) */
        var $filteredRows = $rows.filter(function(){
            var value = $(this).find('td').eq(column).text().toLowerCase();
            return value.indexOf(inputContent) === -1;
        });
        /* Clean previous no-result if exist */
        $table.find('tbody .no-result').remove();
        /* Show all rows, hide filtered ones (never do that outside of a demo ! xD) */
        $rows.show();
        $filteredRows.hide();
        /* Prepend no-result row if all rows are filtered */
        if ($filteredRows.length === $rows.length) {
            $table.find('tbody').prepend($('<tr class="no-result text-center"><td colspan="'+ $table.find('.filters th').length +'">No result found</td></tr>'));
        }
    });
});
  </script>
@endsection


@section('content')

        <div class="panel panel-primary filterable">
            <div class="panel-heading">
                <h3 class="panel-title">Planes y Temas</h3>
                <div class="pull-right">
                  <button class="btn btn-default btn-xs btn-plus" data-toggle="modal" data-target="#squarespaceModal"><span class="glyphicon glyphicon-plus"></span> Asociar Plan</button>
                  <a class="btn btn-default btn-xs btn-plus"  href="/escuelas/{{$escuela->facultad_id}}"> Regresar</a>
                </div>
            </div>
            <div class="panel-body">
            @if($contador>0)
            <table class="table">
                <thead>
                    <tr class="filters">
                        <th>N</th>
                        <th>Tema</th>
                        <th>Plan de estudios</th>
                        <th>Periodo</th>
                        <th>Enlace</th>
                        <th>Curso(s)</th>
                        <th style="width: 10%;">Editar</th>   
                        <th style="width: 10%;">Eliminar</th>                        
                    </tr>
                </thead>
                <tbody>
                <?php $n=1; ?>
                  @foreach($planes_temas as $p)
                  <tr>
                    <td><?php echo $n; $n++; ?></td>
                    <td>{{$p->tema}}</td>
                    <td>{{$p->nombre}}</td></td>
                    <td>{{$p->periodo_inicio}} - {{$p->periodo_fin}}</td>                    
                    <td><a href="{{$p->enlace_plan}}" target="_blank">Entrar aquí</a></td>
                    <td>{{$p->curso}}</td></td>
                    <td><a href="/editar_plan/{{$p->id}}" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i> Editar</a></td>
                    <td><a href="/eliminar_plan/{{$p->id}}"class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Eliminar</a></td>
                  </tr>
                  @endforeach
                </tbody>
            </table>
            @else
            <div>No tiene temas asociados</div>
            @endif
            </div>
          </div>

       
<div class="modal fade" id="squarespaceModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
            <h3 class="modal-title" id="lineModalLabel">Registrar nuevo plan</h3>
        </div>
        <div class="modal-body">
            
            <!-- content goes here -->
            <form class="" method="post" class="form" role="form" action="{{route('store_plan')}}" enctype="">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

              <div class="form-group col-lg-12">
                <label for="exampleInputEmail1">Tema</label>
                <select name="tema_id" class="form-control" required="">
                  <option value="">Seleccionar</option>
                  @foreach($temas as $te)
                  <option value="{{$te->id}}">{{$te->nombre}}</option>
                  @endforeach
                </select>
                <input type="hidden" class="form-control" name="escuela_id" id="escuela_id" required="" value="{{$escuela->id}}"/>
              </div>     
              <div class="form-group col-lg-12">
                <label for="exampleInputEmail1">Plan de estudios</label>
                <input class="form-control" name="nombre" id="nombre" required=""/>
              </div>   
              <div class="form-group col-lg-6">
                <label for="exampleInputEmail1">Periódo inicio</label>
                <input class="form-control" name="periodo_inicio" id="periodo_inicio" required=""/>
              </div>  
              <div class="form-group col-lg-6">
                <label for="exampleInputEmail1">Periódo fin</label>
                <input class="form-control" name="periodo_fin" id="periodo_fin" required=""/>
              </div>   
              <div class="form-group col-lg-12">
                <label for="exampleInputEmail1">Enlace</label>
                <input class="form-control" name="enlace_plan" id="enlace_plan" required=""/>
              </div>  
              <div class="form-group col-lg-12">
                <label for="exampleInputEmail1">Cursos</label>
                <input class="form-control" name="cursos" id="cursos" required=""/>
              </div>   
			        <input type="submit" class="btn btn-success" value="Guardar" />   
            </form>

        </div>
       
    </div>
  </div>
</div>
@endsection