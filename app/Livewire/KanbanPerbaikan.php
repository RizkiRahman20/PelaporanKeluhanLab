<?php

namespace App\Livewire;

use App\Models\Perbaikan;
use App\Models\RiwayatPerbaikan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class KanbanPerbaikan extends Component
{
    use WithFileUploads;

    public array $columns = [
        'antrean' => [
            'label' => 'Antrean',
            'icon' => 'clock',
            'color' => '#FFF15A',
        ],
        'menunggu_sparepart' => [
            'label' => 'Menunggu Sparepart',
            'icon' => 'exclamation',
            'color' => '#FF6B6B',
        ],
        'dikerjakan' => [
            'label' => 'Dikerjakan',
            'icon' => 'wrench',
            'color' => '#74BDF5',
        ],
        'selesai' => [
            'label' => 'Selesai',
            'icon' => 'check',
            'color' => '#42F542',
        ],
    ];

    public ?int $selectedPerbaikanId = null;

    public bool $showDetailModal = false;
    public bool $showSelesaiModal = false;

    public ?string $catatan_pbk = null;
    public $ft_perbaikan = null;

    public array $detailData = [];

    protected function getAssignedLabIds()
    {
        $user = Auth::user();

        if (! $user) {
            return collect();
        }

        return $user
            ->penugasanUserLabs()
            ->where('status_aktif', 'aktif')
            ->pluck('id_lab');
    }

    protected function baseQuery(): Builder
    {
        $labIds = $this->getAssignedLabIds();

        return Perbaikan::query()
            ->with([
                'laporan.lab',
                'laporan.penugasan.user',
            ])
            ->whereHas('laporan', function (Builder $query) use ($labIds) {
                $query->whereIn('id_lab', $labIds);
            });
    }

    protected function findPerbaikanForAdmin(int $idPerbaikan): Perbaikan
    {
        return $this->baseQuery()
            ->where('id_perbaikan', $idPerbaikan)
            ->firstOrFail();
    }

    protected function perbaikanTerkunci(Perbaikan $perbaikan): bool
    {
        return $perbaikan->status_perbaikan === 'selesai'
            || $perbaikan->app_validasi === 'divalidasi';
    }

    protected function bolehDiselesaikan(Perbaikan $perbaikan): bool
{
    return $perbaikan->status_perbaikan === 'dikerjakan';
}

    protected function makePublicUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/storage/')) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        if (str_starts_with($path, 'public/')) {
            $path = str_replace('public/', '', $path);
        }

        return Storage::url($path);
    }

    public function updateStatus(int $idPerbaikan, string $statusBaru): void
{
    if (! array_key_exists($statusBaru, $this->columns)) {
        $this->dispatch(
            'kanban-notify',
            type: 'error',
            message: 'Status tidak valid.'
        );

        return;
    }

    $perbaikan = $this->findPerbaikanForAdmin($idPerbaikan);

    if ($this->perbaikanTerkunci($perbaikan)) {
        $this->dispatch(
            'kanban-notify',
            type: 'warning',
            message: 'Perbaikan yang sudah selesai/divalidasi tidak bisa dipindahkan lagi.'
        );

        return;
    }

    if ($statusBaru === 'selesai') {
        if (! $this->bolehDiselesaikan($perbaikan)) {
            $this->dispatch(
                'kanban-notify',
                type: 'warning',
                message: 'Perbaikan harus masuk ke status Dikerjakan terlebih dahulu sebelum diselesaikan.'
            );

            return;
        }

        $this->openSelesaikan($idPerbaikan);

        return;
    }

    if ($perbaikan->status_perbaikan === $statusBaru) {
        return;
    }

    $statusLama = $perbaikan->status_perbaikan;

    $updateData = [
        'status_perbaikan' => $statusBaru,
    ];

    if ($statusBaru === 'dikerjakan' && blank($perbaikan->tgl_mulai)) {
        $updateData['tgl_mulai'] = now()->toDateString();
    }

    $perbaikan->update($updateData);

    RiwayatPerbaikan::create([
        'tgl_ubah' => now()->toDateString(),
        'catatan_rw' => "Status diubah dari {$statusLama} ke {$statusBaru} melalui Kanban.",
        'id_perbaikan' => $perbaikan->id_perbaikan,
        'id_user' => Auth::id(),
    ]);

    $this->dispatch(
        'kanban-notify',
        type: 'success',
        message: 'Status perbaikan berhasil diupdate.'
    );
}

    public function openDetail(int $idPerbaikan): void
    {
        $perbaikan = $this->findPerbaikanForAdmin($idPerbaikan);

        $this->selectedPerbaikanId = $perbaikan->id_perbaikan;

        $fotoLaporan = $perbaikan->laporan?->file_foto;

        $this->detailData = [
            'no_laporan' => $perbaikan->id_laporan,
            'lab' => $perbaikan->laporan?->lab?->nm_lab ?? '-',
            'pelapor' => $perbaikan->laporan?->nm_pelapor ?? '-',
            'nim' => $perbaikan->laporan?->nim_pelapor ?? '-',
            'fakultas' => $perbaikan->laporan?->fakultas_pelapor ?? '-',
            'kategori' => $perbaikan->laporan?->kategori ?? '-',
            'catatan_lpr' => $perbaikan->laporan?->catatan_lpr ?? '-',
            'status_perbaikan' => $perbaikan->status_perbaikan,
            'app_validasi' => $perbaikan->app_validasi,
            'file_foto' => $fotoLaporan,
            'foto_url' => $this->makePublicUrl($fotoLaporan),
            'ft_perbaikan' => $perbaikan->ft_perbaikan,
            'ft_perbaikan_url' => $this->makePublicUrl($perbaikan->ft_perbaikan),
            'catatan_pbk' => $perbaikan->catatan_pbk ?? '-',
            'alasan_penolakan' => $perbaikan->alasan_penolakan ?? '-',
        ];

        $this->showDetailModal = true;
    }

    public function closeDetail(): void
    {
        $this->showDetailModal = false;
        $this->detailData = [];
        $this->selectedPerbaikanId = null;
    }

    public function openSelesaikan(int $idPerbaikan): void
{
    $perbaikan = $this->findPerbaikanForAdmin($idPerbaikan);

    if ($this->perbaikanTerkunci($perbaikan)) {
        $this->dispatch(
            'kanban-notify',
            type: 'warning',
            message: 'Perbaikan ini sudah selesai/divalidasi dan tidak bisa diselesaikan ulang.'
        );

        return;
    }

    if (! $this->bolehDiselesaikan($perbaikan)) {
        $this->dispatch(
            'kanban-notify',
            type: 'warning',
            message: 'Perbaikan harus masuk ke status Dikerjakan terlebih dahulu sebelum diselesaikan.'
        );

        return;
    }

    $this->selectedPerbaikanId = $perbaikan->id_perbaikan;
    $this->showSelesaiModal = true;

    $this->resetValidation();
    $this->reset(['ft_perbaikan', 'catatan_pbk']);
}

    public function closeSelesai(): void
    {
        $this->showSelesaiModal = false;
        $this->selectedPerbaikanId = null;
        $this->catatan_pbk = null;
        $this->ft_perbaikan = null;

        $this->resetValidation();
    }

    public function selesaikanPerbaikan(): void
    {
        $this->validate([
            'selectedPerbaikanId' => ['required', 'integer'],
            'catatan_pbk' => ['required', 'string', 'max:2000'],
            'ft_perbaikan' => ['required', 'image', 'max:2048'],
        ], [
            'catatan_pbk.required' => 'Catatan perbaikan wajib diisi.',
            'ft_perbaikan.required' => 'Bukti perbaikan wajib diupload.',
            'ft_perbaikan.image' => 'File bukti harus berupa gambar.',
            'ft_perbaikan.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $perbaikan = $this->findPerbaikanForAdmin((int) $this->selectedPerbaikanId);

        if ($this->perbaikanTerkunci($perbaikan)) {
            $this->closeSelesai();

            $this->dispatch(
                'kanban-notify',
                type: 'warning',
                message: 'Perbaikan ini sudah selesai/divalidasi dan tidak bisa diselesaikan ulang.'
            );

            return;
        }

        $path = $this->ft_perbaikan->store('perbaikan', 'public');

        $perbaikan->update([
            'status_perbaikan' => 'selesai',
            'tgl_selesai' => now()->toDateString(),
            'ft_perbaikan' => $path,
            'catatan_pbk' => $this->catatan_pbk,
            'app_validasi' => 'menunggu',
            'alasan_penolakan' => null,
        ]);

        RiwayatPerbaikan::create([
            'tgl_ubah' => now()->toDateString(),
            'catatan_rw' => 'Perbaikan diselesaikan melalui Kanban dan menunggu validasi SPV. Catatan: ' . $this->catatan_pbk,
            'id_perbaikan' => $perbaikan->id_perbaikan,
            'id_user' => Auth::id(),
        ]);

        $this->closeSelesai();

        $this->dispatch(
            'kanban-notify',
            type: 'success',
            message: 'Perbaikan berhasil diselesaikan dan menunggu validasi SPV.'
        );
    }

    public function render()
    {
        $perbaikans = [];

        foreach (array_keys($this->columns) as $status) {
            $perbaikans[$status] = (clone $this->baseQuery())
                ->where('status_perbaikan', $status)
                ->latest()
                ->get();
        }

        return view('livewire.kanban-perbaikan', [
            'perbaikans' => $perbaikans,
        ]);
    }
}