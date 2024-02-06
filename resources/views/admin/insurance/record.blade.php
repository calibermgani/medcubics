<table style="width:100%">
                <thead>
                    <tr>
                   @foreach($columnheading as $heading)     
                        <th>{{$heading}}</th>
                   @endforeach     
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $data_values)
                        <tr>                            
                            @foreach($columns_index as $name)<td><?php echo $data_values->$name; ?></td>@endforeach
                        </tr>
                    @endforeach               
                </tbody>
            </table>
