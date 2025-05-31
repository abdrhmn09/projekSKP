
<!DOCTYPE html>
<html>
<head>
    <title>Laporan SKP</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN SISTEM PENILAIAN KINERJA PEGAWAI</h2>
        <p>SMA Negeri X</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Periode</th>
                <th>Nilai SKP</th>
                <th>Nilai Perilaku</th>
                <th>Nilai Akhir</th>
                <th>Kategori</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->pegawai->user->nip }}</td>
                <td>{{ $item->pegawai->user->name }}</td>
                <td>{{ $item->periode->nama_periode }}</td>
                <td>{{ $item->nilai_skp }}</td>
                <td>{{ $item->nilai_perilaku }}</td>
                <td>{{ $item->nilai_akhir }}</td>
                <td>{{ $item->kategori }}</td>
                <td>{{ $item->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
