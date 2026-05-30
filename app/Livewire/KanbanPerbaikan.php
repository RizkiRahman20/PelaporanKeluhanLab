<?php

namespace App\Livewire;

use App\Models\Perbaikan;
use App\Models\RiwayatPerbaikan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
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
        return Auth::user()
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

    public function updateStatus(int $idPerbaikan, string $statusBaru): void
    {
        if ($statusBaru === 'selesai') {
            return;
        }

        if (! array_key_exists($statusBaru, $this->columns)) {
            return;
        }

        $perbaikan = $this->findPerbaikanForAdmin($idPerbaikan);

        if ($perbaikan->status_perbaikan === 'selesai') {
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
            'catatan_rw' => "Status diubah melalui kanban dari {$statusLama} ke {$statusBaru}.",
            'id_perbaikan' => $perbaikan->id_perbaikan,
        ]);
    }

    public function openDetail(int $idPerbaikan): void
    {
        $perbaikan = $this->findPerbaikanForAdmin($idPerbaikan);

        $this->selectedPerbaikanId = $perbaikan->id_perbaikan;

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

        if ($perbaikan->status_perbaikan === 'selesai') {
            return;
        }

        $this->selectedPerbaikanId = $perbaikan->id_perbaikan;
        $this->catatan_pbk = null;
        $this->ft_perbaikan = null;
        $this->showSelesaiModal = true;
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
        ]);

        $perbaikan = $this->findPerbaikanForAdmin($this->selectedPerbaikanId);

        $path = $this->ft_perbaikan->store('perbaikan', 'public');

        $perbaikan->update([
            'status_perbaikan' => 'selesai',
            'tgl_selesai' => now()->toDateString(),
            'ft_perbaikan' => $path,
            'catatan_pbk' => $this->catatan_pbk,
            'app_validasi' => 'menunggu',
        ]);

        RiwayatPerbaikan::create([
            'tgl_ubah' => now()->toDateString(),
            'catatan_rw' => 'Perbaikan diselesaikan melalui kanban dan menunggu validasi SPV. Catatan: ' . $this->catatan_pbk,
            'id_perbaikan' => $perbaikan->id_perbaikan,
        ]);

        $this->closeSelesai();
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