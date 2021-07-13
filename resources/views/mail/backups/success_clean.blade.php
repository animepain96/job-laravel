Your old backups database was successfully cleaned.
<br>
<br>
These are {{count($data['fileList'])}} was deleted.
<br>
@foreach($data['fileList'] as $file)
    <strong>{{$file}}</strong>
    <br>
@endforeach
