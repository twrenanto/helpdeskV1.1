<div class="container-fluid">
	<h1 class="h3 mb-3 text-gray-800 font-weight-bold"><?= $title; ?></h1>

	<div class="flash-data" data-flashdata="<?= $this->session->flashdata('status') ?>"></div>

	<!-- Datatable -->
	<div class="card shadow mb-4">

		<div class="card-body">
			<a href="#modal-fade" title="Tambah <?= $title; ?>" class="btn btn-primary mb-3" data-toggle="modal">
				<i class="fa fa-plus"></i> Tambah <?= $title; ?>
			</a>
			<div class="table-responsive">
				<table class="table table-striped" id="dataTable" width="100%" cellspacing="0">
					<thead>
						<tr>
							<th>#</th>
							<th>Sub Kategori</th>
							<th>Kategori</th>
							<th>Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php $no = 1;
						foreach ($subkat as $row) { ?>
							<tr>
								<td><?= $no ?></td>
								<td><?= $row->nama_sub_kategori ?></td>
								<td><?= $row->nama_kategori ?></td>
								<td>
									<a href="<?= site_url('subkategori/edit/' . $row->id_sub_kategori) ?>" data-toggle="tooltip" title="Edit <?= $title; ?>" class="btn btn-primary btn-circle btn-sm mr-2"><i class="fa fa-edit"></i>
									</a>
									<a href="<?= site_url('subkategori/hapus/' . $row->id_sub_kategori) ?>" data-toggle="tooltip" title="Hapus <?= $title; ?>" class="btn btn-danger btn-circle btn-sm hapus"><i class="fas fa-trash"></i>
									</a>
								</td>
							</tr>
						<?php $no++;
						} ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<div id="modal-fade" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="m-0 font-weight-bold text-gray-800">Tambah <?= $title; ?></h6>
			</div>
			<div class="modal-body">
				<form id="form-validation" action="<?= site_url('subkategori/tambah') ?>" method="POST">

					<div class="form-group">
						<label>Nama Sub Kategori</label>
						<input class="form-control <?= (form_error('nama_sub_kategori') ? "is-invalid" : "") ?>" name="nama_sub_kategori" placeholder="">
						<div class="invalid-feedback">
							<?= form_error('nama_sub_kategori'); ?>
						</div>
					</div>

					<div class="form-group">
						<label>Kategori</label>
						<?= form_dropdown('id_kategori', $dd_kategori, set_value('id_kategori'), 'class="form-control ' . (form_error('id_kategori') ? "is-invalid" : "") . ' " '); ?>
						<div class="invalid-feedback">
							<?= form_error('id_kategori'); ?>
						</div>
					</div>

					<button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
					<button type="button" class="btn btn-secondary" class="close" data-dismiss="modal">Tutup</button>
				</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	const flashData = $('.flash-data').data('flashdata');
	if (flashData) {
		Swal.fire(
			'Sukses!',
			'Sub Kategori Berhasil ' + flashData,
			'success'
		)
	}

	$('.hapus').on('click', function(e) {
		e.preventDefault();
		const href = $(this).attr('href');

		Swal.fire({
			title: 'Apakah anda yakin?',
			text: "Sub Kategori akan dihapus",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Delete'
		}).then((result) => {
			if (result.value) {
				document.location.href = href;
			}
		})
	});
</script>