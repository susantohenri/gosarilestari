# Henri Generator

Code generator untuk framework CodeIgniter 3 yang menghasilkan **Controller**, **Model**, dan **Migration** secara otomatis dari file JSON.

## Cara Kerja

Generator membaca `structure.json`, lalu menggunakan template (`Controller.php`, `Model.php`, `Migration.php`) untuk menghasilkan file-file yang dibutuhkan. Setiap entity dalam JSON akan menghasilkan:

| File      | Lokasi                    | Contoh |
|-----------|---------------------------|--------|
| Controller| `application/controllers/`| `Income.php` |
| Model     | `application/models/`     | `Incomes.php` |
| Migration | `application/migrations/` | `006_income.php` |

Selain itu, generator juga:
- Memperbarui **seeds migration** (menambah entity baru ke permission admin)
- Memperbarui **Permissions model** (menambah entity ke dropdown)

---

## Menjalankan Generator

```bash
cd application/generator
php Generate.php structure.json
```

### Generate Sebagian

```bash
# Hanya controller
php Generate.php structure.json controller

# Hanya model
php Generate.php structure.json model

# Hanya migration
php Generate.php structure.json migration
```

> **Parameter kedua** menerima: `all` (default), `controller`, `model`, `migration`

---

## Rollback

Mengembalikan semua file yang di-generate ke kondisi git terakhir:

```bash
cd application/generator
bash Rollback.sh
```

> **Perhatian:** Rollback menghapus SEMUA controller, model, dan migration, lalu restore dari git. Pastikan perubahan manual sudah di-commit sebelum menjalankan ini.

---

## Format `structure.json`

File berisi array of object. Setiap object merepresentasikan satu entity/modul.

### Struktur Minimal

```json
[
  {
    "controller": "Category",
    "fields": [
      { "name": "Name" }
    ]
  }
]
```

Ini sudah cukup untuk menghasilkan:
- Controller `Category` → Model `Categorys` → Tabel `category`

### Struktur Lengkap

```json
{
  "controller": "Income",
  "model": "Incomes",
  "table": "income",
  "fields": [ ... ],
  "childs": [ ... ]
}
```

---

## Properti Entity

| Properti    | Wajib | Default              | Keterangan |
|-------------|-------|----------------------|------------|
| `controller`| ✅    | —                    | Nama controller (PascalCase). Juga menentukan nama file dan URL routing |
| `model`     | ❌    | `{controller}s`      | Nama class model. Contoh: controller `Category` → model `Categorys` |
| `table`     | ❌    | `{controller}` lowercase | Nama tabel database. Contoh: controller `Income` → tabel `income` |
| `fields`    | ✅    | —                    | Array of field (lihat bagian Field) |
| `childs`    | ❌    | `[]`                 | Subform / relasi one-to-many (lihat bagian Childs) |

> **Catatan `model`:** Default menambahkan "s" langsung ke nama controller. Jika nama controller `Category`, model default-nya `Categorys` (bukan `Categories`). Override dengan properti `model` jika perlu.

---

## Properti Field

| Properti | Wajib | Default        | Keterangan |
|----------|-------|----------------|------------|
| `name`   | ✅    | —              | Nama field (PascalCase). Menjadi nama kolom di database dan `name` di form |
| `type`   | ❌    | `string`       | Tipe field (lihat tabel tipe di bawah) |
| `label`  | ❌    | `ucfirst(name)`| Label yang ditampilkan di form. Gunakan jika nama field tidak human-readable |
| `width`  | ❌    | `2`            | Lebar kolom di subform (bootstrap grid `col-sm-{width}`) |
| `options`| ❌    | —              | Array of string untuk dropdown statis |
| `model`  | ❌    | —              | **Khusus `type: relation`**. Nama model yang di-relasikan |
| `field`  | ❌    | —              | **Khusus `type: relation`**. Field yang ditampilkan dari model relasi |
| `form`   | ❌    | `true`         | Set `false` untuk menyembunyikan field dari form (tetap ada di migration) |

### Tipe Field

| Type      | Kolom DB     | Input Form          | Keterangan |
|-----------|--------------|---------------------|------------|
| `string`  | `VARCHAR(255) | Text input         | Default. Field teks biasa |
| `date`    | `DATE`       | Datepicker          | Bootstrap datepicker (`yyyy-mm-dd`) |
| `datetime`| `DATETIME`   | Daterangepicker     | Single datetime picker (`YYYY-MM-DD HH:mm`) |
| `int` / `number` | `INT(11) | Number input     | Input angka dengan auto-formatting (comma separator) |
| `tinyint` | `TINYINT(2)` | Text input          | — |
| `boolean` | `TINYINT(1)` | Text input          | — |
| `relation`| `VARCHAR(36)`| Select2 autocomplete| Relasi ke tabel lain via UUID. Kolom otomatis di-index |

---

## Contoh-Contoh Field

### Field Teks Biasa

```json
{ "name": "Name" }
```

Hasil: kolom `VARCHAR(255)`, text input dengan label "Name".

### Field dengan Custom Label

```json
{
  "name": "InvoiceNumber",
  "label": "Invoice Number"
}
```

Nama kolom tetap `InvoiceNumber`, tapi label di form jadi "Invoice Number".

### Field Date

```json
{
  "name": "DateAcquired",
  "type": "date",
  "label": "Date Acquired"
}
```

### Field Datetime

```json
{
  "name": "TransactionTime",
  "type": "datetime",
  "label": "Transaction Time"
}
```

### Field Angka

```json
{
  "name": "Price",
  "type": "int"
}
```

Hasil: kolom `INT(11)`, input dengan auto-formatting angka (comma separator saat mengetik).

### Field Dropdown Statis

```json
{
  "name": "Active",
  "options": ["Yes", "No"]
}
```

Hasil: dropdown `<select>` dengan opsi Yes dan No. Tipe tetap `string` → kolom `VARCHAR(255)`.

### Field Relasi (Foreign Key)

```json
{
  "name": "Category",
  "type": "relation",
  "model": "Categories",
  "field": "Name"
}
```

Hasil:
- Kolom `VARCHAR(36)` + INDEX (menyimpan UUID)
- Form: Select2 autocomplete yang search ke model `Categories`, menampilkan field `Name`
- Endpoint search otomatis tersedia di `/{Controller}/select2/Categories/Name`

### Menyembunyikan Field dari Form

```json
{
  "name": "InternalCode",
  "form": false
}
```

Field tetap ada di migration (kolom database dibuat), tapi tidak muncul di form.

### Field dengan Custom Width (Subform)

```json
{
  "name": "Quantity",
  "type": "int",
  "width": 3
}
```

Properti `width` mengatur lebar kolom di subform menggunakan Bootstrap grid (`col-sm-3`).

---

## Childs (Subform / One-to-Many)

Childs memungkinkan satu entity memiliki detail rows (master-detail pattern). Contoh: Role memiliki banyak Permission.

### Definisi di Entity Parent

```json
{
  "controller": "Order",
  "fields": [
    { "name": "CustomerName", "label": "Customer Name" },
    { "name": "OrderDate", "type": "date", "label": "Order Date" }
  ],
  "childs": [
    {
      "label": "Order Items",
      "controller": "OrderItem",
      "model": "OrderItems"
    }
  ]
}
```

| Properti    | Wajib | Default        | Keterangan |
|-------------|-------|----------------|------------|
| `label`     | ✅    | —              | Label yang ditampilkan sebagai judul subform |
| `controller`| ✅    | —              | Nama controller child |
| `model`     | ❌    | `{controller}s`| Nama model child |

### Cara Kerja Subform

1. Form parent menampilkan tombol "Input {label}" untuk menambah row
2. Setiap row child di-load via AJAX (`/{ChildController}/subformcreate` dan `/{ChildController}/subformread/{uuid}`)
3. Data child disimpan bersamaan saat form parent di-submit
4. Child yang dihapus dari form akan otomatis dihapus dari database
5. Relasi parent-child menggunakan kolom bernama sama dengan nama tabel parent di tabel child

> **Penting:** Entity child harus juga didefinisikan di `structure.json` sebagai entity tersendiri agar controller, model, dan migration-nya ikut di-generate.

---

## Contoh Lengkap Multi-Entity

```json
[
  {
    "controller": "Order",
    "model": "Orders",
    "fields": [
      { "name": "OrderNumber", "label": "Order Number" },
      {
        "name": "Customer",
        "type": "relation",
        "model": "Customers",
        "field": "Name"
      },
      { "name": "OrderDate", "type": "date", "label": "Order Date" },
      { "name": "Status", "options": ["Pending", "Processing", "Completed", "Cancelled"] },
      { "name": "TotalAmount", "type": "int", "label": "Total Amount" },
      { "name": "Notes" }
    ],
    "childs": [
      {
        "label": "Order Items",
        "controller": "OrderItem",
        "model": "OrderItems"
      }
    ]
  },
  {
    "controller": "OrderItem",
    "model": "OrderItems",
    "fields": [
      {
        "name": "Product",
        "type": "relation",
        "model": "Products",
        "field": "Name",
        "width": 3
      },
      { "name": "Quantity", "type": "int", "width": 2 },
      { "name": "Price", "type": "int", "width": 2 }
    ]
  },
  {
    "controller": "Customer",
    "model": "Customers",
    "fields": [
      { "name": "Name" },
      { "name": "Phone" },
      { "name": "Address" }
    ]
  },
  {
    "controller": "Product",
    "model": "Products",
    "fields": [
      { "name": "Name" },
      { "name": "SKU" },
      { "name": "Price", "type": "int" },
      { "name": "Stock", "type": "int" }
    ]
  }
]
```

---

## Yang Otomatis Disediakan per Entity

Setelah generate, setiap entity langsung memiliki:

| Fitur          | URL                              | Keterangan |
|----------------|----------------------------------|------------|
| List (DataTable)| `/{Controller}`                 | Tabel server-side dengan sorting & pagination |
| Create         | `/{Controller}/create`           | Form input |
| Read/Edit      | `/{Controller}/read/{uuid}`      | Form edit dengan data ter-populate |
| Delete         | `/{Controller}/delete/{uuid}`    | Halaman konfirmasi delete |
| DataTable API  | `/{Controller}/dt`               | Endpoint JSON untuk DataTable |
| Select2 Search | `/{Controller}/select2/{Model}/{Field}` | Endpoint autocomplete untuk field relasi |

Semua fitur di atas dikontrol oleh **permission** per role (index, create, read, update, delete).

---

## Kolom Otomatis di Setiap Tabel

Setiap tabel yang di-generate otomatis memiliki kolom berikut (tidak perlu didefinisikan di fields):

| Kolom     | Tipe         | Keterangan |
|-----------|--------------|------------|
| `uuid`    | `VARCHAR(36)` PK | Primary key, di-generate otomatis oleh MySQL `UUID()` |
| `orders`  | `INT(11)` UNIQUE AUTO_INCREMENT | Nomor urut, digunakan untuk sorting default |
| `createdAt`| `DATETIME`   | Waktu pembuatan record |
| `updatedAt`| `DATETIME`   | Waktu update terakhir |

---

## Catatan Penting

1. **Jalankan dari folder `generator/`** — Semua path di `Generate.php` relatif terhadap lokasi ini.

2. **Migration sequential** — Nomor migration otomatis dihitung dari migration terakhir yang ada. File `seeds` akan dipindah ke urutan paling akhir.

3. **Nama tabel dari controller** — Jika field `table` tidak diisi, nama tabel = `strtolower(controller)`. Controller `OrderItem` → tabel `orderitem`.

4. **Underscore di nama tabel** — Nama tabel yang mengandung underscore akan dikonversi menjadi `}}` di nama class migration (karena CodeIgniter menggunakan underscore sebagai separator di nama file migration). Contoh: tabel `order_item` → class `Migration_order}}item`. **Sebaiknya hindari underscore di nama tabel.**

5. **Commit dulu sebelum generate** — Rollback menggunakan `git checkout` untuk restore file. Pastikan semua perubahan manual sudah di-commit.

6. **Field pertama jadi kolom DataTable** — Field pertama di array `fields` otomatis ditampilkan sebagai kolom di DataTable (selain kolom `orders` yang hidden).

7. **Setelah generate, jalankan migration** — File migration hanya dibuat, belum dieksekusi. Jalankan migration melalui mekanisme CodeIgniter (biasanya via URL atau CLI) untuk membuat tabel di database.
