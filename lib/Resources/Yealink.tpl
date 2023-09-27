<?xml version="1.0" encoding="UTF-8"?>
<YealinkIPPhoneDirectory>
{{ section reiterate start }}
{% if(isset($data)): %}
<DirectoryEntry >
    <Name>{{ $data->Name->First }}</Name>
    {% foreach ($data->Phone as $entry): %}
    {% if(!empty($entry->Number)): %}
    <Telephone label="{{ $entry->Type }} {{ $entry->SubType }}">{{ $entry->Number }}</Telephone>
    {% endif; %}
    {% endforeach; %}
</DirectoryEntry>
{% endif; %}
{{ section reiterate end }}
</YealinkIPPhoneDirectory>