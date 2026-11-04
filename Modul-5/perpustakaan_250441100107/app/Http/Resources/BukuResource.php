<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Mengatur bentuk JSON yang dikirim API untuk satu buku.
 *
 * Pemetaan nama atribut respons  <-  kolom/relasi database:
 *   id          <-  bukus.id
 *   kategori    <-  kategoris.nama        (relasi Buku::kategori, BUKAN kolom kategori_id)
 *   kategori_id <-  bukus.kategori_id     (dipakai formulir ubah untuk memilih <select>)
 *   judul       <-  bukus.judul
 *   penulis     <-  bukus.penulis
 *   tahun       <-  bukus.tahun_terbit    (nama atribut berbeda dari nama kolom)
 */
class BukuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'kategori'    => $this->kategori?->nama,
            'kategori_id' => $this->kategori_id,
            'judul'       => $this->judul,
            'penulis'     => $this->penulis,
            'tahun'       => (int) $this->tahun_terbit,
        ];
    }
}
