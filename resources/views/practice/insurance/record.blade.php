<table style="width:100%">
    <thead>
        <tr>
       @for ($i = 0; $i < count($columnheading); $i++)     
            <th>{{ $columnheading[$i] }}</th>
       @endfor     
        </tr>
    </thead>
    <tbody>
        @foreach($data as $ins_data)
        <tr>
            @for ($i = 0; $i < count($columns); $i++)
                <td>{{ $ins_data->$columns[$i] }}</td>
            @endfor
        </tr>
        @endforeach
    </tbody>
</table>