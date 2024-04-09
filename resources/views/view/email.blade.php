<h4>{{ $email['title'] }}</h4>
<body>
    {{ $email['body'] }}
    <h4>Rincian</h4>
        <div style="margin-top: -8px;">
            <p style="font-size:10px;">Nama : {{ $email['name'] }}</p>
            <p style="font-size:10px;">Jurusan : {{ $email['major_name'] }}</p>
            <p style="font-size:10px;">Tagihan : {{ $email['bill_name'] }}</p>
            <p style="font-size:10px;">Status : {{ $email['status'] }}</p> 
            <p style="font-size:10px;">Total biaya : Rp.{{ $email['price'] }}</p>  
        </div>
    <p>Silakan klik tombol Menunggu Pembayaran</p>
   
    
</body>