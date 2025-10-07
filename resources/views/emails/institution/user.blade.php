<p>Halo {{ $data['name'] }},</p>

<p>Terima kasih. Kami telah menerima pendaftaran lembaga Anda dengan detail berikut:</p>
<ul>
    <li>Tipe: {{ $data['type'] }}</li>
    <li>Nama Lembaga: {{ $data['institution_name'] }}</li>
    <li>Alamat: {{ $data['address'] }}</li>
    @if(!empty($data['description']))
        <li>Deskripsi: {{ $data['description'] }}</li>
    @endif
</ul>

<p>Tim Inkluvia akan meninjau dan menghubungi Anda dalam 1-3 hari kerja.
Jika butuh cepat, balas email ini atau WhatsApp kami di 0857-4979-7955.</p>

<p>Salam hangat,<br>Tim Inkluvia</p>


