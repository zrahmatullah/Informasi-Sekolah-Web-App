@component('mail::message')
# 📢 Pengumuman dari Sekolah

**Judul:**  
### {{ $surat->judul }}

Yth. Siswa/i Sekolah,

Kami berharap kalian semua dalam keadaan sehat dan semangat dalam belajar.

Melalui email ini, kami ingin menyampaikan **surat edaran terbaru** yang dikeluarkan oleh pihak sekolah. Surat ini berisi informasi penting yang perlu diperhatikan oleh seluruh siswa/i.

---

@component('mail::panel')
📝 **Detail Surat:**  
Judul: **{{ $surat->judul }}**  
Tanggal: {{ \Carbon\Carbon::now()->format('d M Y') }}
@endcomponent

---

Anda dapat membaca atau mengunduh surat edaran tersebut melalui tombol di bawah ini:

@component('mail::button', ['url' => url('/storage/' . $surat->file), 'color' => 'success'])
📄 Unduh Surat Edaran
@endcomponent

Mohon untuk segera membaca dan memahami isi surat tersebut.

Apabila ada pertanyaan atau klarifikasi lebih lanjut, silakan hubungi wali kelas atau pihak sekolah.

Terima kasih atas perhatian dan kerja samanya.

Salam hormat,  
**Manajemen Sekolah**  
{{ config('app.name') }}
@endcomponent
