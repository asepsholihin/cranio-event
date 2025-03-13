<table>
    <thead>
    <tr>
        <th>No</th>
        <th>Name</th>
        <th>Title</th>
        <th>No Passpor</th>
        <th>Tipe Kamar</th>
        <th>Room List</th>
        <th>Keterangan</th>
    </tr>
    </thead>
    <tbody>
    @foreach($participants as $key => $participant)
    @php
    $notes = $participant->room_group_notes;
    $findStyle = substr($notes ,strpos($notes,'style='));
    $getStyle = strpos($findStyle,'>');
    $style = substr($findStyle,0,$getStyle);

    $addStyle = 'style="color:#4d4d4d;"';
    @endphp
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $participant->name }}</td>
            <td>{{ $participant->title }}</td>
            <td>{{ $participant->no_passport }}</td>
            @if($participant->merge_room_count)
                <td rowspan="{{ $participant->merge_room_count }}">{{ $participant->room_type }} - {{ $participant->merge_room_count }}</td>
            @else
                <td>{{ $participant->room_type }} - {{ $participant->merge_room_count }}</td>
            @endif
            <td style="color:#e64c4c;background-color:#4ce64c;">{{ $participant->group_hotel_room }} - {{ $style }}</td>
            <td {{ $style }}>{!! $participant->room_group_notes !!}</td>
        </tr>
    @endforeach
    </tbody>
</table>