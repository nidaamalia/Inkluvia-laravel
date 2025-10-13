<p>Pendaftaran lembaga baru:</p>
<ul>
    <li>Nama: {{ $data['name'] }}</li>
    <li>Email: {{ $data['email'] }}</li>
    <li>Tipe: {{ $data['type'] }}</li>
    <li>Nama Lembaga: {{ $data['institution_name'] }}</li>
    <li>Alamat: {{ $data['address'] }}</li>
    @if(!empty($data['description']))
        <li>Deskripsi: {{ $data['description'] }}</li>
    @endif
    <li>Dikirim pada: {{ now()->format('d M Y H:i') }}</li>

</ul>


