<div class="table-responsive">
    <table class="table table-bordered table-striped" id="example1">
        <thead>
            <tr>
                <th>Short Name</th>
                <th>Provider</th>
                <th>Type</th>
                <th>NPI</th>
                <th>Scheduled</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($providers) && $providers !='')
            @foreach($providers as $provider)
            <?php 
                $provider_id = App\Http\Helpers\Helpers::getEncodeAndDecodeOfId($provider->id,'encode');
                $provider_types = App\Models\Provider_type::get_provider_types_name($provider->provider_types_id);
                $scheduled_count = App\Models\ProviderScheduler::getScheduledCountByProviderId($provider_id,'Provider');
            ?>
            <tr data-url="{{ url('practicescheduler/provider/'.$provider_id) }}" class="js-table-click clsCursor cur-pointer">
                <td>
                    <div class="col-lg-12 p-b-0 p-l-0">
                        <a id="someelem{{hash('sha256',@$provider->id )}}" class="someelem" data-id="{{hash('sha256',@$provider->id)}}" href="javascript:void(0);"> {{ @$provider->short_name }}</a>
                        @include ('layouts/provider_hover')
                    </div>
                </td>
                <td>{{ str_limit($provider->provider_name.' '.@$provider->degrees->degree_name,25,'...') }}</td>
                <td>{{ $provider_types }}</td>
                <td>{{ $provider->npi }}</td>
                <td>@if($scheduled_count > 0) Yes @else No @endif</td>
            </tr>
            @endforeach
            @endif
        </tbody>
    </table>
</div><!-- /.box-body -->